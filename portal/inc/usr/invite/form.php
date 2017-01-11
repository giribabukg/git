<?php
class CInc_Usr_Invite_Form extends CHtm_Form {

  public function __construct($aAct, $aCaption, $aCancel = NULL) {
    parent::__construct($aAct, $aCaption, $aCancel);

    $this -> setAtt('class', 'tbl w800');

    $this -> addDef(fie('to_emails', lan('lib.email'), 'string','', array('class' => 'inp w500')));

  }
}