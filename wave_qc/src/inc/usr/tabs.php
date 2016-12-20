<?php
class CInc_Usr_Tabs extends CHtm_Tabs {

  public function __construct($aUid, $aActiveTab = 'dat') {
    parent::__construct($aActiveTab);
    $this -> mUid = $aUid;
    $this -> addTab('dat', 'Data', 'index.php?act=usr.edt&amp;id='.$this -> mUid);
    $this -> addTab('mem', 'Member', 'index.php?act=usr-mem&amp;id='.$this -> mUid);
    $this -> addTab('his', 'History', 'index.php?act=usr-his&amp;id='.$this -> mUid);
    $this -> addLink('Back to list', 'index.php?act=usr');
  }
}