<?php
class CInc_Crp_Sta_Stp_Mod extends CCor_Mod_Table {

  public function __construct() {
    parent::__construct('al_crp_step');

    // 'mand' and 'crp_id' are set automatically
    $this -> addField(fie('mand'));
    $this -> addField(fie('crp_id'));
    $this -> addField(fie('cond'));
    $this -> addField(fie('apl_type'));
    
    // upcoming fields can be set by the user
    $this -> mAvailLang = CCor_Res::get('languages');
    foreach ($this -> mAvailLang as $lLang => $lName) {
      $this -> addField(fie('name_'.$lLang));
      $this -> addField(fie('desc_'.$lLang));
    }

    $lFields = array('from_id', 'to_id', 'flags', 'event', 'trans');
    foreach ($lFields as $lKey => $lValue) {
      $this -> addField(fie($lValue));
    }

    $lFlagFields = array('flag_act', 'flag_stp');
    $lFields+= $lFlagFields;

    foreach ($lFlagFields as $lKey => $lValue) {
      $this -> addField(fie($lValue, '', 'multipleselect'));
    }
  }

  public static function clearCache() {
    $lCkey = 'cor_res_crpstep';
    CCor_Cache::clearStatic($lCkey);
  }

  protected function afterChange() {
    self::clearCache();
  }

}