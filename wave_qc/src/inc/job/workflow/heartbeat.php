<?php
class CInc_Job_Workflow_Heartbeat extends CCor_Obj {
  
  public function __construct($aSrc, $aJobId, $aJob = null) {
    $this -> mSrc = (string)$aSrc;
    $this -> mJid = $aJobId;
    if (!is_null($aJob)) {
      $this -> mJob = $aJob;
    }
    $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');
    $this -> mCrpId = $lCrp[$this->mSrc];
    
    $this->mRecursive = true;
    $this->mUid = CCor_Usr::getAuthId();
  }
  
  public function setUid($aUid) {
    $this->mUid = $aUid;
  }
  
  protected function loadJob() {
    $lFac = new CJob_Fac($this -> mSrc, $this -> mJid);
    $this -> mJob = $lFac -> getDat($this -> mJid);
  }
  
  protected function getSteps() {
    $lRet = array();
    
    $lSta = intval($this -> mJob ['webstatus']);
    $lCrp = CCor_Res::extract('status', 'id', 'crp', $this -> mCrpId);
    if (!empty($lCrp) and isset($lCrp[$lSta])) {
      $lStatusId = $lCrp[$lSta];
    } else {
      return $lRet;
    }
    $lSql='SELECT s.id,s.cond,s.flags ';
    $lSql.='FROM al_crp_step s, al_crp_status c ';
    $lSql.='WHERE c.mand=' . MID . ' AND s.from_id=' . $lStatusId . ' ';
    $lSql.='AND s.to_id = c.id ';
    $lSql.='AND (s.flags & 512) ';
    $lSql.='ORDER BY c.display,s.id';
    
    
    $this -> dbg($lSql);
    
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lRet[] = $lRow -> toArray();
    }
    $this -> dump($lRet);
    return $lRet;
  }
  
  public function heartBeat() {
    if (empty($this -> mJob)) {
      $this -> loadJob();
    }
    $lSteps = $this -> getSteps();
    if (empty($lSteps)) {
      return false;
    }
    foreach ($lSteps as $lStep) {
      $lCondId = $lStep['cond'];
      if ($this -> conditionIsMet($lCondId)) {
        $this -> doStep($lStep['id']);
        if ($this->mRecursive) {
          $this -> mJob = null;
          return $this->heartBeat();
        }
        return true;
      }
    }
    return false;
  }
  
  protected function conditionIsMet($aCondId) {
    if (empty($aCondId)) {
      return true;
    }
    $lReg = new CInc_App_Condition_Registry();
    $lCnd = $lReg -> loadFromDb($aCondId);
    $lCnd -> setContext('data', $this -> mJob);
    $lRet = $lCnd -> isMet();
    if (! $lRet) {
      $this -> dbg('Cond ' . $aCondId . ' is not met');
    } else {
      $this -> dbg('Cond ' . $aCondId . ' is met');
    }
    return $lRet;
  }
  
  protected function doStep($aStepId) {
    $lClass = 'CJob_' . $this -> mSrc . '_Step';
    $lStep = new $lClass($this -> mJid, $this -> mJob);
    $lStep->setUid($this->mUid);
    
    $lHasStepped = $lStep -> doStep($aStepId);
    $this -> dump($aStepId, 'DOING STEP');
    return $lHasStepped;
  }
}