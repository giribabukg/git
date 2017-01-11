<?php
class CInc_Fie_Map_Item_Mod extends CCor_Mod_Table {

  public function __construct() {
    parent::__construct('al_fie_map_items');
    $this -> addField(fie('id'));
    $this -> addField(fie('map_id'));
    $this -> addField(fie('alias'));
    $this -> addField(fie('native'));
    $this -> addField(fie('default_value'));
    if (CCor_Cfg::get('validate.available')) {
      $this->addField(fie('validate_rule'));
    }
    $this->addField(fie('read_filter'));
    $this->addField(fie('write_filter'));
  }

  protected function afterChange() {
    CFie_Map_Mod::clearCache();
  }

}
