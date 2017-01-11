<?php
class CInc_Cms_Categories_Form_Base extends CHtm_Form {

  public function __construct($aAct, $aCaption, $aCancel = NULL) {
    parent::__construct($aAct, $aCaption, $aCancel);

    $this -> addDef(fie('value', lan('lib.key')));

    $this -> mAvailLang = CCor_Res::get('languages');
    foreach ($this -> mAvailLang as $lLang => $lName) {
      $this -> addDef(fie('value_'.$lLang, lan('lib.name').' ('.strtoupper($lLang).')'));
    }
    
    $lLayouts = array('' => '');
    $lLayouts+= CCor_Res::get('htb', array('domain' => 'phl'));
    $this -> addDef(fie('layouts', lan('lib.layout'), 'multipleselect', $lLayouts, array('style' => 'height:100px;')));

    $lTasks = array('' => '');
    $lTasks+= CCor_Res::get('htb', array('domain' => 'apl_task'));
    $this -> addDef(fie('tasks', lan('lib.tasks'), 'multipleselect', $lTasks, array('style' => 'height:200px;')));

    $this -> addDef(fie('tooltip_de', lan('lib.tooltip').' (DE)', 'memo'));
    $this -> addDef(fie('tooltip_en', lan('lib.tooltip').' (EN)', 'memo'));
  }
}