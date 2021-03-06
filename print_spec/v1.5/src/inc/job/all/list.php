<?php
/**
 * Jobs: AlleJobs - Liste
 *
 *  Description
 *
 * @package    JOB
 * @subpackage    All
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 14217 $
 * @date $Date: 2016-05-30 02:51:15 +0800 (Mon, 30 May 2016) $
 * @author $Author: ahanslik $
 */
class CInc_Job_All_List extends CJob_List {

  protected $mShowCopyButton = TRUE;
  protected $mSourceColumn = TRUE;
  protected $mShowDeleteButton = TRUE;
  protected $mSrc = 'all';
  protected $mWithoutLimit = FALSE; // Get job list without lines per page limitation (lpp)

  public function __construct($aWithoutLimit = FALSE, $aAnyUsrID = NULL) {
    $this -> mWithoutLimit = $aWithoutLimit;

    $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');
    $this -> mCrpId = isset($lCrp['rep']) ? $lCrp['rep'] : $lCrp['art']; // default

    $this -> mUsr = CCor_Usr::getInstance();
    $this -> mSrcArr = CCor_Cfg::get('all-jobs'); // array('art', 'rep', 'sec', 'mis', 'adm', 'com', 'tra');

    $lCanDeleteSrcArr = Array();
    foreach ($this -> mSrcArr as $lKey) {
      if ($this -> mUsr -> canDelete('job-'.$lKey)) {
        $lCanDeleteSrcArr[] = $lKey;
      }
    }

    if (empty($lCanDeleteSrcArr)) {
      $this -> mShowDeleteButton = FALSE;
    }

    // Show Csv Export Button
    $this -> mShowCsvExportButton = TRUE;

    parent::__construct('job-'.$this -> mSrc, $this -> mCrpId, '', $aAnyUsrID);

    $this -> mIdField = 'jobid';
    $this -> mCapCls = 'cap2';

    $this -> lAplstatus = array();
    foreach ($lCrp as $lCode => $lId) {
      $lSql = 'SELECT status FROM al_crp_status WHERE mand='.MID.' AND crp_id='.$lId.' AND apl=1';
      $lQry = new CCor_Qry($lSql);
      foreach($lQry as $lRow){
        if (!empty($lRow['status'])){
          $this -> lAplstatus[$lCode][] = $lRow['status'];
        }
      }

    // needed for highlighting late deadlines
    if (isset($this -> mDdl[$lCode])) {
      $lDdl = $this -> mDdl[$lCode];
      foreach ($lDdl as $lddl) {
        $this -> requireAlias($lddl);
      }
        $this -> requireAlias('src');
      }
    }

    $this -> addCtr();

    $lDef = $this -> mDefs['src'];
    $this -> onAddField($lDef);

    $this -> addColumn('src', '', TRUE, array('width' => 16));

    // Show Copy Button
    $this-> mCopyJob = $this -> mUsr -> canCopyJob($this -> mSrcArr);
    if ($this -> mShowCopyButton){
      if (!empty($this-> mCopyJob)){
        $this -> addColumn('cpy', '', FALSE, array('width' => 16));
      }
    }
    // End Show Copy Button

     //$lDef = $this -> mFie[1]; // $lDef wurde schon geladen.Übrigens hat jede Mandant vershiedene Id für Feld 'src'.
    $lDef['name_'.LAN] = '';
    $this -> addField($lDef);
    $this -> onAddField($lDef);

    $this -> addColumns();

    $this -> addFilter('webstatus', lan('lib.status'), $this -> mCrpId);
    if(!CCor_Cfg::get('job-fil.combined', FALSE)) {
      $this -> addFilter('flags', lan('lib.flags'));
    }
    $this -> getFilterbyAlias(); // default: per_prj_verantwortlich

    // Show New Job Button
    if ($this -> mUsr -> canInsert($this -> mMod)) {
      $this -> addButton(lan('job.new'), $this -> getMenu());
    }
    // End Show New Job Button

    $this -> requireAlias('webstatus');
    $this -> requireAlias('status');
    $this -> requireAlias('src');

    if ($this -> mMasterVariantBundleActiv = CCor_Cfg::get('master.varaiant.bundle', FALSE)){
      if ($this -> mColumnIsMasterDefined = $this-> isFieldIsMasterDefined()){
        $this -> requireAlias('master_id');
      }
    }

    $this -> mMaxLines = $this -> mIte -> getCount();
    $this -> addPanel('nav', $this -> getNavBar());
    $this -> addPanel('vie', $this -> getViewMenu());

    $this -> getCriticalPaths();
  }

  protected function getIterator() {
    if (CCor_Cfg::get('job.writer.default') == 'portal') {
      $lMinStatus = CCor_TblIte::getInt('SELECT MIN(status) FROM al_crp_master AS master, al_crp_status AS status WHERE master.id=status.crp_id AND master.mand='.MID);

      $this -> mIte = new CCor_TblIte('all', $this -> mWithoutLimit);
      $this -> mIte -> addField('jobid');
      $this -> addUserConditions();
      $this -> addSrcConditions();
      $this -> addCondition('webstatus', '>=', $lMinStatus);
    }
    else {
      $this -> mIte = new CApi_Alink_Query_Getjoblist($this -> mSrc, $this -> mWithoutLimit);
      $this -> mIte -> addField('jobnr', 'jobnr');
      $this -> addUserConditions();
      $this -> addSrcConditions();
    }
  }

  protected function onAddField($aDef) {
    $this -> mIte -> addDef($aDef);
  }

  protected function getLink() {
    $lSrc = $this -> getVal('src');
    $lJid = $this -> getVal('jobid');
    return 'index.php?act=job-'.$lSrc.'.edt&amp;jobid='.$lJid;
  }

  protected function getDelLink() {
    $lSrc = $this -> getVal('src');
    $lId = $this -> getVal($this -> mIdField);
    $lRet = $this -> mStdLink.'.del&amp;src='.$lSrc.'&amp;id='.$lId;
    return $lRet;
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

  protected function getTdWebstatus() {
    $lVal = $this -> getCurInt();
    $lSrc = $this -> getVal('src');
    if (isset($this -> mCrp[$lSrc])) {
      return $this -> getExtWebstatus($lVal, $this -> mCrp[$lSrc], $lSrc);
    }
    $lDis = $lVal / 10;
    $lPath = CApp_Crpimage::getSrcPath($lSrc, 'img/crp/'.$lDis.'b.gif');
    $lRet = img($lPath, array('style' => 'margin-right:1px'));
    return $this -> tda($lRet);
  }

  protected function getExtWebstatus($aState, $aCrp, $aSrc) {
    $lDisplay = CCor_Cfg::get('status.display', 'progressbar');

    $lVal = $aState;
    $lNam = '[unknown]';
    $lRet = '';

    foreach ($aCrp as $lRow) {
      if (($lDisplay == 'progressbar' && $lVal >= $lRow['status']) OR ($lDisplay == 'activeonly' && $lVal == $lRow['status'])) {
        $lPath = CApp_Crpimage::getSrcPath($aSrc, 'img/crp/'.$lRow['display'].'b.gif');
        $lRet.= img($lPath, array('style' => 'margin-right:1px'));
        $lNam = $lRow['name_'.LAN];
      } else if (($lDisplay == 'progressbar' && $lVal < $lRow['status']) OR ($lDisplay == 'activeonly' && $lVal != $lRow['status'])) {
        $lPath = CApp_Crpimage::getSrcPath($aSrc, 'img/crp/'.$lRow['display'].'l.gif');
        $lRet.= img($lPath, array('style' => 'margin-right:1px'));
      }
    }
    $lRet.= NB.htm($lNam);
    return $this -> tda($lRet);
  }

  protected function onBeforeContent() {
    $lRet = parent::onBeforeContent();

    $this -> mIte = $this -> mIte -> getArray('jobid');

    // Sort Joblist in Master-Varinat Bundle form.
    // If there is a Master Job and his Variants, show first master-job and after the variant-job
    if ($this -> mColumnIsMasterDefined) {
      $this -> mIte = $this -> sortByMasterVariant($this -> mIte);
    }

    $this -> loadApl();
    $this -> loadFlags();
    return $lRet;
  }

  protected function getTdApl() {
    $lSrc = $this -> getVal('src');
    if (empty($lSrc)) {
      return $this -> tda();
    }

    $lSta = $this -> getInt('webstatus');
    $lAplstatus = array();
    // If Jobtype has NO Approval Loop, dont take in Array.
    if (isset($this -> lAplstatus[$lSrc])){
      $lAplstatus = $this -> lAplstatus[$lSrc];
    }
    // if Webstatus is not Approval Loop Status
    if (!in_array($lSta, $lAplstatus)) {
      return $this -> tda();
    }

    $lLoopId = $this -> getInt('loop_id');
    if (empty($lLoopId)) {
      return $this -> tda();
    }

    $lRet = CApp_Apl_Loop::getAplCommitList($lLoopId, $this -> mCurLnk);

    return $this -> tdClass($lRet, 'w16 ac');
  }

  /**
   * Set Jobtype Condition to Itterator
   * Before show Jobs, ask if user has a Right for Jobtype.
   */

  protected function addSrcConditions() {
    $lAvaSrc = Array();
    $lSrcCnd = '';
    $lAvaSrc = CCor_Cfg::get('menu-aktivejobs');
    if (!empty($lAvaSrc)){
      foreach ($lAvaSrc as $lRow)  {
        if ($this -> mUsr -> canRead($lRow)) {
          $lSrcCnd.= '"'.substr($lRow,4).'",';
        }
      }
      if ($lSrcCnd != ''){
        $lSrcCnd = substr($lSrcCnd,0,-1);
        $this -> mIte -> addCondition('src','IN',$lSrcCnd);
      }
    }else {
      return;
    }
  }

  public function getMenu() {
    $lJobTypes = CCor_Cfg::get('all-jobs');
    $lUsr = CCor_Usr::getInstance();

    $lMen = new CHtm_Menu('Button');
    $lMen -> addTh2(lan('job.types'));

    foreach ($lJobTypes as $lKey => $lValue) {
      $lJobType = 'job-'.$lValue; // job-art instead of art
      if ($lUsr -> canInsert($lJobType)) {
        $lColour = CApp_Crpimage::getColourForSrc($lValue);
        $lSrc = (THEME === 'default' ? 'ico/16/'.$lJobType.'.gif' : 'ico/16/'.$lColour.'.gif');
        $lMen -> addItem('index.php?act=job-'.$lValue.'.new', lan($lJobType.'.menu'), $lSrc);
      }
    }

    $lImg = 'img/ico/16/plus.gif';
    $lLnk = (THEME === 'default' ? "javascript:Flow.Std.popMain('".$lMen -> mDivId."')" : "javascript:Flow.Std.popMen('".$lMen -> mDivId."')");
    $lBtn = btn(lan('job.new'), $lLnk, $lImg, 'button', array('class' => 'btn w130', 'id' => $lMen -> mLnkId));
    $lBtn .= $lMen -> getMenuDiv();

    return $lBtn;
  }
}