<?php
class CCrp_Sta_Flag_Form_Edit extends CCrp_Sta_Stp_Form_Base {

  public function __construct($aId, $aCid) {
    parent::__construct('crp-sta.sedtstp', lan('crp-stp.edt'), $aCid);
    $this -> mId = intval($aId);
    $this -> setParam('val[id]', $this -> mId);
    $this -> setParam('old[id]', $this -> mId);
    $this -> load();
  }

  protected function load() {
    $lQry = new CCor_Qry('SELECT * FROM al_crp_step WHERE mand='.MID.' AND id='.$this -> mId);
    if ($lRow = $lQry -> getAssoc()) {
      $this -> assignVal($lRow);
    } else {
      $this -> msg('Step record not found', mtUser, mlError);
    }
  }
}