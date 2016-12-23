<?php
class CInc_Usr_Crp_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('lib.priv');
    // Ask If user has right for this page
    $lpn = 'usr-crp';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdRead);
    }
  }

  protected function actStd() {
    $lUid = $this -> getReqInt('id');
    $lCrp = $this -> getReqInt('crp');
    $lMen = new CUsr_Menu($lUid, 'crp');
    $lMen -> setSubKey('crp_'.$lCrp);
    $lFrm = new CUsr_Crp_Form($lUid, $lCrp);
    $this -> render(CHtm_Wrap::wrap($lMen, $lFrm));
  }

  protected function actSedt() {
    $lUid = $this -> getReqInt('id');
    $lCrp = $this -> getReqInt('crp');

    $lMod = new CUsr_Crp_Mod();
    $lMod -> getPost($this -> mReq);

    $this -> redirect('index.php?act=usr-crp&id='.$lUid.'&crp='.$lCrp);
  }


}