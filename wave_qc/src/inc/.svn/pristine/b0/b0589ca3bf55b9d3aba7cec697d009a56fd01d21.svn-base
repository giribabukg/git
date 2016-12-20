<?php
class CInc_Job_Sku_Form extends CJob_Form {

  protected $mJobId;

  public function __construct($aJobId = 0, $aPage = 'job', $aAct = 'job-sku.sedt', $aJob = NULL) {
     $this -> mJobId = $aJobId;
     $this -> mShowCopyPanel = FALSE;
     parent::__construct('sku', $aAct, $aPage, $this -> mJobId);
     
     $lUsr = CCor_Usr::getInstance();
     if (empty($aJob)) {
    	if (empty($this -> mJobId)) {
    	  $this -> mJob = new CJob_Sku_Dat();
    	  $lTpl = new CCor_Tpl();
    	  $lTpl -> openProjectFile('job/sku/briefing.htm');
    	  $this -> mJob['project_briefing'] = $lTpl -> getContent();
    	} else {
    	  $this -> mJob = new CJob_Sku_Dat();
    	  $this -> mJob -> load($this -> mJobId);
    	}
    } else {
      $this -> mJob = $aJob;
    }

    $this -> setPat('val.id', $this -> mJobId);

    $this -> addPanel('act', lan('lib.actions'), '', 'job.act');
    if ($this -> mCanEdit) {
      $this -> addBtn('act', lan('lib.ok'), '', 'img/ico/16/ok.gif', 'submit', array('class' => 'btn w200' ));
    }
    $this -> addBtn('act', lan('lib.cancel'), 'go("index.php?act=job-sku")', 'img/ico/16/cancel.gif', 'button', array('class' => 'btn w200' ));
    if ($this -> canAssignSkuSur()) {
      $this -> addBtn('act', lan('job.assignskusur'), 'go("index.php?act=job-'.$this -> mSrc.'.assignskusur&jobid='.$this -> mJobId.'")', 'img/ico/16/next-hi.gif', 'button', array('class' => 'btn w200'));
    }
    if (!empty($this -> mJobId) AND $lUsr->canInsert('email') ) {
      $this -> addBtn('act', lan('email.notif'), 'go("index.php?act=job-'.$this -> mSrc.'-his.newmail&jobid='.$this -> mJobId.'&src='.$this -> mSrc.'&frm=job")', 'img/ico/16/plus.gif', 'button', array('class' => 'btn w200' ));
    }
    $this -> addPanel('stp', lan('crp-stp.menu'), '', 'job.stp');
    $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');
    $this -> mCrpId = $lCrp[$this -> mSrc];
    if (!empty($this -> mJobId)) {
      $this -> addRoles();
      $this -> addStatusButtons();
    }

    $lPag = CHtm_Page::getInstance();
    $lPag -> addJsSrc('js/mce/tiny_mce.js');

    $this -> mTabs = new CJob_Sku_Tabs($this -> mJobId, $aPage);

    // Can User Edit Project by CRP STATUS
    // if NOT set $this->mCanEdit = FALSE
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
    $lJs = '';
    $lJs.= 'var gEd;'.LF;
    $lJs.= 'function richPrint(aEd) {'.LF;
    $lJs.= 'var lWin = window.open("index.php?act=job-sku.prn&jobid='.$this -> mJobId.'");'.LF;
    $lJs.= 'gEd = aEd;'.LF;
    $lJs.= '}';
    $lPag = CHtm_Page::getInstance();
    $lPag -> addJs($lJs);
  }

    
}