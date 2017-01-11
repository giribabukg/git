<?php
class CInc_Arc_Mis_Form extends CArc_Form {

  protected $mJobId;

  public function __construct($aAct, $aJobId = 0, $aJob = NULL, $aPage = 'job') {
    
    $this -> mJobId = $aJobId;
    
    parent::__construct('mis', $aAct, $aPage, $this -> mJobId);
    

    $this -> mFla = 0;
    if (empty($aJob)) {
      if (!empty($this -> mJobId)) {
        $this -> mJob = new CJob_Mis_Dat();
        $this -> mJob -> load($this -> mJobId);
        $this -> mFla = $this -> mJob -> getFlags();
      } else {
        $this -> mJob = new CCor_Dat();
      }
    } else {
      $this -> mJob = $aJob;
      $this -> mFla = $this -> mJob -> getFlags();
    }

    $this -> setPat('val.id', $this -> mJobId);

    $this -> addPanel('act', 'Actions', '', 'job.act');
    $lUsr = CCor_Usr::getInstance();
    if ($lUsr -> canEdit('arc-not')) {
      $this -> addBtn('act', lan('lib.ok'), '', 'img/ico/16/ok.gif', 'submit', array('class' => 'btn w200'));
    }
    $this -> addBtn('act', 'Back to List', 'go("index.php?act=arc-mis")', 'img/ico/16/cancel.gif', 'button', array('class' => 'btn w200' ));

    if (!empty($this -> mJobId)) {
      $this -> addPanel('stp', 'Status Changes', '', 'job.stp');
      $this -> mCrpId = 1;
    }

    $this -> mTabs = new CArc_Mis_Tabs($this -> mJobId, $aPage);

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