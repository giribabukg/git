<?php
class CInc_Hlp_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('lib.hlp');
  }

  protected function actStd() {
    $lRet = '';
    $lSrc = $this -> mReq -> src;
    if (!empty($lSrc)) {
      $lVie = new CHlp_Item($lSrc);
    } else {
      $lVie = new CHlp_List();
    }
    $this -> render($lVie);
  }

  protected function actDay() {
    $lDay  = $this -> mReq -> d;
    $lDay2 = $this -> mReq -> d2;
    if (empty($lDay2)) {
      $lDay2 = $lDay;
    }
    $lUsr = CCor_Usr::getInstance();
    $lUsr -> setPref('hom-act.d', $lDay);
    $lUsr -> setPref('hom-act.d2', $lDay2);
    $this -> redirect();
  }

  protected function actTog() {
    $lUsr = CCor_Usr::getInstance();
    $lFin = $lUsr -> getPref('hom-act.fin');
    $lNew = (empty($lFin)) ? TRUE : NULL;
    $lUsr -> setPref('hom-act.fin', $lNew);
    $this -> redirect();
  }

}