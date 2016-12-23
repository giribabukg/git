<?php
class CInc_Arc_Rep_Form extends CArc_Form {

  protected $mJobId;

  public function __construct($aAct, $aJobId = 0, $aJob = NULL, $aPage = 'job') {
    
    $this -> mJobId = $aJobId;
    
    parent::__construct('rep', $aAct, $aPage, $this -> mJobId);

    $this -> mJobId = $aJobId;

    $lUsr = CCor_Usr::getInstance();

    $this -> mFla = 0;
    if (empty($aJob)) {
      if (!empty($this -> mJobId)) {
        $this -> mJob = new CArc_Rep_Dat();
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

    $this -> addPanel('act', lan('lib.actions'), '', 'job.act');
    $lUsr = CCor_Usr::getInstance();
    if ($lUsr -> canEdit('arc-not')) {
      $this -> addBtn('act', lan('lib.ok'), '', 'img/ico/16/ok.gif', 'submit', array('class' => 'btn w200'));
    }
    $this -> addBtn('act', lan('lib.cancel'), 'go("index.php?act=arc-rep")', 'img/ico/16/cancel.gif', 'button', array('class' => 'btn w200' ));

    $this -> mTabs = new CArc_Rep_Tabs($this -> mJobId, $aPage);

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