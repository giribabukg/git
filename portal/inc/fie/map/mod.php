<?php
class CInc_Fie_Map_Mod extends CCor_Mod_Table {

  public function __construct() {
    parent::__construct('al_fie_map_master');
    $this -> addField(fie('id'));
    $this -> addField(fie('mand'));
    $this -> addField(fie('name'));
    $this -> addField(fie('has_native',  '', 'boolean'));
    $this -> addField(fie('has_default',  '', 'boolean'));
    $this -> addField(fie('has_read_filter',  '', 'boolean'));
    $this -> addField(fie('has_write_filter',  '', 'boolean'));
    $this -> addField(fie('has_validate_rule',  '', 'boolean'));
  }

  public static function clearCache() {
    $lCkey = 'cor_res_fiemap_';
    CCor_Cache::clearStatic($lCkey.'0');

    $lFkey = 'cor_res_fie_';
    $lMand = CCor_Res::extract('id', 'id', 'mand');
    $lLang = CCor_Res::extract('code', 'code', 'languages');

    foreach ($lMand as $lMid) {
      CCor_Cache::clearStatic($lCkey.$lMid);
      foreach ($lLang as $lLan) {
        CCor_Cache::clearStatic($lFkey . $lMid.'_'.$lLan);
      }
    }
  }

  public function doDelete($aId) {
    $lId = intval($aId);
    parent::doDelete($lId);
    $lSql = 'DELETE FROM al_fie_map_items ';
    $lSql.= 'WHERE map_id='.$lId;
    return CCor_Qry::exec($lSql);
  }

  protected function afterChange() {
    self::clearCache();
  }

  public static function sendMap($aId) {
    $lId = intval($aId);
    $lSql = 'SELECT name FROM al_fie_map_master WHERE id='.$lId;
    $lName = CCor_Qry::getStr($lSql);
    if (substr($lName, 0, 5) != 'core.') {
      return false;
    }
    $lRows = self::getMapAsArray($lId);
    $lMsg['command'] = 'fieldmap.update';
    $lMsg['params'] = $lRows;
    $lClient = new CApi_Rabbit_Client();
    $lClient->loadFromConfig();
    $lJson = Zend_Json::encode($lMsg);
    $lClient->sendTopic($lJson, 'wave.masterdata.topic', 'core.fieldmap');
    return true;
  }

  public static function getMapAsArray($aId) {
    $lId = intval($aId);
    $lSql = 'SELECT * FROM al_fie_map_master WHERE id='.$lId;
    $lQry = new CCor_Qry($lSql);
    $lRet = $lQry->getAssoc();
    if ($lRet === false) {
      return false;
    }
    unset ($lRet['id']);

    $lSql = 'SELECT mi.*,fv.alias AS validate FROM al_fie_map_items mi LEFT JOIN al_fie_validate fv ON (mi.validate_rule=fv.id) WHERE mi.map_id='.$lId;
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lRec = $lRow->toArray();
      $lAlias = $lRec['alias'];
      unset($lRec['id']);
      unset($lRec['map_id']);
      unset($lRec['alias']);
      unset($lRec['validate_rule']);
      if (empty($lRec['validate'])) {
        unset($lRec['validate']);
      }
      $lRet['items'][$lAlias] = $lRec;
    }
    return $lRet;
  }

  public static function importMap($aArr, $aIsGlobal = true) {
    if (empty($aArr['name'])) {
      CSvc_Base::addLog('empty name!');
      return false;
    }
    $lMap = $aArr['name'];
    if ($aIsGlobal && substr($lMap, 0,5) != 'core.') {
      CSvc_Base::addLog('no core!');
      return false;
    }

    // get or create the map_master record
    $lFlags = explode(',', 'has_native,has_default,has_read_filter,has_write_filter,has_validate_rule');
    $lSql = 'SELECT id FROM al_fie_map_master WHERE name='.esc($lMap);
    $lMapId = CCor_Qry::getInt($lSql);
    if (empty($lMapId)) {
      $lMod = new self();
      if ($aIsGlobal) {
        $lMod->forceVal('mand', 0);
      } else {
        $lMod->forceVal('mand', MID);
      }
      $lMod->forceVal('name', $lMap);
      foreach ($lFlags as $lFlag) {
        $lMod->forceVal($lFlag, $aArr[$lFlag]);
      }
      if (!$lMod->insert()) {
        return false;
      }
      $lMapId = $lMod->getInsertId();
    } else {
      $lMod = new self();
      $lMod->forceVal('id', $lMapId);
      foreach ($lFlags as $lFlag) {
        $lMod->forceVal($lFlag, $aArr[$lFlag]);
      }
      if (!$lMod->update()) {
        return false;
      }
    }
    if (!$lMapId) {
      return false;
    }

    // now we have a mapid, create the items
    $lCur = array();
    $lSql = 'SELECT mi.*,fv.alias AS validate FROM al_fie_map_items mi LEFT JOIN al_fie_validate fv ON (mi.validate_rule=fv.id) WHERE mi.map_id='.$lMapId;
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lCur[$lRow['alias']] = $lRow->toArray();
      $lCurIds[$lRow['id']] = 1; // used to delete no longer existing items
    }

    // preload the validation rules
    $lMap = array();
    $lSql = 'SELECT id,alias FROM al_fie_validate WHERE mand='.CFie_Validate_Mod::WAVE_GLOBAL;
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lMap[$lRow['alias']] = $lRow['id'];
    }

    // main loop : insert or update map items
    $lItems = $aArr['items'];
    foreach ($lItems as $lAlias => $lRow) {
      $lDat = array();
      $lDat['validate_rule'] = 0;
      if (!empty($lRow['validate'])) {
        $lValidAlias = $lRow['validate'];
        if (isset($lMap[$lValidAlias])) {
          $lDat['validate_rule'] = $lMap[$lValidAlias];
        } else {
          CCor_Msg::add('Unknown validate rule '.$lValidAlias, mtApi, mlError);
        }
      }
      if (!empty($lDat['validate_rule']) && $lMap == 'core.xml') {
        $lSql = 'UPDATE al_fie SET validate_rule='.$lDat['validate_rule'].' WHERE native_core='.esc($lAlias);
        CCor_Qry::exec($lSql);
      }
      if (!isset($lCur[$lAlias])) {
        //insert new item
        //$lDat['map_id'] = $lMapId;
        $lDat['alias'] = $lAlias;
        $lDat['native'] = $lRow['native'];
        $lDat['default_value'] = $lRow['default_value'];
        $lDat['read_filter'] = $lRow['read_filter'];
        $lDat['write_filter'] = $lRow['write_filter'];

        $lRecSql = 'INSERT INTO al_fie_map_items SET ';
        foreach ($lDat as $lKey => $lVal) {
          $lRecSql.= '`'.$lKey.'`='.esc($lVal).',';
        }
        $lRecSql.= 'map_id='.$lMapId;
        $lQry->query($lRecSql);
      } else {
        //update existing
        $lOld = $lCur[$lAlias];
        $lOldId = $lOld['id'];
        unset($lCurIds[$lOldId]); // do not delete this item
        $lUpd = array();
        foreach ($lRow as $lKey => $lVal) {
          if ($lOld[$lKey] != $lVal) {
            if ($lKey == 'validate') {
              $lKey = 'validate_rule';
              $lVal = $lDat['validate_rule'];
            }
            $lUpd[$lKey] = $lVal;
          }
        }
        if (!empty($lUpd)) {
          $lRecSql = 'UPDATE al_fie_map_items SET ';
          foreach ($lUpd as $lKey => $lVal) {
            $lRecSql.= '`'.$lKey.'`='.esc($lVal).',';
          }
          $lRecSql = strip($lRecSql).' WHERE id='.$lOldId;
          $lQry->query($lRecSql);
        }
      }
    }

    // delete any items?
    if (!empty($lCurIds)) {
      $lDel = array_keys($lCurIds);
      $lSql = 'DELETE FROM al_fie_map_items WHERE id IN ('.implode(',', $lDel).')';
      $lQry->query($lSql);
    }
    self::clearCache();
    return true;
  }

}
