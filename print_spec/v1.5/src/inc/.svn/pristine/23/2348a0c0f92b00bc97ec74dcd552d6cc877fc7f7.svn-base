<?php
class CInc_Pck_Col_Mod extends CCor_Mod_Table {
  
  public function __construct() {
    parent::__construct('al_pck_columns');

    // 'domain' and 'mand' are set automatically
    $this -> addField(fie('domain'));
    $this -> addField(fie('mand'));

    // upcoming fields can be set by the user
    $lFields = array('pck_id', 'alias', 'col', 'position', 'hidden', 'image', 'color', 'ignoretype', 'htb');
    foreach ($lFields as $lKey => $lValue) {
      if (isset($_REQUEST['val'][$lValue])) {
        $this -> addField(fie($lValue));
      }
    }

    // old solution
//    $this -> addField(fie('pck_id'));
//    $this -> addField(fie('alias'));
//    $this -> addField(fie('col'));
//    $this -> addField(fie('position'));
//    $this -> addField(fie('hidden'));
//    $this -> addField(fie('image'));
//    $this -> addField(fie('color'));
  }
  
}