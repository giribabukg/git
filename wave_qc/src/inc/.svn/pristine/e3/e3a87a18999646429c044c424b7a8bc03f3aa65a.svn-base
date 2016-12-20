<?php
class CCrp_Sta_Flag_Mod extends CCor_Mod_Table {

  public function __construct() {
    parent::__construct('al_crp_step');

    // 'mand' and 'crp_id' are set automatically
    $this -> addField(fie('mand'));
    $this -> addField(fie('crp_id'));

    // upcoming fields can be set by the user
    $lFields = array('from_id', 'to_id', 'name_de', 'name_en', 'flags', 'event', 'trans');
    foreach ($lFields as $lKey => $lValue) {
      if (!empty($_REQUEST['val'][$lValue])) {
        $this -> addField(fie($lValue));
      }
    }

    // old solution
//    $this -> addField(fie('from_id'));
//    $this -> addField(fie('to_id'));
//    $this -> addField(fie('name_de'));
//    $this -> addField(fie('name_en'));
//    $this -> addField(fie('flags'));
//    $this -> addField(fie('event'));
//    $this -> addField(fie('trans'));
  }

}