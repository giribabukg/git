<?php
class CInc_Crp_Sta_Form_Edit extends CCrp_Sta_Form_Base {

  public function __construct($aId, $aCid) {
    parent::__construct('crp-sta.sedt', lan('crp-sta.edt'), $aCid);
    $this -> mId = intval($aId);
    $this -> setParam('val[id]', $this -> mId);
    $this -> setParam('old[id]', $this -> mId);
    $this -> setAtt('class', 'tbl w500');
    $this -> load();
  }

  protected function load() {
    $lQry = new CCor_Qry('SELECT * FROM al_crp_status WHERE mand='.MID.' AND id='.$this -> mId);
    if ($lRow = $lQry -> getAssoc()) {
      $this -> assignVal($lRow);
    } else {
      $this -> msg('Status record not found', mtUser, mlError);
    }
  }
}