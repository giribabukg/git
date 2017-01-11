<?php
class CInc_Cor_Res_Htbmaster extends CCor_Res_Plugin {

  public function get($aParam = NULL) {
    if (isset($this -> mRet[$aParam])) {
      $this -> resHit();
      return $this -> mRet[$aParam];
    }
    $this -> mRet[$aParam] = $this -> refresh($aParam);
    return $this -> mRet[$aParam];
  }

  protected function refresh($aParam = NULL) {
    $lCkey = 'cor_res_htbmaster';
    if ($lRet = $this -> getCache($lCkey)) {
      return $lRet;
    }

    $lRet = array();
    $lQry = new CCor_Qry('SELECT * FROM al_htb_master ORDER BY description');
    foreach($lQry as $lRow) {
      $lRet[$lRow['domain']] = $lRow -> toArray();
    }
    $this -> setCache($lCkey, $lRet);
    return $lRet;
  }

}