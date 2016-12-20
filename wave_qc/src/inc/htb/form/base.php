<?php
class CInc_Htb_Form_Base extends CHtm_Form {

  public function __construct($aAct, $aCaption, $aCancel = NULL) {
    parent::__construct($aAct, $aCaption, $aCancel);

    $this -> addDef(fie('domain', lan('lib.code')));
    $this -> addDef(fie('description', lan('lib.description')));
  }
}