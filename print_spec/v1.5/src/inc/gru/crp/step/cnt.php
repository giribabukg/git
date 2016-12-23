<?php
class CInc_Gru_Crp_Step_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('lib.rights');
    $this -> mMmKey = 'usr';
    
    $lpn = 'gru';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdNone);
    }
  }

  protected function actStd() {
    $lUid = $this -> getReqInt('id');
    $lCrp = $this -> getReqInt('crp');
    $lMen = new CGru_Menu($lUid, 'crp');
    $lMen -> setSubKey('crp_'.$lCrp);
    $lFrm = new CGru_Crp_Step_Form($lUid, $lCrp);
    $this -> render(CHtm_Wrap::wrap($lMen, $lFrm));
  }

  protected function actSedt() {
    $lGid = $this -> getReqInt('id');
    $lCrp = $this -> getReqInt('crp');

    $lMod = new CGru_Crp_Step_Mod();
    $lMod -> getPost($this -> mReq);

    $this -> redirect('index.php?act=gru-crp-step&id='.$lGid.'&crp='.$lCrp);
  }


}