<?php
class CCrp_Sta_Flag_Form_Base extends CHtm_Form {

  public function __construct($aAct, $aCaption, $aCid) {
    $lCid = intval($aCid);
    parent::__construct($aAct, $aCaption, 'crp-sta&id='.$lCid);
    $this -> setParam('cid', $lCid);

    $this -> addDef(fie('crp_id','','hidden'));
    $this -> addDef(fie('mand','','hidden'));
    $this -> addDef(fie('name_de', lan('lib.name').' (DE)'));
    $this -> addDef(fie('name_en', lan('lib.name').' (EN)'));

   # $this -> addDef(fie('1apl', 'Flags Activate', 'bitset', array('dom' => 'apl')));
    $this -> addDef(fie('dead', 'Deadline Jobfield', 'resselect', array('res' => 'eve', 'key' => 'id', 'val' => 'name_'.LAN)));
    $this -> addDef(fie('alias', 'Link to Jobfield', 'resselect', array('res' => 'eve', 'key' => 'id', 'val' => 'name_'.LAN)));
    $this -> addDef(fie('eventa', 'Event Activate', 'resselect', array('res' => 'eve', 'key' => 'id', 'val' => 'name_'.LAN),array('hr' => 'hr')));

   # $this -> addDef(fie('--1', '----------------', 'bitset', array('res' => 'apl')));

    
    $lArr = array('dom' => 'fla');
    $this -> addDef(fie('flags', 'Options Confirm', 'bitset', $lArr));
    $this -> addDef(fie('eventc', 'Event Confirm', 'resselect', array('res' => 'eve', 'key' => 'id', 'val' => 'name_'.LAN),array('hr' => 'hr')));
    $this -> addDef(fie('eventm', 'Event Mandatory', 'resselect', array('res' => 'eve', 'key' => 'id', 'val' => 'name_'.LAN),array('hr' => 'hr')));

    $this -> addDef(fie('1name_de', 'Amendment (DE)'));
    $this -> addDef(fie('1name_en', 'Amendment (EN)'));
    $this -> addDef(fie('1img', 'Icon Amendment','','',array('hr' => 'hr')));
    $this -> addDef(fie('2name_de', 'Approval (DE)'));
    $this -> addDef(fie('2name_en', 'Approval (EN)'));
    $this -> addDef(fie('2img', 'Icon Approval','','',array('hr' => 'hr')));
    $this -> addDef(fie('3name_de', 'Conditional (DE)'));
    $this -> addDef(fie('3name_en', 'Conditional (EN)'));
    $this -> addDef(fie('3img', 'Icon Conditional'));
    
 #   $this -> addDef(fie('apl', 'Display Buttons', 'bitset', array('dom' => 'apl')));
    
    $this -> setVal('crp_id', $lCid);
    $this -> setVal('mand', MID);
    
  }
}