<?php
class CInc_Job_Tra_Header extends CJob_Header {

  protected $mSrc = 'tra';

  public function __construct($aJob) {
    $lArr = CCor_Res::extract('code', 'id', 'crpmaster');
    $lCrp = $lArr[$this -> mSrc];
    parent::__construct($this -> mSrc, $aJob, $lCrp);
  }

}