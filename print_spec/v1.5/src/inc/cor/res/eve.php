<?php
class CInc_Cor_Res_Eve extends CCor_Res_Plugin {
  
  protected function refresh($aParam = NULL) {
    $lRet = array();
    $lQry = new CCor_Qry('SELECT * FROM al_eve WHERE mand='.MID.' ORDER BY name_'.LAN);
    foreach($lQry as $lRow) {
      $lRet[$lRow -> id] = $lRow;
    }
    return $lRet;
  }
  
}