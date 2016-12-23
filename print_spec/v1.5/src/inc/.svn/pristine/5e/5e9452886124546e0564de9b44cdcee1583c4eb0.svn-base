<?php
class CInc_Cor_Res_Jfl extends CCor_Res_Plugin {

  protected function refresh($aParam = NULL) {
    $lRet = array();
    $lQry = new CCor_Qry('SELECT * FROM al_jfl ORDER BY name_'.LAN);
    foreach($lQry as $lRow) {
      $lRet[$lRow['val']] = $lRow;
    }
    return $lRet;
  }

}