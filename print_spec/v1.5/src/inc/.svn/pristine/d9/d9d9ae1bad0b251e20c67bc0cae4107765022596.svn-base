<?php
/**
 * Wec Data Object
 *
 * Insert a new action into the queue
 *
 * @package    Application
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 */
class CInc_App_Wec extends CCor_Obj {

  /**
   * Jobtype
   * @var string
   */
  public $mSrc;

  /**
   * JobId
   * @var string
   */
  public $mJobId;

  /**
   *
   * @param $aSrc string Jobtype
   * @param $aJobId string JobId
   *
   */
  public function __construct($aSrc, $aJobId) {
    $this -> mSrc = $aSrc;
    $this -> mJobId = $aJobId;
  }

  /**
   * @return string Webcenter ProjectId
   */
  public function getWebcenterId() {
    $lRet = '';
    // Look first in Archive
    $lSql = 'SELECT COUNT(*) FROM al_job_arc_'.MID.' WHERE jobid='.esc($this->mJobId);
    $lCnt = CCor_Qry::getInt($lSql);
    if (0 < $lCnt) { // Job is archive. Get WebcenterId from ArchiveTabelle.
      $lClass = 'CArc_Dat';
      $this -> dbg('Webcenter Projekt Id from ArchiveTabelle');
      $lJob = new $lClass($this -> mSrc);
      $lJob -> load($this->mJobId);
      $lRet = $lJob['wec_prj_id'];
      return $lRet;
    }
    $lDefFie = CCor_Res::extract('alias', 'native', 'fie');
    
    $lSql = 'SELECT wec_prj_id FROM al_job_shadow_'.MID.' ';
    $lSql.= 'WHERE src='.esc($this->mSrc).' ';
    $lSql.= 'AND jobid='.esc($this->mJobId).' ';
    $lRet = CCor_Qry::getStr($lSql);
    if ($lRet) {
      return $lRet;
    }

    // Job is active. Get WebcenterId from Networker/Mop/Wave DB
    $lFac = new CJob_Fac($this->mSrc, $this->mJobId);
    $lJob = $lFac -> getDat();
    $lRet = $lJob['wec_prj_id'];
    return $lRet;
  }

  public function createWebcenterProject() {
    $lA = stripos($this -> mJobId, 'A');
    if ($lA === FALSE) {
      $lWecPrjName = intval($this -> mJobId);
    } else {
      $lWecPrjName = 'A'.intval(substr($this -> mJobId, $lA + 1));
    }

    $lWecTpl = CApi_Wec_WebcenterTemplate::getTemplate($this -> mJobId);

    $lWec = new CApi_Wec_Client();
    $lWec -> loadConfig();

    $lQry = new CApi_Wec_Query_Createproject($lWec);
    $lWecPrjId = $lQry -> create($lWecPrjName, $lWecTpl);
    if ($lWecPrjId) {
      $lJobFac = new CJob_Fac($this -> mSrc);
      $lJobMod = $lJobFac -> getMod($this -> mJobId);
      $lArr = array('wec_prj_id' => $lWecPrjId);
      $lJobMod -> forceUpdate($lArr);
    }

    return $lWecPrjId;
  }
}