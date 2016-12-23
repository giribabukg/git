<?php
class CInc_Cor_Res_Crpmaster extends CCor_Res_Plugin {

  public function get($aParam = NULL) {
    if (NULL === $this -> mRet) {
      $this -> mRet = $this -> refresh($aParam);
    } else {
      incCtr('rch');
    }
    return $this -> mRet;
  }

  protected function refresh($aParam = NULL) {
    $lRet = array();
    if (0 < MID) {
      $lQry = new CCor_Qry('SELECT * FROM al_crp_master c WHERE c.mand='.MID.' ORDER BY name_'.LAN);
      foreach($lQry as $lRow) {
        $lRet[$lRow -> id] = $lRow;
      }
    }
    return $lRet;
  }
}