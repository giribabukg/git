<?php
class CInc_Fie_Blocks_Mod extends CCor_Mod_Table {
  
  public function __construct() {
    parent::__construct('al_fie_blocks');
    $this -> addField(fie('id'));
    $this -> addField(fie('src'));
    $this -> addField(fie('code'));
    $this -> addField(fie('name'));

  }
  
}