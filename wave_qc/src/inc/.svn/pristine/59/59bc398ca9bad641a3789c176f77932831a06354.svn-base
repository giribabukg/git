<?php
class CInc_Ldt_Form extends CHtm_Form {
  
  public function __construct($aAct, $aCaption, $aCancel = NULL) {
    parent::__construct($aAct, $aCaption, $aCancel);
    $this -> addDef(fie('src', 'Source', 'tselect', array('dom' => 'src')));
    $this -> mAvailLang = CCor_Res::get('languages');
    foreach ($this -> mAvailLang as $lLang => $lName) {
      $this -> addDef(fie('name_'.$lLang, lan('lib.name')));
    }

    $this -> addDef(fie('std_val', 'Default (Days)', 'integer'));
  }
  
  public function load($aId) {
    $lId = intval($aId);
    $lSql = 'SELECT * FROM al_ldt_master WHERE id='.$lId;
    $lQry = new CCor_Qry($lSql);
    if ($lRow = $lQry -> getAssoc()) {
      $this -> assignVal($lRow);
      $this -> setParam('val[id]', $lId);
      $this -> setParam('old[id]', $lId);
    } else {
      $this -> msg('Record not found', mtUser, mlError);
    }
  }
}