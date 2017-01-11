<?php
class CInc_Cor_Res_Usr extends CCor_Res_Plugin {


  public function get($aParam = NULL) {
    if (NULL === $this -> mRet) {
      $this -> mRet = $this -> refresh();
    } else {
      $this -> resHit();
    }
    if ('' == $aParam) {  // true bei NULL oder ''
      return $this -> mRet;
    } else {
      $lRet = $this -> mRet;
      $lFil = toArr($aParam);
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
    if ($lRet = $this -> getCache('cor_res_usr')) {
      return $lRet;
    }
    $lRet = array();
    $lMid = (defined('MID')) ? '0,'.MID : '0';
    $lQry = new CCor_Qry('SELECT * FROM al_usr u, al_usr_mand m WHERE u.id = m.uid AND m.mand IN('.$lMid.') AND del="N" GROUP BY u.id');
    foreach($lQry as $lRow) {
      $lRow = $lRow -> toArray();
      $lRow['fullname'] = cat($lRow['lastname'], $lRow['firstname'], ', ');
      $lRow['first_lastname'] = cat($lRow['firstname'], $lRow['lastname']);
      $lDep = (empty($lRow['department'])) ? '' : '('.$lRow['department'].')';
      $lRow['departm_fullname'] = cat($lDep, $lRow['fullname']);
      $lRet[$lRow['id']] = $lRow;
    }
    $this -> setCache('cor_res_usr', $lRet);
    return $lRet;
  }

  protected function filterGru($aRet, $aGrp) {
    $lGrps = $lFie = explode(',', $aGrp);
    $lRet = array();
    if (!isset($this -> mMem)) {
      $this -> mMem = array();
      $lSql = 'SELECT gid,uid FROM al_usr_mem WHERE mand IN (0';
      if (defined('MID')) {
        $lSql.= ','.MID;
      }
      $lSql.= ')';
      $lQry = new CCor_Qry($lSql);
      while ($lRow = $lQry->getAssoc()) {
        $this -> mMem[$lRow['gid']][] = $lRow['uid'];
      }
    }
    $lMem = array();
    foreach ($lGrps as $lGrp) {
      $lMem = (isset($this -> mMem[$lGrp])) ? array_merge($lMem, $this -> mMem[$lGrp]) : $lMem;
    }
    foreach ($lMem as $lUid) {
      if (isset($aRet[$lUid])) {
        $lRet[$lUid] = $aRet[$lUid];
      }
    }
    return $lRet;
  }

  protected function filterField($aRet, $aKey, $aVal) {
    $lRet = array();
    foreach ($aRet as $lUid => $lRow) {
      if ($lRow[$aKey] == $aVal) {
        $lRet[$lUid] = $lRow;
      }
    }
    return $lRet;
  }
}