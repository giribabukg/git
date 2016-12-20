<?php
class CInc_Arc_Pro_Form extends CJob_Form {

  protected $mJobId;

  public function __construct($aJobId = 0, $aPage = 'job', $aAct = 'job-pro.sedt') {
    
    $this -> mShowCopyPanel = FALSE;
    
    $this -> mJobId = intval($aJobId);
    
    parent::__construct('pro', $aAct, $aPage, $this -> mJobId);
    
    $this -> mCanEdit = FALSE;

    

    if (!empty($this -> mJobId)) {
      $this -> mJob = new CJob_Pro_Dat();
      $this -> mJob -> load($this -> mJobId);
    }
    $this -> setPat('val.id', $this -> mJobId);

    $this -> addPanel('act', 'Actions', '', 'job.act');
    #$this -> addBtn('act', lan('lib.ok'), '', 'img/ico/16/ok.gif', 'submit', array('class' => 'btn w200'));
    $this -> addBtn('act', lan('lib.cancel'), 'go("index.php?act=arc-pro")', 'img/ico/16/cancel.gif', 'button', array('class' => 'btn w200' ));
    $this -> addPanel('stp', 'Status Changes', '', 'job.stp');
    $this -> mCrpId = 4;

    $this -> mTabs = new CArc_Pro_Tabs($this -> mJobId, $aPage);

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
   
   
   /* $this -> addPage('job');
    $this -> addPart('job', 'ids');
    $this -> addPart('job', 'reg');
    $this -> addPart('job', 'ddl');
    $this -> addPage('det');
    $this -> addPart('det', 'brf');*/
  }


}