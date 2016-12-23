<?php
class CInc_Lan_Form extends CHtm_Form {

  public function __construct($aAct, $aCaption, $aAvailLang = array()) {
    parent::__construct($aAct, $aCaption);

    $this -> addDef(fie('code', lan('lib.code')));
    if ('lan.newlang' == $this -> mAct) {
      $this -> addDef(fie('new_name', lan('lan.new')));
    }
    $this -> mAvailLang = $aAvailLang;

    foreach ($this -> mAvailLang as $lLang => $lName) {
      $this -> addDef(fie('name_'.$lLang, lan('lan.'.$lLang)));
    }

  }

  public function load($aCode) {
    $lQry = new CCor_Qry('SELECT * FROM `al_sys_languages` WHERE `code`='.esc($aCode));
    if ($lRow = $lQry -> getAssoc()) {
      $this -> assignVal($lRow);
    } else {
      $this -> msg('Record not found', mtUser, mlError);
    }
  }

}