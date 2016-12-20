<?php
class CInc_Cor_Res_Mem extends CCor_Res_Plugin {

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
    $lCkey = 'cor_res_mem_'.MID;
    if (!(empty($lPar))) {
      $lCkey.= '_'.$lPar;
    }
    if ($lRet = $this -> getCache($lCkey)) {
      return $lRet;
    }
    $lRet = array();
    $lSql = 'SELECT * FROM `al_usr_mem`';
    $lSql.= ' WHERE `mand`='.MID;
    $lSql.= ' ORDER BY `uid` ASC';

    $lQry = new CCor_Qry($lSql);
    foreach($lQry as $lRow) {
      if (empty($lPar)) {
        $lRet[] = $lRow -> toArray();
      } elseif ('uid' == $lPar) {
        if (!isset($lRet[$lRow -> uid])) {
          $lRet[$lRow -> uid] = array();
        }
        $lRet[$lRow -> uid][] = $lRow -> gid;
      } else { //if ('gid' == $lPar)
        if (!isset($lRet[$lRow -> gid])) {
          $lRet[$lRow -> gid] = array();
        }
        $lRet[$lRow -> gid][] = $lRow -> uid;
      }
    }
    #echo '<pre>--refresh----'.get_class().'---';var_dump($lRet,'#############');echo '</pre>';
    $this -> setCache($lCkey, $lRet);
    return $lRet;
  }

}