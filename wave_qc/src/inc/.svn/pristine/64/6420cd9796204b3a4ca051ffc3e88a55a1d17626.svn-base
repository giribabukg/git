<?php
class CInc_Cor_Usr_Sta extends CCor_Obj {

  protected $mId;
  protected $mMem;
  protected $mStp;
   
  public function __construct($aUid, & $aMem) {
    $this -> mId = intval($aUid);
    $this -> mMem = $aMem -> getStr();
    $this -> mStp = array();
    $this -> loadRights();
  }
  
  protected function loadRights() {
    $lSql = 'SELECT sta_id FROM al_usr_rig_status WHERE usr_id='.$this -> mId;
    $lQry = new CCor_Qry($lSql);
    foreach($lQry as $lRow) {
      $lSid = $lRow['sta_id'];
      $this -> mStp[$lSid] = true;
    }
    if (empty($this -> mMem)) return;
    $lSql = 'SELECT sta_id FROM al_gru_rig_status WHERE gru_id IN ('.$this -> mMem.')';
    $lQry = new CCor_Qry($lSql);
    foreach($lQry as $lRow) {
      $lSid = $lRow['sta_id'];
      $this -> mStp[$lSid] = true;
    }
  }
  
  public function canDo($aStatus) {
    $lStp = intval($aStatus);
    return (isset($this -> mStp[$lStp]));
  }
  
}