<?php
class CInc_Prf_Mod extends CCor_Mod_Table {

  public function __construct() {
    parent::__construct('al_sys_pref', 'code,mand');
    $this -> addField(fie('mand'));
    $this -> addField(fie('grp'));
    $this -> addField(fie('code'));
    $this -> addField(fie('name_'.LAN));
    $this -> addField(fie('val'));
    $this -> mAutoInc = FALSE;
  }

}