<?php
class CInc_Pck_Form_Edit extends CPck_Form_Base {

  public function __construct($aId, $aDom) {
    parent::__construct('pck.sedt', lan('pck.edt'));
    $this -> mId = intval($aId);
    $this -> setParam('val[id]', $this -> mId);
    $this -> setParam('old[id]', $this -> mId);    
    $this -> mDom = $aDom;
    $this -> setDom($aDom);
    $this -> load();
    $this -> setParam('mand', $this -> getVal('mand'));
  }

  protected function load() {
    $lQry = new CCor_Qry('SELECT * FROM al_pck_master WHERE mand IN ( 0,'.MID.') AND del="N" AND domain='.esc($this -> mDom));
    if ($lRow = $lQry -> getAssoc()) {
      $this -> assignVal($lRow);
    } else {
      $this -> msg('Record not found', mtUser, mlError);
    }
  }
}