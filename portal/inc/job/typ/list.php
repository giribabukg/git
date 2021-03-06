<?php
/**
 * ToDo: Description
 *
 * @author Geoffrey Emmans <emmans@qbf.de>
 * @package JOB
 * @subpackage TYP
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 14215 $
 * @date $Date: 2016-05-29 20:39:13 +0200 (Sun, 29 May 2016) $
 * @author $Author: ahanslik $
 */
class CInc_Job_Typ_List extends CJob_List {

  protected $mSrc = 'typ';
  protected $mWithoutLimit = FALSE; // Get job list without lines per page limitation (lpp)

  public function __construct($aSrc, $aCrp = 0, $aWithoutLimit = FALSE, $aAnyUsrID = NULL) {
    $this -> mSrc = $aSrc;
    $this -> mWithoutLimit = $aWithoutLimit;
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canDelete('job-'.$this -> mSrc)) {
      $this -> mShowDeleteButton = FALSE;
    }

    parent::__construct('job-'.$this -> mSrc, $aCrp, '', $aAnyUsrID);
    $this -> mImg = 'img/ico/40/'.LAN.'/job-'.$this -> mSrc.'.gif';

    $this -> mIdField = 'jobid';

    $this -> lAplstatus = array();
    $lQry = new CCor_Qry('SELECT status FROM al_crp_status WHERE mand='.MID.' AND crp_id='.$this -> getCrpId().' AND apl=1');
    foreach ($lQry as $lRow){
      $this -> lAplstatus[] = $lRow['status'];
    }

    $this -> addFilter('webstatus', lan('lib.status'), $this -> getCrpId());
    if(!CCor_Cfg::get('job-fil.combined', FALSE)) {
      $this -> addFilter('flags', lan('lib.flags'));
    }
    $this -> getFilterbyAlias(); // default: per_prj_verantwortlich

    if ($lUsr -> canInsert($this -> mMod)) {
      $this -> addBtn(lan($this -> mMod.'.new'), 'go("index.php?act=".$this -> mMod.".new")', 'img/ico/16/plus.gif');
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

    $this -> loadFlags();
    $this -> loadApl();
    return $lRet;
  }
}