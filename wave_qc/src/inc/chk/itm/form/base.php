<?php
class CInc_Chk_Itm_Form_Base extends CHtm_Form {

  public function __construct($aAct, $aCaption, $aCancel = NULL) {
    parent::__construct($aAct, $aCaption, $aCancel);

    $this -> addDef(fie('item_code', lan('lib.code')));

    $this -> mAvailLang = CCor_Res::get('languages');
    foreach ($this -> mAvailLang as $lLang => $lName) {
      $this -> addDef(fie('name_'.$lLang, lan('lib.name').' ('.strtoupper($lLang).')'));
    }

    $lPar = array('res' => 'cond', 'key' => 'id', 'val' => 'name');
    $this -> addDef(fie('cnd_id', lan('lib.condition'), 'resselect', $lPar));
  }

  public function setDomain($aDomain) {
    $this -> setParam('domain', $aDomain);
    $this -> setParam('val[domain]', $aDomain);
    $this -> setParam('old[domain]', $aDomain);
  }
  
  public function setMasterId($aMasterId) {
    $this -> setParam('master_id', $aMasterId);
    $this -> setParam('val[master_id]', $aMasterId);
    $this -> setParam('old[master_id]', $aMasterId);
  }
}