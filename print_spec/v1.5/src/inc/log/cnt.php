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

      //redirect to start page
      $lAct = $this -> getReq('nact');
      if (empty($lAct)) {
        $lAct = 'act='.$lUsr -> getPref('log.url', 'hom-wel');
      } else {
        $lAct = urldecode($lAct);
      }
      $this -> redirect('index.php?'.$lAct);
    } else {
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
    $lTpl -> setPat('pg.banner',          getImgPath('img/login/banner.png'));
    $lTpl -> setPat('pg.loginbutton',     getImgPath('img/login/btn-recover.gif'));
    $lTpl -> setPat('pg.mand.cust',       getImgPath('img/login/cust.png'));
    $lTpl -> setPat('pg.mand.customer',   getImgPath('img/login/customer.png'));
    $lTpl -> setPat('pg.mand.headerback', getImgPath('img/login/header-back.png'));
    $lTpl -> setPat('pg.cust.name',       CUSTOMER_NAME_LOGIN);
    $lTpl -> setPat('pg.lib.wait',        htm(lan('lib.wait')));
    $lTpl -> setPat('pg.log.pwd.forgot',  htm(lan('log.pwd.forgot')));
    $lTpl -> setPat('pg.usr.name',        htm(lan('usr.name')));
    $lTpl -> setPat('pg.usr.pwd',         htm(lan('usr.pwd')));

    $lTpl -> setPat('pg.sysMsg', CInc_Sys_Msg_Cnt::getMessages());

    CCor_Msg::getInstance() -> clear();
    $lTpl -> setPat('log.btn', btn(lan('usr.create_new_pwd'), '', '', 'submit'));
    $lTpl -> render();
  }

  protected function actSrecover() {
    $lNam = $this -> getReq('usr');
    $lNam = trim($lNam);

    $lSql = 'SELECT * FROM al_usr WHERE user="'.addslashes($lNam).'" OR email="'.addslashes($lNam).'"';
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

    if (!empty($lEmailTpl) AND isset($lEmailTpl['pwd'])) {
      $lUsrTpl = $lEmailTpl['pwd'];
    }

    if (!empty($lUsrTpl)) {
      $lUid = intval($lRow['id']);
      $lPwd = CApp_Pwd::createPassword(8);
      $lEnc = CApp_Pwd::encryptPassword($lPwd);
      $lSql = 'UPDATE al_usr SET pass="'.addslashes($lEnc).'" WHERE id='.$lUid;
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
      $lTpl -> setPat('portal_name', $lPortalName);
      $lDate = date(lan('lib.date.xxl'), strtotime("+2 week"));
      $lTpl -> setPat('date', $lDate);

      $lTpl -> addUserPat($lUid, 'to');
      $lTpl -> addUserPat(1, 'from');
      $lNam = cat($lRow['firstname'], $lRow['lastname']);
      $lTpl -> setPat('new.pwd', $lPwd);
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
      $lMail -> setReciverId($lRow['id']);
      $lMail -> setMailType(mailSys);
//       $lMail -> send();
      $lMail -> insert(false);

      $this -> msg(lan('usr.send_new_pwd.msg'), mtUser, mlInfo);
    } else {
      $this -> msg('No/Kein Emailtemplate', mtUser, mlWarn);
    }
    $this -> redirect();
  }

}