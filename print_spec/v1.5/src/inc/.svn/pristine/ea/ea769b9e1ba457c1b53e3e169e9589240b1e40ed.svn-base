<?php
class CInc_Sys_Lang_Mod extends CCor_Mod_Table {

  public function __construct() {
    parent::__construct('al_sys_lang', 'code,mand');
    $this -> addField(fie('code'));
    $this -> addField(fie('mand'));
    $this -> mAvailLang = CCor_Res::get('languages');
    foreach ($this -> mAvailLang as $lLang => $lName) {
      $this -> addField(fie('value_'.$lLang));
    }

    $this -> mAutoInc = FALSE;
  }

}