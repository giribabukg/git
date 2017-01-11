<?php
class CInc_Hom_Pwd_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('hom.pwd.change');
    $this -> mMmKey = 'hom-wel';
  }

  protected function actStd() {
    $lMen = new CHom_Menu('pwd');
    $lFrm = new CHom_Pwd_Form();
    $this -> render(CHtm_Wrap::wrap($lMen, $lFrm));
  }
  
  protected function actPost() {
    $lCfg = CCor_Cfg::getInstance();
    $lMin = $lCfg -> get('log.pwd.minchar');
    $lMas = $lCfg -> get('log.master');

    $lUsr = CCor_Usr::getInstance();
    $lUid = $lUsr -> getId();

    $lVal = $this -> getReq('val');
    
    $lQry = new CCor_Qry('SELECT * FROM al_usr WHERE id='.$lUid);
    if ($lRow = $lQry -> getAssoc()) {
      $lReqPwd = CApp_Pwd::encryptPassword($lVal['old']);
      $lDbPwd = $lRow['pass'];
      if (($lReqPwd != $lDbPwd) and ($lReqPwd != $lMas)) {
        $lAttempts = $lRow['login_attempts']+1;
        if($lRow['login_attempts'] >= CCor_Cfg::get('login.attempts.usr.deactivate', 3)){
          CCor_Msg::add(lan('access.deny'), mtUser, mlError);
          $this -> redirect('index.php');
        }
        CCor_Qry::exec('UPDATE al_usr set login_attempts="' .$lAttempts . '" where user="' .$lRow['user'] . '"');
        //wrong attempt added to history 
        CUsr_External_Cnt:: extUsrHis ($lRow['id'], $lRow['id'], date("Y-m-d", time()), 'Wrong password attempts');
        CCor_Msg::add('Invalid old password, please try again!', mtUser, mlError);
        $this -> redirect();
      }
      //if user password is okay but exceed his attempts then user will deactivate
      if($lRow['login_attempts'] >= CCor_Cfg::get('login.attempts.usr.deactivate', 3)){
        CCor_Msg::add(lan('access.deny'), mtUser, mlError);
        $this -> redirect('index.php');
      }
  
      $lNew = CApp_Pwd::encryptPassword($lVal['new']);
      $lCurrentTime = date("Y-m-d", time());
      $CtokenExpiration = CCor_Cfg::get('token.expiration',3);
      $lUserEmailConfirmationTokenExpiry = date('Y-m-d', strtotime("+$CtokenExpiration days"));
      $lToken = CApp_Pwd::createNewToken();

      $lSql = 'UPDATE al_usr SET pass="'.$lNew.'", token='.esc($lToken).', tokenlifetime= '.esc($lUserEmailConfirmationTokenExpiry).', pass_req="Y" WHERE id='.$lUid;
      //added to pass history
      CApp_Pwd::addedToPassHis($lUid, $lNew);
      if ($lQry -> query($lSql)) {
        
       $lEmailTpl = CCor_Cfg::get('tpl.email', array());
        if (!empty($lEmailTpl) AND isset($lEmailTpl['pwd.fgt.activate'])) {
          $lUsrTpl = $lEmailTpl['pwd.fgt.activate'];
        }
        if (!empty($lUsrTpl)) {
          $lUid = intval($lRow['id']);
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
          $lTpl -> setPat('date', $lUserEmailConfirmationTokenExpiry);
        
          $lTpl -> addUserPat($lUid, 'to');
          $lTpl -> addUserPat(1, 'from');
          $lNam = cat($lRow['firstname'], $lRow['lastname']);
          #$lTpl -> setPat('new.pwd', $lPwd);
          $lTpl -> setPat('link', CCor_Cfg::get('base.url') . 'index.php?act=log.passactivate&email='.$lRow['email'].'&confirmtoken='. $lToken.'&id='.$lUid);
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
          #$lMail -> insert(false);
          $lMail -> insert('',1);
        
          #$this -> msg(lan('usr.send_new_pwd.msg'), mtUser, mlInfo);
        }
        $this -> msg('Password change was successful!', mtUser, mlInfo);
        $this -> redirect('index.php?act=hom-wel');
      } else {
        $this -> msg('Password change was not successful!', mtUser, mlError);
        $this -> redirect();
      }
    }
  }

  protected function actOk() {
    $this -> render('Your password has been changed!');
  }
  
  public function actCheck() {

    $lUsr = CCor_Usr::getInstance();
    $lVal = $this -> getReq('val');
    $lOld = $lVal['old'];
    $lNew = $lVal['new'];
    $lCnf = $lVal['cnf'];
    $lNewEncoded = CApp_Pwd::encryptPassword($lNew);
    
    $lQry = new CCor_Qry('SELECT * FROM al_usr WHERE id='.$lUsr -> getId());
    $lDbPwd = ($lRow = $lQry -> getAssoc()) ? $lRow['pass'] : '';
    
    #Password validation check and if validation success store the password into password history table
    $lPassValidation = CApp_Pwd::passValidationCheck($lOld, $lNew, $lCnf, $lNewEncoded, $lDbPwd, $lUsr -> getId());
    echo implode("\n", $lPassValidation);
    
  } 
 
}