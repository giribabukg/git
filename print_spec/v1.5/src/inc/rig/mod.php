<?php
class CInc_Rig_Mod extends CCor_Mod_Table {

  public function __construct() {
    parent::__construct('al_sys_rig_usr', 'code,mand');
    $this -> addField(fie('mand'));
    $this -> addField(fie('grp'));
    $this -> addField(fie('code'));
    $this -> mAvailLang = CCor_Res::get('languages');
    foreach ($this -> mAvailLang as $lLang => $lName) {
      $this -> addField(fie('name_'.$lLang));
    }
    $this -> addField(fie('desc_de'));
    $this -> addField(fie('desc_en'));
    $this -> addField(fie('level'));
    $this -> mAutoInc = FALSE;
  }

}