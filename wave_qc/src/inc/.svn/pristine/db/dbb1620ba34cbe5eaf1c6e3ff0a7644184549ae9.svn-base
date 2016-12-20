<?php
class CInc_Cor_Res_Languages extends CCor_Res_Plugin {

  public function get($aParam = NULL) {
    if (NULL === $this -> mRet) {
      $this -> mRet = $this -> refresh($aParam);
    } else {
      $this -> resHit();
    }
    return $this -> mRet;
  }

  protected function refresh($aParam = NULL) {
    $lRet = array();
    $lCkey = 'cor_res_languages';
    if ($lRet = $this -> getCache($lCkey)) {
      return $lRet;
    }

    $lQry = new CCor_Qry('SELECT * FROM `al_sys_languages` ORDER BY `code`');
    foreach($lQry as $lRow) {
      if(defined('LAN')) {
        $lRet[$lRow['code']] = $lRow['name_'.LAN];
      } else {
        $lRet[$lRow['code']] = $lRow['name_en'];
      }
    }

    $this -> setCache($lCkey, $lRet);
    return $lRet;
  }

}