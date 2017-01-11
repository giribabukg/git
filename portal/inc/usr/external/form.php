<?php

class CInc_Usr_External_Form extends CHtm_Form
{

  public function __construct ($aAct, $aCaption, $aCancel = NULL)
  {
    parent::__construct($aAct, $aCaption, $aCancel);
    // $this -> mAltLan = TRUE; 
    $this -> setAtt('class', 'tbl w600');
    $this -> addDef(fie('anrede', 'anrede'));
    $this -> addDef(fie('firstname', 'firstname'));
    $this -> addDef(fie('lastname', 'lastname'));
  }

  public function load ($aId)
  {
    //next version
  }
}