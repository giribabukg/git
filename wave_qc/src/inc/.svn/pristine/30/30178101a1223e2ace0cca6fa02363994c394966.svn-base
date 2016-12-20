<?php
class CInc_Job_Sec_Step extends CJob_Step {

  public function __construct($aJobId, $aJob = NULL) {
    parent::__construct('sec', $aJobId, $aJob);
  }

  protected function getMod($aJobId = NULL) {
    return new CJob_Sec_Mod($aJobId);
  }

  protected function getDat($aJobId) {
    $lRet = new CJob_Sec_Dat();
    $lRet -> load($aJobId);
    return $lRet;
  }
}