<?php
class CInc_Job_Multi_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);

    $this -> mTitle = lan('job.multiple-edit.menu');
    $this -> mMmKey = 'opt';

    $lPriv = 'job.multiple-edit';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lPriv)) {
      $this -> setProtection('*', $lPriv, rdNone);
    }
  }

  protected function actStd() {
    $this -> redirect('index.php?act=job-multi-ord');
  }
}