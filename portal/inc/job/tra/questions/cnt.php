<?php
class CInc_Job_Tra_Questions_Cnt extends CJob_Questions_Cnt {
  
  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mSrc = 'tra';
  }

}