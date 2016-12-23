<?php
class CInc_Jfl_Mod extends CCor_Mod_Table {

  public function __construct() {
    parent::__construct('al_jfl');

    // 'id' is set automatically
    $this -> addField(fie('id'));
    $this -> addField(fie('mand'));

    // upcoming fields can be set by the user
    $lFields = array('code', 'val');
    $this -> mAvailLang = CCor_Res::get('languages');
    foreach ($this -> mAvailLang as $lLang => $lName) {
      $lFields[] = 'name_'.$lLang;
      $lFields[] = 'set_'.$lLang;
      $lFields[] = 'reset_'.$lLang;
    }

    foreach ($lFields as $lKey => $lValue) {
      if (!empty($_REQUEST['val'][$lValue])) {
        $this -> addField(fie($lValue));
      }
    }
  }

  public static function clearCache() {
    $lCkey = 'cor_res_jfl';
    CCor_Cache::clearStatic($lCkey);
  }

  protected function afterChange() {
    self::clearCache();
  }
}