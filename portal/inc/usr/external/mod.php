<?php

class CInc_Usr_External_Mod extends CCor_Mod_Table
{

  public function __construct ()
  {
    parent::__construct('al_usr_tmp_external');
    $this -> addField(fie('id'));
    $this -> addField(fie('firstname'));
    $this -> addField(fie('lastname'));
    $this -> addField(fie('email'));
  }
}