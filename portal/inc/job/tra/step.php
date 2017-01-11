<?php
class CInc_Job_Tra_Step extends CJob_Step {

  public function __construct($aJobId, $aJob = NULL) {
    parent::__construct('tra', $aJobId, $aJob);
  }

  protected function getMod($aJobId = NULL) {
    return new CJob_Tra_Mod($aJobId);
  }

  protected function getDat($aJobId) {
    $lRet = new CJob_Tra_Dat();
    $lRet -> load($aJobId);
    return $lRet;
  }
}