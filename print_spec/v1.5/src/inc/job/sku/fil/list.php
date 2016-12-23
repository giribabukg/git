<?php
class CInc_Job_Sku_Fil_List extends CJob_Fil_List {

  public function __construct($aSKUID, $aJob, $aSub = '', $aAge = 'job') {
    parent::__construct('sku', intval($aSKUID), $aSub, $aAge, FALSE);
  }
}