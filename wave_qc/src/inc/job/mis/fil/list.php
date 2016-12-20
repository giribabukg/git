<?php
class CInc_Job_Mis_Fil_List extends CJob_Fil_List {

  public function __construct($aJobId, $aJob, $aSub = '', $aAge = 'job') {
    parent::__construct('mis', $aJobId, $aSub, $aAge);
    $this -> checkProject();
  }
}