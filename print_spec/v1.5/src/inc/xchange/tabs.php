<?php
class CInc_Xchange_Tabs extends CHtm_Tabs {

  public function __construct($aActive = 'pro') {
    parent::__construct($aActive);
    $this->addTab('pro', lan('xchange.tab.pro'), 'index.php?act=xchange');
    $this->addTab('job', lan('xchange.tab.job'), 'index.php?act=xchange.job');
    $this->addTab('log', lan('xchange.tab.log'), 'index.php?act=xchange-log');
  }

}