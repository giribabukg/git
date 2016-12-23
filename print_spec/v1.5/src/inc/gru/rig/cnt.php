<?php
class CInc_Gru_Rig_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);

    $this -> mTitle =  lan('lib.rights');
    $this -> mMmKey = 'usr';

    $lCode = 'gru';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lCode)) {
      $this -> setProtection('*', $lCode, rdNone);
    }
  }

  protected function actStd() {
    $lGroupId = $this -> getReqInt('id');
    $lMandatorId = $this -> getReqInt('mid');
    $lRightCode = $this -> getReq('rig');

    $lMenu = new CGru_Menu($lGroupId, 'rig');
    $lAffix = !empty($lRightCode) ? $lRightCode : $lMandatorId;
    $lMenu -> setSubKey('mid_'.$lAffix);

    $lForm = new CGru_Rig_Form($lGroupId, $lMandatorId, $lRightCode);
    $this -> render(CHtm_Wrap::wrap($lMenu, $lForm));
  }

  protected function actSedt() {
    $lGroupId = $this -> getReqInt('id');
    $lMandatorId = $this -> getReqInt('mid');
    $lRightCode = $this -> getReq('rig');

    $lMod = new CGru_Rig_Mod();
    $lMod -> getPost($this -> mReq);
    $this -> redirect('index.php?act=gru-rig&id='.$lGroupId.'&mid='.$lMandatorId.'&rig='.$lRightCode);
  }
}