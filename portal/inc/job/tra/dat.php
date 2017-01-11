<?php
class CInc_Job_Tra_Dat extends CJob_Dat {

  protected $mSrc = 'tra';

  public function __construct() {
    parent::__construct($this -> mSrc);
  }
  
}