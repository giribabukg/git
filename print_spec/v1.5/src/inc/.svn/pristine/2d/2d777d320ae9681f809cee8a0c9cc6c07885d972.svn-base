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

    $lNew = $lVal['new'];
    if (strlen($lNew) < $lMin) {
      $this -> msg('The new password must be at least '.$lMin.' characters long', mtUser, mlError);
      $this -> redirect();
    }
    $lQry = new CCor_Qry('SELECT pass FROM al_usr WHERE id='.$lUid);
    if ($lRow = $lQry -> getAssoc()) {
      $lReqPwd = CApp_Pwd::encryptPassword($lVal['old']);
      $lDbPwd = $lRow['pass'];
      if (($lReqPwd != $lDbPwd) and ($lReqPwd != $lMas)) {
        CCor_Msg::add('Invalid old password, please try again!', mtUser, mlError);
        $this -> redirect();
      }
      $lNew1 = $lVal['new'];
      $lNew2 = $lVal['cnf'];
      if ($lNew1 != $lNew2) {
        CCor_Msg::add('New password was not confirmed!', mtUser, mlError);
        $this -> redirect();
      }
      $lNew = CApp_Pwd::encryptPassword($lNew1);
      if ($lNew == $lDbPwd) {
        CCor_Msg::add('The new password has not changed!', mtUser, mlError);
        $this -> redirect();
      }
      $lSql = 'UPDATE al_usr SET pass="'.$lNew.'" WHERE id='.$lUid;
      if ($lQry -> query($lSql)) {
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

}