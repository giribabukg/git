<?php
class CJob_Art_Cnt extends CCust_Job_Art_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
  }

  protected function actNewsub() {
    $lPid = $this->getInt('pid');
    $lJob = new CJob_Pro_Dat();
    $lJob->load($lPid);

    $lVie = new CJob_Art_Tabs(0);
    $lRet = $lVie->getContent();

    $lFrm = new CJob_Art_Form('job-art.snewsub');

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
      $lSubMod->forceVal('jobid_art', $lJobId);
      $lSubMod->forceVal('jobid', $lJobId);
      $lSubMod->forceVal('job_id', $lJobId);
      $lSubMod->forceVal('src', 'art');
      if(!empty($lMid)){
        $lSubMod->forceVal('master_id', $lMid);
      }
      if ($lSubMod->insert()) {
        $lSid = $lSubMod->getInsertId();
        $lMod -> insertIntoProjectStatusInfo($lJobId, $lPid, $lSid);
        
        //Add link from art to rep job
        $lSql = 'SELECT * FROM al_job_sub_' . MID . ' WHERE id=' . $lMid;
        $lQry = new CCor_Qry($lSql);
        $lJob = $lQry->getDat();
        $lRelatedJobId = intval($lJob['jobid']);
        
        $lSql = 'INSERT INTO al_job_sub_'.intval(MID).' (pro_id, jobid_rep, jobid_art) VALUES (0, '.esc($lRelatedJobId).', '.esc($lJobId).')';
      	CCor_Qry::exec($lSql);
      }
      $this->redirect('index.php?act=job-art.edt&jobid=' . $lJobId);
    }
    $this->redirect('index.php?act=job-pro-sub&jobid=' . $lPid);
  }

  protected function actNewmastersub() {
    $lPid = $this->getInt('pid');
    $lMasterId = $this->getInt('mid');

    $lSql = 'SELECT * FROM al_job_sub_' . MID . ' WHERE id=' . $lMasterId;
    $lQry = new CCor_Qry($lSql);
    $lJob = $lQry->getDat();
    $lRef = intval($lJob['jobid']);

    $lVie = new CJob_Art_Tabs(0);
    $lRet = $lVie->getContent();

    $lFrm = new CJob_Art_Form('job-art.snewsub');
    $lFrm->setJob($lJob);
    $lFrm->setParam('pid', $lPid);
    $lFrm->setParam('mid', $lMasterId);
    $lFrm->addBtn('act', 'Back to Project', 'go("index.php?act=job-pro-sub&jobid=' . $lPid . '")', 'img/ico/16/cancel.gif', 'button', array('class' => 'btn w200'));

    $lRet .= $lFrm->getContent();

    $this->render($lRet);
  }
  
}
