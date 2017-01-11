<?php
class CInc_Usr_Crp_Step_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('lib.priv');
    $this -> mUsr = CCor_Usr::getInstance();
    // Ask If user has right for this page
    $lpn = 'usr-crp-step';
    if (!$this->mUsr->canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdRead);
    }
  }

  protected function actStd() {
    $lUid = $this -> getReqInt('id');
    $lCrp = $this -> getReqInt('crp');
    $lMen = new CUsr_Menu($lUid, 'crp');
    $lMen -> setSubKey('crp_'.$lCrp);
    $lFrm = new CUsr_Crp_Step_Form($lUid, $lCrp);
    $this -> render(CHtm_Wrap::wrap($lMen, $lFrm));
  }

  protected function actSedt() {
    $lUid = $this -> getReqInt('id');
    $lCrp = $this -> getReqInt('crp');
    if($this->mUsr->canEdit("usr-crp-step")) {
      $lMod = new CUsr_Crp_Step_Mod();
      $lMod -> getPost($this -> mReq);
    }
    $this -> redirect('index.php?act=usr-crp-step&id='.$lUid.'&crp='.$lCrp);
  }
}