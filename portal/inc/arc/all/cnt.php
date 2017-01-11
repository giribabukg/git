<?php
class CInc_Arc_All_Cnt extends CArc_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mAva = fsArt;
  }

  public function actStd() {
    $lVie = new CArc_All_List();
    $this -> render($lVie);
  }

}