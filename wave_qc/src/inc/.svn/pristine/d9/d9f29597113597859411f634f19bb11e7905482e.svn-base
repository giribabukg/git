<?php
class CInc_Cor_Res_Cnd extends CCor_Res_Plugin {

  public function get($aParam = NULL) {
    if ((!isset($this -> mRet[$aParam])) OR (NULL === $this -> mRet[$aParam])) {
      $this -> mRet[$aParam] = $this -> refresh($aParam);
    } else {
      incCtr('rch');
    }
    return $this -> mRet[$aParam];
  }

  protected function refresh($aParam = NULL) {
   #echo '<pre>--refresh----'.get_class().'---';var_dump($aParam,'#############');echo '</pre>';
    $lPar = strval($aParam);
    $lCkey = 'cor_res_cnd_'.MID;
    if (!(empty($lPar))) {
      $lCkey.= '_'.$lPar;
    }
    if ($lRet = $this -> getCache($lCkey)) {
      return $lRet;
    }
    $lRet = array();
    $lSql = 'SELECT * FROM `al_cnd` ';
    $lSql.= 'WHERE `mand`='.MID;

    $lQry = new CCor_Qry($lSql);
    foreach($lQry as $lRow) {
      if (empty($lPar)) {
        $lRet[] = $lRow -> toArray();
      } elseif ('uid' == $lPar AND 0 < $lRow -> usr_id) {
        $lRet[$lRow -> usr_id] = $lRow -> toArray();
      } elseif ('gid' == $lPar AND 0 < $lRow -> grp_id)  {
        $lRet[$lRow -> grp_id] = $lRow -> toArray();
      }
    }
    #echo '<pre>--refresh----'.get_class().'---';var_dump($lRet,'#############');echo '</pre>';
    $this -> setCache($lCkey, $lRet);
    return $lRet;
  }

}