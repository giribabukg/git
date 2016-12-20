<?php
class CInc_Fla_Mod extends CCor_Mod_Table {

  public function __construct() {
    parent::__construct('al_fla');

    // 'mand' and 'crp_id' are set automatically
    $this -> addField(fie('mand'));

    // upcoming fields can be set by the user
    $lFields = array('alias', 'ddl_fie',
        'eve_act',  'eve_'.flEve_act.'_ico',  'flags_act',
        'eve_conf', 'eve_'.flEve_conf.'_ico', 'flags_conf',
        'eve_mand',
        'amend_ico', 'approv_ico', 'condit_ico');
    $this -> mAvailLang = CCor_Res::get('languages');
    foreach ($this -> mAvailLang as $lLang => $lName) {
      $lFields[] = 'name_'.$lLang;
      $lFields[] = 'amend_'.$lLang;
      $lFields[] = 'approv_'.$lLang;
      $lFields[] = 'condit_'.$lLang;
    }
    foreach ($lFields as $lKey => $lValue) {
      if ((isset($_REQUEST['old'][$lValue]) AND isset($_REQUEST['val'][$lValue]) AND $_REQUEST['old'][$lValue] != $_REQUEST['val'][$lValue])
          OR ( !empty($_REQUEST['val'][$lValue]) )
      ) {
        $this -> addField(fie($lValue));
      }
    }

  }

}