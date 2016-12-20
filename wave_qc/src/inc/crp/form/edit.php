<?php
class CInc_Crp_Form_Edit extends CCrp_Form_Base {

  public function __construct($aId) {
    parent::__construct('crp.sedt', lan('crp.edt'));

    $this -> mId = intval($aId);
    $this -> setParam('val[id]', $this -> mId);
    $this -> setParam('old[id]', $this -> mId);
    $this -> load();
  }

  protected function load() {
    if (0 == MID) {
      $lSql = 'SELECT * FROM al_crp_mastertpl WHERE mand='.MID.' AND id='.$this -> mId;
    } else {
      $lSql = 'SELECT * FROM al_crp_master WHERE mand='.MID.' AND id='.$this -> mId;
    }
    $lQry = new CCor_Qry($lSql);
    if ($lRow = $lQry -> getAssoc()) {
      $this -> assignVal($lRow);
    } else {
      $this -> msg('Record not found', mtUser, mlError);
    }
  }
}