<?php
class CInc_Tab_Itm_Mod extends CCor_Mod_Table {

  protected $mModule = 'tab_slave';

  public function __construct() {
    parent::__construct('al_'.$this -> mModule);

    $this -> addField(fie('id'));
    $this -> addField(fie('mand'));
    $this -> addField(fie('name'));
    $this -> addField(fie('link'));
    $this -> addField(fie('code'));
    $this -> addField(fie('type'));
    $this -> addField(fie('subtype'));
    $this -> addField(fie('target'));
  }

  public function updateTable() {
    $lID = $this -> getVal('id');
    $lMand = $this -> getVal('mand');

    $lName = $this -> getVal('name');
    $lLink = $this -> getVal('link');
    $lCode = $this -> getVal('code');
    $lType = $this -> getVal('type');
    $lSubType = $this -> getVal('subtype');
    $lTarget = $this -> getVal('target');

    $lOldName = $this -> getOld('name');
    $lOldLink = $this -> getOld('link');
    $lOldCode = $this -> getOld('code');
    $lOldType = $this -> getOld('type');
    $lOldSubType = $this -> getOld('subtype');
    $lOldTarget = $this -> getOld('target');

    if (!empty($lOldSubType)) {
      CCor_Qry::exec('DELETE FROM al_sys_lang WHERE code="tab.'.$lOldType.'.'.$lOldSubType.'.'.$lOldCode.'" AND mand="'.$lMand.'"');
      CCor_Qry::exec('DELETE FROM al_sys_rig_usr WHERE code="tab.'.$lOldType.'.'.$lOldSubType.'.'.$lOldCode.'" AND mand="'.$lMand.'"');
    } else {
      CCor_Qry::exec('DELETE FROM al_sys_lang WHERE code="tab.'.$lOldType.'.'.$lOldCode.'" AND mand="'.$lMand.'"');
      CCor_Qry::exec('DELETE FROM al_sys_rig_usr WHERE code="tab.'.$lOldType.'.'.$lOldCode.'" AND mand="'.$lMand.'"');
    }

    $this -> mAvailLang = CCor_Res::get('languages');
    $lSqlVal = '';
    $lSqlNam = '';
    $lSqlNamMain = '';
    foreach ($this -> mAvailLang as $lLang => $lN) {
      $lSqlVal.= ',value_'.$lLang.'='.esc($lName);
      $lSqlNam.= ',name_'.$lLang.'='.esc('Tabs (Job): '.$lName);
      $lSqlNamMain.= ',name_'.$lLang.'='.esc('Tabs (Main menu): '.$lName);
    }
    if ($lType == 'job') {
      CCor_Qry::exec('REPLACE INTO al_sys_lang SET code="tab.'.$lType.'.'.$lSubType.'.'.$lCode.'",mand='.esc($lMand).$lSqlVal);
      CCor_Qry::exec('REPLACE INTO al_sys_rig_usr SET code="tab.'.$lType.'.'.$lSubType.'.'.$lCode.'",mand="'.$lMand.'",grp="app"'.$lSqlNam.',level="1",desc_en="'.$lName.'",desc_de="'.$lName.'"');
    }
    if ($lType == 'mainmenu') {
      CCor_Qry::exec('REPLACE INTO al_sys_lang SET code="tab.'.$lType.'.'.$lCode.'",mand='.esc($lMand).$lSqlVal);
      CCor_Qry::exec('REPLACE INTO al_sys_rig_usr SET code="tab.'.$lType.'.'.$lCode.'",mand="'.$lMand.'",grp="app"'.$lSqlNamMain.',level="1",desc_en="'.$lName.'",desc_de="'.$lName.'"');
    }
  }
}