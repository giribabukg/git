<?php
/**
 * Archiv: AlleJobs - Liste
 *
 *  Description
 *
 * @package    ARC
 * @subpackage    All
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 9937 $
 * @date $Date: 2012-08-02 13:59:51 +0200 (Do, 02 Aug 2012) $
 * @author $Author: hoffmann $
 */
class CInc_Arc_All_List extends CArc_List {

  protected $mShowCopyButton = TRUE;
  protected $mSourceColumn = TRUE;
  protected $mShowDeleteButton = FALSE;
  protected $mSrc = 'all';
  protected $mWithoutLimit = FALSE; // Get Iterator without User Limit(x.lpp),e.g. by CSV Export

  public function __construct($aWithoutLimit = FALSE) {
    $this -> mWithoutLimit = $aWithoutLimit;

    $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');
    $this -> mCrpId = $lCrp['rep']; // default

    $this -> mUsr = CCor_Usr::getInstance();
    $this -> mSrcArr = CCor_Cfg::get('all-jobs'); // array('art', 'rep', 'sec', 'mis', 'adm', 'com', 'tra');


    // Show Csv Export Button
    $this -> mShowCsvExportButton = TRUE;

    parent::__construct('all', $this -> mCrpId);

    // show numbers at the beginning 
    $this -> addCtr();
    //  Show Copy Button
    $this -> addCopy();
    // Show Scr-Icon
    $this -> addColumn('src', '', TRUE, array('width' => 16));
    // Add the columns stored in userprefs
    $this -> addColumns();

    //$this -> addFilter('prod_art', 'Production Site');
    $this -> getFilterbyAlias('auftragsart'); //default 'per_prj_verantwortlich'

    $this -> mMaxLines = $this -> mIte -> getCount();
    $this -> addPanel('nav', $this -> getNavBar());
    $this -> addPanel('vie', $this -> getViewMenu());

  }

  protected function getIterator() {
    $this -> mIte = new CCor_TblIte('al_job_arc_'.MID, $this -> mWithoutLimit);
  }

  protected function getLink() {
    $lSrc = $this -> getVal('src');
    $lJid = $this -> getVal('jobid');
    return 'index.php?act=arc-'.$lSrc.'.edt&amp;jobid='.$lJid;
  }

  protected function getCriticalPaths() {
    $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');

    $this -> mCrp = array();
    foreach ($lCrp as $lKey => $lValue) {
      $this -> mCrp[$lKey] = CCor_Res::get('crp', $lCrp[$lKey]);
    }
  }

  protected function getTdSrc() {
    $lSrc = $this -> getCurVal();
    $lImg = (THEME === 'default' ? 'job-'.$lSrc : CApp_Crpimage::getColourForSrc($lSrc));
    $lRet = img('img/ico/16/'.$lImg.'.gif');
    return $this -> tdClass($this -> a($lRet), 'w16 ac');
  }

}