<?php
class CInc_Cor_Res_Tpl extends CCor_Res_Plugin {

  protected function refresh($aParam = NULL) {
    $lRet = array();

    $lSql = 'SELECT * FROM al_eve_tpl WHERE mand IN (0,'.MID.') ORDER BY mand';
    $lQry = new CCor_Qry($lSql);

    foreach ($lQry as $lRow) {
        $lRet[$lRow -> id] = $lRow;
    }
    return $lRet;
  }

}