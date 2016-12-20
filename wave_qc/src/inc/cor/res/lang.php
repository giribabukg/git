<?php
class CInc_Cor_Res_Lang extends CCor_Res_Plugin {

  public function get($aParam = 'en') {
    if (!isset($this -> mRet[$aParam]) OR NULL === $this -> mRet[$aParam]) {
      $this -> mRet[$aParam] = $this -> refresh($aParam);
    } else {
      $this -> resHit();
    }
    return $this -> mRet[$aParam];
  }

  protected function refresh($aParam = 'en') {
    $ret_json = array();

    $lCkey = 'cor_res_lang_'.$aParam;
    if ($lRet = $this -> getCache($lCkey)) {
      return $lRet;
    }
    $lRet = array();
    // Wenn der Login fehlschlägt, ist MID nicht definiert.
    if(defined('MID')) {
      $lSql = 'SELECT * FROM al_sys_lang WHERE mand IN(0,'.MID.') ORDER BY code,mand ASC';
    } else {
      $lSql = 'SELECT * FROM al_sys_lang WHERE mand=0 ORDER BY code';
    }
    $lQry = new CCor_Qry($lSql);
    foreach($lQry as $lRow) {
      if ((defined('MID') AND MID == $lRow['mand']) || $lRow["mand"] === "0") {
        $lRet[$lRow['code']] = $lRow['value_'.$aParam];

        $ret_json[$lRow['code']] = $lRow['value_'.$aParam];
      }
    }
    $this -> setCache($lCkey, $lRet);

    //Write Json Cache to File
    $lfile = fopen("tmp/lang_".$aParam.".js", 'w');
    fwrite($lfile, "var lang = ".Zend_Json::encode($ret_json));
    fclose($lfile);

    return $lRet;
  }
}