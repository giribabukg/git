<?php
class CInc_Usg_Info_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);

    $this -> mTitle = lan('usg-info.menu');
    $this -> m2Act = 'usg';
    
    // Ask If user has right for this page
    $lpn = 'usg';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdRead);
    }
  }

  protected function actStd() {
    $lUid = $this -> getInt('id');

    $lMen = new CUsg_Menu($lUid, 'info', $this -> m2Act);
    $lVie = new CUsg_Info_Form($lUid);
    $this -> render(CHtm_Wrap::wrap($lMen, $lVie));
  }

  protected function actSedt() {
    $lUid = $this -> getInt('id');

    $lMod = new CUsg_Info_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> update();
    $this -> redirect('index.php?act=usg-info&id='.$lUid);
  }

}