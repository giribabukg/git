<?php
/**
 * Jobs: Projects - Subproject
 *
 *  Description
 *
 * @package    JOB
 * @subpackage    Projects
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 9257 $
 * @date $Date: 2015-06-23 15:32:51 +0200 (Tue, 23 Jun 2015) $
 * @author $Author: gemmans $
 */
class CInc_Job_Pro_Actsublist extends CJob_All_List {

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

    $this -> mHideFil = 1;
    $this -> mHideSer = 1;
  }

  protected function getIterator() {
    $lWriter = CCor_Cfg::get('job.writer.default', 'alink');
    if ('portal' == $lWriter) {
      $this -> mIte = new CCor_TblIte('all', TRUE);
      $this -> mIte -> addCnd('jobid IN ('.$this -> mSubJobList.')');
      $this -> mIte -> addField('jobid');
    } else {
      $this -> mIte = new CApi_Alink_Query_Getjoblist('', TRUE);
      $this -> mIte -> addCondition('jobid', 'in', $this -> mSubJobList);
    }
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

    $lArr = explode(',', $lCol);
    foreach ($lArr as $lFid) {
      if (isset($this -> mFie[$lFid])) {
        $lDef = $this -> mFie[$lFid];
        $this -> addField($lDef);
        $this -> onAddField($lDef);
      }
    }
  }
}