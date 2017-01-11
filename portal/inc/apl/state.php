<?php
class CInc_Apl_State extends CCor_Dat {
  
  //protected $mId;
  protected $mMid;
  protected $mParent;
  protected $mTable = 'al_job_apl_states';
  
  public function __construct() {
    $this->mMid = MID;
  }
  
  // creation
  public static function createFromArray($aArray) {
    $lRet = new self();
    $lRet->assign($aArray);
    return $lRet;
  }
  
  public static function createFromId($aId) {
    $lRet = null;
    $lRow = self::loadId($aId);
    if ($lRow) {
      $lRet = new self();
      $lRet ->assign($lRow);
    }
    return $lRet;
  }
  
  public static function loadId($aId) {
    $lRet = null;
    $lSql = 'SELECT * FROM al_job_apl_states WHERE id='.intval($aId);
    $lQry = new CCor_Qry($lSql);
    if ($lRow = $lQry->getAssoc()) {
      $lRet = $lRow;
    }
    return $lRet;
  }
  
  // parent
  public function setParent($aParent) {
    $this->mParent = $aParent;
  }
  
  public function getParent() {
    $lRet = null;
    if (empty($this->mParent)) {
      $this->mParent = $this->loadParent();
    }
    if (!empty($this->mParent)) {
      $lRet = $this->mParent;
    }
    return $lRet;
  }
  
  protected function loadParent() {
    $lRet = $this->createParent();
    $lRet->setJob($this['src'], $this['jobid']);
    return $lRet;
  }
  
  protected function createParent() {
    return new CApl_Job();
  }
  
  protected function getBackupUser($aUid) {
    $lSql = 'SELECT p.backup FROM al_usr p, al_usr_pref q';
    $lSql.= ' WHERE p.id='.intval($aUid).' AND p.id=q.uid AND p.backup>0 AND q.mand='.MID.' AND p.mand='.MID;
    $lSql.= ' AND q.code="usr.onholiday" AND q.val="Y"';
      
    return CCor_Qry::getInt($lSql);
  }
  
  // CRUD
  
  public function insert() {
    if (empty($this['mand'])) {
      $this['mand'] = $this->mMid;
    }
  	if (empty($this['datum'])) {
      $this['datum'] = date('Y-m-d H:i:s');
  	}
  	if (empty($this['position'])) {
  	  $this['position'] = $this['pos'];
  	}
  	if (empty($this['user_id'])) {
  	  $this['user_id'] = $this['uid'];
  	}
  	
    $lBackup = $this->getBackupUser($this['user_id']);
    if ($lBackup > 0) {
      $this['user_id'] = $lBackup;
      $this['backupuser_id'] = $this['uid'];
    }
  	
    $lSql = 'INSERT INTO '.$this->mTable.' SET ';
    foreach ($this->mVal as $lKey => $lVal) {
      $lSql.= $lKey.'='.esc($lVal).',';
    }
    $lSql = strip($lSql);
    $lQry = new CCor_Qry();
    $lRes = $lQry->exec($lSql);
    if (!$lRes) return false;
    $lId = $lQry->getInsertId();
    $this['id'] = $lId;
    return $this;
  }
  
  public function update($aValues) {
    $lSql = 'UPDATE '.$this->mTable.' SET ';
    foreach ($aValues as $lKey => $lVal) {
      $lSql.= $lKey.'='.esc($lVal).',';
    }
    $lSql = strip($lSql).' WHERE id='.intval($this['id']);
    $lQry = new CCor_Qry();
    $lRes = $lQry->exec($lSql);
    if (!$lRes) return false;
    
    $this->addValues($aValues);
    return true;
  }
  
  public function setState($aState, $aComment = null) {
    $lUpd = array();
    $lUpd['status'] = intval($aState);
    $lUpd['done'] = 'Y';
    $lUpd['pos'] = 0;
    $lUpd['datum'] = date('Y-m-d H:i:s');
    if (!is_null($aComment)) {
      $lUpd['comment'] = $aComment;
    }
    if (($this['gru_id'] != 0) && ($this['confirm'] = 'one')) {
      $this->cancelOthers($lUpd);
    }
    $this->update($lUpd);
  }
  
  protected function cancelOthers($aUpd) {
    $lUpd = $aUpd;
    unset($lUpd['comment']);
    $lUpd['done'] = '-';
    
    $lSql = 'UPDATE '.$this->mTable.' SET ';
    foreach ($lUpd as $lKey => $lVal) {
      $lSql.= $lKey.'='.esc($lVal).',';
    }
    $lSql = strip($lSql).' WHERE loop_id='.esc($this['loop_id']);
    $lSql.= ' AND prefix='.esc($this['prefix']); // not strictly necessary as we have a unique subloop
    $lSql.= ' AND sub_loop='.$this['sub_loop'];
    $lSql.= ' AND gru_id='.$this['gru_id'];
    $lSql.= ' AND pos='.$this['pos'];
    $lSql.= ' AND done="N"';
    $lSql.= ' AND confirm="one"';
    
    $lQry = new CCor_Qry();
    $lRes = $lQry->exec($lSql);
  }
  
  public function close() {
    if ($this['status'] != 0) return;
    if ($this['done'] != 'N') return;
    if ($this['inv'] != 'Y') return;
    $lArr['status'] = CApp_Apl_Loop::APL_STATE_FORWARD;
    $lArr['done'] = '-';
    $this->update($lArr);
  }

}