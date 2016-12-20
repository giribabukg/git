<?php
class CInc_Job_Com_Header extends CJob_Header {

  protected $mSrc = 'com';

  public function __construct($aJob) {
    $lArr = CCor_Res::extract('code', 'id', 'crpmaster');
    $lCrp = $lArr[$this -> mSrc];
    parent::__construct($this -> mSrc, $aJob, $lCrp);
  }

}