<?php
class CInc_Apl_Job extends CCor_Obj {
  
  protected $mMid;
  protected $mSrc;
  protected $mJid;
  
  public function __construct($aSrc = null, $aJobId = null) {
    $this->mMid = MID;
    if (!empty($aSrc)) {
    	$this->mSrc = $aSrc;
    }
      if (!empty($aSrc)) {
    	$this->mJid = $aJobId;
    }
  }
  
  public function setMid($aMid) {
    $this->mMid = intval($aMid);
  }
  
  public function setJob($aSrc, $aJobId) {
    $this->mSrc = $aSrc;
    $this->mJid = $aJobId;
  }
  
  // children
  public function getLoops($aType = null) {
    $lSql = 'SELECT * FROM al_job_apl_loop ';
    $lSql.= 'WHERE src='.esc($this->mSrc).' ';
    $lSql.= 'AND jobid='.esc($this->mJid).' ';
    if (!empty($aType)) {
      $lSql.= 'AND typ LIKE "'.mysql_real_escape_string($aType).'%"';
    }
    $lSql.= ' ORDER BY id';
    $lRet = array();
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lLoop = $this->createNewChild();
      $lLoop->assign($lRow);
      $lLoop->setParent($this);
      $lRet[$lRow['id']] = $lLoop; 
    }
    return $lRet;
  }
  
  protected function createNewChild() {
    return new CApl_Loop();
  }
  
  public function getLastLoop($aType = null) {
    $lRow = $this -> getLastLoopType($aType);
    if(!$lRow) {
      return false;
    }
    $lLoop = $this->createNewChild();
    $lLoop->assign($lRow);
    $lLoop->setParent($this);
    return $lLoop;    
  }
  
  protected function getLastLoopType($aType = NULL) {
    $lSql = 'SELECT * FROM al_job_apl_loop ';
    $lSql.= 'WHERE src='.esc($this->mSrc).' ';
    $lSql.= 'AND jobid='.esc($this->mJid).' ';
    if (!empty($aType)) {
      $lSql.= 'AND typ LIKE "'.mysql_real_escape_string($aType).'%" ';
    }
    $lSql.= 'ORDER BY id DESC LIMIT 1';
    $lQry = new CCor_Qry($lSql);
    $lRow = $lQry->getAssoc();
    
    return ($lRow) ? $lRow : false;
  }
  
  public function insertLoop($aStepId, $aType = 'apl', $aDdl = null,  $aMode = 1) {
    $lLast = $this->getLastLoop($aType);
    if ($lLast) {
      $lLast->close();
    }
    $lLastLoop = $this -> getLastLoopType($aType);
    
    $lLoop = new CApl_Loop();
    $lLoop['mand']    = $this->mMid;
    $lLoop['src']     = $this->mSrc;
    $lLoop['jobid']   = $this->mJid;
    $lLoop['step_id'] = intval($aStepId);
    $lLoop['typ']     = $aType;
    if($lLastLoop !== FALSE) {
       $lLoop['num']  = intval($lLastLoop['num']) + 1;
    }
    if (!empty($aDdl)) {
      $lLoop['ddl'] = $aDdl;
    }
    $lLoop['apl_mode'] = $aMode;
    $lLoop ->insert();
    return $lLoop;
  }
  
  public function closeOpenLoops() {
    $lLoops = $this->getLoops();
    foreach ($lLoops as $lLoop) {
      if ($lLoop['status'] != 'closed') {
        $lLoop->close();
      }
    }
  }
  

}