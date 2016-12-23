<?php
class CInc_Job_His extends CCor_Obj {

  protected $mSrc;
  protected $mJobId;

  public function __construct($aSrc, $aJobId) {
    $this -> mSrc = $aSrc;
    $this -> mJobId = $aJobId;
    $this -> mUid = CCor_Usr::getAuthId();
    $this -> mDate = date('Y-m-d H:i:s');
  }

  public function setDate($aDate) {
    $this -> mDate = $aDate;
  }

  public function setUser($aUserId) {
    $this -> mUid = intval($aUserId);
  }

  public function add($aType, $aSubject, $aMsg = '', $aAdd = '', $aStepId = '', $aFrom = '', $aTo = '', $aSignatureId = 0) {
    if (is_array($aAdd)) {
      if (isset($aAdd['annotationsall'])) {
        $aMsg = trim($aMsg).LF;
        $aMsg.= '----------------------------------------------------------------------'.LF;
        $aMsg.= trim($aAdd['annotationsall']);
      }
      $aAdd = serialize($aAdd);
    }
    if (is_array($aMsg)) {
      $aMsg = $aMsg['body'];
    }
    $lSql = 'INSERT INTO al_job_his SET ';
    $lSql.= 'mand='.intval(MID).', ';
    $lSql.= 'src="'.addslashes($this -> mSrc).'", ';
    $lSql.= 'src_id="'.addslashes($this -> mJobId).'", ';
    $lSql.= 'user_id='.$this -> mUid.', ';
    $lSql.= 'datum='.esc($this -> mDate).',';
    $lSql.= 'typ='.intval($aType).',';
    $lSql.= 'subject="'.addslashes(trim($aSubject)).'", ';
    $lSql.= 'msg="'.addslashes(trim($aMsg)).'", ';
    if (!empty($aSignatureId)) {
      $lSql.= 'signature_id="'.intval($aSignatureId).'", ';
    }
    $lSql.= 'add_data="'.addslashes(trim($aAdd)).'"';
    if (!empty($aStepId)) {
      $lSql.= ', step_id='.esc($aStepId);
    }
    if (!empty($aFrom)) {
      $lSql.= ', from_status='.esc($aFrom);
    }
    if (!empty($aTo)) {
      $lSql.= ', to_status='.esc($aTo);
    }
    
    $lQry = new CCor_Qry($lSql);
    $lInsertId = $lQry -> getInsertId();
    return $lInsertId;

 #   CCor_Qry::exec($lSql);
  }

}