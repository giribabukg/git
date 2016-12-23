<?php
class CInc_Job_Mis_Step extends CJob_Step {

  public function __construct($aJobId, $aJob = NULL) {
    parent::__construct('mis', $aJobId, $aJob);
  }

  protected function getMod($aJobId = NULL) {
    return new CJob_Mis_Mod($aJobId);
  }

  protected function getDat($aJobId) {
    $lRet = new CJob_Mis_Dat();
    $lRet -> load($aJobId);
    return $lRet;
  }
}