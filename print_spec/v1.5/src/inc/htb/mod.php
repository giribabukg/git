<?php
class CInc_Htb_Mod extends CCor_Mod_Table {

  public function __construct() {
    parent::__construct('al_htb_master');
    $this -> addField(fie('id'));
    $this -> addField(fie('domain'));
    $this -> addField(fie('description'));
  }

  public static function clearCache() {
    $lCkey = 'cor_res_htbmaster';
    CCor_Cache::clearStatic($lCkey);
  }

  protected function afterChange() {
    self::clearCache();
  }

}