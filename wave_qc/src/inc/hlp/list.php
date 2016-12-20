<?php
class CInc_Hlp_List extends CHtm_List {

  public function __construct($aItm) {
    parent::__construct('hlp');

    $this -> setAtt('width', '100%');
    $this -> mTitle = lan('lib.toc');

    $this -> addCtr();
    $lCol = & $this -> addColumn('subject', lan('lib.sbj'), true);
    $this -> getPrefs();

    $this -> mIte = new CCor_TblIte('al_sys_help');
    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);
    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);

    $this -> mMaxLines = $this -> mIte -> getCount();
    $this -> addPanel('nav', $this -> getNavBar());
  }

}