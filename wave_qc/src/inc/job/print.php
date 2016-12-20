<?php
/**
 * Jobs: Print
 *
 *  Description
 *
 * @package    JOB
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 2582 $
 * @date $Date: 2013-10-25 02:00:19 +0800 (Fri, 25 Oct 2013) $
 * @author $Author: ahanslik $
 */
class CInc_Job_Print extends CJob_Form {

  public function __construct($aSrc, $aJob, $aPage = 'job', $aJobId = '') {
    parent::__construct($aSrc, '', $aPage, $aJobId);

    $this -> mJob = $aJob;
    $this -> mJobId = $aJobId;

    $this -> mFla = $this -> mJob -> getFlags();
    $this -> mCanEdit = TRUE;
    $this -> mState = fsPrint;

    $lTemplate = $this -> getPrintTemplates();
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