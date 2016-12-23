<?php
class CInc_Eve_Form extends CHtm_Form {

  public function __construct($aAct, $aCaption, $aCancel = NULL) {
    parent::__construct($aAct, $aCaption, $aCancel);

    $this -> addDef(fie('mand', '', 'hidden'));
    $this -> addDef(fie('typ', lan('lib.type'), 'resselect', array('res' => 'evetype', 'key' => 'code', 'val' => 'name')));
    $this -> mAvailLang = CCor_Res::get('languages');
    foreach ($this -> mAvailLang as $lLang => $lName) {
      $this -> addDef(fie('name_'.$lLang, lan('lib.name').' '.lan('lan.'.$lLang)));
    }

    $this -> setVal('mand', MID);

    $this->mJobFields = CCor_Res::getByKey('alias', 'fie');
  }

  public function load($aId) {
    $lId = intval($aId);
    $lSql = 'SELECT * FROM al_eve WHERE mand='.MID.' AND id='.$lId;
    $lQry = new CCor_Qry($lSql);
    if ($lRow = $lQry -> getDat()) {
      $this -> assignVal($lRow);
      $this -> setParam('old[id]', $lId);
      $this -> setParam('val[id]', $lId);

      if (!empty($lRow['typ'])) {
        $this->addDef(fie('typ', '', 'hidden'));
        $this->loadTyp($lRow['typ'], $lId);
      }
    } else {
      $this -> msg('Record not found', mtUser, mlError);
    }
  }

  public function loadTyp($aType, $aEveId = NULL) {
    $lSql = 'SELECT fields FROM al_eve_types WHERE code='.esc($aType).' AND mand='.MID;
    $lQry = new CCor_Qry($lSql);
    if ($lRow = $lQry -> getDat()) {
      if (empty($lRow['fields'])) return false;
      $lArr = explode(',', $lRow['fields']);
      foreach ($lArr as $lAlias) {
        if (isset($this->mJobFields[$lAlias])) {
          $lField = $this->mJobFields[$lAlias];
          $lField['alias'] = 'info_'.$lAlias;
          $lFields[] = $lField;
        }
      }
      if (!empty($lFields)) {
        $this->addDef(fie('info_prefix', 'Name'));
        $this->addDef(fie('name', '', 'hidden'));
        foreach ($lFields as $lField) {
          $this->addDef($lField);
        }
      }
      if (!is_null($aEveId)) {
        $lSql = 'SELECT alias,val FROM al_eve_infos WHERE eve_id='.intval($aEveId);
        $lQry ->query($lSql);
        foreach ($lQry as $lRow) {
          $this->setVal('info_'.$lRow['alias'], $lRow['val']);
        }
      }
    }
    return (!empty($lFields));
  }

}