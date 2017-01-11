<?php
class CInc_Job_Pro_Header extends CJob_Header {

  public function __construct($aJob) {
    $lArr = CCor_Res::extract('code', 'id', 'crpmaster');
    $lCrp = $lArr['pro'];
    parent::__construct('pro', $aJob, $lCrp);
  }

  protected function getBookmarkMenu() {
    $lMen = new CJob_Bookmarks($this -> mSrc, $_REQUEST['jobid'], $this -> mJob['stichw']);
    return $lMen -> getContent();
  }

  protected function getRelatedMenu() {
    return '';
  }
}