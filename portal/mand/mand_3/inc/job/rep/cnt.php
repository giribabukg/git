<?php
class CJob_Rep_Cnt extends CCust_Job_Rep_Cnt {
  
  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
  }

  protected function actNewsub() {
    $lPid = $this->getInt('pid');
    $lJob = new CJob_Pro_Dat();
    $lJob->load($lPid);

    $lVie = new CJob_Rep_Tabs(0);
    $lRet = $lVie->getContent();

    $lFrm = new CJob_Rep_Form('job-rep.snewsub');

    $lFrm->setJob($lJob);
    $lFrm->setParam('pid', $lPid);
    $lFrm->addBtn('act', 'Back to Project', 'go("index.php?act=job-pro-sub&jobid=' . $lPid . '")', 'img/ico/16/cancel.gif', 'button', array('class' => 'btn w200'));

    $lRet .= $lFrm->getContent();

    $this->render($lRet);
  }

  protected function actSnewsub() {
    $lPid = $this->getInt('pid');
    $lMid = $this->getInt('mid');

    $lObj = 'CJob_' . $this->mSrc . '_Mod';
    $lMod = new $lObj();
    $lMod->getPost($this->mReq);

    if($lMod->insert()){
      $lJobId = $lMod->getInsertId();
      $this->afterInsert($lMod);
      $lSubMod = new CJob_Pro_Sub_Mod();
      $lSubMod->getPost($this->mReq);
      $lSubMod->forceVal('pro_id', $lPid);
      $lSubMod->forceVal('jobid_rep', $lJobId);
      $lSubMod->forceVal('jobid', $lJobId);
      $lSubMod->forceVal('job_id', $lJobId);
      $lSubMod->forceVal('src', 'rep');
      $lSubMod->forceVal('is_master', 'X');
      if(!empty($lMid)){
        $lSubMod->forceVal('master_id', $lMid);
      }
      if ($lSubMod->insert()) {
        $lSid = $lSubMod->getInsertId();
        $lMod -> insertIntoProjectStatusInfo($lJobId, $lPid, $lSid);
      }
      $this->redirect('index.php?act=job-rep.edt&jobid=' . $lJobId);
    }
    $this->redirect('index.php?act=job-pro-sub&jobid=' . $lPid);
  }
}
