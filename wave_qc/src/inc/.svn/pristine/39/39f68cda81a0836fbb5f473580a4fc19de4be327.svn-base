<?php
class CInc_Job_Mis_Apl_Form extends CJob_Form {

  protected $mJobId;

  public function __construct($aAct, $aJobId = 0, $aJob = NULL, $aPage = 'job') {
    parent::__construct('mis', $aAct, $aPage);

    $this -> mJobId = $aJobId;
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

    $lKnr = $this -> mJob['net_knr'];
    if (empty($lKnr)) {
      $this -> mJob['net_knr'] = CCor_Cfg::get(MAND.'.def.knr');
    }

    $this -> setPat('val.id', $this -> mJobId);
    if (!empty($this -> mJobId)) {
      $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');
      $this -> mCrpId = $lCrp[$this -> mSrc];
      // Korrekturumlauf Status rausfinden
      $this -> lAplstatus = CCor_Qry::getInt('SELECT status FROM al_crp_status WHERE mand='.MID.' AND crp_id='.$this -> mCrpId.' AND apl=1' );
      $this -> addPanel('stp', lan('crp-stp.menu'), '', 'job.stp');
      $this -> addAplButtons($this -> lAplstatus);
    }
  }

  public function setJob($aJob) {
    $this -> mJob = $aJob;
  }

}