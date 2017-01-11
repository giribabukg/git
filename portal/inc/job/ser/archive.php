<?php
/**
 * Jobs: Search - Archive
 *
 *  Description
 *
 * @package    JOB
 * @subpackage    Ser
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 11065 $
 * @date $Date: 2015-10-28 18:13:28 +0100 (Wed, 28 Oct 2015) $
 * @author $Author: jwetherill $
 */
class CInc_Job_Ser_Archive extends CHtm_List {

  protected $mUsr;

  public function __construct($aFil = array()) {

    parent::__construct('job-ser');

    $this -> mShowHdr = FALSE;
    $this -> mShowSubHdr = FALSE;
    $this -> mStdLnk = '';
    $this -> setAtt('width', '100%');
    $this -> getCriticalPaths();
    $this -> mFie = CCor_Res::get('fie');
    $this -> mUsr = CCor_Usr::getInstance();

    $this -> mDefs = CCor_Res::getByKey('alias', 'fie');
    $this -> mCnd = new CCor_Cond();

    $this -> getPrefs('job-ser');

    $this -> getIterator();
    $this -> addCtr();

    $lDef = $this -> mDefs['src'];
    $this -> onAddField($lDef);

    $this -> addColumn('src', '', TRUE, array('width' => 16));
    $this -> addColumns();

    unset($this -> mSer['anf']);
    unset($this -> mSer['job']);
    unset($this -> mSer['arc']);

    $this -> mFil = $aFil;

    $this -> addFilterConditions();
    $this -> addSearchConditions();
    $this -> addSrcConditions();

    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);
    $this -> mMaxLines = $this -> mIte -> getCount();

    $this -> mAllFlags = CCor_Res::get('fla');
    $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');

    $this -> lAplstatus = array();
    foreach ($lCrp as $lCode => $lId) {
      $lSql = 'SELECT status FROM al_crp_status WHERE mand='.MID.' AND crp_id='.$lId.' AND apl=1';
      $lQry = new CCor_Qry($lSql);
      foreach($lQry as $lRow){
        if (!empty($lRow['status'])){
          $this -> lAplstatus[$lCode][] = $lRow['status'];
        }
      }

      if (isset($this -> mDdl[$lCode])) {
        $lDdl = $this -> mDdl[$lCode];
        foreach ($lDdl as $lddl) {
          $this -> requireAlias($lddl);
        }
        $this -> requireAlias('src');
      }
    }

    $this -> mShowFlaWithFlags = CCor_Cfg::get('show.flawithflags', TRUE);
  }

  protected function requireAlias($aAlias) {
    if (!isset($this -> mDefs[$aAlias])) {
      $this -> dbg('Field '.$aAlias. ' not defined', mlWarn);
      return;
    }

    $lDef = $this -> mDefs[$aAlias];
    $this -> mIte -> addDef($lDef);
  }

  protected function getCriticalPaths() {
    $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');
    $this -> mCrp = array();

    $lAllJobs = CCor_Cfg::get('menu-aktivejobs');
    foreach ($lAllJobs as $lJobs) {
      if ('all' != substr($lJobs, 4, 3)) {
        if (isset($lCrp[ltrim($lJobs, 'job-')])) {
        $this -> mCrp[ltrim($lJobs, 'job-')] = CCor_Res::get('crp', $lCrp[ltrim($lJobs, 'job-')]);
      }
    }
  }
  }

  protected function addCondition($aAlias, $aOp, $aValue) {
    if (!isset($this -> mDefs[$aAlias])) {
      $this -> dbg('Unknown Field '.$aAlias, mlWarn);
      return;
    }
    $this -> mIte -> addCondition($aAlias, $aOp, $aValue);
  }

  protected function getIterator() {
    $this -> mIte = new CCor_TblIte('al_job_arc_'.MID);
    $this -> mIte -> addField('jobid');
  }

  protected function addFilterConditions() {
    if (empty($this -> mFil)) return;

    foreach ($this -> mFil as $lKey => $lValue) {
      if (!empty($lValue)) {
        if (is_array($lValue) AND $lKey == "webstatus") {
          $lStates = "";

          foreach ($lValue as $lWebstatus => $foo) {
            if ($lWebstatus == 0) {
              break;
            } else {
              $lStates.= '"'.$lWebstatus.'",';
            }
          }

          if (!empty($lStates)) {
            $lStates = substr($lStates, 0, -1);

            $this -> addCondition('webstatus', 'in', $lStates);
          }
        } elseif (is_array($lValue) AND $lKey == "flags") {
          $lStates = "";

          foreach ($lValue as $lKey => $lValue) {
            if($lKey == 0){
              break;
            } else {
              $lStates.= "((flags & ".$lKey.") = ".$lKey.") OR ";
            }
          }
          $lStates = (!empty($lStates)) ? substr($lStates, 0, strlen($lStates) - 4) : '';

          if (!empty($lStates)) {
            $lJobIds = '';
            $lSQL = 'SELECT jobid FROM al_job_shadow_'.MID.' WHERE '.$lStates.';';
            $lQry = new CCor_Qry($lSQL);
            foreach ($lQry as $lRow) {
              $lJobId = trim($lRow['jobid']);
              if (!empty($lJobId)) {
                $lJobIds.= '"'.$lJobId.'",';
              }
            }
            $lJobIds = strip($lJobIds);

            if (!empty($lJobIds)) {
              $this -> mIte -> addCondition('jobid', 'IN', $lJobIds);
            } else {
              $this -> mIte -> addCondition('jobid', '=', 'NOJOBSFOUND');
            }
          }
        } else {
          $this -> addCondition($lKey, '=', $lValue);
        }
      }
    }
  }

  protected function addSearchConditions() {
    if (empty($this -> mSer)) return;
    foreach ($this -> mSer as $lAli => $lVal) {
      if (empty($lVal)) continue;
      if (!isset($this -> mDefs[$lAli])) {
        $this -> dbg('Unknown Field '.$lAli, mlWarn);
        continue;
      }
      $lDef = $this -> mDefs[$lAli];
      $lCnd = $this -> mCnd -> convert($lAli, $lVal, $lDef['typ']);
      if ($lCnd) {
        foreach($lCnd as $lItm) {
          if ('flags' == $lItm['field']) continue;
          $this -> mIte -> addCondition($lItm['field'], $lItm['op'], $lItm['value']);
        }
      }
    }
  }

  protected function addColumns() {
    $lUsr = CCor_Usr::getInstance();
    $lCol = explode(',', $lUsr -> getPref($this -> mMod.'.cols'));

    foreach ($lCol as $lFid) {
      if (isset($this -> mFie[$lFid])) {
        $lDef = $this -> mFie[$lFid];
        $this -> addField($lDef);
        $this -> onAddField($lDef);
      }
    }
  }

  protected function onAddField($aDef) {
    $this -> mIte -> addDef($aDef);
  }

  protected function loadFlags() {
    $lArr = array_keys($this -> mIte);
    if (empty($lArr)) return;
    $lSql = 'SELECT jobid,flags FROM al_job_shadow_'.intval(MID).' WHERE jobid IN (';
    foreach ($lArr as $lJid) {
      $lSql.= esc($lJid).',';
    }
    $lSql = strip($lSql).')';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $this -> mIte[$lRow['jobid']]['flags'] = $lRow['flags'];
    }
  }

  protected function getTdSrc() {
    $lSrc = $this -> getCurVal();
    $lImg = (THEME === 'default' ? 'job-'.$lSrc : CApp_Crpimage::getColourForSrc($lSrc));
    $lRet = img('img/ico/16/'.$lImg.'.gif');
    return $this -> tdClass($this -> a($lRet), 'w16');
  }

  protected function getTdJobnr() {
    $lJid = $this -> getVal('jobid');
    return $this -> tda(jid($lJid));
  }

  protected function getTdFlags() {
    $lCur = $this -> getCurInt();
    $lFla = CCor_Res::extract('val', 'name_'.LAN, 'jfl');
    $lRet = '';
    foreach ($lFla as $lKey => $lVal) {
      if (bitSet($lCur, $lKey)) {
        $lRet.= img('img/jfl/'.$lKey.'.gif', array('data-toggle' => 'tooltip', 'data-tooltip-head' => lan('lib.flags'), 'data-tooltip-body' => $lVal));
      }
    }
    return $this -> tdClass($lRet, 'w16');
  }

  protected function getTdWebstatus() {
    return $this -> tda('Archive');
  }

  protected function getLink() {
    $lSrc = $this -> getVal('src');
    $lJid = $this -> getVal('jobid');
    return 'index.php?act=arc-'.$lSrc.'.edt&amp;jobid='.$lJid;
  }

  /**
   * Set Jobtype Condition to Itterator
   * Before show Jobs, ask if user has a Right for Jobtype.
   */
  protected function addSrcConditions() {
    $lAvaSrc = Array();
    $lSrcCnd ='';
    $lAvaSrc = CCor_Cfg::get('menu-aktivejobs');
    if (!empty($lAvaSrc)){
      foreach ($lAvaSrc as $lRow)  {
        if ($this -> mUsr -> canRead($lRow)) {
          $lSrcCnd.= '"'.substr($lRow,4).'",';
        }
      }

      if ($lSrcCnd != ''){
        $lSrcCnd = substr($lSrcCnd,0,-1);
        $this -> mIte -> addCondition('src','in',$lSrcCnd);
      }else{
        $this -> mIte -> addCondition('src','=',$lSrcCnd);
      }
    }else {
      return;
    }
  }
}