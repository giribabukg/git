<?php
class CInc_Arc_Rep_Cnt extends CArc_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mAva = fsArt;
  }

  public function actStd() {
    $lVie = new CArc_Rep_List();
    $this -> render($lVie);
  }

  public function actEdt() {
    $lJid = $this -> getReq('jobid');
    $lPag = $this -> getReq('page');

    $lJob = new CArc_Dat('rep');
    $lJob -> load($lJid);

    $lUsr = CCor_Usr::getInstance();
    $lUsr -> addRecentJob('arc-rep', $lJid, $lJob['stichw']);

    $lVie = new CJob_Rep_Header($lJob);
    $lRet = $lVie -> getContent();

    $lVie = new CArc_Rep_Tabs($lJid, $lPag);
    $lRet.= $lVie -> getContent();

    $lFrm = new CArc_Rep_Form('arc-rep.sedt', $lJid, $lJob, $lPag);
    $lRet.= $lFrm -> getContent();

    $this -> render($lRet);
  }

  protected function actSedt() {
    $lJobId = $this -> getReq('jobid');
    $lStep = $this -> getReq('step');
    $lPage = $this -> getReq('page', 'not');

    $lMod = new CArc_Rep_Mod($lJobId);
    $lMod -> getPost($this -> mReq);
    if( $lMod -> update()) {
      if ($lStep > 0){
        $this -> redirect('index.php?act=arc-rep.step&sid='.$lStep.'&jobid='.$lJobId);
        exit;
      }
    }

    $this -> redirect('index.php?act=arc-rep.edt&jobid='.$lJobId.'&page='.$lPage);
  }

}