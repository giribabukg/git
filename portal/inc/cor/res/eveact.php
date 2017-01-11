<?php
class CInc_Cor_Res_Eveact extends CCor_Res_Plugin {

  protected function refresh($aParam = NULL) {
    $lSQL = 'SELECT *';
    $lSQL.= ' FROM al_eve_act';
    $lSQL.= ' WHERE mand='.MID;
    $lSQL.= ' ORDER BY id;';

    $lRet = array();
    $lQry = new CCor_Qry($lSQL);
    foreach($lQry as $lRow) {
      $lRet[$lRow -> id] = $lRow;
    }

    return $lRet;
  }

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

  protected function filterField($aRet, $aKey, $aValue) {
    $lRet = array();
    foreach ($aRet as $lUid => $lRow) {
      if ($lRow[$aKey] == $aValue) {
        $lRet[$lUid] = $lRow;
      }
    }

    return $lRet;
  }

  protected function filterEve($aKey, $aValue) {
    $lRet = array();
    foreach ($aKey as $lKey => $lValue) {
      if ($lValue['eve_id'] == $aValue) {
        $lRet[$lKey] = $lValue;
      }
    }

    return $lRet;
  }

  protected function filterCond($aKey, $aValue) {
    $lRet = array();
    foreach ($aKey as $lKey => $lValue) {
      if ($lValue['cond_id'] == $aValue) {
        $lRet[$lKey] = $lValue;
      }
    }

    return $lRet;
  }
}