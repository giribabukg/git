<?php
class CInc_Job_Pro_Dat extends CJob_Dat {

  public function __construct($aSrc = 'pro') {
    parent::__construct($aSrc);
  }

  protected function doLoad($aId) {
    $lId = intval($aId);
    $this -> mIte = new CCor_TblIte('al_job_pro_'.MID);
    $this -> mIte -> addCnd('id='.esc($lId));
    $this -> mIte -> addCnd('del="N"');
    $this -> mIte -> getIterator();
    if ($lRes = $this -> mIte -> getDat()) {
      $this -> assign($lRes);
      $this -> mJobId = $aId;
      $this -> mJid = $this -> mJobId;
      return TRUE;
    } else {
      return FALSE;
    }
  }

  public function setId($aId) {
    $this -> mJobId = $aId;
  }
}