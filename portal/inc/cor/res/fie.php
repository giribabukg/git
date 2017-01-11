<?php
class CInc_Cor_Res_Fie extends CCor_Res_Plugin {

  public function get($aParam = NULL) {
    if (NULL == $this -> mRet) {
      $this -> mRet = $this -> refresh($aParam);
    } else {
      $this -> resHit();
    }
    if (empty($aParam)) {
      return $this -> mRet;
    } else {
      if (is_array($aParam)) {
        $lRet = $this -> mRet;   // all
        $lFil = toArr($aParam);  // array('field' => 'value')
        if (!empty($lFil)) {
          foreach($lFil as $lKey => $lVal) {
            $lFnc = 'filter'.ucfirst($lKey);
            if ($this -> hasMethod($lFnc)) {
              $lRet = $this -> $lFnc($lRet, $lVal);
            } else {
              $lRet = $this -> filterField($lRet, $lKey, $lVal);
            }
          }
        }
        return $lRet;
      } else {
        return $this -> filterBySrc($aParam);
      }
    }
  }

  protected function refresh($aParam = NULL) {
    $lKey = 'cor_res_fie_'.MID.'_'.LAN;
    if ($lRet = $this -> getCache($lKey)) {
      return $lRet;
    }

    $lAliase = array(); // contains later alias => id
    $lSteerAlias = array();
    $lQry = new CCor_Qry('SELECT * FROM al_fie WHERE `mand`='.MID.' ORDER BY name_'.LAN);
    foreach($lQry as $lRow) {
      $lRet[$lRow -> id] = $lRow -> toArray();

      $lAli = $lRet[$lRow -> id]['alias'];
      $lAliase[$lAli] = $lRow -> id;

      $lFeature = toArr($lRet[$lRow -> id]['feature']);
      if (!empty($lFeature)) {
        if(isset($lFeature['IsColor'])) {
          $lRet[$lRow -> id]['IsColor'] = TRUE;
          unset($lFeature['IsColor']);
        } else {
          $lRet[$lRow -> id]['IsColor'] = FALSE;
        }

        if(isset($lFeature['IsImage'])) {
          $lRet[$lRow -> id]['IsImage'] = TRUE;
          unset($lFeature['IsImage']);
        } else {
          $lRet[$lRow -> id]['IsImage'] = FALSE;
        }

        foreach ($lFeature as $lKey => $lVal) {
          $lRet[$lRow -> id][$lKey] = $lVal;
          if ('SteerAlias' == $lKey) {
            $lTemp = explode(",",$lVal);
            foreach ($lTemp as $lTempVal){
              $lSteerAlias[$lTempVal] = $lAli;
            }

          }
        }
      }
    }
    foreach ($lSteerAlias as $lKey => $lVal) {
      $lId = $lAliase[$lKey];
      $lRet[$lId]['SteeredBy'] = $lVal;
    }
    $this -> setCache($lKey, $lRet);
    return $lRet;
  }

  protected function filterBySrc($aSrc) {
    $lSrc = explode(',', $aSrc);
    $lRet = array();
    foreach ($this -> mRet as $lKey => $lVal) {
      if (in_array($lVal['src'], $lSrc)) {
        $lRet[$lKey] = $lVal;
      }
    }
    return $lRet;
  }

  protected function filterField($aRet, $aKey, $aVal) {
    $lRet = array();
    foreach ($aRet as $lId => $lRow) {
      if (isset($lRow[$aKey]) AND $lRow[$aKey] == $aVal) {
        $lRet[$lId] = $lRow;
      }
    }
    return $lRet;
  }

  protected function filterFlags($aHaystack, $aNeedle) {
    $lResult = array();
    foreach ($aHaystack as $lKey => $lValue) {
      if (bitset($lValue['flags'], $aNeedle)) {
        $lResult[$lKey] = $lValue;
      }
    }
    return $lResult;
  }
}