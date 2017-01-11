<?php
class CInc_Cor_Res_Categories extends CCor_Res_Plugin {

  public function get($aParam = NULL) {
  if (is_array($aParam)) {
    $lParam  = implode('|',$aParam);
  } else {
    $lParam = $aParam;
  }

  if (isset($this -> mRet[$lParam])) {
     $this -> resHit();
     return $this -> mRet[$lParam];
  }
  $this -> mRet[$lParam] = $this -> refresh($lParam);
  return $this -> mRet[$lParam];

  }

  protected function refresh($aParam = NULL) {
    $lParam = explode('|',$aParam);
    if (1 < count($lParam)) {
      $lDomain = $lParam[0];
      $lFirst  = $lParam[1];
      $lSort   = $lParam[2];
    } else {
      $lDomain = $aParam;
      $lFirst  = 'value';
      $lSort   = 'value_'.LAN;
    }

    $aParam = str_replace('|', '_', $aParam);
    $lCkey = 'cor_res_cms_categories_'.$aParam.'_'.LAN;
    if ($lRet = $this -> getCache($lCkey)) {
      return $lRet;
    }

    $lRet = array();
    $lSql = 'SELECT '.$lFirst.', value_'.LAN.' FROM `al_cms_categories` WHERE `mand`='.MID.' AND `active`=1 ORDER BY '.$lSort;
    $lQry = new CCor_Qry($lSql);
    foreach($lQry as $lRow) {
      if (MID == $lRow['mand']) {
        $lRet[ $lRow[$lFirst] ] = $lRow['value_'.LAN];
      } elseif(0 == $lRow['mand']) {
        $lRet[ $lRow[$lFirst] ] = $lRow['value_'.LAN];
      }
    }
    $this -> setCache($lCkey, $lRet);
    return $lRet;
  }

}