<?php
class CInc_Job_All_Cnt extends CJob_Cnt {
  
  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mAva = fsArt;
  }
  
  public function actStd() {
    $lVie = new CJob_All_List();
    $this -> render($lVie);
  }
}