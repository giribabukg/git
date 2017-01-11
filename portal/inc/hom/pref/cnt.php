<?php
class CInc_Hom_Pref_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('hom.pref');
    $this -> mMmKey = 'hom-wel';
  }

  protected function actStd() {
    $lMen = new CHom_Menu('pref');
    $lFrm = new CHom_Pref_Form();
    $this -> render(CHtm_Wrap::wrap($lMen, $lFrm));
  }

  protected function actPost() {
    $lVal = $this -> getReq('val');
    $lOld = $this -> getReq('old');
    $lTokenVal = $this -> getReq(sec_token);
    if(!isset($lTokenVal) || $lTokenVal !== $_SESSION[sec_token]) {
      $this -> redirect('index.php?act=hom-wel');
    }
    $lUsr = CCor_Usr::getInstance();
    foreach ($lVal as $lKey => $lValue) {
      $lUsr -> setPref($lKey, $lValue);
    }

    $this -> redirect('index.php?act=hom-wel');
  }

}