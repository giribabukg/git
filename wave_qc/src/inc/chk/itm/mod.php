<?php
class CInc_Chk_Itm_Mod extends CCor_Mod_Table {

  public function __construct() {
    parent::__construct('al_chk_items');

    $this -> addField(fie('id'));
    $this -> addField(fie('item_code'));
    $this -> addField(fie('domain'));
    $this -> addField(fie('master_id'));

    $this -> mAvailLang = CCor_Res::get('languages');
    foreach ($this -> mAvailLang as $lLang => $lName) {
      $this -> addField(fie('name_'.$lLang));
    }

    $this -> addField(fie('cnd_id'));
  }

  protected function afterChange() {
    $lDomain = $this -> getVal('domain');
    if (!empty($lDomain)) {
      $lCKey = 'cor_res_chk_'.$lDomain.'_';
      $this -> dbg('Clearing '.$lCKey);
      CCor_Cache::clearStatic($lCKey.'de');
      CCor_Cache::clearStatic($lCKey.'en');
    }
  }

  protected function beforePost($aNew = FALSE) {
    if ($aNew) {
      $this -> setVal('mand', MID);
    }
  }
}