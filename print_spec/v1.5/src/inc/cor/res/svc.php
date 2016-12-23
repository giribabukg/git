<?php
class CInc_Cor_Res_Svc extends CCor_Res_Plugin {

  protected function refresh($aParam = NULL) {
    $lMID = (defined('MID')) ? '0,'.MID : '0';

    $lSQL = 'SELECT * FROM al_sys_svc WHERE mand IN ('.$lMID.') ORDER BY act ASC;';
    $lQry = new CCor_Qry($lSQL);

    $lRet = array();
    foreach ($lQry as $lRow) {
      $lRet[$lRow -> id] = $lRow;
    }

    return $lRet;
  }
}