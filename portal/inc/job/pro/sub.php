<?php
/**
 * Jobs: Projects - Subproject
 *
 *  Description
 *
 * @package    JOB
 * @subpackage    Projects
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 3870 $
 * @date $Date: 2014-03-04 14:39:20 +0100 (Tue, 04 Mar 2014) $
 * @author $Author: ahanslik $
 */
class CInc_Job_Pro_Sub extends CCor_Ren {

  var $mSubJobs = array();
  var $mSubJobList = ''; // SubJobList
  var $mSubArcList = ''; // SubArchiveList
  var $mSubSkuList = ''; // SubSkuList

  public function __construct($aJobId) {
    $this -> mJobId = intval($aJobId);
    $this-> mUsr = CCor_Usr::getInstance();
    // Get Sub Joblist
    $this -> mSubJobList = $this -> getSubJobList();
    // SKU active?
    if (CCor_Licenses::get('job-sku') AND $this -> mUsr -> canRead('job-sku')) {
      $this -> mSubSkuList = $this -> getSubSkuList();
    }
  }

  protected function getCont() {
    // Get Subjoblist if it exists.
    if ($this -> mSubJobList != '' OR $this -> mSubSkuList != '') {
      $this -> mSubArcList = $this -> getSubArcList($this -> mSubJobList);
      $lVie = new CJob_Pro_Wrap($this -> mJobId, $this -> mSubJobList, $this -> mSubArcList, $this -> mSubSkuList);
      return $lVie -> getCont();
    } else {
      // No Sub job
      return lan('no.jobs.yet');
    }
  }

  /**
   * Get Subjoblis from Table 'al_job_sub_X'
   * @return Array Array of Subjoblist.
   */
  protected function getSubJobList() {
    // Get JobList from al_job_sub_X with Condition ProjectId.
    $lRet = '';
    $lSql = '';
    $lAllJobs = CCor_Cfg::get('all-jobs');
    foreach ($lAllJobs as $lJobs) {
      if (!empty($lJobs)) {
        $lSql.= ',jobid_'.$lJobs;
      }
    }
    $lSql = substr($lSql, 1);

    $lSql = 'SELECT '.$lSql.' FROM al_job_sub_'.intval(MID);
    $lSql.= ' WHERE pro_id='.$this -> mJobId;

    $lQry = new CCor_Qry($lSql);

    foreach ($lQry as $lRow) {
      foreach ($lRow as $lKey => $lVal) {
        if (!empty($lVal)) {
          $lSubJobsId[] = $lVal;
        }
      }
    }

    if (!empty($lSubJobsId)) {
      $lArrAl = array_map("esc", $lSubJobsId); //jedes Element wird ".mysql_escaped."
      $lSubJobList = implode(',', $lArrAl);
      $this -> dump($lSubJobList);
      $lRet = $lSubJobList;
    }

    return $lRet;
  }

  /**
   * Get Sub Archive Joblist
   * @return Array Array of Sub Archive Joblist.
   */
  protected function getSubArcList($aSubJobList) {
    $lRet = '';
    $lSql = 'Select jobid from al_job_arc_'.MID;
    $lSql.= ' WHERE jobid in ('.$aSubJobList.')';
    $lQry = new CCor_Qry($lSql);
    if ($lQry -> getAssoc()) {
      foreach ($lQry as $lRow) {
        $lRet.= '"'.$lRow['jobid'].'"';
        $lRet.= ',';
      }
      $lRet = substr($lRet, 0, -1);
      $this -> dbg('Archive Subjoblist:', $lRet);
    }

    return $lRet;
  }

  /**
   * Get Sub SKU Joblist
   * @return Array Array of Sub SKU Joblist
   */
  protected function getSubSkuList() {
    $lRet = '';
    $lSql = 'SELECT * FROM al_job_sku_'.MID.' p, al_job_sku_sur_'.MID.' q';
    $lSql.= ' WHERE p.id=q.sku_id AND q.pro_id='.$this -> mJobId;
    $lQry = new CCor_Qry($lSql);
    if ($lQry -> getAssoc()) {
      foreach ($lQry as $lRow) {
        $lRet.= '"'.$lRow['id'].'"';
        $lRet.= ',';
      }

      $lRet = substr($lRet, 0, -1);
    }

    return $lRet;
  }
}