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
    $this->mHasChanged = false;
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

  protected function beforePost($aNew = false) {
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

  public function saveOptions($aId, $aValues, $aIsEdit = true, $aOldValues = null) {
    $lId = intval($aId);

    $lQry = new CCor_Qry();

    $lTyp = $this->getVal('validate_type');
    $lVals = $aValues[$lTyp];

    // if editing, delete old options and check if we have a change
    if ($aIsEdit) {
      if (!is_null($aOldValues)) {
        $lOld = $aOldValues[$lTyp];
        $lDiff = array_diff_assoc($lVals, $lOld);
        if (empty($lDiff)) {
          return; // nothing to do
        }
      }
      $this->dbg('Options changed');
      $this->mHasChanged = true;
      $lSql = 'DELETE FROM al_fie_validate_options WHERE validate_id=' . $lId;
      $lQry->query($lSql);
    }
    // insert the values
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
    $this->mHasChanged = true;
  }

  public function checkChanged() {
    if (!$this->mHasChanged) {
      return;
    }
    CCor_Cache::clearStatic('cor_res_validate_0');
    $lMand = CCor_Res::extract('id', 'id', 'mand');
    foreach ($lMand as $lMid) {
      CCor_Cache::clearStatic('cor_res_validate_'.$lMid);
    }

    if (CFie_Validate_Mod::areWeOnGlobal()) {
      $this->sendUpdate();
    }
  }

  protected function sendUpdate() {
    $lRows = CFie_Validate_Mod::getGlobalsAsArray();
    $lMsg['command'] = 'validate.rules.update';
    $lMsg['params'] = $lRows;
    $lClient = new CApi_Rabbit_Client();
    $lClient->loadFromConfig();
    $lJson = Zend_Json::encode($lMsg);
    $lClient->sendTopic($lJson, 'wave.masterdata.topic', 'core.validate');
  }

  public static function areWeOnGlobal() {
    return CCor_Cfg::get('validate.global', false);
  }

  public static function getGlobalsAsArray() {
    $lRet = array();
    $lSql = 'SELECT * FROM al_fie_validate WHERE mand='.self::WAVE_GLOBAL;
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lAlias = $lRow['alias'];
      if (empty($lAlias)) {
        continue;
      }
      $lId = $lRow['id'];
      $lCopy = $lRow->toArray();
      unset($lCopy['id']);
      unset($lCopy['mand']);
      $lRet[$lId] = $lCopy;
    }
    if (empty($lRet)) {
      return $lRet;
    }
    $lSql = 'SELECT * FROM al_fie_validate_options WHERE validate_id IN ('.implode(',', array_keys($lRet)).')';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lRet[$lRow['validate_id']]['options'][$lRow['option_name']] = $lRow['option_value'];
    }
    return array_values($lRet);
  }

  public static function getIdByAlias($aAlias) {
    $lSql = 'SELECT id FROM al_fie_validate WHERE alias='.esc($aAlias);
    return CCor_Qry::getInt($lSql);
  }

  public static function updateGlobalRow($aRow) {
    $lOpt = isset($aRow['options']) ? $aRow['options'] : null;
    unset($aRow['options']);
    $lAlias = (isset($aRow['alias'])) ? $aRow['alias'] : null;
    if (empty($lAlias)) {
      return false;
    }
    $lId = self::getIdByAlias($lAlias);

    $lMod = new CFie_Validate_Mod();
    $lMod->setVal('mand', self::WAVE_GLOBAL);
    foreach ($aRow as $lKey => $lVal) {
      //$lMod->setOld($lKey, $lVal);
      if ($lKey == 'alias') {
        $lMod->setVal($lKey, $lVal);
      } else {
        $lMod->forceVal($lKey, $lVal);
      }
    }
    if ($lId) {
      $lMod->forceVal('id', $lId);
      if ($lMod->update()) {
        $lValues[$aRow['validate_type']] = $lOpt;
        $lMod->saveOptions($lId, $lValues, true);
      }
    } else {
      if  ($lMod->insert()) {
        $lId = $lMod->getInsertId();
        $lValues[$aRow['validate_type']] = $lOpt;
        $lMod->saveOptions($lId, $lValues, false);
      } else {
        return false;
      }
    }
    return $lId;
  }

  public static function updateAllGlobalRows($aRows) {
    $lSuccess = true;
    $lSave = array();
    foreach ($aRows as $lRow) {
      $lId = CFie_Validate_Mod::updateGlobalRow($lRow);
      if ($lId) {
        $lSave[] = $lId;
      } else {
        $lSuccess = false;
      }
    }
    if ($lSuccess) {
      $lSql = 'SELECT id FROM al_fie_validate WHERE mand=' . self::WAVE_GLOBAL;
      $lQry = new CCor_Qry($lSql);
      $lMod = new CFie_Validate_Mod();
      foreach ($lQry as $lRow) {
        if (!in_array($lRow['id'], $lSave)) {
          $lMod->delete($lRow['id']);
        }
      }
    }
    return $lSuccess;
  }

}
