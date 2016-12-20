<?php
class CInc_Chk_Form_Base extends CHtm_Form {

  public function __construct($aAct, $aCaption, $aCancel = NULL) {
    parent::__construct($aAct, $aCaption, $aCancel);

    $this -> addDef(fie('domain', lan('lib.domain')));

    $this -> mAvailLang = CCor_Res::get('languages');
    foreach ($this -> mAvailLang as $lLang => $lName) {
      $this -> addDef(fie('name_'.$lLang, lan('lib.name').' ('.strtoupper($lLang).')'));
    }

    $lPar = array('res' => 'cond', 'key' => 'id', 'val' => 'name');
    $this -> addDef(fie('cnd_id', lan('lib.condition'), 'resselect', $lPar));
  }
}