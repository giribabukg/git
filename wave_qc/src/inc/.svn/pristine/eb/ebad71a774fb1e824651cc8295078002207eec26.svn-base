<?php
class CInc_Arc_Pro_Cnt extends CArc_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mAva = fsPro;
  }

  protected function actStd() {
    $lVie = new CArc_Pro_List();
    $this -> render($lVie);
  }

  protected function actSub() {
    $lPid = $this -> getInt('jid');
    $lVie = new CArc_Pro_Sub($lPid);
    $lVie -> render();
  }

  protected function actEdt() {
    $lJid = $this -> getInt('jobid');
    $lPag = $this -> getReq('page');

    $lJob = new CJob_Pro_Dat();
    $lJob -> load($lJid);

    $lUsr = CCor_Usr::getInstance();
    $lUsr -> addRecentJob('pro', $lJid, $lJob['project']);

    $lVie = new CJob_Pro_Header($lJob);
    $lRet = $lVie -> getContent();

    $lVie = new CArc_Pro_Tabs($lJid, $lPag);
    $lRet.= $lVie -> getContent();

    $lFrm = new CArc_Pro_Form($lJid, $lPag);
    $lRet.= $lFrm -> getContent();

    $this -> render($lRet);
  }

}