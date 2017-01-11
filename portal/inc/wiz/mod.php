<?php
class CInc_Wiz_Mod extends CCor_Mod_Table {

  public function __construct() {
    parent::__construct('al_wiz_master');
    $this -> addField(fie('id'));
    $this -> mAvailLang = CCor_Res::get('languages');
    foreach ($this -> mAvailLang as $lLang => $lName) {
      $this -> addField(fie('name_'.$lLang));
    }
  }

}