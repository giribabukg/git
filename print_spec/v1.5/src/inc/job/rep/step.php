<?php
class CInc_Job_Rep_Step extends CJob_Step {

  public function __construct($aJobId, $aJob = NULL) {
    parent::__construct('rep', $aJobId, $aJob);
  }

  protected function getMod($aJobId = NULL) {
    return new CJob_Rep_Mod($aJobId);
  }

  protected function getDat($aJobId) {
    $lRet = new CJob_Rep_Dat();
    $lRet -> load($aJobId);
    return $lRet;
  }
}