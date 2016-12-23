<?php
class CInc_Fie_Blocks_Form extends CHtm_Form {
  
  public function __construct($aAct, $aCaption) {
    parent::__construct($aAct, $aCaption);
    
    $lArr = array();
    $lArr['pro'] = 'Project';
    $lArr['pde'] = 'Print Development';
    $lArr['mba'] = 'Master Base Artwork';
    $lArr['tpl'] = 'Variants';
    $lArr['pac'] = 'Packaging';
    $this -> addDef(fie('src', 'Source', 'select', $lArr));
    
    $this -> addDef(fie('code', 'Code'));
    $this -> addDef(fie('name', 'Caption'));
  }
  
}