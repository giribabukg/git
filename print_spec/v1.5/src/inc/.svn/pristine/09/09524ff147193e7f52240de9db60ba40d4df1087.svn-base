<?php
class CInc_Job_Pro_Step extends CJob_Step {

  public function __construct($aJobId, $aJob = NULL) {
    parent::__construct('pro', intval($aJobId), $aJob);
  }

  protected function getMod($aJobId = NULL) {
    return new CJob_Pro_Mod($aJobId);
  }

  protected function getDat($aJobId) {
    $lRet = new CJob_Pro_Dat();
    $lRet -> load($aJobId);
    return $lRet;
  }

  public function setTiming($aDisplay, $aTime) {
    $lDis = intval($aDisplay);
    $lFti = 'fti_'.$lDis;
    $lLti = 'lti_'.$lDis;
    $lQry = new CCor_Qry('SELECT '.$lFti.' FROM al_job_pro_'.intval(MID).' WHERE id='.esc($this -> mJobId));
    if ($lRow = $lQry -> getDat()) {
      $lSql = 'UPDATE al_job_pro_'.intval(MID).' SET '.$lLti.'='.esc($aTime);
      $lTim = $lRow[$lFti];
      if ('0000-00-00 00:00:00' == $lTim) {
        $lSql.= ','.$lFti.'='.esc($aTime);
      }
      $lSql.= ' WHERE id='.esc($this -> mJobId);
      $lQry -> exec($lSql);
    }
  }

  protected function finishSub($aAlias) {
    $lAlias = $aAlias.'te'; // trans can have seven charcters only in al_crp_step
    $lSql = 'UPDATE al_job_pro_'.intval(MID).' SET '.$lAlias.'=2 WHERE id='.$this -> mJobId;
    CCor_Qry::exec($lSql);
  }
}