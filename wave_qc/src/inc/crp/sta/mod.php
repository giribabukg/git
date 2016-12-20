<?php
class CInc_Crp_Sta_Mod extends CCor_Mod_Table {

  public function __construct() {
    parent::__construct('al_crp_status');

    // 'mand' and 'crp_id' are set automatically
    $this -> addField(fie('mand'));
    $this -> addField(fie('crp_id'));

    $this -> mAvailLang = CCor_Res::get('languages');
    foreach ($this -> mAvailLang as $lLang => $lName) {
      $this -> addField(fie('name_'.$lLang));
      $this -> addField(fie('desc_'.$lLang));
    }
    // upcoming fields can be set by the user
    $lFields = array('status', 'display', 'img', 'apl', 'flags', 'pro_con', 'report_map');//22651 Project Critical Path Functionality
    foreach ($lFields as $lKey => $lValue) {
      $this -> addField(fie($lValue));
    }

    // 'mandbystat' are set automatically
    $this -> addField(fie('mandbystat'));
  }

}