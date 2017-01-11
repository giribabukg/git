<?php
class CInc_Apl_Loop extends CCor_Dat {

  protected $mMid;
  protected $mParent;
  protected $mTable = 'al_job_apl_loop';

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
    $lSql = 'SELECT * FROM al_job_apl_loop WHERE id='.intval($aId);
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

  // children
  public function getSubLoops($aPrefix = null) {
    $lSql = 'SELECT * FROM al_job_apl_subloop ';
    $lSql.= 'WHERE loop_id='.$this['id'];
    if (!empty($aPrefix))  {
      $lSql.= 'AND prefix='.esc($aPrefix).' ';
    }
    $lSql.= ' ORDER BY prefix,id DESC';

    $lRet = array();
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lItm = CApl_Subloop::createFromArray($lRow);
      $lItm->setParent($this);
      $lRet[$lRow['prefix']][$lRow['id']] = $lItm;
    }
    return $lRet;
  }

  public function getLastSubLoop($aPrefix = null) {
    $lSql = 'SELECT * FROM al_job_apl_subloop ';
    $lSql.= 'WHERE loop_id='.$this['id'].' ';
    if (!empty($aPrefix))  {
      $lSql.= 'AND prefix='.esc($aPrefix).' ';
    }
    $lSql.= 'ORDER BY id DESC';
    $lQry = new CCor_Qry($lSql);
    $lRow = $lQry->getAssoc();
    if (!$lRow) {
      return false;
    }
    $lRet = CApl_Subloop::createFromArray($lRow);
    $lRet->setParent($this);
    return $lRet;
  }

  public function insertSubLoop($aPrefix, $aFilename = '', $aFileVersion = '') {
    $lSub = new CApl_Subloop();
    $lSub['loop_id'] = $this['id'];
    $lSub['prefix'] = $aPrefix;
    if (!empty($aFilename)) {
      $lSub['file_name'] = $aFilename;
    }
    if (!empty($aFileVersion)) {
      $lSub['file_version'] = $aFileVersion;
    }
    $lSub ->insert();
    $lSub->setParent($this);
    return $lSub;
  }

  /**
   * Only use for APLs without subloops
   * @param array $aValues
   * @return CApl_State
   */
  public function insertState($aValues) {
    $lState = new CApl_State();
    $lState->assign($aValues);
    $lState['loop_id'] = $this['id'];
    $lState ->insert();
    $lState->setParent($this);
    return $lState;
  }

  public function loadStates() {
    $lSql = 'SELECT * FROM al_job_apl_states WHERE loop_id='.intval($this['id']);
    $lSql.= ' ORDER by pos,gru_id';
    //echo $lSql;
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lRet[$lRow['id']] = CApl_State::createFromArray($lRow);
    }
    return $lRet;
  }

  // CRUD

  public function insert() {
    $lJob = $this->getParent();
    $lJob->closeOpenLoops();
    if (empty($this['mand'])) {
      $this['mand'] = $this->mMid;
    }
  	$this['start_date'] = date('Y-m-d H:i:s');
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

  // actions
  public function close() {
    /*$lUpd['status'] = 'closed';
    $lUpd['close_date'] = date('Y-m-d');
    $this->update($lUpd);*/

    $lSubs = $this->getSubLoops();
    if (!empty($lSubs))  {
      foreach ($lSubs as $lPrefix => $lRows) {
        if (!empty($lRows)) {
          foreach ($lRows as $lId => $lSub) {
            if ($lSub['subloop_state'] != 'closed') {
              $lSub->close();
            }
          }
        }
      }
    }
  }

  public function isComplete() {
    $lLid = intval( $this['id'] );
    $lSql = 'SELECT COUNT(*) FROM al_job_apl_states ';
    $lSql.= 'WHERE loop_id='.$lLid.' ';
    $lSql.= 'AND done="N" ';
    $lSql.= 'AND inv="Y" ';
    $lSql.= 'AND del != "Y" ';
    $lCount = CCor_Qry::getInt($lSql);
    return (0 == $lCount);
  }

  public function setCompleted() {
    if ($this['completed'] == 'Y') {
      return;
    }
    $lLid = intval( $this['id'] );
    $this->update(array('completed' => 'Y'));

    $lSql = 'SELECT typ FROM al_job_apl_loop WHERE id='.$lLid;
    $lAplType = CCor_Qry::getStr($lSql);

    $lSrc = $this['src'];
    $lJid = $this['jobid'];
    $lFac = new CJob_Fac($lSrc, $lJid);
    $lJob = $lFac -> getDat();

    $lType = new CApp_Apl_Type($lAplType);
    $lEveId = $lType->getEventCompleted();
    if (!empty($lEveId)) {
      $lEvent = new CJob_Event($lEveId, $lJob);
      $lEvent->execute();
    }
    $lBeat = new CJob_Workflow_Heartbeat($lSrc, $lJid, $lJob);
    $lBeat->heartBeat();
  }

    // Update mail state
  public function sendMails() {
    $lLoopId = intval ( $this ['id'] );
    $lIds = array ();
    $lSql = 'SELECT id,pos,status,prefix,dur,ddlchg,inv ';
    $lSql .= 'FROM al_job_apl_states WHERE loop_id=' . $lLoopId . ' ';
    $lSql .= 'AND del != "Y" ';
    $lSql .= 'ORDER BY prefix,sub_loop,pos';
    $lQry = new CCor_Qry ( $lSql );

    $lMax = 0;
    $lRows = array ();
    foreach ( $lQry as $lRow ) {
      $lRows [] = $lRow;
      if ($lRow ['dur'] > $lMax)
        $lMax = $lRow ['dur'];
    }
    $lOldPrefix = 'EEHAHUHAHAHCHINGCHANGWALLAWALLABINGBANG!';
    $lOldSub = null;
    foreach ($lRows as $lRow) {
      $lSub = $lRow ['sub_loop'];
      $lPrefix = $lRow ['prefix'];
      if ($lSub != $lOldSub) {
        $lMinPos [$lPrefix] = MAX_SEQUENCE;
        $lOldSub = $lSub;
        unset($lIds[$lPrefix]);
      }
      if ($lPrefix != $lOldPrefix) {
        $lMinPos[$lPrefix] = MAX_SEQUENCE;
        $lOldPrefix = $lPrefix;
      }
      $lPos = $lRow ['pos'];
      if (0 == $lRow ['status']) {
        $lIds[$lPrefix][$lRow ['id']] = $lRow;
      }
      if (0 == $lRow ['status'] and $lMinPos[$lPrefix] > $lPos and $lRow['inv'] == 'Y') {
        $lMinPos [$lPrefix] = $lPos;
      }
    }
    $lDurationInDates = CCor_Date::getWorkdays ( $lRow ['dur'] ); // aufgrund der Sortierung kommt die groesste Durationtime als erstes
    foreach ($lIds as $lPrefix => $lRows) {
      foreach ($lRows as $lId => $lRow) {
        $lMin = $lMinPos [$lRow ['prefix']];
        if (($lMin >= $lRow ['pos']) && ('N' == $lRow ['ddlchg'])) {
          // set new mail status
          $lItm = new CApi_Mail_Item ('1', '2', '3', '4');
          $lItm->setNewMailState($lId);
          $lNewUsrDdl = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') + $lRow ['dur'], date('Y')));
          $lState = CApl_State::createFromId($lId);
          $lArr = array(
              'ddlchg' => 'Y',
              'ddl' => $lNewUsrDdl
          );
          $lState->update($lArr);
        }
      }
    }
  }

}
