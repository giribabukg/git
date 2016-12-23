<?php
class CInc_Cor_Res_Gru extends CCor_Res_Plugin {

  public function get($aParam = NULL) {
    if (NULL === $this -> mRet) {
      $this -> mRet = $this -> refresh($aParam);
    } else {
      incCtr('rch');
    }
    if ('' == $aParam) {  // true bei NULL oder ''
      return $this -> mRet;
    } else {
      $lRet = $this -> mRet;   // alle Gruppen
      $lFil = toArr($aParam);  // z.B.  array('gid' => string '56')
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
    }
  }

  protected function refresh($aParam = NULL) {
    #$lQry = new CCor_Qry('SELECT a.*, max(a.mand) FROM al_gru a WHERE a.mand IN(0,'.MID.') GROUP BY a.code ORDER BY a.name');
    $lQry = new CCor_Qry('SELECT * FROM al_gru WHERE mand IN(0,'.MID.') ORDER BY name');
    foreach($lQry as $lRow) {
#      if(MID == $lRow['mand'])
#        $lRet[$lRow -> id] = $lRow -> toArray();
#      elseif(0 == $lRow['mand'])
        $lRet[$lRow -> id] = $lRow -> toArray();

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

  protected function filterGid($aRet, $aVal) {
    $lRet = array();
    foreach ($aRet as $lId => $lRow) {
      if ($lRow['parent_id'] == $aVal) {
        $lRet[$lId] = $lRow;
      }
    }
    return $lRet;
  }
}