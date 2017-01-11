<?php
class CInc_Usr_Crp_Status_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('lib.priv');
    $this -> mUsr = CCor_Usr::getInstance();
    $this -> mLpn = "usr-crp-sta";
    // Ask If user has right for this page
    if (!$this->mUsr->canRead($this->mLpn)) {
      $this -> setProtection('*', $this->mLpn, rdRead);
    }
  }

  protected function actStd() {
    $lUid = $this -> getReqInt('id');
    $lCrp = $this -> getReqInt('crp');
    $lMen = new CUsr_Menu($lUid, 'crp_status_edit');
    $lMen -> setSubKey('crp_status_edit_'.$lCrp);
    $lFrm = new CUsr_Crp_Status_Form($lUid, $lCrp);
    $this -> render(CHtm_Wrap::wrap($lMen, $lFrm));
  }

  protected function actSedt() {
    $lUid = $this -> getReqInt('id');
    $lCrp = $this -> getReqInt('crp');
    if($this->mUsr->canEdit($this->mLpn)) {
      $lMod = new CUsr_Crp_Status_Mod();
      $lMod -> getPost($this -> mReq);
    }
    $this -> redirect('index.php?act=usr-crp-status&id='.$lUid.'&crp='.$lCrp);
  }
}