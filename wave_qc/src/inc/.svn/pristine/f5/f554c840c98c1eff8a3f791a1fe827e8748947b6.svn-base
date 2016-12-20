<?php
class CInc_Apl_Types_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan($aMod.'.menu');
    $this -> mUsr = CCor_Usr::getInstance();
    if (!$this -> mUsr -> canRead($aMod)) {
      #$this -> denyAccess();
    }
  }

  protected function actStd() {
    $lVie = new CApl_Types_List();
    $this -> render($lVie);
  }

  protected function actEdt() {
    $lId  = $this -> getInt('id');
    $lVie = new CApl_Types_Form('apl-types.sedt', lan('apl-types.edit'));
    $lVie -> load($lId);
    $this -> render($lVie);
  }

  protected function actSedt() {
    $lMod = new CApl_Types_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> update();
    $this -> redirect();
  }

  protected function actNew() {
    $lVie = new CApl_Types_Form('apl-types.snew', lan('apl-types.new'));
    $this -> render($lVie);
  }

  protected function actSnew() {
    $lMod = new CApl_Types_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> forceVal('mand', MID);
    $lMod -> insert();
    $this -> redirect();
  }

}