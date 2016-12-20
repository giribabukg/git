<?php
class CInc_Arc_Pro_His_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('job-his.menu');
  }

  protected function getStdUrl() {
    $lJid = $this -> mReq -> jobid;
    return 'index.php?act='.$this -> mMod.'&jobid='.$lJid;
  }

  protected function actStd() {
    $lJid = $this -> getReqInt('jobid');

    $lJob = new CJob_Pro_Dat();
    $lJob -> load($lJid);

    $lRet = '';

    $lVie = new CJob_Pro_Header($lJob);
    $lRet.= $lVie -> getContent();

    $lVie = new CArc_Pro_Tabs($lJid, 'his');
    $lRet.= $lVie -> getContent();

    $lVie = new CArc_His_List('pro', $lJid, 'arc');
    $lRet.= $lVie -> getContent();

    $this -> render($lRet);
  }

  protected function actNew() {
    $lJid = $this -> getReqInt('jobid');

    $lRet = '';

    $lJob = new CJob_Pro_Dat();
    $lJob -> load($lJid);

    $lVie = new CJob_Pro_Header($lJob);
    $lRet.= $lVie -> getContent();
    $lVie = new CJob_His_Form('pro', $lJid, 'snew', 'arc');
    $lRet.= $lVie -> getContent();

    $this -> render($lRet);
  }

  protected function actSnew() {
    $lJid = $this -> getReqInt('jobid');
    $lMod = new CJob_His_Mod('pro', $lJid);
    $lMod -> getPost($this -> mReq, FALSE);
    $lMod -> insert();
    $this -> redirect('index.php?act=arc-pro-his&jobid='.$lJid);
  }

}