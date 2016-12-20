<?php
class CInc_Cor_Res_Ddl extends CCor_Res_Plugin {

  public function get($aParam = NULL) {
    if (isset($this -> mRet[$aParam])) {
      $this -> resHit();
      return $this -> mRet[$aParam];
    }
    $this -> mRet[$aParam] = $this -> refresh($aParam);
    return $this -> mRet[$aParam];
  }

  protected function refresh($aParam = NULL) {
    $lCkey = 'cor_res_ddl';
    if ($lRet = $this -> getCache($lCkey)) {
      return $lRet;
    }
    $lRet = array();

    $lSql = 'SELECT f.alias as alias,s.status as status';
    $lSql.= ' FROM al_crp_ddl d';
    $lSql.= ' LEFT JOIN al_fie f ON d.fie_id=f.id';
    $lSql.= ' LEFT JOIN al_crp_status s ON d.status_id=s.id ';
    $lSql.= 'WHERE 1';
    $lSql.= ' AND d.fie_id>0';
    $lSql.= ' AND d.mand='.MID;
    $lSql.= ' AND d.crp_id=';

    $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');
    foreach ($lCrp as $lcode => $lId) {
      $lsql = $lSql.esc($lId);
      $lQry = new CCor_Qry($lsql);
      foreach($lQry as $lRow) {
        $lRet[$lcode][$lRow['status']] = $lRow['alias'];
      }
    }
    # echo '<pre>---ddl.php---';var_dump($lSql,$lRet,'#############');echo '</pre>';

    $this -> setCache($lCkey, $lRet);
    return $lRet;
  }

}