<?php
class CInc_Htb_Itm_Mod extends CCor_Mod_Table {

  public function __construct() {
    parent::__construct('al_htb_itm');
    $this -> addField(fie('id'));
    $this -> addField(fie('mand'));
    $this -> addField(fie('domain'));
    $this -> addField(fie('value'));
    $this -> mAvailLang = CCor_Res::get('languages');
    foreach ($this -> mAvailLang as $lLang => $lName) {
      $this -> addField(fie('value_'.$lLang));
  }
  }
  
  protected function afterChange() {
  	$lDom = $this->getVal('domain');
  	if (!empty($lDom)) {
      $lCkey = 'cor_res_htb_'.$lDom.'_';
      $this->dbg('Clearing '.$lCkey);
  	  CCor_Cache::clearStatic($lCkey.'de');
  	  CCor_Cache::clearStatic($lCkey.'en');
  	}
  }
  

}