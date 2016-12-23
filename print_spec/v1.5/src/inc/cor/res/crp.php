<?php
class CInc_Cor_Res_Crp extends CCor_Res_Plugin {

  public function get($aParam = NULL) {
    if ((!isset($this -> mRet[$aParam])) OR (NULL === $this -> mRet[$aParam])) {
      $this -> mRet[$aParam] = $this -> refresh($aParam);
    } else {
      incCtr('rch');
    }
    return $this -> mRet[$aParam];
  }

  protected function refresh($aParam = NULL) {
    $lPar = intval($aParam);
    $lCkey = 'cor_res_crp_'.MID;
    if (0 < $lPar) {
      $lCkey.= '_'.$lPar;
    }
    if ($lRet = $this -> getCache($lCkey)) {
      return $lRet;
    }
    $lRet = array();
    $lSql = 'SELECT * FROM al_crp_status ';
    $lSql.= 'WHERE mand='.MID;
    if (!empty($lPar)) {
      $lSql.= ' AND crp_id='.$lPar;
    }
    $lSql.= ' AND display > -1';
    $lSql.= ' ORDER BY status';
    $lQry = new CCor_Qry($lSql);
    foreach($lQry as $lRow) {
      $lRet[$lRow -> id] = $lRow -> toArray();
    }
    $this -> setCache($lCkey, $lRet);
    return $lRet;
  }

}