<?php
class CInc_Job_Related extends CCor_Obj {

  public function __construct($aSrc, $aJobId, $aMid = null) {
    $this->mSrc = $aSrc;
    $this->mJid = $aJobId;
    $this->mMid = (empty($aMid)) ? MID : intval($aMid);
  }

  public function getRelated() {
    $this->mRel = array();
    $this->addProject();
    $this->addRelated();
    return $this->mRel;
  }

  protected function add($aSrc, $aJid, $aKeyword = '') {
    if (($aSrc == $this->mSrc) && ($aJid == $this->mJid)) return;
    $lRow['src'] = $aSrc;
    $lRow['jid'] = $aJid;
    $lRow['caption'] = $aKeyword;
    $this->mRel[] = $lRow;
  }

  protected function addProject() {
    if ($this->mSrc == 'pro') return;

    $lSql = 'SELECT pro_id FROM al_job_sub_'.$this->mMid;
    $lSql.= ' WHERE jobid_'.$this->mSrc.'='.esc($this->mJid);
    $lPid = CCor_Qry::getInt($lSql);
    if (!empty($lPid)) {
      $this->add('pro', $lPid, 'Project '.$lPid);
    }
  }

  protected function addRelated($aMenu) {
    if ($this->mSrc == 'pro') return;
    $lJid = $this->mJid;
    $lSql = 'SELECT master_id FROM al_job_sub_'.MID.' WHERE jobid_'.$this->mSrc.'='.esc($lJid);
    $lMas = CCor_Qry::getInt($lSql);
    if (empty($lMas)) return;

    $lArr = array();
    while (!empty($lMas)) {
      $lSql = 'SELECT master_id,jobid,src FROM al_job_sub_'.MID.' WHERE id='.$lMas;
      $lQry = new CCor_Qry($lSql);
      $lRow = $lQry->getDat();
      if (!empty($lRow['jobid'])) {
        $lSub['src'] = $lRow['src'];
        $lSub['jid'] = $lRow['jobid'];
        $lArr[] = $lSub;
      }
      $lMas = $lRow['master_id'];
    }
    if (empty($lArr)) return;

    $lArr = array_reverse($lArr);

    $lSql = 'SELECT src,jobid,stichw FROM al_job_shadow_'.MID;
    $lSql.= ' WHERE jobid IN (';
    foreach ($lArr as $lSub) {
      $lId = $lSub['jid'];
      $lSql.= esc($lId).',';
    }
    $lSql = strip($lSql);
    $lSql.= ')';
    $lJobs = array();
    $lQry->query($lSql);
    foreach ($lQry as $lRow) {
      $lJobs[$lRow['jobid']] = $lRow;
    }
    foreach ($lArr as $lSub) {
      $lCurJid  = $lSub['jid'];
      if (!isset($lJobs[$lCurJid])) continue;
      $lRow = $lJobs[$lCurJid];
      $lSrcName = lan('job-'.$lRow['src'].'.menu');
      $lName = strtoupper($lSrcName).' '.jid($lCurJid);
      $lName = cat($lName, $lRow['stichw'], ': ');
      $this->add($lRow['src'], $lCurJid, $lName);
    }
  }

}