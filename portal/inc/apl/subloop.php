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

  public static function restart($aSubloopId, $aMsg, $aAddApproved = true) {
    $lSid = intval($aSubloopId);
    $lDat = CApl_Subloop::createFromId($lSid);
    $lOld = clone($lDat);
    unset($lDat['id']);
    $lNew = $lDat->insert();
    $lLoop = $lDat->getParent();

    //$lOld = CApl_Subloop::createFromId($lSid);
    $lAll = (bool)$aAddApproved;
    $lRows = $lOld->loadStates();
    $lOld->close();

    // determine the original event ID based on loop id and Prefix/country
    $lLid = intval($lDat['loop_id']);
    $lSql = 'SELECT event_id FROM al_job_apl_loop_events WHERE loop_id='.$lLid.' ';
    $lSql.= 'AND event_prefix='.esc($lDat['prefix']);
    $lEve = CCor_Qry::getInt($lSql);

    // determine which email templates to use for which task
    $lTaskTpl = array();
    if (!empty($lEve)) {
      $lSql = 'SELECT param FROM al_eve_act WHERE eve_id=' . $lEve;
      $lEveQry = new CCor_Qry($lSql);
      foreach ($lEveQry as $lParamRow) {
        $lPar = @unserialize($lParamRow['param']);
        if (isset($lPar['task']) && isset($lPar['tpl'])) {
          $lTaskTpl[$lPar['task']] = $lPar['tpl'];
        }
      }
    }
    $lDefaultTpl = CCor_Cfg::getFallback('invitation-tpl.'.$lLoop['src'], 'invitation-tpl');

    // if we skip approved positions, setup an ignore list
    // (doing that in the main loop below would fail to recognize some approved group positions)
    $lIgnore = array();
    if (!$lAll) {
      foreach ($lRows as $lRow) {
        $lStatus = $lRow['status'];
        $lTask = $lRow['task'];
        if ((strpos($lTask, 'approve') === false) || ($lStatus != CApp_Apl_Loop::APL_STATE_APPROVED)) {
          continue;
        }
        $lGid = $lRow['gru_id'];
        $lPos = $lRow['position'];
        $lUid = $lRow['user_id'];
        $lKey = (empty($lGid)) ? 'U'.$lUid.'_'.$lPos : 'G'.$lGid.'_'.$lPos;
        $lIgnore[$lKey] = true;
      }
    }

    $lMsgRec = array('body' => $aMsg);
    $lFac = new CJob_Fac($lLoop['src'], $lLoop['jobid']);
    $lJob = $lFac -> getDat();

    $lCurDay = new DateTime();
    $lDifDay = new DateInterval('P2D');
    $lOldPos = 0;
    $lCurPos = 0;
    foreach ($lRows as $lRow) {
      $lGid = $lRow['gru_id'];
      $lPos = $lRow['position'];
      $lUid = $lRow['user_id'];
      $lKey = (empty($lGid)) ? 'U' . $lUid . '_' . $lPos : 'G' . $lGid . '_' . $lPos;
      if (isset($lIgnore[$lKey])) {
        continue;
      }
      // fix position if we've ignored someone
      if ($lPos != $lOldPos) {
        $lOldPos = $lPos;
        $lCurDay->add($lDifDay);
        $lCurPos++;
      }

      // insert each row
      unset($lRow['id']);
      $lRow['sub_loop'] = $lNew['id'];
      $lRow['pos'] = $lCurPos;
      $lRow['position'] = $lCurPos;
      $lRow['status'] = 0;
      $lRow['done'] = 'N';
      $lRow['comment'] = '';
      $lRow['ddlchg'] = 'N';
      $lRow['ddl'] = $lCurDay->format('Y-m-d');
      $lRow->insert();

      $lRec = $lRow->toArray();

      $lTask = $lRow['task'];
      $lTpl = isset($lTaskTpl[$lTask]) ? $lTaskTpl[$lTask] : $lDefaultTpl;
      $lRec['tpl'] = $lTpl;

      $lRec['apl_id'] = $lRow['id'];
      $lSender = new CApp_Sender('email_usr', $lRec, $lJob, $lMsgRec);
      $lSender -> setMailType(mailAplInvite);
      $lSent = $lSender->sendItem($lRow['user_id'], $lCurPos, $lRow['id']);
    }
    $lLoop->sendMails();
    return $lNew;
  }

}
