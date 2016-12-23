<?php
class CInc_Job_Sec_Fil_List extends CJob_Fil_List {

  public function __construct($aJobId, $aJob, $aSub = '', $aAge = 'job') {
    parent::__construct('sec', $aJobId, $aSub, $aAge);
    $this -> checkProject();
  }
}