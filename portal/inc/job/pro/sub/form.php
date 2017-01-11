<?php
/**
 * Jobs: Projekte - Subprojekte - Formular
 *
 *  Description
 *
 * @package    JOB
 * @subpackage    Pro
 * @subsubpackage    Sub
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 102 $
 * @date $Date: 2012-05-31 11:17:19 +0200 (Thu, 31 May 2012) $
 * @author $Author: gemmans $
 */
class CInc_Job_Pro_Sub_Form extends CJob_Form {

  protected $mProId;
  #protected $mSubId;
  protected $mJobId;
  protected $m2Act;
  protected $mSrc;

  public function __construct($aAct, $aProId = 0, $aJob = NULL, $aPage = 'job', $aItemId = NULL) {
    $this -> m2Act = 'job-pro-sub';
    $this -> mSrc = 'pro-sub';
    parent::__construct($this -> mSrc, $aAct, $aPage);

    $this -> mJobId = 0;
    $this -> mProId = $aProId;

    $this -> mFla = 0;
      $this -> mJob = $aJob;
    
    $lKnr = $this -> mJob['net_knr'];
    if (empty($lKnr)) {
      $this -> mJob['net_knr'] = CCor_Cfg::get(MAND.'.def.knr');
    }
    
    $this -> setPat('val.id', $this -> mProId);
	     

   

    $this -> addPanel('act', lan('lib.actions'), '', 'job.act');
    if ($this -> mCanEdit) {
      $this -> addBtn('act', lan('lib.ok'), '', 'img/ico/16/ok.gif', 'submit', array('class' => 'btn w200' ));
    }
    $this -> addBtn('act', lan('lib.cancel'), 'go("index.php?act='.$this -> m2Act.'&jobid='.$this -> mProId.'")', 'img/ico/16/cancel.gif', 'button', array('class' => 'btn w200' ));
    $this -> mTabs = new CJob_Art_Tabs($this -> mJobId, $aPage);

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
    
    /**
     * JobId #23302 :Deadline changes and all other changes
     *  are taken into all task  after confirmation across click on button.
     */
    // Button Get Project Timing
    if (!is_null($aItemId)){
      $this -> addBtn('act', lan('lib.save.and.update'), 'setSubsUpdate(this,"1")', 'img/ico/16/clock_refresh.gif', 'button', array('class' => 'btn w200' ));
  }
    
  }

  public function setJob($aJob) {
    $this -> mJob = $aJob;
  }

  protected function onBeforeContent() {
    parent::onBeforeContent();
    $lSides = $this -> mJob['druckdurchgang'];
    $lDis = ($lSides > 1) ? 'block' : 'none';
    $this -> setPat('frm.co2', $lDis);
    $lDis = ($lSides > 2) ? 'block' : 'none';
    $this -> setPat('frm.co3', $lDis);
  }

}