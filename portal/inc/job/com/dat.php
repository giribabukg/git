<?php
class CInc_Job_Com_Dat extends CJob_Dat {

  protected $mSrc = 'com';

  public function __construct() {
    parent::__construct($this -> mSrc);
  }
  
}