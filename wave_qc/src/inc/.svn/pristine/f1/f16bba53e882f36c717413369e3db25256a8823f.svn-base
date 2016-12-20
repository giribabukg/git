<?php
class CInc_Fla_Form_Base extends CHtm_Form {

  protected $mImages = array();

  public function __construct($aAct, $aCaption) {
    parent::__construct($aAct, $aCaption, 'fla');

    #$this -> addDef(fie('crp_id','','hidden'));
    $this -> addDef(fie('mand','','hidden'));
    $this -> mAvailLang = CCor_Res::get('languages');
    foreach ($this -> mAvailLang as $lLang => $lName) {
      $this -> addDef(fie('name_'.$lLang, lan('lib.name').' '.lan('lan.'.$lLang)));
    }
    // Auswertung d. Arrays array('res'... in htm/fie/fac
    # $this -> addDef(fie('1apl', 'Flags Activate', 'bitset', array('dom' => 'apl')));
    $this -> addDef(fie('ddl_fie', 'Deadline Jobfield', 'resselect', array('res' => 'fie', 'key' => 'alias', 'val' => 'name_'.LAN)));
    $this -> addDef(fie('alias',    'Link to Jobfield', 'resselect', array('res' => 'fie', 'key' => 'alias', 'val' => 'name_'.LAN),'',array('_hr' => 'hr')));

    $lArr = array('dom' => 'fla');
    $this -> addDef(fie('flags_act', 'Options Activate', 'bitset', $lArr));
    $this -> addDef(fie('eve_act', 'Event Activate', 'resselect', array('res' => 'eve', 'key' => 'id', 'val' => 'name_'.LAN)));
    $lImg = (!empty($this -> mImages['eve_'.flEve_act.'_ico']) ? $this -> mImages['eve_'.flEve_act.'_ico'] : '');
    $this -> addDef(fie('eve_'.flEve_act.'_ico', 'Icon Event Activate','string','',array("data-change" => "autocomplete", "data-source" => "ajx.flagimg", "data-autocomplete-body" => "'<span class=\"informal\"><img src=\"'+item.label+'\" />'+item.value+'</span>'"),array('_img' => $lImg, '_hr' => 'hr')));
    # $this -> addDef(fie('--1', '----------------', 'bitset', array('res' => 'apl')));


    $lArr = array('dom' => 'flc');
    $this -> addDef(fie('flags_conf', 'Options Confirm', 'bitset', $lArr));
    $this -> addDef(fie('eve_conf', 'Event Confirm', 'resselect', array('res' => 'eve', 'key' => 'id', 'val' => 'name_'.LAN)));
    $lImg = (!empty($this -> mImages['eve_'.flEve_conf.'_ico']) ? $this -> mImages['eve_'.flEve_conf.'_ico'] : '');
    $this -> addDef(fie('eve_'.flEve_conf.'_ico', 'Icon Event Confirm','string','',array("data-change" => "autocomplete", "data-source" => "ajx.flagimg", "data-autocomplete-body" => "'<span class=\"informal\"><img src=\"'+item.label+'\" />'+item.value+'</span>'"),array('_img' => $lImg, '_hr' => 'hr')));
    $this -> addDef(fie('eve_mand', 'Event Mandatory', 'resselect', array('res' => 'eve', 'key' => 'id', 'val' => 'name_'.LAN),'',array('_hr' => 'hr')));

    foreach ($this -> mAvailLang as $lLang => $lName) {
      $this -> addDef(fie('amend_'.$lLang, 'Amendment ('.strtoupper($lLang).')'));
    }
    $lImg = (!empty($this -> mImages['amend_ico']) ? $this -> mImages['amend_ico'] : '');
    $this -> addDef(fie('amend_ico', 'Icon Amendment','string','',array("data-change" => "autocomplete", "data-source" => "ajx.flagimg", "data-autocomplete-body" => "'<span class=\"informal\"><img src=\"'+item.label+'\" />'+item.value+'</span>'"),array('_img' => $lImg, '_hr' => 'hr')));

    foreach ($this -> mAvailLang as $lLang => $lName) {
      $this -> addDef(fie('approv_'.$lLang, 'Approval ('.strtoupper($lLang).')'));
    }
    $lImg = (!empty($this -> mImages['approv_ico']) ? $this -> mImages['approv_ico'] : '');
    $this -> addDef(fie('approv_ico', 'Icon Approval','string','',array("data-change" => "autocomplete", "data-source" => "ajx.flagimg", "data-autocomplete-body" => "'<span class=\"informal\"><img src=\"'+item.label+'\" />'+item.value+'</span>'"),array('_img' => $lImg, '_hr' => 'hr')));

    foreach ($this -> mAvailLang as $lLang => $lName) {
      $this -> addDef(fie('condit_'.$lLang, 'Conditional ('.strtoupper($lLang).')'));
    }
    $lImg = (!empty($this -> mImages['condit_ico']) ? $this -> mImages['condit_ico'] : '');
    $this -> addDef(fie('condit_ico', 'Icon Conditional','string','',array("data-change" => "autocomplete", "data-source" => "ajx.flagimg", "data-autocomplete-body" => "'<span class=\"informal\"><img src=\"'+item.label+'\" />'+item.value+'</span>'"),array('_img' => $lImg)));

    $this -> setVal('mand', MID);

  }
}