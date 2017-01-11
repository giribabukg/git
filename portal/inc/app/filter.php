<?php
class CInc_App_Filter extends CCor_Obj {


  public static function getTypes() {
    $lRet = array();
    $lRet[] = 'trim';
    $lRet[] = 'rtrim';
    $lRet[] = 'ltrim';
    $lRet[] = 'numeric';
    $lRet[] = 'strip_tags';
    $lRet[] = 'strip_linefeeds';
    $lRet[] = 'strip_whitespace';
    $lRet[] = 'strtoupper';
    $lRet[] = 'strtolower';
    $lRet[] = 'entity_decode';
    $lRet[] = 'entity_encode';
    $lRet[] = 'group_to_partnerid';
    $lRet[] = 'partnerid_to_group';
    $lRet[] = 'date';
    $lRet[] = 'datetime';
    $lRet[] = 'core_date';
    return $lRet;
  }

  protected static $mPreferredDateFormats = array(
      'm/d/Y','d/m/Y','d.m.Y','Y-m-d','Ymd'
  );

  protected static $mPreferredDateTimeFormats = array(
      'm/d/Y H:i:s','d/m/Y H:i:s','d.m.Y H:i:s','Y-m-d H:i:s'
  );

  protected static $mErrors = array();

  public static function filter($aValue, $aFilter = '') {
    self::$mErrors = array();
    if (empty($aFilter)) {
      return $aValue;
    }
    if (!is_scalar($aValue)) {
      CCor_Msg::add('Invalid type '.gettype($aValue).' in filter '.$aFilter, mtApi, mlError);
      return $aValue;
    }
    $lFunc = 'filter'.$aFilter;
    $lHas = method_exists('CApp_Filter', $lFunc);
    if ($lHas) {
      return self::$lFunc($aValue);
    } else {
      CCor_Msg::add('Unknown filter '.$aFilter, mtApi, mlError);
      return $aValue;
    }
  }

  protected static function addError($aMsg) {
    CCor_Msg::add($aMsg, mtUser, mlError);
    self::$mErrors[] = $aMsg;
  }

  public static function getErrors() {
    return self::$mErrors;
  }

  public static function hasErrors() {
    return !empty(self::$mErrors);
  }

  public static function filterTrim($aValue) {
    return trim($aValue);
  }

  public static function filterRtrim($aValue) {
    return rtrim($aValue);
  }

  public static function filterLtrim($aValue) {
    return ltrim($aValue);
  }

  public static function filterStrip_tags($aValue) {
    return strip_tags($aValue);
  }

  public static function filterStrip_linefeeds($aValue) {
    return strtr($aValue, array(CR => '', LF => ''));
  }

  public static function filterStrip_whitespace($aValue) {
    return preg_replace('/\s+/', '', $aValue);
  }

  public static function filterStrtoupper($aValue) {
    return strtoupper($aValue);
  }

  public static function filterStrtolower($aValue) {
    return strtolower($aValue);
  }

  public static function filterEntity_decode($aValue) {
    return html_entity_decode($aValue);
  }

  public static function filterEntity_encode($aValue) {
    return htmlentities($aValue);
  }

  public static function filterGroup_to_partnerid($aValue) {
    if (empty($aValue)) return $aValue;
    $lSql = 'SELECT kundenid FROM al_gru WHERE id='.intval($aValue);
    $lVal = CCor_Qry::getStr($lSql);
    if (false === $lVal) {
      self::addError('Unknown GroupID '.$aValue);
      $lVal = null;
    }
    return $lVal;
  }

  public static function filterPartnerid_to_group($aValue) {
    if (empty($aValue)) return $aValue;
    $lSql = 'SELECT id  FROM al_gru WHERE kundenid='.esc($aValue);
    $lVal = CCor_Qry::getInt($lSql);
    if (false === $lVal) {
      self::addError('Unknown PartnerID '.$aValue);
      $lVal = null;
    }
    return $lVal;
  }

  public static function filterDate($aValue) {
    $lVal = trim($aValue);
    if (is_numeric($aValue)) {
      $lNum = intval((float)$lVal); // necessary when we get 2.0160426000000000E+07
      if ($lNum == 0) {
        return ''; // sometimes, we receive '00000000'
      }
      $lNumStr = (string)$lNum;
      if (strlen($lNumStr) == 8) {
        return substr($lNumStr, 0, 4).'-'.substr($lNumStr, 4, 2).'-'.substr($lNumStr, 6, 2);
      }
      return date('Y-m-d', $lVal); // assuming timestamp
    }
    $lFmtArr = self::$mPreferredDateFormats;
    foreach ($lFmtArr as $lFmt) {
      $lCur = substr($lVal, 0, 7 + strlen($lFmt));
      $lDt = date_create_from_format($lFmt, $lCur);
      if ($lDt) {
        return $lDt->format('Y-m-d');
      }
    }
    self::addError('Unknown date format '.$aValue);
    return $aValue;
  }

  public static function filterDatetime($aValue) {
    $lVal = trim($aValue);
    if (is_numeric($lVal)) {
      $lNum = intval((float)$lVal); // necessary when we get 2.0160426000000000E+07
      if ($lNum == 0) {
        return '';
      }
      $lNumStr = (string)$lNum;
      if (strlen($lNumStr) == 14) {
        $lDate = substr($lNumStr, 0, 4).'-'.substr($lNumStr, 4, 2).'-'.substr($lNumStr, 6, 2);
        $lTime = substr($lNumStr, 8, 2).':'.substr($lNumStr, 10, 2).':'.substr($lNumStr, 12, 2);
        return $lDate.' '.$lTime;
      }
      return date('Y-m-d H:i:s', $lVal); // assuming timestamp
    }
    $lFmtArr = self::$mPreferredDateTimeFormats;
    foreach ($lFmtArr as $lFmt) {
      $lDt = date_create_from_format($lFmt, $lVal);
      if ($lDt) {
        return $lDt->format('Y-m-d H:i:s');
      }
    }
    self::addError('Unknown datetime format '.$aValue);
    return $aValue;
  }

  public static function filterCore_date($aValue) {
    $lVal = trim($aValue);
    $lDat = new CCor_Date($lVal);
    return $lDat->getFmt('Ymd');
  }

}
