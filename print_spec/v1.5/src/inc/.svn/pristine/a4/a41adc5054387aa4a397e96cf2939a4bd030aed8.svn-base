<?php

class CInc_Fie_Validate_Mod extends CCor_Mod_Table {

  const WAVE_GLOBAL = -2;

  public function __construct() {
    parent::__construct('al_fie_validate');
    $this->addField(fie('id'));
    $this->addField(fie('alias'));
    $this->addField(fie('mand'));
    $this->addField(fie('name'));
    $this->addField(fie('validate_type'));
  }

  public static function loadItem($aId) {
    $lSql = 'SELECT * FROM al_fie_validate WHERE id=' . esc($aId);
    $lQry = new CCor_Qry($lSql);
    if ($lRow = $lQry->getAssoc()) {
      $lRet = $lRow;

      $lOpt = array();
      $lSql = 'SELECT option_name,option_value FROM al_fie_validate_options ';
      $lSql.= 'WHERE validate_id=' . esc($aId).' ';
      $lQry2 = new CCor_Qry($lSql);
      foreach ($lQry2 as $lRow2) {
        $lOpt[$lRow2['option_name']] = $lRow2['option_value'];
      }
      $lRet['options'] = $lOpt;
      return $lRet;
    }
    return false;
  }

  protected function beforePos2t($aNew = false) {
    $lNewAlias = $this->getVal('alias');
    if (empty($lNewAlias)) {
      return;
    }
    if ($aNew || (!$aNew && $this->fieldHasChanged('alias'))) {
      if (!$this->isUniqueAlias($lNewAlias)) {
        $this->msg('Alias must be unique or empty!', mtUser, mlError);
        $this->mCancel = true;
        return;
      }
    }
  }

  public function saveOptions($aId, $aValues, $aIsEdit = true) {
    $lId = intval($aId);

    $lQry = new CCor_Qry();

    if ($aIsEdit) {
      $lSql = 'DELETE FROM al_fie_validate_options WHERE validate_id=' . $lId;
      $lQry->query($lSql);
    }

    $lTyp = $this->getVal('validate_type');
    $lVals = $aValues[$lTyp];

    if (empty($lVals)) {
      return;
    }
    $lBase = 'INSERT INTO al_fie_validate_options SET validate_id=' . $lId . ',';
    foreach ($lVals as $lKey => $lVal) {
      if ('' === $lVal) {
        continue;
      }
      $lSql = $lBase;
      $lSql .= 'option_name=' . esc($lKey) . ',';
      $lSql .= 'option_value=' . esc($lVal);
      $lQry->query($lSql);
    }
  }

  public function isUniqueAlias($aAlias) {
    $sql = 'SELECT COUNT(*) FROM al_fie_validate WHERE alias='.esc($aAlias).' ';
    $num = CCor_Qry::getInt($sql);
    return $num == 0;
  }

  protected function afterDelete($aId) {
    $lSql = 'DELETE FROM al_fie_validate_options WHERE validate_id=' . esc($aId);
    CCor_Qry::exec($lSql);
  }

  protected function afterChange() {
    $lMand = CCor_Res::extract('id', 'id', 'mand');
    foreach ($lMand as $lMid) {
      CCor_Cache::clearStatic('cor_res_validate_'.$lMid);
    }
    CCor_Cache::clearStatic('cor_res_validate_0');
  }

  public static function areWeOnGlobal() {
    return CCor_Cfg::get('validate.global', false);
  }

}
