<?php
class CInc_Cor_Res_Chkitems extends CCor_Res_Plugin {

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

  protected function refresh($aParam = NULL) {
    $lQry = new CCor_Qry('SELECT * FROM al_chk_items WHERE mand='.MID);
    foreach ($lQry as $lRow) {
      $lRet[$lRow -> id] = $lRow -> toArray();
    }
    return $lRet;
  }

  protected function filterDomain($aRet, $aVal) {
    $lRet = array();
    foreach ($aRet as $lId => $lRow) {
      if ($lRow['domain'] == $aVal) {
        $lRet[$lId] = $lRow;
      }
    }
    return $lRet;
  }
}