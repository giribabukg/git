<?php
class CInc_Log_Cnt extends CCor_Cnt {
  /*
   * If user logged with admin pass, activate all messages
   * @var boolead
   */
  private $mUsrLoggedAsAdmin = FALSE;


  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);

    if (!defined('LAN')) {
      $lLan = CCor_Cfg::get('default.lang', LANGUAGE);
      define('LAN', $lLan);
    }

    $this -> mTitle = lan('log.menu');
  }

  protected function actStd() {
    $this -> actIn();
  }

  protected function actIn() {
    $lHtmz = CUST_PATH_HTM;
    if(!file_exists($lHtmz.'login.htm')){
      $lHtmz = 'htm/default/';
    }
    
    $lTpl = new CCor_Tpl();
    $lTpl -> open($lHtmz.'login.htm');

    $lMsg = new CHtm_MsgBox(TRUE);
    $lNac = $this -> getReq('nact');

    $lTpl -> setPat('pg.nact',            htm($lNac));
    $lTpl -> setPat('pg.error',           $lMsg -> getContent());
    $lTpl -> setPat('pg.versioninfo',     CCor_Cfg::get('versioninfo'));
    //CSS
    $lTpl -> setPat('pg.utilcss',         $this->getCssPathFor('util.css'));
    $lTpl -> setPat('pg.stylecss',        $this->getCssPathFor('style.css'));
    $lTpl -> setPat('pg.jqueryuicss',     'js'.DS.'jquery'.DS.'jquery-ui.css');
    $lTpl -> setPat('pg.jqueryuithemecss','js'.DS.'jquery'.DS.'jquery.ui.theme.css');
    //JS
    $lTpl -> setPat('pg.jqueryjs', 'js'.DS.'jquery'.DS.'jquery.min.js');
    $lTpl -> setPat('pg.jqueryuijs', 'js'.DS.'jquery'.DS.'jquery-ui.min.js');
    //IMG
    $lTpl -> setPat('pg.favicon',         getImgPath('img/pag/favicon.ico'));
    $lTpl -> setPat('pg.banner',          getImgPath('img/login/banner.png')); // MAND ist zu diesem Zeitpunkt nicht bekannt!
    $lTpl -> setPat('pg.loginbutton',     getImgPath('img/login/btn-login.gif')); // MAND ist zu diesem Zeitpunkt nicht bekannt!
    $lTpl -> setPat('pg.mand.head',       getImgPath('img/login/bg_head.png'));
    $lTpl -> setPat('pg.mand.cust',       getImgPath('img/login/cust.png'));
    $lTpl -> setPat('pg.mand.customer',   getImgPath('img/login/customer.png'));
    $lTpl -> setPat('pg.mand.headerback', getImgPath('img/login/header-back.png'));
    $lTpl -> setPat('pg.cust.name',       CUSTOMER_NAME_LOGIN);
    $lTpl -> setPat('pg.lib.wait',        htm(lan('lib.wait')));
    $lTpl -> setPat('pg.log.pwd.forgot',  htm(lan('log.pwd.forgot')));
    $lTpl -> setPat('pg.usr.name',        htm(lan('usr.name')));

    $lTpl -> setPat('pg.sysMsg', CInc_Sys_Msg_Cnt::getMessages());

    CCor_Msg::getInstance() -> clear();
    $lTpl -> setPat('log.btn', btn('Login', '', '', 'submit'));
    $lTpl -> render();
  }
  
  protected function getCssPathFor($aFilename)
  {
    $lSpecific = CUST_PATH_HTM.'css/'.$aFilename;
    return (file_exists($lSpecific)) ? $lSpecific : 'htm/default/css/'.$aFilename;
  }

  protected function actCommit() {
    $lCfg = CCor_Cfg::getInstance();
    $lMag = $lCfg -> get('log.admin');
    $lMas = $lCfg -> get('log.master');

    $lUsr = $this -> getReq('usr');
    $lPwd = $this -> getReq('pwd');
    $lEnc = CApp_Pwd::encryptPassword($lPwd);
    
    
    //force connect
    $lQry = new CCor_Qry('SELECT 1');

    $lMandAdmin = CCor_Qry::getInt('SELECT id FROM al_sys_mand WHERE pass='.esc($lEnc));
    if ($lEnc == $lMas OR $lEnc == $lMag) {
      $lQry -> query('SELECT * FROM al_usr u, al_usr_mand m WHERE user='.esc($lUsr).' AND u.id = m.uid GROUP BY u.id');
      // User logged with Admin Pass
      $this -> mUsrLoggedAsAdmin = ($lEnc == $lMag) ? TRUE : FALSE; // Activate all messages
      $lRow =  $lQry -> getAssoc();
    } else {
      $lQry -> query('SELECT * FROM al_usr u, al_usr_mand m WHERE user='.esc($lUsr).' AND pass='.esc($lEnc).' AND u.id = m.uid GROUP BY u.id');
      $lRow =  $lQry -> getAssoc();
      if ($lMandAdmin && !$lRow) {
        $lQry -> query('SELECT * FROM al_usr WHERE user='.esc($lUsr).' AND mand='.esc($lMandAdmin));
        $lRow = $lQry -> getAssoc();
      }
    }

    if ($lRow) {
      //wrong attempts
      if ($lRow['login_attempts'] >= CCor_Cfg::get('login.attempts.usr.deactivate', 3)) {
        CCor_Msg::add(lan('access.deny'), mtUser, mlWarn);
        $this -> redirect('index.php');
      }
      
      $lUid = $lRow['id'];
      //Load Target Mand
      $lQry -> query("SELECT * FROM al_sys_mand WHERE id = (SELECT val FROM al_usr_pref WHERE uid = ".$lUid." AND code = 'sys.mid')");
      $lTargetMand = $lQry ->getAssoc();

      //Load all Mands
      $lAllMands = CCor_Res::extract("id", "disabled", "mand");

      //Deleted User
      if ($lRow['del'] == 'Y') {
        CCor_Msg::add(lan('log.wrn'), mtUser, mlWarn);
        $this -> redirect('index.php');
      }
      //Disabled Mand or User
      else if(($lRow['disabled'] === 'Y' || $lTargetMand["disabled"] === "Y") && $this->mUsrLoggedAsAdmin === FALSE) {
        //Check if User has more than this mand
        $lQry ->query("SELECT * FROM al_usr_mand WHERE uid = ".$lUid);
        $lUsrMands = $lQry->getAssocs();
        //If User has Mand 0 he has access to all Mands
        if($lUsrMands[0]["mand"] === "0") {
          $i = 1;
          foreach ($lAllMands as $key => $value) {
            $lUsrMands[$i]["uid"] = $lUid;
            $lUsrMands[$i]["mand"] = $key;
            $i++;
          }
        }
        $lfoundTargetMand = 0;
        if(count($lUsrMands) > 1) {
          foreach($lUsrMands as $lCMand) {
          if($lCMand["mand"] !== $lTargetMand["id"] && $lAllMands[$lCMand["mand"]] === "N") {
              $lNewTargetMand = $lCMand["mand"];
              $lQry -> query("UPDATE `al_usr_pref` SET `val`='".$lNewTargetMand."' WHERE  `uid`=".$lUid." AND `code`='sys.mid' AND `mand`=0");
              $lfoundTargetMand = 1;
              break;
            }
          }
        }
        if($lfoundTargetMand == 0) {
          //Exit this and go on with Login
          CCor_Msg::add(lan('log.dis'), mtUser, mlWarn);
          $this -> redirect('index.php');
        }
      }

      $this -> doLogin($lUid, $lRow);
      $lUsr = CCor_Usr::getInstance();
      
      #Password Expiry users
      if ($lRow['password_disable'] == 'Y') {
        #CCor_Msg::add('Please update your password', mtUser, mlWarn);
        $this -> redirect('index.php?act=log.enablepass&id='.$lUid);
      }
      
      //already request for password change
      if ($lRow['pass_req'] == 'Y') {
        CCor_Msg::add(lan('usr.alrdy.req.pass'), mtUser, mlWarn);
        $this -> redirect('index.php');
      }

      //redirect to start page
      $lAct = $this -> getReq('nact');
      if (empty($lAct)) {
        $lAct = 'act='.$lUsr -> getPref('log.url', 'hom-wel');
      } else {
        $lAct = urldecode($lAct);
      }
      $this -> redirect('index.php?'.$lAct);
    } else {
      //wrong attempts
      $lQry = new CCor_Qry('select * from al_usr where user="' . $lUsr . '"');
      $lRow = $lQry -> getAssoc();
      if($lRow){
        if($lRow['login_attempts'] >= CCor_Cfg::get('login.attempts.usr.deactivate', 3)){
          CCor_Msg::add(lan('access.deny'), mtUser, mlWarn);
          $this -> redirect('index.php');
        }
        $lAttempts = $lRow['login_attempts']+1;
        CCor_Qry::exec('UPDATE al_usr set login_attempts="' .$lAttempts . '" where user="' .$lUsr . '"');
        //added to history
        $lUserId = $lRow['id'];
        CUsr_External_Cnt:: extUsrHis ($lRow['id'], $lRow['id'], date("Y-m-d", time()), lan('usr.pass.attempts'));
       }
      # addmessage
      CCor_Msg::add(lan('log.msg'), mtUser, mlError);
      $this -> redirect('index.php');
    }
  }

  protected function doLogin($aUid, $aRow) {
    $lLoginManager = new CCor_Usr_Login();
    $lLoginManager->login($aUid, $aRow);

    // Login with admin pass
    if ($this -> mUsrLoggedAsAdmin){
      $this->msg('All Messages are activated',mtUser,mlWarn);
      // Set Session variable 'usr.logadmin'
      $lSys = CCor_Sys::getInstance();
      $lSys['usr.loggedasadmin'] = TRUE;
    }
  }

  private function doLogout() {
    $lLoginManager = new CCor_Usr_Login();
    $lLoginManager->logout();
  }

  protected function actOut() {
    $this -> doLogout();
    $this -> redirect();
  }

  protected function actRecover() {
    $lHtmz = '';
    foreach ($_COOKIE as $lKey => $lVal) {
      if ('Mand' == $lKey AND 0 < $lVal) {
        $lHtmz = 'mand'.DS.'mand_'.$lVal.DS.'htm'.DS;
        break;
      }
    }

    if($lHtmz == ''){
        $lHtmz = 'htm/default/';
    } else {
      if(!file_exists($lHtmz.'recoverpassword.htm')){
        $lHtmz = CUST_PATH_HTM;
        if(!file_exists($lHtmz.'recoverpassword.htm')){
          $lHtmz = 'htm/default/';
        }
      }
    }

    $lTpl = new CCor_Tpl();
    $lTpl -> open($lHtmz.'recoverpassword.htm');

    $lMsg = new CHtm_MsgBox(TRUE);
    $lTpl -> setPat('pg.error',           $lMsg -> getContent());
    $lTpl -> setPat('pg.versioninfo',     CCor_Cfg::get('versioninfo'));
    $lTpl -> setPat('pg.utilcss',         $lHtmz.'css'.DS.'util.css');
    $lTpl -> setPat('pg.stylecss',        $lHtmz.'css'.DS.'style.css');
    $lTpl -> setPat('pg.favicon',         getImgPath('img/pag/favicon.ico'));
    $lTpl -> setPat('pg.loginbutton',     getImgPath('img/login/btn-recover.gif'));
    $lTpl -> setPat('pg.mand.cust',       getImgPath('img/login/cust.png'));
    $lCaptcha = $this -> getCaptchaObject();
    $lCaptcha -> generate();    //command to generate session + create image
    $lTpl->setPat('captcha', $lCaptcha -> render());
    $lTpl->setPat('captcha_id', $lCaptcha -> getId());
    
    $lTpl -> setPat('pg.sysMsg', CInc_Sys_Msg_Cnt::getMessages());

    CCor_Msg::getInstance() -> clear();
    $lTpl -> setPat('log.btn', btn(lan('usr.create_new_pwd'), '', '', 'submit'));
    $lTpl -> render();
  }
  
  protected function actSrecover() {
    $lNam = $this -> getReq('usr');
    $lNam = trim($lNam);
    $lNam = escWithoutDb($lNam);

    $lCap = $this -> getReq('captcha');
    $lCaptcha = $this -> getCaptchaObject();
    if ( ! $lCaptcha -> isValid($lCap)) {
      $this -> msg(lan('chk.captcha.error'));
      CCor_Cache::clearStatic('cor_res_usr');
      $this -> redirect('index.php?act=log.recover');
    }
    $lSql = 'SELECT * FROM al_usr WHERE user="'.$lNam.'" OR email="'.$lNam.'"';
    $lQry = new CCor_Qry($lSql);
    if ($lRow = $lQry -> getDat()) {
      // user is already deleted
      if ($lRow['del'] == 'Y') {
        $this -> msg(lan('log.msg'), mtUser, mlWarn);
        $this -> redirect();
      }

      // ???
      $lFla = intval($lRow['flags']);
      if (!bitSet($lFla, 1)) {
        $this -> msg(lan('usr.not_act'), mtUser, mlWarn);
        $this -> redirect();
      }

      // user has no email address
      if (empty($lRow['email'])) {
        $this -> msg(lan('log.no_email'), mtUser, mlWarn);
        $this -> redirect();
      }

      if (!defined('MID')) {
        define('MID', $lRow['mand']);
      }
    } else {
      $this -> msg(lan('usr.invalid'), mtUser, mlError);
      $this -> redirect('index.php?act=log.recover');
      $lOk = FALSE;
    }

    // create new pwd
    $lEmailTpl = CCor_Cfg::get('tpl.email', array());
 
    if (!empty($lEmailTpl) AND isset($lEmailTpl['pwd.fgt.activate'])) {
      $lUsrTpl = $lEmailTpl['pwd.fgt.activate'];
    }
    if (!empty($lUsrTpl)) {
      $lUid = intval($lRow['id']);

      $lToken = CApp_Pwd::createNewToken();
      $CtokenExpiration = CCor_Cfg::get('token.expiration',3);
      $lUserEmailConfirmationTokenExpiry = date('Y-m-d', strtotime("+$CtokenExpiration days"));
      
      $lSql = 'UPDATE al_usr SET token='.esc($lToken).', tokenlifetime= '.esc($lUserEmailConfirmationTokenExpiry).' WHERE id='.esc($lUid).' ';
      CCor_Qry::exec($lSql);

      $lTpl = new CApp_Tpl();
      if (is_int($lUsrTpl)) {
      $lTpl -> loadTemplate($lUsrTpl);
      } else {
        $lTpl -> loadTemplate(0, $lUsrTpl, LAN);
      }

      $lQry = new CCor_Qry('SELECT id, name_'.LAN.' as name FROM al_sys_mand');
      foreach($lQry as $lRow1) {
        $lMand[$lRow1 -> id] = $lRow1 -> name;
      }
      $lPortalName = (isset($lMand[MID])) ? $lMand[MID] : CUSTOMER_NAME;
      //Encrypted URL
      $lData = http_build_query (array('email'=> $lRow['email'], 'confirmtoken'=> $lToken));
      $lData= CApp_Pwd::EnDecryptor('encrypt', urldecode($lData));
      
      $lTpl -> setPat('portal_name', $lPortalName);
      $lDate = date(lan('lib.date.xxl'), strtotime("+2 week"));
      $lTpl -> setPat('date', $lUserEmailConfirmationTokenExpiry);

      $lTpl -> addUserPat($lUid, 'to');
      $lTpl -> addUserPat(1, 'from');
      $lNam = cat($lRow['firstname'], $lRow['lastname']);
      #$lTpl -> setPat('new.pwd', $lPwd);
      #$lTpl -> setPat('link', CCor_Cfg::get('base.url') . 'index.php?act=log.passupdate&email='.$lRow['email'].'&confirmtoken='. $lToken);
      $lTpl -> setPat('link', CCor_Cfg::get('base.url') . 'index.php?act=log.passupdate&data='.$lData);
      
      if ( in_array($lRow['anrede'], array('Herr', 'Mr.')) )
      $lTpl -> setPat('geehrte', 'geehrter');
      elseif ( in_array($lRow['anrede'], array('Frau', 'Mrs.', 'Miss')) )
      $lTpl -> setPat('geehrte', 'geehrte');
      else
      $lTpl -> setPat('geehrte', 'geehrte/r');
      $lTpl -> setPat('from.anrede', lan('lib.salutation.value'));
      $lTpl -> setPat('from.firstname', 'Administrator');
      $lTpl -> setPat('from.lastname', 'Team');
      $lTpl -> setPat('from.email', 'info@5flow.eu');
      $lTpl -> setPat('from.phone', '');

      $lSub = $lTpl -> getSubject();
      $lBod = $lTpl -> getBody();

      $lMail = new CApi_Mail_Item('info@5flow.eu', lan('admin.team'), $lRow['email'], $lNam);
      $lMail -> setSubject($lSub);
      $lMail -> setText($lBod);
      //$lMail -> send();
      $lMail -> insert(false);
      $this -> msg(lan('send.email.chg.pass'), mtUser, mlInfo);
      #$this -> msg(lan('usr.send_new_pwd.msg'), mtUser, mlInfo);
    }
    else {
      $this -> msg('No/Kein Emailtemplate', mtUser, mlWarn);
    }
    $this -> redirect();
  }
  
  protected function actEnablepass() {
    $lUid = $this -> getReq('id');
    $lPassCondions = CCor_Cfg::get('hom-pwd.conditions');
    list ($lLength, $lLowerCase, $lUpperCase, $lDigit, $lSpecial) = array_values($lPassCondions);
    
    if ($lUid !== FALSE) {
      $lSql = 'SELECT session_id FROM al_usr_login WHERE user_id="'.$lUid.'" ORDER BY zeit desc';
      $lRes = CCor_Qry::getStr($lSql);
      if ($lRes != session_id()) {
        $this -> redirect('index.php?act=log');
      }
    } else {
      $this -> redirect('index.php?act=log');
    }
    
    $lHtmz = CUST_PATH_HTM;
    if(!file_exists($lHtmz.'enablepassword.htm')){
      $lHtmz = 'htm/default/';
    }
    
    $lTpl = new CCor_Tpl();
    $lTpl -> open($lHtmz.'enablepassword.htm');
  
    $lMsg = new CHtm_MsgBox(TRUE);
    $lTpl -> setPat('pg.error',           $lMsg -> getContent());
    $lTpl -> setPat('pg.versioninfo',     CCor_Cfg::get('versioninfo'));
    //CSS
    $lTpl -> setPat('pg.utilcss',         $this->getCssPathFor('util.css'));
    $lTpl -> setPat('pg.stylecss',        $this->getCssPathFor('style.css'));
    $lTpl -> setPat('pg.jqueryuicss',     'js'.DS.'jquery'.DS.'jquery-ui.css');
    $lTpl -> setPat('pg.jqueryuithemecss','js'.DS.'jquery'.DS.'jquery.ui.theme.css');
    //JS
    $lTpl -> setPat('pg.jqueryjs', 'js'.DS.'jquery'.DS.'jquery.min.js');
    $lTpl -> setPat('pg.jqueryuijs', 'js'.DS.'jquery'.DS.'jquery-ui.min.js');
    
    $lTpl -> setPat('pg.favicon',         getImgPath('img/pag/favicon.ico'));
    $lTpl -> setPat('pg.mand.cust',       getImgPath('img/login/cust.png'));
    $lTpl -> setPat('pwd.restrictions.length', htm(sprintf(lan('pwd.restrictions.length'),$lLength)));
    $lLowerCase =='off' ? $lTpl -> setPat('pwd.restrictions.lcase', none): $lTpl -> setPat('pwd.restrictions.lcase', htm(lan('pwd.restrictions.lcase')));
    $lUpperCase =='off' ? $lTpl -> setPat('pwd.restrictions.ucase', none): $lTpl -> setPat('pwd.restrictions.ucase', htm(lan('pwd.restrictions.ucase')));
    $lDigit =='off' ? $lTpl -> setPat('pwd.restrictions.number', none): $lTpl -> setPat('pwd.restrictions.number', htm(lan('pwd.restrictions.number')));
    $lSpecial =='off' ? $lTpl -> setPat('pwd.restrictions.special.char', none): $lTpl -> setPat('pwd.restrictions.special.char', htm(lan('pwd.restrictions.special.char')));
    $lTpl -> setPat('id',         htm($this->getReq('id')));
    $lTpl -> setPat('pg.sysMsg', CInc_Sys_Msg_Cnt::getMessages());
    CCor_Msg::getInstance() -> clear();
    $lTpl -> setPat('log.btn', btn(lan('usr.create_new_pwd'), '', '', 'submit'));
    session_destroy();
    $lTpl -> render();
  }
  
  protected function actSEnablepass() {
    $lUid = $this-> getReq('id');
    $lOld = $this -> getReq('old');
    $lNew = $this -> getReq('new');
    $lCnew = $this -> getReq('cnew');
    
    $lSql = 'SELECT * FROM al_usr WHERE id="'.addslashes($lUid).'" AND del !="Y"';
    $lQry = new CCor_Qry($lSql);
    $lRow = $lQry -> getDat();
    if (!$lRow){
      $this -> msg(lan('usr.invalid'), mtUser, mlError);
      $this -> redirect('index.php?act=log');
    }
    //validation
    $lNewEncoded = CApp_Pwd::encryptPassword($lNew);
    $lDbPwd = $lRow['pass'];
    $lPassValidation = CApp_Pwd::passValidationCheck($lOld, $lNew, $lCnew, $lNewEncoded, $lDbPwd, $lUid);
     if (count($lPassValidation)) {
      foreach ($lPassValidation as $lPass) {
        $this -> msg($lPass, mtUser, mlError);
      }
      $this -> redirect('index.php?act=log.enablepass&id=' . $lUid);
    } 
    
    $lReqPwd = CApp_Pwd::encryptPassword($lOld);
    if (($lReqPwd != $lDbPwd)) {
      $lAttempts = $lRow['login_attempts']+1;
      if($lRow['login_attempts'] >= CCor_Cfg::get('login.attempts.usr.deactivate', 3)){
        CCor_Msg::add(lan('access.deny'), mtUser, mlError);
        $this -> redirect('index.php');
      }
      CCor_Qry::exec('UPDATE al_usr set login_attempts="' .$lAttempts . '" where user="' .$lRow['user'] . '"');
      //Wrong attempt added to history
      CUsr_External_Cnt:: extUsrHis ($lRow['id'], $lRow['id'], date("Y-m-d", time()), lan('usr.pass.attempts'));
      CCor_Msg::add(lan('usr.invalid.old.pass'), mtUser, mlError);
      $this -> redirect('index.php?act=log.enablepass&id='.$lUid);
    }
    $lCurrentTime = date("Y-m-d", time());
    $lSql = 'UPDATE al_usr SET pass="'.$lNewEncoded.'", lastreset_password= "'.$lCurrentTime.'", password_disable= "N", login_attempts=0, pass_req="N" WHERE id='.$lUid;
    if ($lQry -> query($lSql)) {
      //Added to password history
      CApp_Pwd::addedToPassHis($lUid, $lNewEncoded);
      $this -> msg(lan('usr.pass.succ'), mtUser, mlInfo);
      $this -> redirect('index.php?act=log');
    } else {
      $this -> msg(lan('usr.pass.not.succ'), mtUser, mlError);
      $this -> redirect();
    }
    }

  
  protected function getCaptchaObject() {
    $lBasePath = CCor_Cfg::get('application.base.path');
    $lBaseUrl = CCor_Cfg::get('base.url');
    $lCaptcha = new Zend_Captcha_Image();
    $lCaptcha->setTimeout('300')
    ->setWordLen('5')
    ->setHeight('80')
    ->setFontSize('30')
    ->setWidth('150')
    ->setLineNoiseLevel('2')
    ->setTimeout('300')
    ->setFont($lBasePath.DS.'htm'.DS.'font'.DS.'verdana.ttf')//application path
    ->setImgUrl($lBaseUrl.'tmp'.DS.'captcha') //must browser url
    ->setImgDir($lBasePath.DS.'tmp'.DS.'captcha'); //application path
      return $lCaptcha;
  }
  
  public function EmailSend($aEmailTpl, $aFrom, $aFromName, $aTo, $aToName, $aLink, $aAnrede, $aUserEmailConfirmationTokenExpiry, $aFromPhone=NULL){
  
    $lEmailTpl = CCor_Cfg::get('tpl.email', array());
    if ( ! empty($lEmailTpl) and isset($lEmailTpl[$aEmailTpl])) {
      $lUsrTpl = $lEmailTpl[$aEmailTpl];
    }
    if ( ! empty($lUsrTpl)) {
      // email send
      $lTpl = new CApp_Tpl();
      if (is_int($lUsrTpl)) {
        $lTpl -> loadTemplate($lUsrTpl);
      } else {
        $lTpl -> loadTemplate(0, $lUsrTpl, LAN);
      }
      // exp_date will work later
      $lQry = new CCor_Qry('SELECT id, name_'.LAN.' as name FROM al_sys_mand');
      foreach($lQry as $lRow1) {
        $lMand[$lRow1 -> id] = $lRow1 -> name;
      }
      $lPortalName = (isset($lMand[MID])) ? $lMand[MID] : CUSTOMER_NAME;
      $lTpl -> setPat('portal_name', $lPortalName);
      
      $lTpl -> setPat('link', $aLink);
      $lTpl -> setPat('to.anrede', $aAnrede);
      if (in_array($aAnrede, array('Herr','Mr.')))
        $lTpl -> setPat('geehrte', 'geehrter');
      elseif (in_array($aAnrede, array('Frau', 'Mrs.', 'Miss')))
      $lTpl -> setPat('geehrte', 'geehrte');
      else
        $lTpl -> setPat('geehrte', 'geehrte/r');
      
      $lTpl -> setPat('to.name', $aToName);
      $lTpl -> setPat('from.name', $aFromName);
      $lTpl -> setPat('exp_date', $aUserEmailConfirmationTokenExpiry);
      $lTpl -> setPat('from.anrede', lan('lib.salutation.value'));
      $lTpl -> setPat('from.firstname', 'Administrator');
      $lTpl -> setPat('from.lastname', 'Team');
      $aFrom ? $lTpl -> setPat('from.email', $aFrom) :$lTpl -> setPat('from.email', 'info@5flow.eu');
      #$lTpl -> setPat('from.email', 'info@5flow.eu');
      $lTpl -> setPat('from.phone', $aFromPhone);
      
      $lSub = $lTpl -> getSubject();
      $lBod = $lTpl -> getBody();
      $lTxt = $lTpl -> getContent();
      
      #$lMail = new CApi_Mail_Item($aFrom, $aFromName, $aTo, $aToName);
      $lMail = new CApi_Mail_Item('info@5flow.eu', lan('admin.team'), $aTo, $aToName);
      $lMail -> setSubject($lSub);
      $lMail -> setText($lBod);
      $lMail -> insert('',1);
      $this -> msg(lan('usr.msg.succ'), mtUser, mlInfo);
     } else {
      $this -> msg('No/Kein Emailtemplate', mtUser, mlWarn);
    }
  }
  
  protected function actExternalUser() {
    $lUrl = CApp_Pwd::EnDecryptor('decrypt',$this -> getReq('data'));
    $lEmails = parse_str($lUrl, $lEmail);
    $lUsrEmail= array($lEmail['uemail'], $lEmail['invemail']);
    //if url not false and decoded emails are invalid then back to index page
    if ($lUrl != FALSE) {
      foreach ($lUsrEmail as $email){
        if (isValidEmail($email)==FALSE){
          $this -> redirect('index.php?act=log');
        }
      }
    } else {
      //if url is false
      $this -> redirect('index.php?act=log');
    }
    
    $lHtmz = '';
    unset($_COOKIE['PHPSESSID']);
    foreach ($_COOKIE as $lKey => $lVal) {
      if ('Mand' == $lKey and 0 < $lVal) {
        $lHtmz = 'mand' . DS . 'mand_' . $lVal . DS . 'htm' . DS;
        break;
      }
    }
  
    if ($lHtmz == '') {
      $lHtmz = 'htm/default/';
    } else {
      if ( ! file_exists($lHtmz . 'external_usr_register.htm')) {
        $lHtmz = CUST_PATH_HTM;
        if ( ! file_exists($lHtmz . 'external_usr_register.htm')) {
          $lHtmz = 'htm/default/';
        }
      }
    }
    $lTpl = new CCor_Tpl();
    $lTpl -> open($lHtmz . 'external_usr_register.htm');
  
    $lMsg = new CHtm_MsgBox(TRUE);
    $lTpl -> setPat('pg.error', $lMsg -> getContent());
    $lTpl -> setPat('pg.versioninfo', CCor_Cfg::get('versioninfo'));
    $lTpl -> setPat('pg.utilcss', $lHtmz . 'css' . DS . 'util.css');
    $lTpl -> setPat('pg.stylecss', $lHtmz . 'css' . DS . 'style.css');
    $lTpl -> setPat('pg.favicon', getImgPath('img/pag/favicon.ico'));
    $lTpl -> setPat('pg.sysMsg', CInc_Sys_Msg_Cnt::getMessages());
    $lTpl -> setPat('usremail',htm($lEmail['uemail']));
    $lTpl -> setPat('inviteremail',htm($lEmail['invemail']));
    $lTpl -> setPat('data',htm($this -> getReq('data')));
    
    if (CCor_Cfg::get('ext.usr.terms.conditions')){
      $lTpl -> setPat('ext.usr.terms.conditions', CCor_Cfg::get('ext.usr.terms.conditions'));
     }
    else {
      $lTpl -> setPat('ext.terms.condition.display', none);
    }
    $lCaptcha = $this -> getCaptchaObject();
    $lCaptcha -> generate();    //command to generate session + create image
    $lTpl -> setPat('captcha', $lCaptcha -> render());
    $lTpl -> setPat('captcha_id', $lCaptcha -> getId());
  
    CCor_Msg::getInstance() -> clear();
    $lTpl -> render();
    exit;
  }
  
  protected function actSExternalUser() { 
    $lValues = $this -> getReq('val');
    $lUrl = $this -> getReq('data');
  	$lCap = $this -> getReq('captcha');
    $lEmail = $lValues['email'];
    $lCnfEmail = $this -> getReq('cnfemail');
    $lInviterEmail = $lValues['inviteremail'];
    //Check the user email, domain, blacklist and the inviter email
    $lCheckEmailDomain = $this -> checkEmailDomain($lEmail, $lCnfEmail, $lInviterEmail);
    if ($lCheckEmailDomain){
      $this -> msg($lCheckEmailDomain, mtUser, mlError);
      $this -> redirect('index.php?act=log.externaluser&data='.$lUrl);
    }
    //Captcha validation
    $lCaptcha = $this -> getCaptchaObject();
    if ( ! $lCaptcha -> isValid($lCap)) {
      $this -> msg(lan('chk.captcha.error'), mtUser, mlError);
      $this -> redirect('index.php?act=log.externaluser&data='.$lUrl);
    }
    //Create and expired token
    $lToken = CApp_Pwd::createNewToken();
    $CtokenExpiration = CCor_Cfg::get('token.expiration',3);
    $UserEmailConfirmationTokenExpiry = date('Y-m-d', strtotime("+$CtokenExpiration days"));
    
    //Check user email is available in al_usr_tmp_external?? if you are not invited by some1 you will get this error
    //if we want both options ex. request from group or without request then need one flag for identified 
    $lQry = new CCor_Qry('select * from al_usr_tmp_external where email='.esc($lEmail). ' AND inviteremail='.esc($lInviterEmail));
    $lExtUsrReq = $lQry -> getAssoc();
    if(!$lExtUsrReq){
      $this -> msg(lan('ext.usr.not.allowed'), mtUser, mlError);
      $this -> redirect('index.php?act=log.externaluser&data='.$lUrl);
    }
    
    //For email generate variables
    $lFrom = $lValues['email'];
    $lNam = cat($lValues['firstname'], $lValues['lastname']);
    $aLink = CCor_Cfg::get('base.url') .'index.php?act=log.extemailconfirmation&confirmtoken=' . $lToken;
    //If user not in al_usr but in al_usr_tmp_external then access to update and send new token
    //if email is available in al_usr return exist email
       
    $lCheckUserInExt = $this -> checkUserInExt($lValues, $lToken, $UserEmailConfirmationTokenExpiry, $aLink);
    //Already in external user and trying 2nd time then send new token
    if ($lCheckUserInExt == '1') {
      $this -> redirect('index.php?act=log');
    }    // if any error
    elseif ($lCheckUserInExt) {
      $this -> msg($lCheckUserInExt, mtUser, mlError);
      $this -> redirect('index.php?act=log.externaluser&data='.$lUrl);
    } else {
      // Complete new user
      $i = 1;
      $lUsername = $lValues["email"];
      while ($this -> checkUsername($lUsername)) {
        $lUsername = $lUsername . '_' . $i ++ ;
      }
      $lSql = 'INSERT INTO al_usr_tmp_external SET ';
      foreach ($lValues as $lKey => $lVal) {
        $lSql .= '`' . $lKey . '`=' . esc($lVal) . ',';
      }
      $lSql .= '`user`=' . esc($lUsername) . ',';
      $lSql .= '`token`=' . esc($lToken) . ',';
      $lSql .= '`tokenlifetime`=' . esc($UserEmailConfirmationTokenExpiry) . ',';
      $lSql .= 'created=NOW();';
      CCor_Qry::exec($lSql);
      //Send email to first time
      $lSendEmail = $this -> EmailSend('extusrtoken', $aFrom = NULL, $aFromName = $lNam, $aTo = $lFrom, $aToName = $lNam, $aLink = $aLink, 
          $aAnrede = $lValues['anrede'], $UserEmailConfirmationTokenExpiry);
    }
  	  $this -> redirect('index.php');
    }
  
  protected function checkUserInExt($lValues, $lToken, $UserEmailConfirmationTokenExpiry, $aLink) {
    $lEmail = $lValues['email'];
    $lNam = cat($lValues['firstname'], $lValues['lastname']);
    $lSql = 'select email from al_usr_tmp_external where email=' . esc($lEmail);
    $lSql .= ' union ';
    $lSql .= 'select email from al_usr where email=' . esc($lEmail);
    $lQry2 = new CCor_Qry($lSql);
    $lRow2 = $lQry2 -> getAssoc();
    $lEmailFound = $lRow2['email'];
    if ($lEmailFound) {
      // if this user not in al_usr then access to update and send new token
      $lQry1 = new CCor_Qry('select * from al_usr_tmp_external where email="' . $lEmailFound . '"');
      $lRow1 = $lQry1 -> getAssoc();
      //User email and user name is availabe thats means user already created profile
      if ($lRow1['email'] && $lRow1['user']) {
        $lSql = 'UPDATE al_usr_tmp_external SET ';
        $lSql .= '`token`=' . esc($lToken) . ',';
        $lSql .= '`tokenlifetime`=' . esc($UserEmailConfirmationTokenExpiry);
        $lSql .= ' where `email`=' . esc($lValues['email']);
        $lSql .= ' AND `firstname`=' . esc($lValues['firstname']);
        $lSql .= ' AND `lastname`=' . esc($lValues['lastname']);
        $lSql .= ' AND `inviteremail`=' . esc($lValues['inviteremail']);
        $lSql .= ' AND `phone`=' . esc($lValues['phone']) . ';';
        CCor_Qry::exec($lSql);
  
        $lQry = new CCor_Qry('select * from al_usr_tmp_external where email="' . $lEmailFound . '"');
        $lRow = $lQry -> getAssoc();
  
        if($lRow['token'] != $lToken){
          return lan('ext.usr.reg.keep.data');
        }
        else {
          if($lRow['flags']=='1'){
          $this -> msg(lan('ext.usr.alrdy.activate'), mtUser, mlError);
          $this -> redirect('index.php');
          }
          $lSendEmail = $this -> EmailSend('extusrtoken', $aFrom = NULL, $aFromName = $lNam, $aTo = $lEmail, $aToName = $lNam, $aLink = $aLink, $aAnrede = $lValues['anrede'],$UserEmailConfirmationTokenExpiry);
          return 1;
        }
        //First time updated user data to external user table  
      } elseif (!$lRow['firstname'] || !$lRow['user']){
        
        $lQry = new CCor_Qry('select * from al_usr where email=' . esc($lValues['email']));
        $lEmailAlrdy = $lQry -> getAssoc();       
        if ($lEmailAlrdy){
          return lan('usr.email.taken');
        }
        
        $i = 1;
        $lUsername = $lValues["email"];
        while ($this -> checkUsername($lUsername)) {
          $lUsername = $lUsername . '_' . $i ++ ;
        }
        $lSql = 'UPDATE al_usr_tmp_external SET ';
        $lSql .= '`anrede`=' . esc($lValues['anrede']) . ',';
        $lSql .= '`firstname`=' . esc($lValues['firstname']) . ',';
        $lSql .= '`lastname`=' . esc($lValues['lastname']) . ',';
        $lSql .= '`phone`=' . esc($lValues['phone']) . ',';
        $lSql .= '`user`=' . esc($lUsername) . ',';
        $lSql .= '`token`=' . esc($lToken) . ',';
        $lSql .= '`tokenlifetime`=' . esc($UserEmailConfirmationTokenExpiry) . ',';
        $lSql .= 'created=NOW()';
        $lSql .= ' where `email`=' . esc($lValues['email']);
        $lSql .= ' AND `inviteremail`=' . esc($lValues['inviteremail']) . ';';
        CCor_Qry::exec($lSql);
        
        $lSendEmail = $this -> EmailSend('extusrtoken', $aFrom = NULL, $aFromName = $lNam, $aTo = $lEmail, $aToName = $lNam, $aLink = $aLink, $aAnrede = $lValues['anrede'],$UserEmailConfirmationTokenExpiry);
        return 1;
      }
    }
  }
  
  protected function checkEmailDomain($aEmail, $aCnfEmail, $aInviterEmail) {
    $aEmail = strtolower($aEmail);
    $aCnfEmail = strtolower($aCnfEmail);
    $aInviterEmail = strtolower($aInviterEmail);

    if ($aEmail != $aCnfEmail){
      return lan('ext.email.not.matching');
    }
    $lUserInviterEmails = array($aEmail, $aInviterEmail);
    foreach ($lUserInviterEmails as $lUserInviterEmail) {
      $lUserEmailValidation = isValidEmail($lUserInviterEmail);
      if ($lUserEmailValidation) {
        if ($lUserEmailValidation == $aInviterEmail) {
          $lQry = new CCor_Qry('select * from al_usr where email="'.escWithoutDb($aInviterEmail).'"');
          $lRow = $lQry -> getAssoc();
          if ( ! $lRow['email']) {
            return lan('ext.usr.inviter.invalid');
          }
        }
        //Not allowed blacklist domain or if not available in group table
        $lDomain = array_pop(explode('@', $aEmail));
        #$lQry = new CCor_Qry('SELECT id, mand,parent_id FROM al_gru WHERE comp_dom like "%'.$lDomain.'%"');
        #$lRowFound = $lQry -> getAssoc();
        
        if ((in_array($lDomain, CCor_Cfg::get('blacklisted_domains')))) {
          return lan('ext.usr.invalid.domain');
        }
      } else {
        return lan('ext.usr.invalid.email');
      }
    }
  }
  
  protected function checkUsername($aUsername) {
    $lQry = new CCor_Qry('select * from al_usr where user='.esc($aUsername));
    $lUser = $lQry -> getAssoc();
    if($lUser) {
      return true;
    }
    else {
      return false;
    }
  }


  public function actExtEmailConfirmation() {
    // ?act=log.emailconfirmation&confirmtoken=
    $lCtoken = $this -> getReq('confirmtoken'); // confirmation token expiration
    $lSql = 'select * from al_usr_tmp_external where token="' . $lCtoken . '"';
    $lQry = new CCor_Qry($lSql);
    $lRow = $lQry -> getAssoc();
    $lResults = array();
    
    if ($lRow) {
      $lPresentDate = date('Y-m-d', strtotime(date("Y-m-d")));
      $lQry = new CCor_Qry();
      // if current date is greater than expire date
      if ($lPresentDate > $lRow['tokenlifetime']) {
        $lResults[] = lan('usr.token.expired');
        $lCheck = true;
      } elseif ($lRow['flags'] == "1") {
        $lResults[] = lan('ext.usr.alrdy.activate');
        $lCheck = true;
      } else {
        #$lResults[] = 'Your email account has been confirmed and the admin has been now notified to confirm and activate WAVE account. When account has been approved you will be informed by Email';
        $lResults[] = lan('ext.usr.acc.confirm');
        $lSql = 'UPDATE al_usr_tmp_external set flags="1" where email="' .$lRow['email'] . '"';
        if ($lQry -> query($lSql)) {
        #Send email to group admin  
        $lApplication_Admin_Grp = CCor_Cfg::get('application.grp.admin.email');
        $lNam = cat($lRow['firstname'], $lRow['lastname']);
        $lFromUser = array($lRow['anrede'], $lNam, $lRow['email'], $lRow['phone']);
        
        if ($lApplication_Admin_Grp) {
            $this -> SendEMailToAppAdminUsrs($lApplication_Admin_Grp, $lFromUser);
          } else {
            //Send email to Inviter for approval
            $lQry2 = new CCor_Qry('select * from al_usr where email="' . $lRow['inviteremail'] . '"');
            $lRow2 = $lQry2 -> getAssoc();
            $lToNam = cat($lRow2['firstname'], $lRow2['lastname']);
            $lLink = CCor_Cfg::get('base.url') . 'index.php?act=usr-external';
            $this -> EmailSend('ext.usr.req.admin', $aFrom = $lRow['email'], $aFromName=$lNam, $aTo=$lRow['inviteremail'], $aToName=$lToNam, 
                $lLink, $aAnrede=$lRow2['anrede'], $aUserEmailConfirmationTokenExpiry=NULL, $lRow['phone']);
          }
        }
        $lCheck = true;
      }
    } else {
      $lResults[] = lan('usr.token.invalid');
      $lCheck = true;
    }
    if ($lCheck) {
      echo '<script language="javascript">';
      echo 'alert("' . implode(', ', $lResults) . '");';
      echo '</script>';
      echo '<table border="0" align="center">';
      echo '<tr>';
      echo '<td align="center" style="color:green; font-size:20px;font-weight:bold;padding:40px;">' .implode(', ', $lResults) . '</td>';
      echo '</tr>';
      echo '</table>';
      exit();
    }
  }
  
  protected function SendEMailToAppAdminUsrs ($aGroups, $aFromUser) {
    foreach ($aGroups as $lGroup) {
      $lSql = 'SELECT u.id,u.email,u.firstname,u.lastname,u.anrede FROM al_usr u, al_usr_mem m WHERE u.id=m.uid AND u.del="N" AND m.gid=' .$lGroup;
      $lQry = new CCor_Qry($lSql);
      // $lUsers = CCor_Res::getByKey('id', 'usr', array('gru' => $lGroup));
      foreach ($lQry as $lRow) {
        $lEmailTpl = CCor_Cfg::get('tpl.email', array());
        if ( ! empty($lEmailTpl) and isset($lEmailTpl['ext.usr.req.admin'])) {
          $lUsrTpl = $lEmailTpl['ext.usr.req.admin'];
        }
        if ( ! empty($lUsrTpl)) {
          // email send
          $lTpl = new CApp_Tpl();
          if (is_int($lUsrTpl)) {
            $lTpl -> loadTemplate($lUsrTpl);
          } else {
            $lTpl -> loadTemplate(0, $lUsrTpl, LAN);
          }
          if (in_array($lRow['anrede'], array('Herr', 'Mr.')))
            $lTpl -> setPat('geehrte', 'geehrter');
          elseif (in_array($lRow['anrede'], array(
              'Frau', 'Mrs.', 'Miss')))
            $lTpl -> setPat('geehrte', 'geehrte');
          else
            $lTpl -> setPat('geehrte', 'geehrte/r');
          
          $lNam = cat($lRow['firstname'], $lRow['lastname']);
          $lTpl -> setPat('link', CCor_Cfg::get('base.url') . 'index.php?act=usr-external');
          $lTpl -> setPat('to.anrede', $lRow['anrede']);
          $lTpl -> setPat('to.name', $lNam);
          
          $lTpl -> setPat('from.name', $aFromUser[1]);
          $lTpl -> setPat('from.email', $aFromUser[2]);
          $lTpl -> setPat('from.phone', $aFromUser[3]);
          
          $lSub = $lTpl -> getSubject();
          $lBod = $lTpl -> getBody();
          $lTxt = $lTpl -> getContent();
          
          $lMail = new CApi_Mail_Item($aFromUser[2], $aFromUser[1], $lRow['email'], $lNam);
          $lMail -> setSubject($lSub);
          $lMail -> setText($lBod);
          $lMail -> insert('', 1);
        }
      }
    }
  }
  //update password/ forget password
  protected function actPassUpdate() {
    $lUrl = CApp_Pwd::EnDecryptor('decrypt',$this -> getReq('data'));
    $lUrlParse = parse_str($lUrl, $lData);
    $lConfirmToken = escWithoutDb($lData['confirmtoken']);
    $lEmail = escWithoutDb($lData['email']);
    
    if ($lUrl != FALSE) {
      if (!isValidEmail($lEmail)){
        $this -> redirect('index.php?act=log');
      }
    } else {
      //if url is false
      $this -> redirect('index.php?act=log');
    }
    
    $lPassCondions = CCor_Cfg::get('hom-pwd.conditions');
    list ($lLength, $lLowerCase, $lUpperCase, $lDigit, $lSpecial) = array_values($lPassCondions);
    $lPresentDate = date('Y-m-d', strtotime(date("Y-m-d")));
    
    $lQry = new CCor_Qry('SELECT * from al_usr WHERE token="' .$lConfirmToken.'" AND email="'.$lEmail.'"');
    $lRow = $lQry -> getAssoc();
   
    if ($lRow) {
      $lResults = '';
       if ($lPresentDate > $lRow['tokenlifetime']) {
        $lResults .= lan('usr.token.expired');
      }
     /*  elseif ($lRow['token_used'] == "N") {
         $lResults .= lan('usr.token.used');
      }  */
    } else {
         $lResults .= lan('usr.token.invalid');
      }
      if ($lResults) {
        echo '<script language="javascript">';
        echo 'alert("' . $lResults . '");';
        echo '</script>';
        echo '<table border="0" align="center">';
        echo '<tr>';
        echo '<td align="center" style="color:green; font-size:20px;font-weight:bold;padding:40px;">' .$lResults . '</td>';
        echo '</tr>';
        echo '</table>';
        exit();
      }
      
    $lHtmz = '';
    foreach ($_COOKIE as $lKey => $lVal) {
      if ('Mand' == $lKey AND 0 < $lVal) {
        $lHtmz = 'mand'.DS.'mand_'.$lVal.DS.'htm'.DS;
        break;
      }
    }
  
    if($lHtmz == ''){
      $lHtmz = 'htm/default/';
    } else {
      if(!file_exists($lHtmz.'password_update.htm')){
        $lHtmz = CUST_PATH_HTM;
        if(!file_exists($lHtmz.'password_update.htm')){
          $lHtmz = 'htm/default/';
        }
      }
    }
  
    $lTpl = new CCor_Tpl();
    $lTpl -> open($lHtmz.'password_update.htm');
  
    $lMsg = new CHtm_MsgBox(TRUE);
    $lTpl -> setPat('pg.error',           $lMsg -> getContent());
    $lTpl -> setPat('pg.versioninfo',     CCor_Cfg::get('versioninfo'));
    $lTpl -> setPat('pg.utilcss',         $lHtmz.'css'.DS.'util.css');
    $lTpl -> setPat('pg.stylecss',        $lHtmz.'css'.DS.'style.css');
    $lTpl -> setPat('pg.favicon', getImgPath('img/pag/favicon.ico'));
    $lTpl -> setPat('pg.mand.cust',       getImgPath('img/login/cust.png'));

    $lTpl -> setPat('pwd.restrictions.length', htm(sprintf(lan('pwd.restrictions.length'),$lLength)));
    $lLowerCase =='off' ? $lTpl -> setPat('pwd.restrictions.lcase', none): $lTpl -> setPat('pwd.restrictions.lcase', htm(lan('pwd.restrictions.lcase')));
    $lUpperCase =='off' ? $lTpl -> setPat('pwd.restrictions.ucase', none): $lTpl -> setPat('pwd.restrictions.ucase', htm(lan('pwd.restrictions.ucase')));
    $lDigit =='off' ? $lTpl -> setPat('pwd.restrictions.number', none): $lTpl -> setPat('pwd.restrictions.number', htm(lan('pwd.restrictions.number')));
    $lSpecial =='off' ? $lTpl -> setPat('pwd.restrictions.special.char', none): $lTpl -> setPat('pwd.restrictions.special.char', htm(lan('pwd.restrictions.special.char')));
    $lTpl -> setPat('email',         htm($lEmail));
    $lTpl -> setPat('confirmtoken',         htm($lConfirmToken));
    $lTpl -> setPat('data',htm($this -> getReq('data')));
    
    $lTpl -> setPat('pg.sysMsg', CInc_Sys_Msg_Cnt::getMessages());
    CCor_Msg::getInstance() -> clear();
    $lTpl -> setPat('log.btn', btn(lan('usr.create_new_pwd'), '', '', 'submit'));
    $lTpl -> render();
  }
  
  public function actSpassupdate() {
    // ?act=log.passupdate&email=polash@yahoo.com&confirmtoken=12345678
    $lUrl = $this -> getReq('data');
    $lNewPass = escWithoutDb($this-> getReq('newpass'));
    $lCnfPass = $this-> getReq('cnfpass');
    $lConfirmToken = $this -> getReq('confirmtoken');
    $lEmail = $this -> getReq('email');
    $lQry = new CCor_Qry('SELECT * FROM al_usr WHERE email="'.$lEmail.'"');
    $lDbPwd = ($lRow = $lQry -> getAssoc()) ? $lRow['pass'] : '';
    
    $lNew = CApp_Pwd::encryptPassword($lNewPass);
    $lPresentDate = date('Y-m-d', strtotime(date("Y-m-d")));
    
    $lPassValidation = CApp_Pwd::passValidationCheck($lNewPass,$lNewPass,$lCnfPass, $lNew, $lDbPwd, $lRow['id']);
	if (count($lPassValidation)) {
      foreach ($lPassValidation as $lPass) {
        $this -> msg($lPass, mtUser, mlError);
      }
      //$this -> redirect('index.php?act=log.passupdate&email=' . $lEmail . '&confirmtoken=' .$lConfirmToken);
      $this -> redirect('index.php?act=log.passupdate&data=' . $lUrl);
      
    } else {
      $lSql = 'UPDATE al_usr SET pass=' . esc($lNew) . ', lastreset_password= "' .$lPresentDate .
           '", password_disable= "N", login_attempts=0, pass_req="N" WHERE email=' .esc($lEmail) . '';
      CCor_Qry::exec($lSql);
      // pass history
      CApp_Pwd::addedToPassHis($lRow['id'], $lNew);
      $this -> msg(lan('usr.succ.pass.update'));
      $this -> redirect('index.php?act=log');
    }
  }
  
  protected function actPassActivate() {
    $lConfirmToken = $this -> getReq('confirmtoken');
    $lEmail = $this -> getReq('email');
    $lPresentDate = date('Y-m-d', strtotime(date("Y-m-d")));
    
    $lQry = new CCor_Qry('SELECT * from al_usr WHERE token=' . esc($lConfirmToken) . ' AND email=' . esc($lEmail) . ' AND id="' . $this -> getReq('id') . '" ');
    $lRow = $lQry -> getAssoc();
     
    if ($lRow) {
      $lResults = '';
      $lQry = new CCor_Qry();
      // if current date is greater than expire date
      if ($lPresentDate > $lRow['tokenlifetime']) {
        $lResults .= lan('usr.token.expired');
      }
      elseif ($lRow['pass_req'] == "N") {
        $lResults .= lan('usr.token.used');
      }
      else {
       $lSql = 'UPDATE al_usr SET lastreset_password= "'.$lPresentDate.'", password_disable= "N", login_attempts=0, pass_req="N" WHERE email='.esc($lEmail).'';
       CCor_Qry::exec($lSql);
       $lResults .= lan('usr.pass.activate');
       } 
    } else {
      $lResults .= lan('usr.token.invalid');
    }
    if ($lResults) {
      echo '<script language="javascript">';
      echo 'alert("' . $lResults . '");';
      echo '</script>';
      echo '<table border="0" align="center">';
      echo '<tr>';
      echo '<td align="center" style="color:green; font-size:20px;font-weight:bold;padding:40px;">' .$lResults . '</td>';
      echo '</tr>';
      echo '</table>';
      exit();
    }
  }
 }