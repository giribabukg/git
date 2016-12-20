<?php
class CInc_Svc_Cache extends CSvc_Base {

  protected function doExecute() {
    $lSub = $this -> getPar('sub', '');
    $lIdx = $this -> getPar('mode', 'old');

    $lMode = ('all' == $lIdx) ? Zend_Cache::CLEANING_MODE_ALL : Zend_Cache::CLEANING_MODE_OLD;
    $lCache = CCor_Cache::getInstance($lSub);
    $lCache -> clean($lMode);

    return true;
  }
}