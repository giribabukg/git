<?php
class CInc_Prf_Form_Base extends CHtm_Form {

  public function __construct($aAct, $aCaption, $aCancel = NULL) {
    parent::__construct($aAct, $aCaption, $aCancel);

    $this -> addDef(fie('code', 'Code'));

    $lArr[0] = '[global]';
    $lArr[-1] = '['.lan('lib.mand.all').']';
    $lRes = CCor_Res::extract('id', 'name_'.LAN, 'mand');
    $lArr[MID] = $lRes[MID];

    $this -> addDef(fie('mand', lan('lib.mand'), 'select', $lArr));

    $lArr = array('dom' => 'prf');
    $this -> addDef(fie('grp', lan('lib.group'), 'tselect', $lArr));

    $this -> addDef(fie('name_'.LAN, 'Name'));
    $this -> addDef(fie('val',lan('lib.value')));
  }

}