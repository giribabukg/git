<?php
class CInc_Cor_Res_Fla extends CCor_Res_Plugin {

  public function get($aParam = NULL) {
    if ((!isset($this -> mRet[$aParam])) OR (NULL === $this -> mRet[$aParam])) {
      $this -> mRet[$aParam] = $this -> refresh($aParam);
    } else {
      incCtr('rch');
    }
    return $this -> mRet[$aParam];
  }

  protected function refresh($aParam = NULL) {
    $lCkey = 'cor_res_fla_'.MID;
    if ($lRet = $this -> getCache($lCkey)) {
      error_log('.....CInc_Cor_Res_Fla.....refresh.....$lRet1.......'.var_export($lRet,true)."\n",3,'logggg.txt');
      return $lRet;
    }
    $lRet = array();
    $lSql = 'SELECT * FROM al_fla WHERE mand='.MID;
    $lSql.= ' ORDER BY name_'.LAN;
    $lQry = new CCor_Qry($lSql);
    foreach($lQry as $lRow) {
      $lRet[$lRow -> id] = $lRow -> toArray();
    }
    $this -> setCache($lCkey, $lRet);
    error_log('.....CInc_Cor_Res_Fla.....refresh.....$lRet2.......'.var_export($lRet,true)."\n",3,'logggg.txt');
    return $lRet;
  }

}