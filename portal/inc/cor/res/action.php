<?php
class CInc_Cor_Res_Action extends CCor_Res_Plugin {

  public function get($aParam = NULL) {
    if ((!isset($this -> mRet[$aParam])) OR (NULL === $this -> mRet[$aParam])) {
      $this -> mRet[$aParam] = $this -> refresh($aParam);
    } else {
      incCtr('rch');
    }
    return $this -> mRet[$aParam];
  }

  protected function refresh($aParam = NULL) {
    #$lPar = intval($aParam);
    $lCkey = 'cor_res_action_'.MID;
    if ($lRet = $this -> getCache($lCkey)) {
      return $lRet;
    }
    $lRet = array();
    $lSql = 'SELECT * FROM al_eve_act WHERE active=1 AND mand='.MID;
    #if (!empty($lPar)) {
    #  $lSql.= ' AND `eve_id`='.$lPar;
      $lSql.= ' ORDER BY `eve_id`,`pos`';
    #}
    $lQry = new CCor_Qry($lSql);
    foreach($lQry as $lRow) {
      $lRet[ $lRow['eve_id'] ][ $lRow['id'] ] = $lRow -> toArray();
    }
    $this -> setCache($lCkey, $lRet);
    return $lRet;
  }

}