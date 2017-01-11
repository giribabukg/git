<?php
class CInc_Job_Sec_List extends CJob_List {

  protected $mAva = fsSec;
  protected $mSrc = 'sec';
  protected $mShowCopyButton = TRUE;
  protected $mWithoutLimit = FALSE; // Get job list without lines per page limitation (lpp)

  public function __construct($aWithoutLimit = FALSE) {
    $this -> mWithoutLimit = $aWithoutLimit;

    $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');
    // error_log('.....CInc_Job_Sec_List.....lCrp.....'.var_export($lCrp,true)."\n",3,'logggg.txt');
    // error_log('.....CInc_Job_Sec_List.....$this -> mSrc.....'.var_export($this -> mSrc,true)."\n",3,'logggg.txt');
    $this -> mCrpId = $lCrp[$this -> mSrc];

    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canDelete('job-'.$this -> mSrc)) {
      $this -> mShowDeleteButton = FALSE;
    }

    $this -> mShowCsvExportButton = TRUE;

    parent::__construct('job-'.$this -> mSrc, $this -> mCrpId);
    $this -> mImg = 'img/ico/40/'.LAN.'/'.$this -> mMod.'.gif';

    $this -> mIdField = 'jobid';

    $this -> lAplstatus = array();
    // error_log('.....CInc_Job_Sec_List.....$this -> mCrpId.....'.var_export($this -> mCrpId,true)."\n",3,'logggg.txt');
    $lQry = new CCor_Qry('SELECT status FROM al_crp_status WHERE mand='.MID.' AND crp_id='.$this -> mCrpId.' AND apl=1');
    foreach ($lQry as $lRow) {
      $this -> lAplstatus[] = $lRow['status'];
    }
    // error_log('.....CInc_Job_Sec_List.....$this -> lAplstatus.....'.var_export($this -> lAplstatus,true)."\n",3,'logggg.txt');

    $this -> addFilter('webstatus', lan('lib.status'), $this -> mCrpId);
    if(!CCor_Cfg::get('job-fil.combined', FALSE)) {
      $this -> addFilter('flags', lan('lib.flags'));
    }
    $this -> getFilterbyAlias(); // default: per_prj_verantwortlich

    // error_log('.....CInc_Job_Sec_List.....$this -> mMod.....'.var_export($this -> mMod,true)."\n",3,'logggg.txt');
    // error_log('.....CInc_Job_Sec_List.....$lUsr -> canInsert($this -> mMod).....'.var_export($lUsr -> canInsert($this -> mMod),true)."\n",3,'logggg.txt');

    if ($lUsr -> canInsert($this -> mMod)) {
      $this -> addBtn(lan($this -> mMod.'.new'), 'go("index.php?act='.$this -> mMod.'.new")', '<i class="ico-w16 ico-w16-plus"></i>');
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
    $this -> mIte -> addDef($aDef);
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