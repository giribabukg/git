<?php
class CInc_Arc_Pro_Header extends CJob_Header {
  
  public function __construct($aJob) {
    parent::__construct('pro', $aJob, 4);
  }
  
  protected function getBookmarkMenu() {
    $lMen = new CJob_Bookmarks($this -> mSrc, $_REQUEST['jobid'], $this -> mJob['project']);
    return $lMen -> getContent();
  }
  
}