<?php
class CInc_Cor_Res_Cond extends CCor_Res_Plugin {

  public function get($aParam = NULL) {
    if ((!isset($this -> mRet[$aParam])) OR (NULL === $this -> mRet[$aParam])) {
      $this -> mRet[$aParam] = $this -> refresh($aParam);
    } else {
      incCtr('rch');
    }
    return $this -> mRet[$aParam];
  }

  protected function refresh($aParam = NULL) {
    $lCacheKey = 'cor_res_cond_'.MID;
    if ($lRet = $this -> getCache($lCacheKey)) {
      return $lRet;
    }

    $lRet = array();
    $lSql = 'SELECT * FROM al_cond WHERE mand='.MID;
    $lSql.= ' ORDER BY name';
    $lQry = new CCor_Qry($lSql);
    foreach($lQry as $lRow) {
      $lRet[$lRow['id']] = $lRow -> toArray();
    }
    $this -> setCache($lCacheKey, $lRet);
    return $lRet;
  }
}