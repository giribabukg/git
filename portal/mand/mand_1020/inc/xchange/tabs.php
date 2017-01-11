<?php
class CXchange_Tabs extends CCust_Xchange_Tabs {

  public function __construct($aActive = 'pro') {
    $this->addTab('job', 'Mass Import', 'index.php?act=xchange.job');
    $this->addTab('log', 'File Log', 'index.php?act=xchange-log');
  }

}