<?php
class CInc_Fie_Map_Form extends CHtm_Form {

  public function __construct($aAct, $aCaption) {
    parent::__construct($aAct, $aCaption);
    $this -> setAtt('class', 'tbl w600');
    $this -> addDef(fie('id',  '', 'hidden'));
    $this -> addDef(fie('name',  'Name'));
  }
  
  public function load($aId) {
    $lQry = new CCor_Qry('SELECT * FROM al_fie_map_master WHERE id='.intval($aId));
    if ($lRow = $lQry -> getAssoc()) {
      $this -> assignVal($lRow);
    } else {
      $this -> msg('Record not found', mtUser, mlError);
    }
  }
}