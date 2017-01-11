<?php
/**
 * Jobs: Projects - Subproject
 *
 *  Description
 *
 * @package    JOB
 * @subpackage    Projects
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 8848 $
 * @date $Date: 2015-05-20 18:06:06 +0200 (Wed, 20 May 2015) $
 * @author $Author: jwetherill $
 */
class CInc_Job_Pro_Arcsublist extends CJob_All_List {

  var $mSubJobs = array();
  var $mShowCsvExportButton = FALSE;
  var $mShowDeleteButton = FALSE;
  var $mShowCopyButton = FALSE;
  var $mShowHdr = FALSE;
  var $mShowSubHdr = FALSE;
  
  public function __construct($aProId, $aSubJobList = Array(), $aWithoutLimit = FALSE) {
   $this -> mJobId = $aProId;
   $this -> mSubJobList = $aSubJobList;
   $this -> mSrc = 'pro-sub';
   
   parent::__construct();
   
   $this -> mDdl = Array();
   $this -> mHideFil =  1;
   $this -> mHideSer =  1;
  }

  protected function getIterator() {
    $this -> mIte = new CCor_TblIte('al_job_arc_'.MID);
    $this -> mIte -> addCondition('jobid', 'in', $this -> mSubJobList);
    $this -> mIte -> addField('jobid');
  }
  
  protected function getColumns(){
    $lRet = Array();
    $lRet = CCor_Cfg::get('job-pro.subfields');
    return $lRet;
  }
    
  protected function addColumns() {
    $lUsr = CCor_Usr::getInstance();
    $lCol = $lUsr -> getPref('job-pro-sub.cols');
    if (empty($lCol)) return;

    $lArr = explode(',',$lCol);
    foreach ($lArr as $lFid) {
      if (isset($this -> mFie[$lFid])) {
        $lDef = $this -> mFie[$lFid];
        $this -> addField($lDef);
        $this -> onAddField($lDef);
      }
    }
  }

  protected function getLink() {
    $lSrc = $this -> getVal('src');
    $lJid = $this -> getVal('jobid');
    return 'index.php?act=arc-'.$lSrc.'.edt&amp;jobid='.$lJid;
  }
   
}