<?php
class CInc_Job_Com_List extends CJob_List {

  protected $mAva = fsCom;
  protected $mSrc = 'com';
  protected $mWithoutLimit = FALSE; // Get job list without lines per page limitation (lpp)

  public function __construct($aWithoutLimit = FALSE, $aAnyUsrID = NULL) {
    $this -> mWithoutLimit = $aWithoutLimit;

    $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');
    $this -> mCrpId = $lCrp[$this -> mSrc];

    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canDelete('job-'.$this -> mSrc)) {
      $this -> mShowDeleteButton = FALSE;
    }

    $this -> mShowCsvExportButton = TRUE;

    parent::__construct('job-'.$this -> mSrc, $this -> mCrpId, '', $aAnyUsrID);
    $this -> mImg = 'img/ico/40/'.LAN.'/job-'.$this -> mSrc.'.gif';

    $this -> mIdField = 'jobid';

    $this -> lAplstatus = array();
    $lQry = new CCor_Qry('SELECT status FROM al_crp_status WHERE mand='.MID.' AND crp_id='.$this -> mCrpId.' AND apl=1');
    foreach ($lQry as $lRow){
      $this -> lAplstatus[] = $lRow['status'];
    }

    $this -> addFilter('webstatus', lan('lib.status'), $this -> mCrpId);
    if(!CCor_Cfg::get('job-fil.combined', FALSE)) {
      $this -> addFilter('flags', lan('lib.flags'));
    }
    $this -> getFilterbyAlias(); // default: per_prj_verantwortlich

    if ($lUsr -> canInsert($this -> mMod)) {
      $this -> addBtn(lan($this -> mMod.'.new'), 'go("index.php?act='.$this -> mMod.'.new")', 'img/ico/16/plus.gif');
    }

    $this -> addSort('last_status_change');
    $this -> addButton(lan('lib.sort'), $this -> getButtonMenu($this -> mMod));

    $this -> requireAlias('webstatus');
    $this -> requireAlias('status');
    $this -> requireAlias('src');

    $this -> mMaxLines = $this -> mIte -> getCount();
    $this -> addPanel('nav', $this -> getNavBar());
    $this -> addPanel('vie', $this -> getViewMenu());
  }

  protected function onAddField($aDef) {
    $lAlink = CCor_Cfg::get('all-jobs_ALINK');

    if (in_array($this -> mSrc, $lAlink)) {
      $this -> mIte -> addDef($aDef);
    }
  }

  protected function onBeforeContent() {
    $lRet = parent::onBeforeContent();

    $this -> mIte = $this -> mIte -> getArray('jobid');

    // Sort Joblist in Master-Varinat Bundle form.
    // If there is a Master Job and his Variants, show first master-job and after the variant-job
    if ($this -> mColumnIsMasterDefined) {
      // Masr-Variant Bundle activated
      $this -> mIte = $this -> sortByMasterVariant($this -> mIte);
    }

    $this -> loadFlags();
    $this -> loadApl();
    return $lRet;
  }
}