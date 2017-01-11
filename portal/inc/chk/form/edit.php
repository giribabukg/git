<?php
class CInc_Chk_Form_Edit extends CChk_Form_Base {

  public function __construct($aId) {
    parent::__construct('chk.sedt', lan('chk.edt'));

    $this -> mId = intval($aId);
    $this -> setParam('val[id]', $this -> mId);
    $this -> setParam('old[id]', $this -> mId);
    $this -> load();
  }

  protected function load() {
    $lQry = new CCor_Qry('SELECT * FROM al_chk_master WHERE id='.$this -> mId);
    if ($lRow = $lQry -> getAssoc()) {
      $this -> assignVal($lRow);
    } else {
      $this -> msg('Record not found', mtUser, mlError);
    }
  }
}