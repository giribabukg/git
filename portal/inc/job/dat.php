<?php
/**
 * Jobs: Data
 *
 *  ABSTRACT! Description
 *
 * @package    JOB
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 14568 $
 * @date $Date: 2016-06-15 18:16:59 +0200 (Wed, 15 Jun 2016) $
 * @author $Author: ahajali $
 */
abstract class CInc_Job_Dat extends CCor_Dat {

  protected $mSrc = '';
  protected $mJobId = '';

  public function __construct($aSrc = '') {
    if (!empty($aSrc)) {
      $this -> mSrc = $aSrc;
    }
    $this -> mCheckMethod = true;
  }

  public function getSrc() {
    return $this -> mSrc;
  }

  public function getId() {
    return $this -> mJobId;
  }

  public function setnewJobId($aJobId) { //used in job/step.php > doStep
    $lOld = $this->mVal['jobid'];
    $this -> mJobId = $aJobId;
    $this -> mVal['jobid'] = $aJobId;

    if (isset($this->mVal['jobnr'])) {
      if (substr($lOld, 0, 1) == 'A') {
        $lJobNr = intval(substr($aJobId,1));
        $this->mVal['jobnr'] = $lJobNr;
      } 
    }
  }

  public function load($aId) {
    if ($this -> mSrc == 'pro') return $this -> doLoad($aId);
    if (CCor_Cfg::get('job.writer.default') == 'portal') {
      return $this->doLoadPdb($aId);
    } else return $this -> doLoad($aId);
  }

  protected function doLoad($aId) {
    $lQry = new CApi_Alink_Query_Getjobdetails($aId, $this -> getSrc());
    $lFie = CCor_Res::get('fie');
    foreach ($lFie as $lDef) {
      if (!empty($lDef['native'])) {
        $lQry -> addDef($lDef);
      }
    }
    $lRes = $lQry -> query();
    if (!$lRes) return FALSE;
    $this -> assign($lQry -> getDat());
    $this -> mJobId = $aId;
    return $aId;
  }
  
  protected function doLoadPdb($aJobId) {
    $this -> mIte = new CCor_TblIte('al_job_'.$this -> mSrc.'_'.MID);
    $lIdColumnName = (!CCor_Cfg::get('view.projekt.joblist.tree',true) AND $this -> mSrc == 'sub') ? 'id' : 'jobid';
    $this -> mIte -> addCnd($lIdColumnName.' = '.esc($aJobId));
    $this -> mIte -> addCnd('webstatus >= 10');
    $this -> mIte -> getIterator();
    $lRes = $this -> mIte -> getDat();
    if (!$lRes) return FALSE;
    $this -> assign($lRes);
    $this -> mJobId = $aJobId;
    return $aJobId;
  }

  public function redirectUrl() {
    $lRet = '';
    $lSrc = $this -> doGet('src');
    #echo '<pre>---dat.php---';var_dump($lSrc,'#############');echo '</pre>';
    if ($lSrc != $this -> mSrc && !empty($lSrc)) {
      $lRet = 'index.php?act=job-'.$lSrc.'.edt&jobid='.$this -> mJobId;
    }
    return $lRet;
  }

  public function getFlags() {
    $lRet = 0;
    $lSql = 'SELECT flags FROM al_job_shadow_'.intval(MID).' WHERE jobid='.esc($this -> mJobId);
    $lQry = new CCor_Qry($lSql);
    if ($lRow = $lQry -> getDat()) {
      $lRet = intval($lRow['flags']);
    }
    return $lRet;
  }

  public function updateShadow() {
    if (!empty($this -> mJobId)) {
      CJob_Utl_Shadow::reflectData($this -> mVal);
      CJob_Utl_Shadow::reflectReportData($this->mVal);
    }
  }

  public function addRecentJob() {
    if (!empty($this -> mJobId)) {
      $lUsr = CCor_Usr::getInstance();
      $lUsr -> addRecentJob($this -> mSrc, $this -> mJobId, $this -> doGet('stichw'));
    }
  }

}