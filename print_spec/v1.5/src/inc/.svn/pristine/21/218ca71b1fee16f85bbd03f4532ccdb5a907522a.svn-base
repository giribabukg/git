<?php
class CInc_Job_Rep_Header extends CJob_Header {

  public function __construct($aJob) {
    $lArr = CCor_Res::extract('code','id','crpmaster');
    $lCrp = $lArr['rep'];
    parent::__construct('rep', $aJob, $lCrp);
  }

}