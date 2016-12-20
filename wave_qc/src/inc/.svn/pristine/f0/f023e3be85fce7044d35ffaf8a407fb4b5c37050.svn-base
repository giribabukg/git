<?php
class CInc_Cor_Res_Rol extends CCor_Res_Plugin {
  
   protected function refresh($aParam = NULL) {
    $lCkey = 'cor_res_rol_'.MID;
    if ($lRet = $this -> getCache($lCkey)) {
      return $lRet;
    }
    $lRet = array();
    
    $lQry = new CCor_Qry('SELECT * FROM al_rol WHERE mand='.MID.' ORDER BY name');
    foreach($lQry as $lRow) {
      $lRet[$lRow -> id] = $lRow -> toArray();
    }
    $this -> setCache($lCkey, $lRet);
    return $lRet;
  }

}