<?php
class CInc_Pixelboxx_Utils extends CCor_Obj {
  
  protected static $mStrucFields;
  protected static $mMetaFields;
  protected static $mMetaMap;
  
  public static function getStructureFields() {
    if (isset(self::$mStrucFields)) {
      return self::$mStrucFields;
    }
    $lMap = CCor_Cfg::get('pixelboxx.map.structure', 'elements.meta');
    $lSql = 'SELECT i.alias FROM al_fie_map_items i,al_fie_map_master m ';
    $lSql.= 'WHERE m.name='.esc($lMap).' AND m.id=i.map_id AND mand='.MID.' ';
    $lSql.= 'AND i.default_value<>""';
    $lSql.= 'ORDER BY i.default_value,i.id';
    
    $lQry = new CCor_Qry($lSql);
    $lArr = array();
    foreach ($lQry as $lRow) {
      $lArr[] = $lRow['alias'];
    }
    self::$mStrucFields = $lArr;
    return self::$mStrucFields;
  }
  
  public static function getMetaFields() {
    if (isset(self::$mMetaFields)) {
      return self::$mMetaFields;
    }
    $lMap = CCor_Cfg::get('pixelboxx.map.structure', 'elements.meta');
    $lSql = 'SELECT i.alias FROM al_fie_map_items i,al_fie_map_master m ';
    $lSql.= 'WHERE m.name='.esc($lMap).' AND m.id=i.map_id AND mand='.MID.' ';
    $lSql.= 'ORDER BY i.id';
  
    $lQry = new CCor_Qry($lSql);
    $lArr = array();
    foreach ($lQry as $lRow) {
      $lArr[] = $lRow['alias'];
    }
    self::$mMetaFields = $lArr;
    return self::$mMetaFields;
  }

  public static function getMetaMap() {
    if (isset(self::$mMetaMap)) {
      return self::$mMetaMap;
    }
    $lMap = CCor_Cfg::get('pixelboxx.map.structure', 'elements.meta');
    $lSql = 'SELECT i.alias,i.native FROM al_fie_map_items i,al_fie_map_master m ';
    $lSql.= 'WHERE m.name='.esc($lMap).' AND m.id=i.map_id AND mand='.MID.' ';
    $lSql.= 'ORDER BY i.default_value,i.id';
  
    $lQry = new CCor_Qry($lSql);
    $lArr = array();
    foreach ($lQry as $lRow) {
      if (empty($lRow['native'])) {
        continue;
      }
      $lArr[$lRow['alias']] = $lRow['native'];
    }
    self::$mMetaMap = $lArr;
    return self::$mMetaMap;
  }
  
  public static function getClient() {
    if (isset(self::$mClient)) {
      return self::$mClient;
    }
    $lClient = new CApi_Pixelboxx_Client();
    $lClient->loadAuthFromConfig();
     
    self::$mClient = $lClient;
    return self::$mClient;
  }
  
  public static function getPrefsFrom($aPrefix) {
    $lUsr = CCor_Usr::getInstance();
    $lPreferences = $lUsr->getPrefObject();
    $lPrefix = (substr($aPrefix,-1) == '.') ? $aPrefix : $aPrefix.'.';
    $lLen = strlen($lPrefix);
    foreach ($lPreferences as $lKey => $lVal) {
      if (substr($lKey,0, $lLen) == $lPrefix) {
        $lPrefKey = substr($lKey, $lLen);
        $lRet[$lPrefKey] = $lVal;
      }
    }
    return $lRet;
  }
  
  public static function getHashes($aKeyValues) {
    if (empty($aKeyValues)) {
      return array();
    }
    $lRet = array();
    $lHash = 0;
    $lFunc = 'crc32';
    foreach ($aKeyValues as $lKey => $lVal) {
      if (empty($lHash)) {
        $lHash = $lFunc($lVal);
      } else {
        $lHash = $lFunc($lHash.' / '.$lVal);
      }
      $lRet[$lKey] = $lHash;
    }
    return $lRet;
  }
  
  
}