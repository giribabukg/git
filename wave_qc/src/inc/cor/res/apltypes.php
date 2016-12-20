<?php
class CInc_Cor_Res_Apltypes extends CCor_Res_Plugin {

  protected function refresh($aParam = NULL) {
    $lRet = array();
    $lQry = new CCor_Qry('SELECT * FROM al_apl_types WHERE mand='.MID.' ORDER BY name');
    foreach($lQry as $lRow) {
      $lRet[$lRow -> id] = $lRow;
    }
    return $lRet;
  }

}