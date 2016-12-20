<?php
class CInc_Cor_Res_Evetype extends CCor_Res_Plugin {

  protected function refresh($aParam = NULL) {
    $lRet = array();
    $lQry = new CCor_Qry('SELECT * FROM al_eve_types WHERE mand='.MID.' ORDER BY name');
    foreach($lQry as $lRow) {
      $lRet[$lRow -> code] = $lRow;
    }
    return $lRet;
  }

}