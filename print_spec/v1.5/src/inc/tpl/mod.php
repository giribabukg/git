<?php
class CInc_Tpl_Mod extends CCor_Mod_Table {

  public function __construct() {
    parent::__construct('al_eve_tpl');
    $this -> addField(fie('id'));
    $this -> addField(fie('mand'));
    $this -> addField(fie('lang'));
    $this -> addField(fie('name'));
    $this -> addField(fie('subject'));
    $this -> addField(fie('msg'));
  }

}