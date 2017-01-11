<?php

class CInc_Hom_Usr_Cnt extends CCor_Cnt
{

  public function __construct (ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('hom.usr.details');
    $this -> mMmKey = 'hom-wel';
    
  }

  protected function actStd() {
    $lUsr = CCor_Usr::getInstance();
    $lUid = $lUsr -> getId();
    $lMen = new CHom_Menu('usr');
    $lFrm = new CHom_Usr_Form('hom-usr.sedt', lan('hom.usr.details'));
    $lFrm -> load($lUid);
    $this -> render(CHtm_Wrap::wrap($lMen, $lFrm));
  }

  protected function actSedt() {
    $lUsr = CCor_Usr::getInstance();
    $lUid = $lUsr -> getId();
    $lVal = $this -> mReq -> getVal('val');
    
    $lQry = new CCor_Qry();
    $lSql = 'UPDATE al_usr SET ';
    $lSql .= '`user`=' . esc($lVal['user']) . ',';
    $lSql .= '`company`=' . esc($lVal['company']) . ',';
    $lSql .= '`location`=' . esc($lVal['location']) . ',';
    $lSql .= '`department`=' . esc($lVal['department']) . ',';
    $lSql .= '`phone`=' . esc($lVal['phone']);
    $lSql.= ' WHERE id=' . $lUid;
    if($lQry -> query($lSql)) {
      $this -> msg(lan('hom.usr.okay'), mtUser, mlInfo);
      $this -> redirect('index.php?act=hom-wel');
    } else {
      $this -> msg(lan('hom.usr.notokay'), mtUser, mlError);
      $this -> redirect();
    }
  }

  public function actCheck() {
    $lUsr = CCor_Usr::getInstance();
    $lUid = $lUsr -> getId();
    $lVal = $this -> getReq('val');
    $lUsrName = $lVal['usr'];
    $lPass = $lVal['pass'];
    
    if(empty($lUsrName) or empty($lPass) or strlen($lUsrName) < 2) {
      echo lan('hom.usr.mand.fie');
      exit();
    }
    // Duplicate username not allowed
    $lSql = 'SELECT user FROM al_usr WHERE user=' . esc($lUsrName) . ' AND id !=' .$lUid;
    $lSql .= ' union ';
    $lSql .= 'select user from al_usr_tmp_external WHERE user=' . esc($lUsrName) .' AND id !=' . $lUid;
    $lQry = CCor_Qry::getStr($lSql);
    if($lQry) {
      echo lan('hom.usr.error.inuse');
      exit();
    }
    // If input and DB pass not same then not update
    $lQry = new CCor_Qry('SELECT * FROM al_usr WHERE id=' . $lUsr -> getId());
    $lDbPwd = ($lRow = $lQry -> getAssoc()) ? $lRow['pass'] : '';
    
    $lUserPass = CApp_Pwd::encryptPassword($lPass);
    if($lUserPass != $lDbPwd) {
      echo lan('hom.usr.correct.pass');
    }
  }
}