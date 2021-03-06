<?php
class CInc_Rol_Mod extends CCor_Mod_Table {

  public function __construct() {
    parent::__construct('al_rol');
    $this -> addField(fie('id'));
    $this -> addField(fie('name'));
    $this -> addField(fie('typ'));
    $this -> addField(fie('alias'));
  }

  protected function beforePost($aNew = FALSE) {
    if ($aNew) {
      $this -> setVal('mand', MID);
    }
  }

  protected function afterChange() {
    $lCkey = 'cor_res_rol';
    CCor_Cache::clearStatic($lCkey);
  }
  
  public function saveRoleHis($aUid, $aGruId, $aDate, $aTyp, $aSubject, $aMsg) {
    $lQry = new CCor_Qry();
    $lRoleHisSql = "INSERT INTO al_role_his (uid,role_id,datum,typ,subject,msg) VALUES ('" .$aUid . "','" . $aGruId . "','" . $aDate . "','" . $aTyp . "','" .
        $aSubject . "','" . $aMsg . "')";
    $lQry -> query($lRoleHisSql);
  }

}