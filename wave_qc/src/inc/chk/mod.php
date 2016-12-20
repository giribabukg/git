<?php
class CInc_Chk_Mod extends CCor_Mod_Table {

  public function __construct() {
    parent::__construct('al_chk_master');

    $this -> addField(fie('id'));
    $this -> addField(fie('domain'));

    $this -> mAvailLang = CCor_Res::get('languages');
    foreach ($this -> mAvailLang as $lLang => $lName) {
      $this -> addField(fie('name_'.$lLang));
    }

    $this -> addField(fie('cnd_id'));
  }

  public static function clearCache() {
    $lCKey = 'cor_res_chkmaster';
    CCor_Cache::clearStatic($lCKey);
  }

  protected function afterChange() {
    self::clearCache();
  }

  protected function beforePost($aNew = FALSE) {
    if ($aNew) {
      $this -> setVal('mand', MID);
    }
  }
}