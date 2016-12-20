<?php
class CInc_Job_Typ_Fil_List extends CJob_Fil_List {

  protected $mSrc = 'typ';

  public function __construct($aSrc, $aJobId, $aJob, $aSub = '', $aAge = 'job') {
    $this -> mSrc = $aSrc;
    parent::__construct($this -> mSrc, $aJobId, $aSub, $aAge);
    $this -> checkProject();
  }
}