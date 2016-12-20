<?php
class CInc_Arc_Tra_Cnt extends CArc_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mAva = fsTra;
  }

  public function actStd() {
    $lVie = new CArc_Tra_List();
    $this -> render($lVie);
  }

  public function actEdt() {
    $lJid = $this -> getReq('jobid');
    $lPag = $this -> getReq('page');

    $lJob = new CArc_Dat('tra');
    $lJob -> load($lJid);

    $lUsr = CCor_Usr::getInstance();
    $lUsr -> addRecentJob('arc-tra', $lJid, $lJob['stichw']);

    $lVie = new CJob_Tra_Header($lJob);
    $lRet = $lVie -> getContent();

    $lVie = new CArc_Tra_Tabs($lJid, $lPag);
    $lRet.= $lVie -> getContent();

    $lFrm = new CArc_Tra_Form('arc-tra.sedt', $lJid, $lJob, $lPag);
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
        $this -> redirect('index.php?act=arc-tra.step&sid='.$lStep.'&jobid='.$lJobId);
        exit;
      }
    }

    $this -> redirect('index.php?act=arc-tra.edt&jobid='.$lJobId.'&page='.$lPage);
  }

}