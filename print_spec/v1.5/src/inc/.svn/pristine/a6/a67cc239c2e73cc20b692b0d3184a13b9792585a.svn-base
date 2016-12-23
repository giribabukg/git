<?php
class CInc_Usr_Agent_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);

    $this -> mTitle = lan('usr-agent.menu');
    $this -> mReq -> expect('id');

    $lClass = 'usr-agent';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lClass)) {
      $this -> setProtection('*', $lClass, rdRead);
    }
  }

  protected function actStd() {
    $lUserId = $this -> getInt('id');

    $lMenu = new CUsr_Menu($lUserId, 'agent');
    $lList = new CUsr_Agent_List($lUserId);

    $this -> render(CHtm_Wrap::wrap($lMenu, $lList));
  }

  protected function getStdUrl() {
    $lUserId = $this -> getInt('id');
    return 'index.php?act=usr-agent&id='.$lUserId;
  }
}