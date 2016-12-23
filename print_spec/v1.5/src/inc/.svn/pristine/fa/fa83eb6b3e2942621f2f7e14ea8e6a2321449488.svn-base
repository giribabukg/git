<?php
class CInc_Cor_Res_Cndmaster extends CCor_Res_Plugin {

  public function get($aParam = NULL) {
    if ((!isset($this -> mRet[$aParam])) OR (NULL === $this -> mRet[$aParam])) {
      $this -> mRet[$aParam] = $this -> refresh($aParam);
    } else {
      incCtr('rch');
    }
    return $this -> mRet[$aParam];
  }

  protected function refresh($aParam = NULL) {
    $lPar = strval($aParam);
    $lCkey = 'cor_res_cndmaster_'.MID;
    if ($lRet = $this -> getCache($lCkey)) {
      return $lRet;
    }
    $lRet = array();
    $lSql = 'SELECT * FROM `al_cnd_master` ';
    $lSql.= 'WHERE `mand`='.MID;

    $lQry = new CCor_Qry($lSql);
    foreach($lQry as $lRow) {
      $lRet[] = $lRow -> toArray();
    }
    #echo '<pre>--refresh----'.get_class().'---';var_dump($lRet,'#############');echo '</pre>';
    $this -> setCache($lCkey, $lRet);
    return $lRet;
  }

}