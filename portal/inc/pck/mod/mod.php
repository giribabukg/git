<?php
class CInc_Pck_Mod_Mod extends CCor_Mod_Table {
  
  public function __construct($aArrFields, $aArrFieldCaptions,$aId ='') {
    parent::__construct('al_pck_items');
    $this -> mId = $aId;
    $lFields = $aArrFields;
    
    $lCaptions = $aArrFieldCaptions;
    $this -> addField(fie('pck_id'));
    
    
    foreach ($lFields as $lRow){
      $this -> addField(fie('col'.$lRow['col']));
    }
      
  }
  
  protected function beforePost($aNew = FALSE) {
  if ($aNew) {
      $this -> setVal('mand', MID);
     
     }
   }
  
}