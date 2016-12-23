<?php
class CInc_Apl_Types_Mod extends CCor_Mod_Table {

  public function __construct() {
    parent::__construct('al_apl_types');
    $this -> addField(fie('mand'));
    $this -> addField(fie('code'));
    $this -> addField(fie('name'));
    $this -> addField(fie('short'));
    $this -> addField(fie('apl_mode'));
    $this -> addField(fie('flags'));
    $this -> addField(fie('event_completed'));
  }

}