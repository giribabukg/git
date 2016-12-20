<?php
class CInc_Arc_Tabs extends CHtm_Tabs {

  public function __construct($aMod, $aJobId = 0, $aActiveTab = 'job') {
    parent::__construct($aActiveTab);

    $this -> mMod = $aMod;
    $this -> mSrc = str_replace('arc-','',$this->mMod);
    $this -> mJid = $aJobId;
    $this -> mUsr = CCor_Usr::getInstance();

    $lCfg = CCor_Cfg::getInstance();
    $lJobMaskTabs = $lCfg -> get('job.mask.tabs');
    $this -> lDefaultTabs = $lCfg -> get('job-'.$this -> mSrc.'.mask.tabs', $lJobMaskTabs);

    if ($this -> inForm()) {
      foreach ($this -> lDefaultTabs as $lRow) {
        $this -> addTab($lRow, lan('job.tab.'.$lRow), "javascript:Flow.Std.pagSel('".$lRow."')");
      }
    } else {
      $this -> addTab('job', lan('job.tab.job'), 'index.php?act='.$this -> mMod.'.edt&amp;jobid='.$this -> mJid);
      if (in_array('det',$this -> lDefaultTabs)) {
        $this -> addTab('det', lan('job.tab.det'), 'index.php?act='.$this -> mMod.'.edt&amp;jobid='.$this -> mJid.'&amp;page=det');
      }
    }

    if ($this -> mUsr -> canRead('arc-not') && CCor_Cfg::get('arc.edt.fie.arc', false)) {
      $this -> addTab('not', lan('job.tab.not'), 'index.php?act='.$this -> mMod.'.edt&amp;jobid='.$this -> mJid.'&amp;page=not');
    }

    if ($this -> mUsr -> canRead('job-his')) {
      $this -> addTab('his', lan('job-his.menu'), 'index.php?act='.$this -> mMod.'-his&amp;jobid='.$this -> mJid);
    }
    
    if ($this -> mMod == 'arc-pro') {
      $this -> addTab('sub', lan('job.tab.items'), 'index.php?act='.$this -> mMod.'-sub&amp;jobid='.$this -> mJid);
    }

    // APL Tab
    if ($this -> mUsr -> canRead('job-apl')) {
      $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');
      if (isset($lCrp[$this -> mSrc])) {
        $lCrpId = $lCrp[$this -> mSrc];

        $lArrApl = CCor_Res::getByKey('apl', 'crp', $lCrpId);
        #$lSql = 'SELECT status FROM al_crp_status WHERE mand='.MID.' AND crp_id='.$lCrpId.' AND apl=1 LIMIT 0,1';
        #$lAplstatus = CCor_Qry::getInt($lSql);
        if (isset($lArrApl[1])) {//(0 < $lAplstatus) {
          $this -> addTab('apl', lan('job-apl.menu'), 'index.php?act=job-apl&amp;src='.$this -> mSrc.'&amp;jobid='.$this -> mJid.'&amp;mod=arc');
        }
      }
    }
    // End APL Tab
    
    if ($this -> mUsr -> canRead('job-fil')) {
      $this -> addTab('fil', lan('job-fil.menu'), 'index.php?act='.$this -> mMod.'-fil&amp;jobid='.$this -> mJid);
    }
    
    #START 22754 "Additional tabs"
    $lType = new CCor_Qry('SELECT type,subtype,code FROM al_tab_slave WHERE mand IN (0, '.MID.') AND type="job" AND subtype IN ("all", "'.$this -> mSrc.'") ORDER BY id;');
    $lMoreTabs = array();
    foreach ($lType as $lKey => $lValue) {
      if ($this -> mUsr -> canRead('tab.job.'.$lValue['subtype'].'.'.$lValue['code'])) {
        $this -> addTab($lValue['code'], lan('tab.job.'.$lValue['subtype'].'.'.$lValue['code']), 'index.php?act=hom-tab-job&code='.$lValue['code'].'&src='.$lValue['subtype'].'&all='.$this -> mSrc.'&jobid='.$this -> mJid.'&arc=TRUE');
        $lMoreTabs[] = $lValue['code'];
      }
    }
    #STOP 22754 "Additional tabs"

    if (empty($this -> mJid)) {
      $this -> setDisabled('his');
      $this -> setDisabled('sub');
      $this -> setDisabled('fil');
      $this -> setDisabled('cos');
      $this -> setDisabled('dpm');
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