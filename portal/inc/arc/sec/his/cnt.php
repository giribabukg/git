<?php
class CInc_Arc_Sec_His_Cnt extends CArc_His_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mSrc = 'sec';
  }
}