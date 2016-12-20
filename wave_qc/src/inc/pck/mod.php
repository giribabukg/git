<?php
class CInc_Pck_Mod extends CCor_Mod_Table {

  public function __construct() {
    parent::__construct('al_pck_master');

    // 'id' and 'mand' are set automatically
    $this -> addField(fie('id'));
    $this -> addField(fie('mand'));

    // upcoming fields can be set by the user
    $lFields = array('domain', 'colcount', 'width', 'height');
    $this -> mAvailLang = CCor_Res::get('languages');
    foreach ($this -> mAvailLang as $lLang => $lName) {
      $lFields[] = 'description_'.$lLang;
    }
    foreach ($lFields as $lKey => $lValue) {
      if (!empty($_REQUEST['val'][$lValue])) {
        $this -> addField(fie($lValue));
      }
    }

  }

  protected function beforePost($aNew = FALSE) {
    if ($aNew) {
      $this -> setVal('mand', MID);
    }
  }

}