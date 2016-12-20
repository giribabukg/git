<?php
class CInc_Rig_Form_Base extends CHtm_Form {

  public function __construct($aAct, $aCaption, $aCancel = NULL) {
    parent::__construct($aAct, $aCaption, $aCancel);

    $lArr[0] = '[global]';
    $lArr[-1] = '['.lan('lib.mand.all').']';
 #   $lArr+= CCor_Res::extract('id', 'name_'.LAN, 'mand');
    $lRes = CCor_Res::extract('id', 'name_'.LAN, 'mand');
    if (0 < MID) {
      $lArr[MID] = $lRes[MID];
    }
    $this -> addDef(fie('mand', lan('lib.mand'), 'select', $lArr));
    $lArr = array('dom' => 'rgr');
    $this -> addDef(fie('grp', lan('lib.group'), 'tselect', $lArr));
    $this -> addDef(fie('code', 'Code'));
    $this -> mAvailLang = CCor_Res::get('languages');
    foreach ($this -> mAvailLang as $lLang => $lName) {
      $this -> addDef(fie('name_'.$lLang, lan('lib.name').' ('.lan('lan.'.$lLang).')'));
    }

    $lArr = array('dom' => 'pri');
    $this -> addDef(fie('level', 'Flags', 'bitset', $lArr));

    $this -> setVal('level', 15);
    $this -> addDef(fie('desc_de', lan('lib.description').' (DE)', 'memo'));
    $this -> addDef(fie('desc_en', lan('lib.description').' (EN)', 'memo'));
  }
}