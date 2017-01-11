<?php
class CInc_Cms_Categories_Mod extends CCor_Mod_Table {

  public function __construct() {
    parent::__construct('al_cms_categories');

    $this -> addField(fie('id'));
    $this -> addField(fie('mand'));
    $this -> addField(fie('value'));

    $this -> mAvailLang = CCor_Res::get('languages');
    foreach ($this -> mAvailLang as $lLang => $lName) {
      $this -> addField(fie('value_'.$lLang));
    }
    
    $this -> addField(fie('layouts', '', 'multipleselect'));
    
    $this -> addField(fie('tasks', '', 'multipleselect'));
    
    $lFields = array('tooltip_de', 'tooltip_en');//22651 Project Critical Path Functionality
    foreach ($lFields as $lKey => $lValue) {
      $this -> addField(fie($lValue));
    }
  }

  protected function beforePost($aNew = FALSE) {
    if ($aNew) {
      $this -> setVal('mand', MID);
    }
    
    $this -> addTasks();
  }
  
  protected function addTasks() {
    $lCategory = $this -> getVal('value');
    $lTasks = explode(",", $this -> getVal('tasks'));
    
    $lSql = 'DELETE FROM `al_cms_categorytasks` WHERE `mand`='.intval(MID).' AND `category`='.esc($lCategory);
    CCor_Qry::exec($lSql);
    
    foreach($lTasks as $lTask) {
      $lSql = 'INSERT INTO `al_cms_categorytasks` (`mand`, `task`, `category`) VALUES ('.intval(MID).','.esc($lTask).','.esc($lCategory).')';
      CCor_Qry::exec($lSql);
    }
    
    unset($this -> mVal['tasks']);
    unset($this -> mOld['tasks']);
    unset($this -> mUpd['tasks']);
  }
}