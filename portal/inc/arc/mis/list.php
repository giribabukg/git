<?php
class CInc_Arc_Mis_List extends CArc_List {

  protected $mSrc = 'mis';
  protected $mWithoutLimit = FALSE; // Get Iterator without User Limit(x.lpp),e.g. by CSV Export

  public function __construct($aWithoutLimit = FALSE) {
    $this -> mWithoutLimit = $aWithoutLimit;
    // Show Csv Export Button
    $this -> mShowCsvExportButton = TRUE;
    
    parent::__construct($this -> mSrc);
    $this -> mImg = 'img/ico/40/'.LAN.'/job-'.$this -> mSrc.'.gif';
  
    $this -> addCtr();
    $this -> addCopy();
    $this -> addColumns();
    //$this -> addFilter('prod_art', 'Production Site');
    $this -> getFilterbyAlias(); //default 'per_prj_verantwortlich'
    
    $this -> mMaxLines = $this -> mIte -> getCount();
    $this -> addPanel('nav', $this -> getNavBar());
    $this -> addPanel('vie', $this -> getViewMenu());
  }

  protected function getIterator() {
    $this -> mIte = new CCor_TblIte('al_job_arc_'.MID, $this -> mWithoutLimit);
    $this -> mIte -> addCnd('src="mis"');
  }

}