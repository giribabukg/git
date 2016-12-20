<?php
class CInc_Rig_Form_Edit extends CRig_Form_Base {

  public function __construct($aCode, $aMid) {
    parent::__construct('rig.sedt', lan('right.def'));

    $this -> mCode = $aCode;
    $this -> mMid = $aMid;
    $this -> load();
  }

  protected function load() {
    $lQry = new CCor_Qry('SELECT * FROM al_sys_rig_usr WHERE code="'.$this -> mCode.'" AND mand='.esc($this -> mMid));
    if ($lRow = $lQry -> getAssoc()) {
      $this -> assignVal($lRow);
    } else {
      $this -> msg('Record not found', mtUser, mlError);
    }
  }

}