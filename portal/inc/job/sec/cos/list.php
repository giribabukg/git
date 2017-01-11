<?php
class CInc_Job_Sec_Cos_List extends CJob_Cos_List {

  public function __construct($aJobId, $aJob) {
    parent::__construct('sec', $aJobId, $aJob);
  }

}