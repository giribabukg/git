<?php
class CInc_Job_Adm_His_Cnt extends CJob_His_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mSrc = 'adm';
  }

}