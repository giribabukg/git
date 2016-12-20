<?php
class CInc_Pck_Col_Form_Edit extends CPck_Col_Form_Base {

  public function __construct($aId, $aDom) {
    parent::__construct('pck-col.sedt', $aDom, lan('pck-col.edt'), 'pck-col&dom='.$aDom);
    $this -> mId = intval($aId);
    $this -> setParam('val[id]', $this -> mId);
    $this -> setParam('old[id]', $this -> mId);
    $this -> setDom($aDom);
    $this -> load();
    $this -> setParam('mand', $this -> getVal('mand'));
  }

  protected function load() {
    $lQry = new CCor_Qry('SELECT * FROM al_pck_columns WHERE mand IN(0,'.MID.') AND id='.$this -> mId);
    if ($lRow = $lQry -> getAssoc()) {
      $this -> assignVal($lRow);
    } else {
      $this -> msg('Record not found', mtUser, mlError);
    }
  }

}