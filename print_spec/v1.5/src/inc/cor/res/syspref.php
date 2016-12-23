<?php
class CInc_Cor_Res_Syspref extends CCor_Res_Plugin {
  
  /* Const 'MID_PREF' will be defined in login manager.
   * If there are more than one mandator, some prefs need to be stored 
   * separately for each mandator (e.g. the columns in the job view).
   *  
   * If a pref setting exists for both mand 0 and the current mand, the setting
   * for the current mand will be used
   */
  protected function refresh($aParam = NULL) {
    $lRet = array();

    
    $lSql = 'SELECT * FROM al_sys_pref ';
    $lSql.= 'WHERE mand IN (0';
    if (defined('MID_PREF')) {
      $lSql .= ','.MID_PREF;
    } else if (defined('MID')) {
      $lSql .= ','.MID;
    }
    $lSql .= ') ORDER BY mand,code';

    $lQry = new CCor_Qry($lSql);
    foreach($lQry as $lRow) {
      $lRet[$lRow -> code] = $lRow;
    }
    return $lRet;
  }
}