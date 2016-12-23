<?php
class CInc_Pck_Itm_Mod extends CCor_Mod_Table {

  public function __construct($aFields) {
    parent::__construct('al_pck_items');
    $this -> addField(fie('id'));
   # $this -> addField(fie('pick_id'));
    $this -> addField(fie('mand'));
    $this -> addField(fie('domain'));
    foreach ($aFields as $lRow){
      $this -> addField(fie('col'.$lRow['col']));
    }
  }

}