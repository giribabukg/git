<?php

class CInc_Rol_His_Form_Base extends CHtm_Form {

  public function __construct($aAct, $aCaption, $aCancel = NULL) {
    parent::__construct($aAct, $aCaption, $aCancel);
    $this -> setAtt('class', 'tbl w700');
    $this -> addDef(fie('subject', 'Text', 'memo', NULL, array('class' => 'inp w600')));
  }
}