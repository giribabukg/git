<?php
class CInc_Cor_Usr_Crp extends CCor_Obj {

  protected $mId;
  protected $mMem;
  protected $mStp;

  public function __construct($aUid, & $aMem) {
    $this -> mId = intval($aUid);
    $this -> mMem = $aMem -> getArray();
    $this -> mStp = array();
    $this -> loadRights();
  }

  protected function loadRights() {
  	$lUsr = CCor_Usr::getInstance();
  	$lUsrToBackupId = $lUsr->shallIBackupAnyUsr();
  	if ($lUsrToBackupId !== FALSE) {
  		$lSql1Part = ' IN('.$this->mId.','.$lUsrToBackupId.') ';
  		$lSql = 'SELECT gid FROM al_usr_mem WHERE uid='.$lUsrToBackupId;
  		$lQry = new CCor_Qry($lSql);
  		foreach ($lQry as $lRow) {
  			$lUsrToBackupGroups[] = $lRow['gid'];
  		}
  		$this->mMem = array_unique(array_merge($this->mMem, $lUsrToBackupGroups));
  	}else $lSql1Part = ' = '.$this->mId.' ';
  	$this -> mMem = implode(',', $this -> mMem);
  	
    $lSql = 'SELECT stp_id FROM al_usr_rig_stp WHERE fla_id=0 AND usr_id'.$lSql1Part;
    $lQry = new CCor_Qry($lSql);
    foreach($lQry as $lRow) {
      $lSid = $lRow['stp_id'];
      $this -> mStp[$lSid] = true;
    }
    if (empty($this -> mMem)) return;
    $lSql = 'SELECT stp_id FROM al_gru_rig_stp WHERE fla_id=0 AND gru_id IN ('.$this -> mMem.')';
    $lQry = new CCor_Qry($lSql);
    foreach($lQry as $lRow) {
      $lSid = $lRow['stp_id'];
      $this -> mStp[$lSid] = true;
    }
  }

  public function canDo($aStep) {
    $lStp = intval($aStep);
    return (isset($this -> mStp[$lStp]));
  }

}