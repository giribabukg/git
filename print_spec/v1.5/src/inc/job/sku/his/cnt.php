<?php
class CInc_Job_Sku_His_Cnt extends CJob_His_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mMmKey = 'job-sku';
    $this -> mSrc = 'sku';
  }

}