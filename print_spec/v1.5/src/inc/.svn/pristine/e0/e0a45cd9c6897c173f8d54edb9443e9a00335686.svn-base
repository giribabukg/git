<?php
class CInc_Job_Art_Header extends CJob_Header {

  public function __construct($aJob) {
    $lArr = CCor_Res::extract('code', 'id', 'crpmaster');
    $lCrp = $lArr['art'];
    parent::__construct('art', $aJob, $lCrp);
  }

}