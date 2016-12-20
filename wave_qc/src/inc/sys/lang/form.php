<?php
class CInc_Sys_Lang_Form extends CHtm_Form {

  public function __construct($aAct, $aCaption) {
    parent::__construct($aAct, $aCaption);
    $this -> setAtt('class', 'tbl w600');

    $this -> addDef(fie('code', lan('lib.code')));
    $this -> addDef(fie('mand', lan('lib.mand')));
    $this -> mAvailLang = CCor_Res::get('languages');
    foreach ($this -> mAvailLang as $lLang => $lName) {
      $this -> addDef(fie('value_'.$lLang, lan('lan.'.$lLang), 'string', NULL, array('class' => 'inp w400')));
    }

    $this -> setVal('mand', 0); // Default new Item 0 = For all Mandator
  }

  public function load($aCode, $aMand) {
    $lQry = new CCor_Qry('SELECT * FROM al_sys_lang WHERE code="'.addslashes($aCode).'" AND mand='.intval($aMand));
    if ($lRow = $lQry -> getAssoc()) {
      $this -> assignVal($lRow);
    } else {
      $this -> msg('Record not found', mtUser, mlError);
    }
  }


}