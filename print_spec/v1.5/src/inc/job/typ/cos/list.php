<?php
class CInc_Job_Typ_Cos_List extends CJob_Cos_List {

  protected $mSrc   = 'typ';

  public function __construct($aSrc, $aJobId, $aJob) {
    $this -> mSrc = $aSrc;
    parent::__construct($this -> mSrc, $aJobId, $aJob);
  }
 
}