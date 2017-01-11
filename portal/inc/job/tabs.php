<?php
/**
 * Jobs: Tabs
 *
 *  ABSTRACT! Description
 *
 * @package    JOB
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 13183 $
 * @date $Date: 2016-03-31 11:14:51 +0200 (Thu, 31 Mar 2016) $
 * @author $Author: pdohmen $
 */
abstract class CInc_Job_Tabs extends CHtm_Tabs {

  protected $m2Act  = '';
  protected $mSrc   = '';
  protected $mJobId = '';

  public function __construct($aAct, $aJobId = 0, $aActiveTab = 'job') {
    $aActiveTab = (empty($aActiveTab)) ? 'job' : $aActiveTab;

    parent::__construct($aActiveTab);
    $this -> m2Act  = $aAct;
    $this -> mSrc   = str_replace('job-', '', $this -> m2Act);
    $this -> mJobId = $aJobId;

    $lCfg = CCor_Cfg::getInstance();

    $lJobMaskTabs = $lCfg -> get('job.mask.tabs');
    $this -> lDefaultTabs = $lCfg -> get($this -> m2Act.'.mask.tabs', $lJobMaskTabs);

    // bei Projekten gilt: Tab "Details" heisst "Briefing"
    $lTitel_det = $this -> mSrc == 'job-pro' ? lan('job.tab.brf') : lan('job.tab.det') ;

    if ($this -> inForm()) {
      $this -> addTab('job', lan('job.tab.job'), "javascript:Flow.Std.pagSel('job')");
      if (in_array('det', $this -> lDefaultTabs)) {
        $this -> addTab('det', $lTitel_det, "javascript:Flow.Std.pagSel('det')");
      }
    } else {
      $this -> addTab('job', lan('job.tab.job'), 'index.php?act='.$this -> m2Act.'.edt&amp;jobid='.$this -> mJobId);
      if (in_array('det', $this -> lDefaultTabs)) {
        $this -> addTab('det', $lTitel_det, 'index.php?act='.$this -> m2Act.'.edt&amp;jobid='.$this -> mJobId.'&amp;page=det');
      }
    }

    $lUsr = CCor_Usr::getInstance();

    if ($lUsr -> canRead('arc-not') && CCor_Cfg::get('arc.edt.fie.job', false) && $this -> mSrc != 'pro' && $this -> mSrc != 'sku') {
      $this -> addTab('not', lan('job.tab.not'), 'index.php?act='.$this -> m2Act.'.edt&amp;jobid='.$this -> mJobId.'&amp;page=not');
    }

    if ($lUsr -> canRead('job-his')) {
      $this -> addTab('his', lan('job-his.menu'), 'index.php?act='.$this -> m2Act.'-his&amp;jobid='.$this -> mJobId);
    }

    if ($lUsr -> canRead('job-apl')) {
      $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');
      if (isset($lCrp[$this -> mSrc])) {
        $lCrpId = $lCrp[$this -> mSrc];

        $lArrApl = CCor_Res::getByKey('apl', 'crp', $lCrpId);
        #$lSql = 'SELECT status FROM al_crp_status WHERE mand='.MID.' AND crp_id='.$lCrpId.' AND apl=1 LIMIT 0,1';
        #$lAplstatus = CCor_Qry::getInt($lSql);
        if (isset($lArrApl[1])) {//(0 < $lAplstatus) {
          $this -> addTab('apl', lan('job-apl.menu'), 'index.php?act=job-apl&amp;src='.$this -> mSrc.'&amp;jobid='.$this -> mJobId);
        }
      }
    }

    if (1 == $lUsr -> getAuthId() AND $lUsr -> canRead('job-flags') AND !(in_array($this -> m2Act, array('job-pro','job-sku')))) {
      $this -> addTab('flag', lan('job-flag.menu'), 'index.php?act=job-flag&amp;src='.$this -> mSrc.'&amp;jobid='.$this -> mJobId);
    }

    // SKUs in Projects
    if (($lUsr -> canRead('job-sku')) AND ($this -> m2Act == 'job-pro')) {
      $this -> addTab('sku', lan('sku-sur.menu'), 'index.php?act='.$this -> m2Act.'-sku&amp;jobid='.$this -> mJobId);
    }

    // SKUs in SKUS
    if (($lUsr -> canRead('job-sku')) AND ($this -> m2Act == 'job-sku')) {
      $this -> addTab('sur', lan('sku-pro.menu'), 'index.php?act='.$this -> m2Act.'-sur&amp;jobid='.$this -> mJobId);
      $this -> addTab('sub', lan('sku-job.menu'), 'index.php?act='.$this -> m2Act.'-sub&amp;jobid='.$this -> mJobId);
    }

    // SKUs in Jobs
    if (($lUsr -> canRead('job-sku')) AND !($this -> m2Act == 'job-sku') AND !($this -> m2Act == 'job-pro')) {
      $this -> addTab('sku', lan('sku-sub.menu'), 'index.php?act='.$this -> m2Act.'-sku&amp;jobid='.$this -> mJobId);
    }

    if (($lUsr -> canRead('job-pro-sub')) AND ($this -> m2Act == 'job-pro')) {
      $this -> addTab('sub', lan('job.tab.items'), 'index.php?act='.$this -> m2Act.'-sub&amp;jobid='.$this -> mJobId);
    }

    if ($lUsr -> canRead('job-fil')) {
      $this -> addTab('fil', lan('job-fil.menu'), 'index.php?act='.$this -> m2Act.'-fil&amp;jobid='.$this -> mJobId);
    }
    if ($lUsr -> canRead('job-cos')) {
      $this -> addTab('cos', lan('job-cos.menu'), 'index.php?act='.$this -> m2Act.'-cos&amp;jobid='.$this -> mJobId);
    }

    #START 22754 "Additional tabs"
    $lType = new CCor_Qry('SELECT type,subtype,code FROM al_tab_slave WHERE mand IN (0, '.MID.') AND type="job" AND subtype IN ("all", "'.$this -> mSrc.'") ORDER BY id;');
    $lMoreTabs = array();
    foreach ($lType as $lKey => $lValue) {
      if ($lUsr -> canRead('tab.job.'.$lValue['subtype'].'.'.$lValue['code'])) {
        $this -> addTab($lValue['code'], lan('tab.job.'.$lValue['subtype'].'.'.$lValue['code']), 'index.php?act=hom-tab-job&code='.$lValue['code'].'&src='.$lValue['subtype'].'&all='.$this -> mSrc.'&jobid='.$this -> mJobId);
        $lMoreTabs[] = $lValue['code'];
      }
    }
    #STOP 22754 "Additional tabs"

    //check critical path phrase type
    $lPhraseType = CCor_Res::extract('code', 'eve_phrase', 'crpmaster');
    $lTyp = $lPhraseType[$this -> mSrc];
    if(!empty($lTyp)) {
      if(($lTyp == 'product' && $lUsr -> canRead('job-cms.product')) || ($lTyp == 'job' && $lUsr -> canRead('job-cms'))) {
        $this -> addTab('cms', lan('job-'.$lTyp.'-cms.menu'), 'index.php?act=job-cms&amp;jobid='.$this -> mJobId.'&amp;src='.$this -> mSrc.'&amp;typ='.$lTyp);
        $lMoreTabs[] = 'cms';
      }
    }

    if ($lUsr -> canRead('questions-job') && CCor_Cfg::get('job.'.$this->mSrc.'.questionLists', false)) {
      $lNum = CQuestions_Mod::getCountUnanswered($this->mJobId);
      $this -> addTab('questions', lan('job-question.menu')." (".$lNum.")", 'index.php?act='.$this -> m2Act.'-questions&amp;jobid='.$this -> mJobId);
    }

    if (empty($this -> mJobId)) { // keine Tabs anzeigen
      $this -> setDisabled('his');
      $this -> setDisabled('sub');
      $this -> setDisabled('fil');
      $this -> setDisabled('cos');
      $this -> setDisabled('apl');
      $this -> setDisabled('flag');
      $this -> setDisabled('sur');
      $this -> setDisabled('sku');
      if (!empty($lMoreTabs)) {
        foreach ($lMoreTabs as $lCode) {
          $this -> setDisabled($lCode);
        }
      }
    }
  }

  protected function inForm() {
    return in_array($this -> mActiveTab, $this -> lDefaultTabs);
  }

}