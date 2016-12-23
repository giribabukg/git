<?php
class CInc_Job_Fil_View_Menu extends CHtm_Vmenu {

  public function __construct($aKey) {
    parent::__construct(lan('job-fil-view'));

    $this -> setKey($aKey);

    $this -> addItem('fetch', 'index.php?act=job-fil-view.cat', lan('job-fil-view.cat'));
  }
}