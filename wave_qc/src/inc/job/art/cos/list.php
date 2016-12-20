<?php
class CInc_Job_Art_Cos_List extends CJob_Cos_List {
  
  public function __construct($aJobId, $aJob) {
    parent::__construct('art', $aJobId, $aJob);
  }
 
}