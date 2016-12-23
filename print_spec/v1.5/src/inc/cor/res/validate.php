<?php
class CInc_Cor_Res_Validate extends CCor_Res_Plugin {

  protected function refresh($aParam = NULL) {
    $lRet = array();
    $lMid = (defined('MID')) ? MID : 0;
    $lCkey = 'cor_res_validate_'.$lMid;

    $lOpt = array();
    $lSql = 'SELECT * FROM al_fie_validate_options';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lOpt[$lRow['validate_id']][$lRow['option_name']] = $lRow['option_value'];
    }

    $lSql = 'SELECT * FROM al_fie_validate WHERE mand IN (0,-2,'.$lMid.') ORDER BY name';

    $lQry = new CCor_Qry($lSql);
    foreach($lQry as $lRow) {
      $lId = $lRow['id'];
      $lCurRow = $lRow->toArray();
      if (isset($lOpt[$lId])) {
        $lCurRow['options'] = $lOpt[$lId];
      }
      $lRet[$lId] = $lCurRow;
    }
    $this -> setCache($lCkey, $lRet);
    return $lRet;
 }

}
