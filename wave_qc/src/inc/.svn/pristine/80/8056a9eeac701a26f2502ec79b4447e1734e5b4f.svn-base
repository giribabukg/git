<?php

class CInc_Hom_Usr_Cnt extends CCor_Cnt
{

  public function __construct (ICor_Req $aReq, $aMod, $aAct)
  {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('hom.usr.change');
    $this -> mMmKey = 'hom-wel';
    
  }

  protected function actStd ()
  {
    
    $lUsr = CCor_Usr::getInstance();
    $lUid = $lUsr -> getId();
    $lMen = new CHom_Menu('usr');
    $lFrm = new CHom_Usr_Form('hom-usr.sedt', lan('hom.usr.change'));
    $lFrm -> load($lUid);
    $this -> render(CHtm_Wrap::wrap($lMen, $lFrm));
  }

  protected function actSedt ()
  {
    $lUsr = CCor_Usr::getInstance();
    $lUid = $lUsr -> getId();
    
    $lVal = $this -> mReq -> getVal('val');
    $lOld = trim($lVal['user']);
    $lNew = trim($lVal['new']);
    $lCnf = trim($lVal['confirm']);
    
    // $lVal = $this -> getReq('val');
    if (empty($lOld) or empty($lNew) or empty($lCnf) or strlen($lNew) < 2 or
         strlen($lCnf) < 2) {
      $this -> msg(lan('hom.usr.error'), mtUser, mlError);
      $this -> getFormvalue($lVal);
    }
    
    $lQry = new CCor_Qry('SELECT id FROM al_usr WHERE user="' . $lNew . '"');
    if ($lRow = $lQry -> getAssoc()) {
      if ($lUid != $lRow['id']) {
        CCor_Msg::add(lan('hom.usr.error.inuse'), mtUser, mlError);
        $this -> getFormvalue($lVal);
      }
    }
    
    $lQry = new CCor_Qry('SELECT user FROM al_usr WHERE id=' . $lUid);
    if ($lRow = $lQry -> getAssoc()) {
      if ($lOld != $lRow['user']) {
        CCor_Msg::add(lan('hom.usr.error.old'), mtUser, mlError);
        $this -> getFormvalue($lVal);
      }
      if ($lOld == $lNew) {
        CCor_Msg::add(lan('hom.usr.error.new'), mtUser, mlError);
        $this -> getFormvalue($lVal);
      }
      if ($lNew != $lCnf) {
        
        CCor_Msg::add(lan('hom.usr.error.cnf'), mtUser, mlError);
        $this -> getFormvalue($lVal);
      }
      
      $lSql = 'UPDATE al_usr SET user="' . $lNew . '" WHERE id=' . $lUid;
      if ($lQry -> query($lSql)) {
        $this -> msg(lan('hom.usr.okay'), mtUser, mlInfo);
        $this -> redirect('index.php?act=hom-wel');
      } else {
        $this -> msg(lan('hom.usr.notokay'), mtUser, mlError);
        $this -> redirect();
      }
    }
  }

  protected function getFormvalue ($lVal)
  {
    $lMen = new CHom_Menu('usr');
    $lFrm = new CHom_Usr_Form('hom-usr.sedt', lan('hom.usr.change'));
    $lFrm -> assignVal($lVal);
    $this -> render(CHtm_Wrap::wrap($lMen, $lFrm));
    return $this -> redirect('index.php?act=hom-usr.sedt');
  }

  protected function actOk ()
  {
    $this -> render(lan('hom.usr.okay'));
  }
}