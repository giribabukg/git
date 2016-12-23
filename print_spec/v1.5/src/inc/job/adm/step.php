<?php
class CInc_Job_Adm_Step extends CJob_Step {
  
  public function __construct($aJobId, $aJob = NULL) {
    parent::__construct('adm', $aJobId, $aJob);
  }
  
  protected function getMod($aJobId = NULL) {
    return new CJob_Adm_Mod($aJobId);
  }
  
  protected function getDat($aJobId) {
    $lRet = new CJob_Adm_Dat();
    $lRet -> load($aJobId);
    return $lRet;
  }
  
}