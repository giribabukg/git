<?php
/**
 * Native Job Field List from MIS
 *
 * Get a list of all Job fields and additional fields from QBF MIS
 *
 * @package    Ressource
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 */

class CInc_Cor_Res_Native extends CCor_Res_Plugin {

  protected function refresh($aParam = NULL) {
    return array();
    $lCkey = 'cor_res_native';
    if ($lRet = $this -> getCache($lCkey)) {
      return $lRet;
    }


    $lQry = new CApi_Alink_Query_Getjobfields();
    $lRet = $lQry -> getList();

    $this -> setCache($lCkey, $lRet);
    return $lRet;
  }

}
