<?php
class CInc_Job_Sku_Dat extends CJob_Dat {

  public function __construct() {
    parent::__construct('sku');
  }

  protected function doLoad($aSKUID) {
    $lSKUID = $aSKUID;
    $lQry = new CCor_Qry('SELECT * FROM al_job_sku_'.intval(MID).' WHERE id='.esc($lSKUID));
    if ($lRow = $lQry -> getDat()) {
      $this -> assign($lRow);
      $this -> mJobId = $lSKUID;
      $this -> mJid = $lSKUID;
    } else {
      return FALSE;
    }
  }

}