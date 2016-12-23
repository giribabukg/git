<?php
class CInc_Cor_Usr_Annotate extends CCor_Obj {

  protected $mId;

  public function __construct($aUid, $aJob = array(), $aJobId = '', $aSrc = '') {
    $this -> mId = intval($aUid);
    
    
    if (empty($aJob)) {
      $lFac = new CJob_Fac($aSrc, $aJobId);
      $this -> mJob = $lFac -> getDat();
    } else $this -> mJob = $aJob;
    
    $this -> mSrc = (empty($aSrc)) ? $this->getVal('src') : $aSrc;
    
    $this -> mJobId = $this->getVal('jobid');
  }
  
  
  public function getVal($aKey, $aDefault = '') {
    if (isset($this -> mJob[$aKey])) {
      return $this -> mJob[$aKey];
    } else {
      return $aDefault;
    }
  }

  public function canAnnotate() {
    $lWebStatus = $this->getVal('webstatus');
    
    // Is it allowed to annotate in this webstatus?
    $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');
    $lCrpId = $lCrp[$this -> mSrc];
    $lStatus = CCor_Res::extract('status', 'flags', 'crp', $lCrpId);
    if (bitset($lStatus[$lWebStatus], 16)) {
      return false;
    }

    
    // Is user active in an APL now?
    $lApl = new CInc_App_Apl_Loop($this -> mSrc, $this -> mJobId);
    $lActiveUser = $lApl -> isUserActiveNow($this -> mId);
    if ($lActiveUser) return true;
    
    // Is this user in a special role that allow to annotate?
    $lPassiveRole = $this->isUserInPassiveRole($this -> mId);
    if ($lPassiveRole) return true;
    
    $lUsr = CCor_Usr::getInstance();
    if ($lUsr->canInsert('viewer-annot')) return true;
    
    return false;
  }
  
  protected function isUserInPassiveRole($aUid) {
    $lUid = $aUid;
    $lBingo = false;
    $lPassiveAplRoles = implode(',', CCor_Cfg::get('passive-apl-roles'));
    if (empty($lPassiveAplRoles)) return false;
    $lSql = 'SELECT '.$lPassiveAplRoles.' FROM al_job_shadow_'.MID.' WHERE jobid='.esc($this -> mJobId);
    $lQry = new CCor_Qry($lSql);
    foreach (CCor_Cfg::get('passive-apl-roles') as $lKey => $lVal) {
      foreach ($lQry as $lRow) {
        if ($lRow[$lVal] == $lUid) {
          $lBingo = true;
          break;
        }
      }
    }
    
    return $lBingo;
  }
  
}