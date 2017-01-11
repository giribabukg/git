<?php
class CInc_Hom_Wel_Myinbox_Cnt extends CInc_Hom_Wel_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mMod = 'hom-wel-myinbox';
  }
  
  protected function actOrd() {
    $this -> mReq -> expect('fie');
    $lFie = $this -> mReq -> getVal('fie');
    $lUsr = CCor_Usr::getInstance();
    $lUsr -> setPref($this -> mPrf.'.ord', $lFie);
    $this -> redirect();
  }
}