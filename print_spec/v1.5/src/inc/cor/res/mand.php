<?php
class CInc_Cor_Res_Mand extends CCor_Res_Plugin {

  protected function refresh($aParam = NULL) {
    $lRet = array();
    $lQry = new CCor_Qry('SELECT * FROM al_sys_mand WHERE id>0 ORDER BY name_'.LAN);
    foreach($lQry as $lRow) {
      $lRet[$lRow -> id] = $lRow;
    }
    return $lRet;
  }

}