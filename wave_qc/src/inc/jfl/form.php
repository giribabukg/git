<?php
class CInc_Jfl_Form extends CHtm_Form {

  public function __construct($aAct, $aCaption, $aCancel = NULL) {
    parent::__construct($aAct, $aCaption, $aCancel);

    $this -> addDef(fie('code', lan('lib.code')));
    $this -> addDef(fie('val', lan('lib.value')));

    $lArr[0] = '[global]';
    $lArr[-1] = '['.lan('lib.mand.all').']';
    $lRes = CCor_Res::extract('id', 'name_'.LAN, 'mand');
    $lArr[MID] = $lRes[MID];

    $this -> addDef(fie('mand', lan('lib.mand'), 'select', $lArr));

    $this -> mAvailLang = CCor_Res::get('languages');
    foreach ($this -> mAvailLang as $lLang => $lName) {
      $this -> addDef(fie('name_'.$lLang, lan('lib.name').' ('.strtoupper($lLang).')'));
      $this -> addDef(fie('set_'.$lLang, lan('jfl.set').' ('.strtoupper($lLang).')'));
      $this -> addDef(fie('reset_'.$lLang, lan('jfl.reset').' ('.strtoupper($lLang).')'));
    }
  }

  public function load($aId) {
    $lId = intval($aId);
    $lSql = 'SELECT * FROM al_jfl WHERE id='.$lId;
    $lQry = new CCor_Qry($lSql);
    if ($lRow = $lQry -> getDat()) {
      $this -> assignVal($lRow);
      $this -> setParam('old[id]', $lId);
      $this -> setParam('val[id]', $lId);
    } else {
      $this -> msg('Record not found', mtUser, mlError);
    }
  }
}