<?php
class CInc_Usr_Rig_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('lib.rights');
    $this -> mUsr = CCor_Usr::getInstance();
    // Ask If user has right for this page
    $lpn = 'usr-rig';
    if (!$this->mUsr->canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdRead);
    }
  }

  protected function actStd() {
      $lUid = $this -> getReqInt('id');
      $lMid = $this -> getReqInt('mid');
      $lRig = $this -> getReq('rig');
      $lMen = new CUsr_Menu($lUid, 'rig');
      if(!empty($lRig)) $lPid = $lRig;
      else $lPid = $lMid;
      $lMen -> setSubKey('mid_'.$lPid);
      $lVie = new CUsr_Rig_Form($lUid, $lMid, $lRig);
      $this -> render(CHtm_Wrap::wrap($lMen, $lVie));
  }

  protected function actSedt() {
    $lUid = $this -> getReqInt('id');
    $lMid = $this -> getReqInt('mid');
    if($this->mUsr->canEdit("usr-rig")) {
      $lRig = $this -> getReq('rig');
      $lMod = new CUsr_Rig_Mod();
      $lMod -> getPost($this -> mReq);
      $this -> redirect('index.php?act=usr-rig&id='.$lUid.'&mid='.$lMid.'&rig='.$lRig);
    }
    $this -> redirect('index.php?act=usr-rig&id='.$lUid.'&mid='.$lMid);
  }
}