<?php
class CInc_Prf_Form_Edit extends CPrf_Form_Base {
  
  public function __construct($aCode, $aMid) {
    parent::__construct('prf.sedt', lan('lib.editpreference'));

    $this -> mCode = $aCode;
    $this -> mMid = $aMid;
    $this -> load();
  }
  
  protected function load() {
    $lQry = new CCor_Qry('SELECT * FROM al_sys_pref WHERE code="'.addslashes($this -> mCode).'" AND mand='.esc($this -> mMid));
    if ($lRow = $lQry -> getAssoc()) {
      $this -> assignVal($lRow);
    } else {
      $this -> msg('Record not found', mtUser, mlError);
    }
  }
}