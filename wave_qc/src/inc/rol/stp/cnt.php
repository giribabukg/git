<?php
class CInc_Rol_Stp_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('lib.rights');
    $this -> mMmKey = 'usr';
    // Ask If user has right for this page
    $lpn = 'rol';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdNone);
    }
  }

  protected function actStd() {
    $lRid = $this -> getReqInt('id');
    $lCrp = $this -> getReqInt('crp');
    $lMen = new CRol_Menu($lRid, 'stp');
    $lMen -> setSubKey('stp_'.$lCrp);
    $lFrm = new CRol_Stp_Form($lRid, $lCrp);
    $this -> render(CHtm_Wrap::wrap($lMen, $lFrm));
  }

  protected function actSedt() {
    $lRid = $this -> getReqInt('id');
    $lCrp = $this -> getReqInt('crp');

    $lMod = new CRol_Stp_Mod();
    $lMod -> getPost($this -> mReq);

    $this -> redirect('index.php?act=rol-stp&id='.$lRid.'&crp='.$lCrp);
  }

}