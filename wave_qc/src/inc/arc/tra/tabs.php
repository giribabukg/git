<?php
class CInc_Arc_Tra_Tabs extends CArc_Tabs {

  public function __construct($aJobId = 0, $aActiveTab = 'job') {
    parent::__construct('arc-tra', $aJobId , $aActiveTab);
  }

}