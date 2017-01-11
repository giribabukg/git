<?php
class CInc_Cor_Res_Fiemap extends CCor_Res_Plugin {

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
        $lRet = $this -> mRet;
        $lFil = toArr($aParam);
        if (!empty($lFil)) {
          foreach($lFil as $lKey => $lVal) {
            $lRet = $this -> filterField($lRet, $lKey, $lVal);
          }
        }
        return $lRet;
      } else {
        return $this -> filterField($this->mRet, 'map', $aParam);
      }
    }
  }

  protected function refresh($aParam = NULL) {
    $lKey = 'cor_res_fiemap_'.MID;
    if ($lRet = $this -> getCache($lKey)) {
      return $lRet;
    }
    $lMand = '0';
    if (defined('MID')) {
      $lMand.=','.MID;
    }
    $lSql = 'SELECT i.*,m.name AS map FROM al_fie_map_items i,al_fie_map_master m ';
    $lSql.= 'WHERE mand IN ('.$lMand.') AND i.map_id=m.id ORDER BY map,alias';
    $lQry = new CCor_Qry($lSql);
    foreach($lQry as $lRow) {
      $lRet[$lRow -> id] = $lRow -> toArray();
    }
    $this -> setCache($lKey, $lRet);
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
}
