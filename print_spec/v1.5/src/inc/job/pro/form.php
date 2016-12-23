<?php
/**
 * Jobs: Projekte - Formular
 *
 *  Description
 *
 * @package    JOB
 * @subpackage    Pro
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 8892 $
 * @date $Date: 2015-05-26 17:54:22 +0800 (Tue, 26 May 2015) $
 * @author $Author: ahanslik $
 */
class CInc_Job_Pro_Form extends CJob_Form {

  protected $mJobId;

  public function __construct($aJobId = 0, $aPage = 'job', $aAct = 'job-pro.sedt', $aJob = NULL) {
     $this -> mJobId = $aJobId;
     $this -> mShowCopyPanel = FALSE;

     parent::__construct('pro', $aAct, $aPage, $this -> mJobId);

     $lUsr = CCor_Usr::getInstance();
     if (empty($aJob)) {
    	if (empty($this -> mJobId)) {
    	  $this -> mJob = new CJob_Pro_Dat();
    	  $lTpl = new CCor_Tpl();
    	  $lTpl -> openProjectFile('job/pro/briefing.htm');
    	  $this -> mJob['project_briefing'] = $lTpl -> getContent();
    	} else {
    	  $this -> mJob = new CJob_Pro_Dat();
    	  $this -> mJob -> load($this -> mJobId);
    	  $this -> mFla = $this -> mJob -> getFlags();
    	}
    } else {
      $this -> mJob = $aJob;
    }

    $this -> setPat('val.id', $this -> mJobId);

    $this -> addPanel('act', lan('lib.actions'), '', 'job.act');
    if ($this -> mCanEdit) {
      $this -> addBtn('act', lan('lib.ok'), '', 'img/ico/16/ok.gif', 'submit', array('class' => 'btn w200'));
    }
    $this -> addBtn('act', lan('lib.cancel'), 'go("index.php?act=job-pro")', 'img/ico/16/cancel.gif', 'button', array('class' => 'btn w200'));
    if (!empty($this -> mJobId)) {
      $this -> addBtn('act', lan('lib.print'), 'pop("index.php?act=job-pro.prn&jobid='.$this -> mJobId.'")', 'img/ico/16/print.gif', 'button', array('class' => 'btn w200'));
    }
    if (!empty($this -> mJobId) AND $lUsr -> canInsert('email') ) {
      $this -> addBtn('act', lan('email.notif'), 'go("index.php?act=job-'.$this -> mSrc.'-his.newmail&jobid='.$this -> mJobId.'&src='.$this -> mSrc.'&frm=job")', 'img/ico/16/email.gif', 'button', array('class' => 'btn w200'));
    }

    $this -> addPanel('stp', lan('crp-stp.menu'), '', 'job.stp');
    $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');
    $this -> mCrpId = $lCrp[$this -> mSrc];
    if (!empty($this -> mJobId)) {
      $this -> addRoles();

      $lProCrp = CJob_Pro_Crp::getInstance(array($this -> mJobId)); // ist hier == ProId

//       $this -> mProStatus = $lProCrp -> getProStatus($this -> mJobId);
//       $this -> mJobsAmount = $lProCrp -> getProjectsAmount($this -> mJobId);
//       $this -> mSubAmount = $lProCrp -> getSubAmount($this -> mJobId);
//       $this -> mStatusClosedMax = $lProCrp -> getStatusClosedMax($this -> mJobId);
//       $this -> mStatusClosed = $lProCrp -> getStatusClosed($this -> mJobId);
//       $this -> mViewJoblist = $lProCrp -> getViewJoblist();
      $this -> m1StatusNoFromStep = $lProCrp -> get1StatusNoFromStep();
      $this -> mAutoProStatus = $lProCrp -> getAutoProStatus($this -> mJobId);
      if (empty($this -> mAutoProStatus)) {
        unset($this -> mAutoProStatus);
      }
//       if (empty($this -> mStatusClosedMax)) {
//         unset($this -> mStatusClosedMax);
//       }

      $this -> addStatusButtons();
    }

    $lPag = CHtm_Page::getInstance();
    $lPag -> addJsSrc('js/mce/tiny_mce.js');

    $this -> mTabs = new CJob_Pro_Tabs($this -> mJobId, $aPage);

    $this -> canStatusEdit();

    $lTemplate = $this -> getTemplates();
    foreach($lTemplate as $lSite => $lTempl) {
      $this -> addPage($lSite);
      foreach($lTempl as $lTpl => $lSrc) {
        if(!empty($lSrc)){
          $this -> addPart($lSite, $lTpl, $lSrc);
        } else  {
          $this -> addPart($lSite, $lTpl);
        }
      }
    }

    $this -> addPrintJs();
  }

  protected function addPrintJs() {
    $lJs = ''.LF;
    $lJs.= 'var gEd;'.LF;
    $lJs.= 'function richPrint(aEd) {'.LF;
    $lJs.= 'var lWin = window.open("index.php?act=job-pro.prn&jobid='.$this -> mJobId.'");'.LF;
    $lJs.= 'gEd = aEd;'.LF;
    $lJs.= '}'.LF;

    $lPag = CHtm_Page::getInstance();
    $lPag -> addJs($lJs);
  }
}