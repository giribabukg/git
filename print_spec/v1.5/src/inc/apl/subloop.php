<?php
class CInc_Apl_Subloop extends CCor_Dat {
  
  protected $mId;
  protected $mMid;
  protected $mParent;
  protected $mTable = 'al_job_apl_subloop';
 
  public function __construct() {
    $this->mMid = MID;
  }
  
  //* creation
  public static function createFromArray($aArray) {
    $lRet = new CApl_Subloop();
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
    $lSql = 'SELECT * FROM al_job_apl_subloop WHERE id='.intval($aId);
    $lQry = new CCor_Qry($lSql);
    if ($lRow = $lQry->getAssoc()) {
      $lRet = $lRow;
    }
    return $lRet;
  }
  
  //* parent 
  public function setParent($aParent) {
    $this->mParent = $aParent;
  }
  
  /**
   * 
   * @return CApl_Job
   */
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
    $lRet = CApl_Loop::createFromId($this['loop_id']);
    return $lRet;
  }
  
  protected function createParent() {
    return new CApl_Loop();
  }
  
  //* children
  public function loadStates() {
    $lSql = 'SELECT * FROM al_job_apl_states WHERE sub_loop='.intval($this['id']);
    $lSql.= ' ORDER by position,gru_id';
    //echo $lSql;
    $lRet = array();
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lRet[$lRow['id']] = CApl_State::createFromArray($lRow);
    }
    return $lRet;
  }
  
  public function loadDisplayStates($aUid = null) {
    $lRet = array();
    $lStates = $this->loadStates();
    if (empty($lStates)) {
      return $lRet;
    }
        
    $lNum = 0;
    foreach ($lStates as $lId => $lRow) {
      $lNum++;
      $lPos = $lRow['position'];
      $lGroup = $lRow['gru_id'];
      $lUser  = $lRow['user_id'];
      
      if (empty($lGroup)) {
        $lGpKey = 'U'.$lUser.'_'.$lPos;
      } else {
        $lGpKey = 'G'.$lGroup.'_'.$lPos;
      }
            
      if ($lRow['del'] != 'N') continue;
      if ($lRow['inv'] != 'Y') continue;
      if (($lRow['done'] == '-') && (isset($lBlacklist[$lGpKey]))) {
        // workaround : if whole group is cancelled because apl was closed,
        // it wouldn't show in previous APLs if we only check 'done' = '-'
        continue;
      }
      
      if ($lGroup != 0) {
        if (isset($lBlacklist[$lGpKey])) {
          if (($lUser != $aUid) && ($lRow['done'] != 'Y')) {
            continue;
          }
        }
        $lBlacklist[$lGpKey] = 1;
      }
      if (isset($lRet[$lGpKey])) {
        // can happen if more than one member has approved
        $lOld = $lRet[$lGpKey];
        if ($lOld['done'] != 'Y') {
          $lRet[$lGpKey] = $lRow; // replace as new user has approved
        } elseif ($lRow['done'] == 'Y') {
          $lRet[$lGpKey.'_'.$lNum] = $lRow;
        }
      } else {
        $lRet[$lGpKey] = $lRow;
      }
    }
    return $lRet;
  }  

  
  public function insertState($aValues) {
    $lState = new CApl_State();
    $lState->assign($aValues);
    $lState['mand'] = MID;
    $lState['sub_loop'] = $this['id'];
    if ($lParent = $this->getParent()) {
      $lState['loop_id'] = $lParent['id'];
    }
    $lState ->insert();
    $lState->setParent($this);
    return $lState;
  }
  
  // CRUD
  
  public function insert() {
  	$this['create_date'] = date('Y-m-d H:i:s');
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
    //echo $lSql;
    CCor_Qry::exec($lSql);
  }
  
  public function close() {
    $lArr = array();
    $lArr['subloop_state'] = 'closed';
    $lArr['close_date'] = date('Y-m-d');
    $this->update($lArr);
    
    $lStates = $this->loadStates();
    if (!empty($lStates)) {
      foreach ($lStates as $lState) {
        $lState->close();
      }
    }
  }
  

}
