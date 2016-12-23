<?php
class CInc_Arc_Adm_Cnt extends CArc_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mAva = fsAdm;
  }

  public function actStd() {
    $lVie = new CArc_Adm_List();
    $this -> render($lVie);
  }

  public function actEdt() {
    $lJid = $this -> getReq('jobid');
    $lPag = $this -> getReq('page');

    $lJob = new CArc_Dat('adm');
    $lJob -> load($lJid);

    $lUsr = CCor_Usr::getInstance();
    $lUsr -> addRecentJob('arc-adm', $lJid, $lJob['stichw']);

    $lVie = new CJob_Adm_Header($lJob);
    $lRet = $lVie -> getContent();

    $lVie = new CArc_Adm_Tabs($lJid, $lPag);
    $lRet.= $lVie -> getContent();

    $lFrm = new CArc_Adm_Form('arc-adm.sedt', $lJid, $lJob, $lPag);
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
        $this -> redirect('index.php?act=arc-adm.step&sid='.$lStep.'&jobid='.$lJobId);
        exit;
      }
    }

    $this -> redirect('index.php?act=arc-adm.edt&jobid='.$lJobId.'&page='.$lPage);
  }

}