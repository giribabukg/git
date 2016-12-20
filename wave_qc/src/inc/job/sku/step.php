<?php
class CInc_Job_Sku_Step extends CJob_Step {

  public function __construct($aSKUID, $aJob = NULL) {
    parent::__construct('sku', intval($aSKUID), $aJob);
  }

  protected function getMod($aSKUID = NULL) {
    return new CJob_Sku_Mod($aSKUID);
  }

  protected function getDat($aSKUID) {
    $lRet = new CJob_Sku_Dat();
    $lRet -> load($aSKUID);
    return $lRet;
  }

  public function setTiming($aDisplay, $aTime) {
    $lDis = intval($aDisplay);
    $lFti = 'fti_'.$lDis;
    $lLti = 'lti_'.$lDis;
    $lQry = new CCor_Qry('SELECT '.$lFti.' FROM al_job_sku_'.intval(MID).' WHERE id='.esc($this -> mJobId));
    if ($lRow = $lQry -> getDat()) {
      $lSql = 'UPDATE al_job_sku_'.intval(MID).' SET '.$lLti.'='.esc($aTime);
      $lTim = $lRow[$lFti];
      if ('0000-00-00 00:00:00' == $lTim) {
        $lSql.= ','.$lFti.'='.esc($aTime);
      }
      $lSql.= ' WHERE id='.esc($this -> mJobId);
      $lQry -> exec($lSql);
    }
  }

  protected function finishSub($aAlias) {
    $lAlias = $aAlias;
    $lSql = 'UPDATE al_job_sku_'.intval(MID).' SET '.$lAlias.'=2 WHERE id='.esc($this -> mJobId);
    CCor_Qry::exec($lSql);
  }

}