<?php
class CInc_Job_Adm_Fil_List extends CJob_Fil_List {

  public function __construct($aJobId, $aJob, $aSub = '', $aAge = 'job') {
    parent::__construct('adm', $aJobId, $aSub, $aAge, FALSE);
    $this -> checkProject();
  }
}