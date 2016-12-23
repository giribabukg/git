<?php
class CInc_Rol_Rig_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);

    $this -> mTitle = lan('lib.rights');
    $this -> mMmKey = 'usr';

    $lCode = 'rol';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lCode)) {
      $this -> setProtection('*', $lCode, rdNone);
    }
  }

  protected function actStd() {
    $lRightId = $this -> getReqInt('id');
    $lMandatorId = $this -> getReqInt('mid');

    $lMenu = new CRol_Menu($lRightId, 'rig');
    $lMenu -> setSubKey('mid_'.$lMandatorId);

    $lForm = new CRol_Rig_Form($lRightId, $lMandatorId);
    $this -> render(CHtm_Wrap::wrap($lMenu, $lForm));
  }

  protected function actSedt() {
    $lRightId = $this -> getReqInt('id');
    $lMandatorId = $this -> getReqInt('mid');

    $lMod = new CRol_Rig_Mod();
    $lMod -> getPost($this -> mReq);
    $this -> redirect('index.php?act=rol-rig&id='.$lRightId.'&mid='.$lMandatorId);
  }
}