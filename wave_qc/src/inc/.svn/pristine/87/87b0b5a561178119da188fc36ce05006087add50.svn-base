<?php
class CInc_Usr_Info_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('usr-info.menu');
    
    // Ask If user has right for this page
    $lpn = 'usr-info';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdRead);
    }
    
  }

  protected function actStd() {
    $lUid = $this -> getInt('id');

    $lMen = new CUsr_Menu($lUid, 'info');
    $lVie = new CUsr_Info_Form($lUid);
    $this -> render(CHtm_Wrap::wrap($lMen, $lVie));
  }

  protected function actSedt() {
    $lUid = $this -> getInt('id');

    $lMod = new CUsr_Info_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> update();
    $this -> redirect('index.php?act=usr-info&id='.$lUid);
  }

}