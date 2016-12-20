<?php
class CInc_Hom_Tab_Job_Form_Arcform extends CCust_Arc_Form {

  protected $mJobId;
  protected $mSrc;
  protected $mCode;

  public function __construct($aJobId, $aSrc, $aCode) {

    $this -> mJobId = $aJobId;
    $this -> mSrc = $aSrc;
    $this -> mCode = $aCode;
    $lUsr = CCor_Usr::getInstance();
    parent::__construct($this -> mSrc, 'arc-'.$this -> mSrc.'.sedt', $this -> mCode, $this -> mJobId);
   
    // get templates for the current job type
    $FormTpl = new CJob_Formtpl();
    $this -> mTemplates = $FormTpl -> mArchivTemplates;
   
    $this -> mFla = 0;
    if (empty($aJob)) {
      if (!empty($this -> mJobId)) {
        $this -> mJob = new CArc_Dat($this -> mSrc);
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
    
    if ($lUsr -> canEdit('arc-not')) {
      $this -> addBtn('act', lan('lib.ok'), '', 'img/ico/16/ok.gif', 'submit', array('class' => 'btn w200'));
    }
    $this -> addBtn('act', lan('lib.cancel'), 'go("index.php?act=arc-'.$this -> mSrc.'")', 'img/ico/16/cancel.gif', 'button', array('class' => 'btn w200' ));

    $lClassName = 'CJob_'.ucfirst($this -> mSrc).'_Tabs';
    $this -> mTabs = new $lClassName($this -> mJobId, $this -> mCode);

    $lTemplate = $this -> getTemplates();
    foreach ($lTemplate as $lSite => $lTempl) {
      $this -> addPage($lSite);
      foreach ($lTempl as $lTpl => $lSrc) {
        if (!empty($lSrc)) {
          $this -> addPart($lSite, $lTpl, $lSrc);
        } else {
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