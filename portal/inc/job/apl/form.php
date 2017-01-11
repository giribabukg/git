<?php
class CInc_Job_Apl_Form extends CJob_Form {

  //--- Anzeige der APL-Buttons unterhalb der Liste
  public function __construct($aSrc, $aAct, $aJobId = 0, $aJob = NULL, $aPage = 'job') {
    parent::__construct($aSrc, $aAct, $aPage);

    $this -> mJobId = $aJobId;
    $this -> mFla = 0;
    if (empty($aJob)) {
      if (!empty($this -> mJobId)) {

        $lClass = 'CJob_'.$this -> mSrcCnt.'_Dat';
        $this -> mJob = new $lClass();
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

    //--- Anzeige der APL-Buttons unterhalb der Liste
    if (!empty($this -> mJobId)) {
      $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');
      $this -> mCrpId = $lCrp[$this -> mSrc];
      // Korrekturumlauf Status rausfinden
      $lSql = 'SELECT status FROM al_crp_status WHERE mand='.MID.' AND crp_id='.$this -> mCrpId.' AND apl=1';
      $lQry = new CCor_Qry($lSql);
      $this -> lAplstatus = $lQry -> getImplode('status');
      //$this -> lAplstatus = CCor_Qry::getInt($lSql); //Bug: es kann unterschiedliche APLs geben
      $this -> addPanel('stp', lan('crp-stp.menu'), '', 'job.stp');
      $this -> addAplButtons($this -> lAplstatus);
    }
    #echo '<pre>---'.get_class().'---';var_dump($aSrc, $aAct, $aJobId, $aJob, $aPage, $this -> mJobForm,'#############');echo '</pre>';
  }

}