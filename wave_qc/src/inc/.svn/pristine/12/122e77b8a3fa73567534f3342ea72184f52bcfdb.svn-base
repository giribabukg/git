<?php
class CInc_App_Event_Action_Dms_Copytodalim extends CApp_Event_Action {
  
  public function execute() {
    $this->mJob = $this->mContext['job'];
    $this->mJid = $this->mJob['jobid'];
    $this->mSrc = $this->mJob['src'];
    
    $lJobApl = new CApl_Job($this->mSrc, $this->mJid);
    $lLoop = $lJobApl->getLastLoop();
    $lSub = $lLoop->getLastSubLoop();
    $lSid = $lSub['id'];
    
    $lDms = new CApi_Dms_Query();
    $lList = $lDms->getFileList(MANDATOR_ENVIRONMENT, $this->mSrc, $this->mJid, 0);
    
    if (empty($lList)) {
      return true;
    }
    $lRow = array_shift($lList);
    
    $lSql = 'UPDATE al_job_apl_subloop SET file_name='.esc($lRow['filename']).',file_version='.esc($lRow['fileversionid']);
    $lSql.= ' WHERE id='.esc($lSid);
    CCor_Qry::exec($lSql);
    
    $lQue = new CApp_Queue('copydmstodalim');
    $lQue->setParam('src', $this->mSrc);
    $lQue->setParam('jid', $this->mJid);
    
    $lQue->setParam('vid', $lRow['fileversionid']);
    $lQue->setParam('fn', $lRow['filename']);
    $lQue->setParam('sid', $lSid);
    $lQue->insert();
    return true;
  }

}
