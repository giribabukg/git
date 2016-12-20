<?php
class CInc_Tab_Mod extends CCor_Mod_Table {

  protected $mModule = 'tab_master';

  public function __construct() {
    parent::__construct('al_'.$this -> mModule);

    $this -> addField(fie('id'));
    $this -> addField(fie('mand'));
    $this -> addField(fie('name'));
    $this -> addField(fie('type'));
  }
}