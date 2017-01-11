<?php
class CInc_Job_Sec_Header extends CJob_Header {

  public function __construct($aJob) {
    $lArr = CCor_Res::extract('code','id','crpmaster');
    $lCrp = $lArr['sec'];
    parent::__construct('sec', $aJob, $lCrp);
  }

}