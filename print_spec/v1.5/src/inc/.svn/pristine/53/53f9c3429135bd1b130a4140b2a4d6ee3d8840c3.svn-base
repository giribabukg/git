<?php
class CInc_Arc_Pro_Sub_Cnt extends CArc_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mAva = fsPro;
    $this -> mTitle = lan('job-sub.menu');
    $this -> mViewJoblist = CCor_Cfg::get('view.projekt.joblist', TRUE);

  }

  protected function getStdUrl() {
    $lJobId = $this -> mReq -> jobid;
    return 'index.php?act='.$this -> mMod.'&jobid='.$lJobId;
  }

  protected function actStd() {
    $lJobId = $this -> getInt('jobid');

    $lJob = new CJob_Pro_Dat();
    $lJob -> load($lJobId);

    $lVie = new CJob_Pro_Header($lJob);
    $lRet = $lVie -> getContent();

    $lVie = new CArc_Pro_Tabs($lJobId, 'sub');
    $lRet.= $lVie -> getContent();

    $lIsArchived = TRUE;
    if ($this -> mViewJoblist) {
      $lVie = new CJob_Pro_Sub_Job_List($lJobId, FALSE, $lIsArchived);
    } else {
      $lVie = new CJob_Pro_Sub_List($lJobId, $lIsArchived);
    }
    $lRet.= $lVie -> getContent();

    $this -> render($lRet);
  }

}