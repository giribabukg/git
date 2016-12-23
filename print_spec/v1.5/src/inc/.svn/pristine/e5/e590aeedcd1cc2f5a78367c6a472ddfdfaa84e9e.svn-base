<?php
class CInc_Job_Com_Step extends CJob_Step {

  public function __construct($aJobId, $aJob = NULL) {
    parent::__construct('com', $aJobId, $aJob);
  }

  protected function getMod($aJobId = NULL) {
    return new CJob_Com_Mod($aJobId);
  }

  protected function getDat($aJobId) {
    $lRet = new CJob_Com_Dat();
    $lRet -> load($aJobId);
    return $lRet;
  }
}