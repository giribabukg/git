<?php
class CInc_Sys_Svc_Mod extends CCor_Mod_Table {
  
  public function __construct() {
    parent::__construct('al_sys_svc');

    // 'id' is set automatically
    $this -> addField(fie('id'));

    // upcoming fields can be set by the user
    $lFields = array('name', 'act', 'pos', 'from_time', 'to_time', 'tick', 'params', 'dow', 'flags', 'mand');
    foreach ($lFields as $lKey => $lValue) {
      $this -> addField(fie($lValue));
    }
  }
   
}