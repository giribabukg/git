<?php
class CInc_Cor_Res_Rights extends CCor_Res_Plugin {
 
  protected function refresh($aParam = NULL) {
    $lMid = (defined('MID')) ? MID : 0;
    $lCkey = 'cor_res_rights';
    if ($lRet = $this -> getCache($lCkey)) {
      return $lRet;
    }
    
    $lRet = array();
    $lSql = 'SELECT * FROM al_sys_rig_usr';
    $lQry = new CCor_Qry($lSql);
    foreach($lQry as $lRow) {
      $lRet[$lRow -> code] = $lRow;
    }
    $this->setCache($lCkey, $lRet);
    return $lRet;
  }

}