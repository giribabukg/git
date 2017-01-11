<?php
class CInc_Cor_Res_Syspref extends CCor_Res_Plugin {

  public function get($aParam = NULL) {
    if (NULL === $this -> mRet) {
      $this -> mRet = $this -> refresh($aParam);
    } else {
      incCtr('rch');
    }

    if ('' == $aParam) {
      return $this -> mRet;
    } else {
      $lRet = $this -> mRet;
      $lFil = toArr($aParam);
      if (!empty($lFil)) {
        foreach ($lFil as $lKey => $lVal) {
          $lFnc = 'filter'.ucfirst($lKey);
          if ($this -> hasMethod($lFnc)) {
            $lRet = $this -> $lFnc($lRet, $lVal);
          } else {
            $lRet = $this -> filterField($lRet, $lKey, $lVal);
          }
        }
      }

      return $lRet;
    }
  }

  /* Const 'MID_PREF' will be defined in login manager.
   * If there are more than one mandator, some prefs need to be stored 
   * separately for each mandator (e.g. the columns in the job view).
   *  
   * If a pref setting exists for both mand 0 and the current mand, the setting
   * for the current mand will be used
   */
  protected function refresh($aParam = NULL) {
    $lRet = array();

    $lSql = 'SELECT * FROM al_sys_pref';
    $lSql.= ' WHERE mand IN (0';
    if (defined('MID_PREF')) {
      $lSql .= ','.MID_PREF;
    } elseif (defined('MID')) {
      $lSql .= ','.MID;
    }
    $lSql .= ') ORDER BY mand,code;';

    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lRet[$lRow -> code] = $lRow;
    }

    return $lRet;
  }

  protected function filterCode($aHaystack, $aNeedle) {
    $lRet = array();
    foreach ($aHaystack as $lKey => $lValue) {
      if (wildcard($aNeedle, $lValue['code'])) {
        $lRet[$lKey] = $lValue;
      }
    }
    return $lRet;
  }

  protected function filterMand($aHaystack, $aNeedle) {
    $lRet = array();
    foreach ($aHaystack as $lKey => $lValue) {
      if ($lValue['mand'] == $aNeedle) {
        $lRet[$lKey] = $lValue;
      }
    }
    return $lRet;
  }
}