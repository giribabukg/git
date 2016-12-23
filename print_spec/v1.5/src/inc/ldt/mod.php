<?php
class CInc_Ldt_Mod extends CCor_Mod_Table {
  
  public function __construct() {
    parent::__construct('al_ldt_master');
    $this -> addField(fie('id'));
    $this -> addField(fie('src'));
    $this -> mAvailLang = CCor_Res::get('languages');
    foreach ($this -> mAvailLang as $lLang => $lName) {
      $this -> addField(fie('name_'.$lLang));
    }
    $this -> addField(fie('std_val'));
  }
   
}