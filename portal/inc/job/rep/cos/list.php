<?php
class CInc_Job_Rep_Cos_List extends CJob_Cos_List {
  
  public function __construct($aJobId, $aJob) {
    parent::__construct('rep', $aJobId, $aJob);
  }
 
}