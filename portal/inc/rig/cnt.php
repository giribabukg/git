<?php
class CInc_Rig_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('right.def');
    $this -> mMmKey = 'usr';
    $lpn = 'rig';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdNone);
    }
  }

  protected function actStd() {
    $lVie = new CRig_List();
    $this -> render($lVie);
  }

  protected function actEdt() {
    $lId  = $this -> mReq -> getVal('id');
    $lMid = $this -> mReq -> getVal('mand');
    $lVie = new CRig_Form_Edit($lId, $lMid);
    $this -> render($lVie);
  }

  protected function actSedt() {
    $lMod = new CRig_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> update();
    $this -> redirect();
  }

  protected function actNew() {
    $lVie = new CRig_Form_Base('rig.snew', 'New Right');
    $this -> render($lVie);
  }

  protected function actSnew() {
    $lMod = new CRig_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> insert();
    $this -> redirect();
  }

}