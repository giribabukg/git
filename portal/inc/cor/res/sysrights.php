<?php
/**
 * Customer - Core: Ressource - All System Preferences
 *
 *  Description "Singleton" used in licenses
 *
 * @package    COR
 * @subpackage  Ressource
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 10553 $
 * @date $Date: 2015-09-25 18:00:34 +0200 (Fri, 25 Sep 2015) $
 * @author $Author: psarker $
 */
class CCor_Res_SysRights extends CCor_Res_Plugin {

  protected function refresh($aParam = NULL) {
    $lCkey = 'cor_res_sysrights';
    if ($lRet = $this -> getCache($lCkey)) {
      return $lRet;
    }
    
    $lRet = array();
    $lSql = 'SELECT DISTINCT `code` FROM `al_sys_rig_usr`';
    $lQry = new CCor_Qry($lSql);
    foreach($lQry as $lRow) {
      $lRet[$lRow['code']] = TRUE;
    }
    #echo '<pre>--src!inc!CCor_Res_SysRights---';var_dump($lRet,'#############');echo '</pre>';
    $this -> setCache($lCkey, $lRet);
    return $lRet;
  }

}
