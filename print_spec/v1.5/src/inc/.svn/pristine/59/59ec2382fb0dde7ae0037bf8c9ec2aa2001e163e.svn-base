<?php
class CInc_Fie_Map_Mod extends CCor_Mod_Table {

  public function __construct() {
    parent::__construct('al_fie_map_master');
    $this -> addField(fie('id'));
    $this -> addField(fie('mand'));
    $this -> addField(fie('name'));
  }
  
  public static function clearCache() {
    $lCkey = 'cor_res_fie_map_master';
    CCor_Cache::clearStatic($lCkey);
  }
  
  protected function afterChange() {
    self::clearCache();
  }
  
}