<?php
/**
 * Approval Loop
 *
 * Maintain the state of a single approval loop
 *
 * @package    Application
 * @subpackage Approval Loop
 * @version $Rev: 13655 $
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 * @date $Date: 2016-04-26 18:57:59 +0800 (Tue, 26 Apr 2016) $
 * @author $Author: gemmans $
 */

class CInc_App_Apl_Loop extends CCor_Obj {

  const APL_STATE_UNKNOWN     = 0;
  const APL_STATE_AMENDMENT   = 1;
  const APL_STATE_CONDITIONAL = 2;
  const APL_STATE_APPROVED    = 3;
  const APL_STATE_FORWARD     = 4;
  const APL_STATE_BACKTOGROUP = 6;
  const APL_STATE_BLOCKFORTHEGROUP = 7;
  const APL_STATE_BREAK       = 100;

  const APL_STATE_DEFAULT     = 0;

  const APL_LOOP_OPEN     = 'open';
  const APL_LOOP_CLOSED   = 'closed';



  protected $mLastOpenFlags = array();
  protected $mUsrEmail = array();

  public function __construct($aSrc, $aJobId, $aType = 'apl', $aMid = NULL, $aWebstatus = NULL) {
    $this -> mSrc = $aSrc;
    $this -> mJobId = $aJobId;
    $this -> mTyp = $aType;
    $this -> mMid = (NULL == $aMid) ? MID : intval($aMid);

    $this -> mId  = NULL;
    $this -> mRow = NULL;
    $this -> mWebStatus = (NULL == $aWebstatus) ? NULL : $aWebstatus;

    $this -> mState = self::APL_STATE_DEFAULT;

    if ('apl' != $this -> mTyp) {
      $this -> getLastOpenFlags($this -> mTyp);
    }

    $this -> mUsrEmail = CCor_Res::extract('id', 'email', 'usr');
  }

  public function getMaxNum() {
    $lSql = 'SELECT MAX(num) FROM al_job_apl_loop WHERE 1 ';
    $lSql.= 'AND mand='.esc($this -> mMid).' ';
    $lSql.= 'AND src='.esc($this -> mSrc).' ';
    $lSql.= 'AND jobid='.esc($this -> mJobId).' ';
    $lSql.= 'AND typ='.esc($this -> mTyp).' ';
    $lRet = CCor_Qry::getStr($lSql);

    return intval($lRet);
  }

  public function getLoopById($aId) {
    $lId = intval($aId);
    $lSql = 'SELECT * FROM al_job_apl_loop WHERE id='.$lId;
    $lQry = new CCor_Qry($lSql);
    $lRow = $lQry -> getDat();
    return $lRow;
  }

  public function closeLoops() {
	$lTyp = (strpos($this -> mTyp, 'apl') !== false ? 'apl' : $this->mTyp);

	$lJob = new CApl_Job($this->mSrc, $this->mJid);
	$lJob->closeOpenLoops();

	$lSql = 'UPDATE `al_job_apl_loop` SET';
	$lSql.= ' `status`='.esc(self::APL_LOOP_CLOSED);
	$lSql.= ',`close_date`='.esc(date('Y-m-d'));
	$lSql.= ' WHERE 1';
	$lSql.= ' AND `src`='.esc($this -> mSrc);
	$lSql.= ' AND `mand`='.esc($this -> mMid);
	$lSql.= ' AND `jobid`='.esc($this -> mJobId);
	$lSql.= ' AND `typ` LIKE "'.mysql_real_escape_string($lTyp).'%"';

	CCor_Qry::exec($lSql);
  }

  /**
   * Get Last Open LoopId
   * @param boolean $aSetAplId If= TRUE set $this->mId
   * @return integer
   */
  public function getLastOpenLoop($aSetAplId = FALSE, $aDdl = '') {
    $lSql = 'SELECT MAX(id) FROM al_job_apl_loop WHERE 1 ';
    $lSql.= 'AND src='.esc($this -> mSrc).' ';
    $lSql.= 'AND mand='.esc($this -> mMid).' ';
    $lSql.= 'AND jobid='.esc($this -> mJobId).' ';
    $lSql.= 'AND typ LIKE '.esc($this -> mTyp.'%').' '; //@TODO: rework this
    $lSql.= 'AND status='.esc(self::APL_LOOP_OPEN).' ';

    // $this -> dbg('getLastOpenLoop:'.$lSql);
    $lRet = CCor_Qry::getInt($lSql);
    #echo '<pre>---loop.php---'.get_class().'---';var_dump($lSql,$lRet,'#############');echo '</pre>';
    if ($aSetAplId){
      $this -> mId  = $lRet;
    }
    // If Deadline exist, update APL Deadline
    if ($aDdl != ''){
      $lSql = 'UPDATE al_job_apl_loop';
      $lSql.= ' SET ddl='.esc($aDdl);
      $lSql.= ' WHERE id ='.esc($this -> mId);
      CCor_Qry::exec($lSql);
      $this -> dbg('Apl Deadline changed for mand_'.$this -> mMid);
    }
    return $lRet;
  }

  public function getLastLoop() {
    $lSql = 'SELECT MAX(id) FROM al_job_apl_loop WHERE 1 ';
    $lSql.= 'AND src='.esc($this -> mSrc).' ';
    $lSql.= 'AND mand='.esc($this -> mMid).' ';
    $lSql.= 'AND jobid='.esc($this -> mJobId).' ';
    $lSql.= 'AND typ LIKE '.esc($this -> mTyp.'%').' '; //@TODO: rework this

    $lRet = CCor_Qry::getInt($lSql);
    return $lRet;
  }

  public function getLastOpenFlags($aFlag = '') {
    $lRet1 = array();
    $lRet = array();
    if ( empty($this -> mLastOpenFlags) OR (!empty($aFlag) AND !isset($this -> mLastOpenFlags[$aFlag])) ) {
      $lSql = 'SELECT max(id) as id,typ,status FROM al_job_apl_loop WHERE 1';
      $lSql.= ' AND src='.esc($this -> mSrc);
      $lSql.= ' AND mand='.esc($this -> mMid);
      $lSql.= ' AND jobid='.esc($this -> mJobId);
      $lSql.= (empty($aFlag) ? ' AND typ!="apl"' : ' AND typ='.esc($aFlag));
      #$lSql.= ' AND status='.esc(self::APL_LOOP_OPEN);
      $lSql.= ' GROUP BY typ ASC, status';
      // $this -> dbg('getLastOpenFlags:'.$lSql);
      $lQry = new CCor_Qry($lSql);
      #echo '<pre>---loop.php---'.get_class().'---';var_dump($lSql,'#############');echo '</pre>';
      foreach ($lQry as $lRow){
        if (self::APL_LOOP_OPEN == $lRow['status']) {
          $lRet1[ $lRow['typ'] ]['open'] = $lRow['id'];
        } else {
          $lRet1[ $lRow['typ'] ]['clos'] = $lRow['id'];
        }
      }
      foreach ($lRet1 as $lTyp => $lRow){
        if (isset($lRow['open']) AND isset($lRow['clos'])) {
          if ($lRow['open'] > $lRow['clos']) {
            $lRet[$lTyp] = $lRow['open'];
          }
        } elseif (isset($lRow['open'])) {
          $lRet[$lTyp] = $lRow['open'];
        }
      }
      $this -> mLastOpenFlags = $lRet;
    }
    if (empty($aFlag)) {
      return $this -> mLastOpenFlags;
    } elseif (isset($this -> mLastOpenFlags[$aFlag])) {
      return $this -> mLastOpenFlags[$aFlag];
    } else {
      return 0;
    }
  }

  /**
   * Create new Loop
   * @param $aDdl
   * @param int $aStepId StepId
   * @return int $this->mId new Loop Id.
   */
  public function createLoop($aDdl = '', $aStepId = '', $aMode = '1') {
    $this -> closeLoops();
    $this -> mRow = NULL;
    $this -> mId  = NULL;
    $this -> mWebStatus = NULL;

    $lArr['mand']   = $this -> mMid;
    $lArr['src']    = $this -> mSrc;
    $lArr['jobid']  = $this -> mJobId;
    $lArr['num']    = $this -> getMaxNum()+1;
    $lArr['typ']    = $this -> mTyp;
    $lArr['apl_mode']    = $aMode;
    $lArr['status'] = self::APL_LOOP_OPEN;
    $lArr['ddl']    = $aDdl;
    $lArr['start_date'] = date('Y-m-d');
    $lArr['step_id'] = $aStepId;

    $lSql = 'INSERT INTO al_job_apl_loop SET ';
    foreach ($lArr as $lKey => $lVal) {
      if (!empty($lVal)) {
        $lSql.= $lKey.'='.esc($lVal).',';
      }
    }
    $lSql = strip($lSql);
    $lQry = new CCor_Qry();
    if (!$lQry -> query($lSql)) return FALSE;

    $this -> mId = $lQry ->getInsertId();
    return $this -> mId;
  }

  protected function getBackupUser($aUid) {
    if (!isset($this->mBackupUser[$aUid])) {
      $lSql = 'SELECT p.backup FROM al_usr p, al_usr_pref q';
      $lSql.= ' WHERE p.id='.intval($aUid).' AND p.id=q.uid AND p.backup>0 AND q.mand='.MID.' AND p.mand='.MID;
      $lSql.= ' AND q.code="usr.onholiday" AND q.val="Y"';
      $this->mBackupUser[$aUid] = CCor_Qry::getInt($lSql);
    }
    return $this->mBackupUser[$aUid];
  }

  public function addItem($aId, $aName, $aAdd = array()) {
    $lArr = array();

    $lArr['pos'] = 1;
    $lArr['dur'] = 1;
    if (!empty($aAdd)) {
      if (isset($aAdd['pos']))     $lArr['pos']    += $aAdd['pos']; // da in DB-Eve/Act ab 0 gespeichert
      if (isset($aAdd['dur']))     $lArr['dur']     = $aAdd['dur'];
      if (isset($aAdd['ddl']))     $lArr['ddl']     = $aAdd['ddl'];
      if (isset($aAdd['gru']))     $lArr['gru_id']  = $aAdd['gru'];
      if (isset($aAdd['confirm'])) $lArr['confirm'] = $aAdd['confirm'];
      if (isset($aAdd['inv']))     $lArr['inv']     = $aAdd['inv'];
      if (isset($aAdd['prefix']))  $lArr['prefix']  = $aAdd['prefix'];
      if (isset($aAdd['done']))    $lArr['done']    = $aAdd['done'];
      if (isset($aAdd['added']))    $lArr['added']    = $aAdd['added'];
    }
    $lArr['position'] = $lArr['pos'];//dieser Wert wird nur lesend genutzt: erhalte d. urspruengl. Reihenfolge!

    $lArr['loop_id'] = (isset($aAdd['loop_id'])) ? $aAdd['loop_id'] : $this -> mId;
    $lArr['typ'] = (isset($aAdd['typ'])) ? $aAdd['typ'] : $this -> mTyp;
    $lArr['status'] = (isset($aAdd['status'])) ? $aAdd['status'] : self::APL_STATE_UNKNOWN;

    $lArr['mand']    = $this -> mMid;
    $lArr['user_id'] = $aId;
    $lArr['uid']     = $aId; //dieser Wert wird nur lesend genutzt: erhalte d. urspruengl. Eigentuemer!
    $lArr['name']    = $aName;

    $lBackup = $this->getBackupUser($aId);
    if ($lBackup > 0) {
      $lArr['user_id'] = $lBackup;
      $lArr['backupuser_id'] = $aId;
    }

    $lSql = 'INSERT INTO al_job_apl_states SET ';
    foreach ($lArr as $lKey => $lVal) {
      $lSql.= $lKey.'='.esc($lVal).',';
    }
    $lSql.= 'datum=NOW()';
    #echo '<pre>---app/apl/loop.php--addItem-';var_dump($lSql,'#############');echo '</pre>';
    $lQry = new CCor_Qry();
    $lRet = $lQry -> query($lSql);
    if ($lRet) {
      $lInsertId = $lQry -> getInsertId();
    } else {
      $lInsertId = 0;
    }
    return $lInsertId;
  }

  /**
   * Insert users to already started APL
   * @param array $aUids - The UserIds that should be inserted
   * @param string $aAfterUid - To decide at which position the user should be insert. if it is not empty this will insert the user at same position, else after me
   * @param string $aPrefix - If we are using parallel workflows, this can decide to which workflow should this user be added.
   * @param string $aMethod - Add, Expand or Forward to user
   * @return boolean
   */
  public function insertUsers($aUids, $aAfterUid = NULL, $aPrefix = '', $aMethod = 'N') {
    if (empty($aUids)) return true;
    $lLid = $this->getLastOpenLoop(true);
    if (empty($lLid)) return false;

#    $lAfter = (is_null($aAfterUid)) ? CCor_Usr::getAuthId() : intval($aAfterUid);
    if (is_null($aAfterUid) OR empty($aAfterUid)) {
      $lAfter = CCor_Usr::getAuthId();
    }
    else $lAfter = intval($aAfterUid);

    $lSql = 'SELECT MIN(pos) AS minpos, prefix, typ ';
    $lSql.= 'FROM al_job_apl_states ';
    $lSql.= 'WHERE loop_id='.$lLid.' ';
    $lSql.= 'AND user_id='.$lAfter.' ';
    $lSql.= 'AND done="N"';
    $lQry = new CCor_Qry($lSql);
    if ($lRow = $lQry->getAssoc()) {
      $lMax = intval($lRow['minpos']);
      $lPrefix = empty($aPrefix) ?  $lRow['prefix'] : $aPrefix;
      $this -> mTyp = $lRow['typ'];
    }

    $this->dbg('------ '.$lSql.' => '.$lMax);
    if (!empty($aAfterUid)) {
      $lMax = $lMax-1;
    }

    $lUsrRes = CCor_Res::extract('id', 'fullname', 'usr');
    $lHis = new CJob_His($this -> mSrc, $this -> mJobId);
    foreach ($aUids as $lUid) {
      if ($lUid < 0) continue;
      if (!isset($lUsrRes[$lUid])) continue;
      $lAdd = array();
      $lAdd['pos'] = $lMax;
      $lAdd['prefix'] = $lPrefix;
      $lAdd['added'] = $aMethod;
      $lName = $lUsrRes[$lUid];
      $lInsertId = $this->addItem($lUid, $lName, $lAdd);
      $lHis -> add(18, lan('his-titel-apl-usr.add'), lan('his-titel-apl-usr.add').': "'.$lName.'"');
      $this->sendAplEmailAfterAddUsr($lUid, $lAdd, $lInsertId);
    }
    foreach ($aUids as $lGid) {
      if ($lGid > 0) continue;
      $lGru = abs($lGid);
      $this->dbg("Adding group ".$lGru);
      $lMem = CCor_Res::extract('id', 'fullname', 'usr', array('gru' => $lGru));
      if (empty($lMem)) {
        $this->dbg('Empty group');
        continue;
      }
      $lGName =  CCor_Res::extract('id', 'name', 'gru', array('id' => $lGru));
      $lHis -> add(18, lan('his-titel-apl-group.add'), lan('his-titel-apl-group.add').': "'.$lGName[$lGru].'"');
      foreach ($lMem as $lUid => $lName) {
        $lAdd = array();
        $lAdd['pos'] = $lMax;
        $lAdd['gru'] = $lGru;
        $lAdd['confirm'] = 'one';
        $lAdd['prefix'] = $lPrefix;
        $lAdd['added'] = $aMethod;
        $lInsertId = $this->addItem($lUid, $lName, $lAdd);

        $this->sendAplEmailAfterAddUsr($lUid, $lAdd, $lInsertId, $lMem);
      }
    }
  }

  /** Send APL Invitation email to Users after inviting them from APL page, in an already started APL.
   *
   * @param Int $aUid: User Id of the invited User
   * @param Array $aAdd:  Mixed param
   * @param Int $aAplStateId: Insert Id value from al_job_apl_states
   */
  protected function sendAplEmailAfterAddUsr($aUid, $aAdd, $aAplStateId, $aUserList=NULL) {
    $lEmailTpl = CCor_Cfg::get('tpl.email');

    $lRec = array();
    $lUid = $aUid;
    $lRec['pos'] = $aAdd['pos'];
    $lRec['sid'] = $lUid;
    $lRec['uid'] = $lUid;
    $lRec['dur'] = '2';
    $lRec['tpl'] = $lEmailTpl['apl']; #136;
    $lRec['usr'] = $lUid;
    $lRec['prefix'] = 'apl';
    $lRec['inv'] = 'Y';
    $lRec['apl_id'] = $aAplStateId;

    $lFac = new CJob_Fac($this -> mSrc, $this -> mJobId);
    $lJob = $lFac -> getDat();

    $lSender = new CApp_Sender('usr', $lRec, $lJob);
    $lSender -> setMailType(mailAplInvite);
    $lSender->sendItem($lUid, $aAdd['pos'], $aAplStateId, $aUserList);
    #$lSender -> execute();
  }

  public function setState($aUid, $aState, $aComment, $aLoopId = NULL, $aAdd = FALSE, $aStateIds = NULL) {
    if ($this -> testJobInApl()) {
      $lLid = (NULL == $aLoopId) ? $this -> getLastOpenLoop() : intval($aLoopId);

      if (NULL !== $aComment) {
        $lComment = trim($aComment);
      } else {
        $lComment = '';
      }
      if(empty($aStateIds)) {
        $lSql = 'SELECT gru_id';
        $lSql.= ' FROM `al_job_apl_states`';
        $lSql.= ' WHERE `loop_id` ='.esc($lLid);
        $lSql.= ' AND `user_id` ='.esc($aUid);
        #      $lSql.= ' AND `confirm` = "one"';
        $lSql.= ' AND `gru_id` >0';
        $lSql.= ' ORDER BY position';
        $lSql.= ' LIMIT 1';
        #echo '<pre>---setState---';var_dump($lSql,'#############');echo '</pre>';
        $lGruId = CCor_Qry::getInt($lSql);
        $lGruArr = CCor_Res::extract('id', 'name', 'gru');
        if (isset($lGruArr[$lGruId])) {
          $lGruName = $lGruArr[$lGruId];
        } else {
          $lGruName = '';
        }
        if (!empty($lGruName)) {
          $lGruName = '('.$lGruName.')';
          #echo '<pre>---setState---'.get_class().'---';var_dump($lComment, $lGruName,strpos($lComment, $lGruName),FALSE === strpos($lComment, $lGruName),'#############');echo '</pre>';
          if (FALSE === strpos($lComment, $lGruName)) {
            $lGruName.= lan('lib.separator_:').' ';
            $lComment = $lGruName.$lComment;
          }
        }
      }

      $lIds = $this->getActiveStateIds();
      if (!empty($lIds)) {
        if(!empty($aStateIds)) {
          foreach($lIds as $lI => $lId) {
            if(!in_array($lId, $aStateIds)) {
              unset($lIds[$lI]);
            }
          }
        }

        $lSql = 'UPDATE al_job_apl_states SET ';
        if (NULL !== $aState) {
          $lSql.= '`status`='.esc($aState).',';
          $lSql.= '`pos`=0,'; // dadurch kann i.d. APL-Seite der naechste User seine APL-Buttons sehen.
          $lSql.= '`done`="Y",'; // Kommentar abgegeben
        }
        if (self::APL_STATE_UNKNOWN != $aState) {
          $lSql.= 'pos=0,'; // dadurch kann i.d. APL-Seite der nächste User seine APL-Buttons sehen.
          $lSql.= 'done="Y",'; // Kommentar abgegeben
        }
        if (!empty($lComment)) {
          if ($aAdd) {
            $lSql.= 'comment=CONCAT(comment,"\\n",'.esc($lComment).'),';
          } else {
            $lSql.= 'comment='.esc($lComment).',';
          }
        }
        $lSql.= 'datum=NOW()';
        $lSql.= ' WHERE 1';
        $lSql.= ' AND `user_id`='.esc($aUid);
        $lSql.= ' AND `inv`="Y"';
        $lSql.= ' AND `done`<>"-"';//update nicht, bei confirm=one AND jemand anderes hat bereits confirmed
        $lSql.= ' AND `loop_id`='.esc($lLid);
        $lSql.= ' AND id IN ('.implode(',', $lIds).')';

        CCor_Qry::exec($lSql);
      }

      $this -> setNewGroupState($aUid, $lLid, $lIds);
      if ($aState != self::APL_STATE_BREAK) {
        $this -> setNewMailState($lLid);
      }
      if ($this->isAplOpenAndCompleted($lLid) && $aState != self::APL_STATE_BREAK) {
        $this->setAplCompleted($lLid);
      }
    }
  }

  public function isAplOpenAndCompleted($aLoopId = null) {
    $lLid = (empty($aLoopId)) ? $this->getLastOpenLoop(false) : intval($aLoopId);
    $lSql = 'SELECT COUNT(*) FROM al_job_apl_states ';
    $lSql.= 'WHERE loop_id='.$lLid.' ';
    $lSql.= 'AND done="N" ';
    $lSql.= 'AND inv="Y" ';
	$lSql.= 'AND del != "Y" ';
    $lCount = CCor_Qry::getInt($lSql);
    return (0 == $lCount);
  }

  protected function setAplCompleted($aLoopId) {
    $lSql = 'UPDATE al_job_apl_loop ';
    $lSql.= 'SET completed="Y" ';
    $lSql.= 'WHERE id='.intval($aLoopId);
    CCor_Qry::exec($lSql);

    $lSql = 'SELECT typ FROM al_job_apl_loop WHERE id='.intval($aLoopId);
    $lAplType = CCor_Qry::getStr($lSql);
    $lType = $this->getEventType($lAplType);
    if (!$lType) {
      return;
    }
    $lEveId = $lType->getEventCompleted();
    if (empty($lEveId)) {
      return;
    }
    $lFac = new CJob_Fac($this -> mSrc, $this->mJobId);
    $lJob = $lFac -> getDat();
    $lEvent = new CJob_Event($lEveId, $lJob);
    $lEvent->execute();
  }

  public function getEventType($aType) {
    $lRet = new CApp_Apl_Type($aType);
    return $lRet;
  }

  public function setFlagState($aUid, $aName, $aVote, $aComment) {
    $lRet = TRUE;
    $lLastId = $this -> getLastOpenFlags($this -> mTyp);
    #$lLastId = (!empty($lLast_Id) ? $lLast_Id[$aFlagId] : 0);
    #echo '<pre>---loop.php---'.get_class().'---';var_dump($lLastId,'#############');echo '</pre>';
    if (0 != $lLastId AND !empty($aVote)) {

      if (NULL !== $aComment) {
        $lComment = trim($aComment);
      } else {
        $lComment = '';
      }
      $lSql = 'SELECT gru_id';
      $lSql.= ' FROM `al_job_apl_states`';
      $lSql.= ' WHERE `loop_id` ='.esc($lLastId);
      $lSql.= ' AND `user_id` ='.esc($aUid);
      #      $lSql.= ' AND `confirm` = "one"';
      $lSql.= ' AND `gru_id` >0';
      $lSql.= ' ORDER BY pos';
      $lSql.= ' LIMIT 0,1';
      #echo '<pre>---setFlagState---';var_dump($lSql,'#############');echo '</pre>';
      $lGruId = CCor_Qry::getInt($lSql);
      $lGruArr = CCor_Res::extract('id', 'name', 'gru');
      if (isset($lGruArr[$lGruId])) {
        $lGruName = $lGruArr[$lGruId];
      } else {
        $lGruName = '';
      }
      if (!empty($lGruName)) {
        $lGruName = '('.$lGruName.')';
        #echo '<pre>---setFlagState---'.get_class().'---';var_dump($lComment, $lGruName,strpos($lComment, $lGruName),FALSE === strpos($lComment, $lGruName),'#############');echo '</pre>';
        if (FALSE === strpos($lComment, $lGruName)) {
          $lGruName.= lan('lib.separator_:').' ';
          $lComment = $lGruName.$lComment;
        }
      }

      $lSqlUpd = 'UPDATE al_job_apl_states SET ';
      $lSql = '`status`='.esc($aVote).',';
      $lSql.= '`pos`=0,'; // dadurch kann i.d. APL-Seite der nächste User seine APL-Buttons sehen.
      $lSql.= '`done`="Y",'; // Kommentar abgegeben
      #echo '<pre>---setFlagState---'.get_class().'---';var_dump($lComment,$aAdd,'#############');echo '</pre>';
      if (!empty($lComment)) {
        #if ($aAdd) {
        #  $lSql.= 'comment=CONCAT(comment,"\\n",'.esc($lComment).'),';
        #} else {
        $lSql.= 'comment='.esc($lComment).',';
      #}
      }
      $lSql.= 'datum=NOW()';
      $lSqlW= ' WHERE 1';
      $lSqlW.= ' AND `user_id`='.esc($aUid);
      $lSqlW.= ' AND `inv`="Y"';
      $lSqlW.= ' AND `done`!="-"';//update nicht, bei confirm=one AND jemand anderes hat bereits confirmed
      $lSqlW.= ' AND `loop_id`='.esc($lLastId);

      $lSqlUpd.= $lSql.$lSqlW;
      #echo '<pre>---setFlagState---';var_dump($lSqlUpd,'#############');echo '</pre>'; exit;

      $lQry = new CCor_Qry($lSqlUpd);#CCor_Qry::exec($lSql);
      $lAffectedRows = $lQry -> getAffectedRows();
      #$lUsr = CCor_Usr::getInstance();
      if (0 == $lAffectedRows) {
        $lSqlIns = 'INSERT INTO al_job_apl_states SET ';
        $lSql.= ',`loop_id`='.esc($lLastId).',';
        $lSql.= '`user_id`='.esc($aUid).',';
        $lSql.= '`uid`='.esc($aUid).',';
        $lSql.= '`name`='.esc($aName).',';
        $lSql.= '`position`=1,';
        $lSql.= '`typ`='.esc($this -> mTyp).',';
        $lSql.= '`mand`='.esc($this -> mMid);
        $lSqlIns.= $lSql;
        $lQry -> query($lSqlIns);
        #echo '<pre>---setFlagState---';var_dump($lSqlIns,'#############');echo '</pre>';
      }
      $this -> setNewGroupState($aUid, $lLastId);
      $this -> setNewMailState($lLastId);
    } else {
      $lRet = FALSE;
    }
    return $lRet;
  }

  public function setNewGroupState($aUserId, $aLoopId, $aActiveIds = NULL) {
    $lActiveIds = $this->getActiveStateIds($aLoopId);
    if (empty($lActiveIds)) return;
    $lSql = 'REPLACE INTO `al_job_apl_states`';
    $lSql.= ' (`id`,`mand`,`loop_id`,`typ`,`user_id`,`pos`,`position`,`datum`,`uid`,`name`,`backupuser_id`,`status`,`comment`,`files`,`done`,`gru_id`,`confirm`,`inv`,`prefix`)';
    $lSql.= ' SELECT';
    $lSql.= ' g.`id`,'.MID.',g.`loop_id`,g.`typ`,g.`user_id`,u.`pos`,g.`position`,u.`datum`,g.`uid`,g.`name`,g.`backupuser_id`,u.`status`,g.`comment`,g.`files`,"-",g.`gru_id`,g.`confirm`,g.`inv`,g.`prefix`';
    $lSql.= ' FROM `al_job_apl_states` u, `al_job_apl_states` g';
    $lSql.= ' WHERE u.`loop_id` ='.esc($aLoopId).' AND u.`loop_id` = g.`loop_id`';
    $lSql.= ' AND u.`user_id` ='.esc($aUserId).' AND g.`user_id` != u.`user_id`';
    $lSql.= ' AND u.`gru_id` >0 AND u.`gru_id` = g.`gru_id`';
    $lSql.= ' AND u.`done` = "Y" AND u.`confirm` = "one"';
    $lSql.= ' AND g.id IN ('.implode(',', $lActiveIds).')';
    #echo '<pre>---loop.php---'.BR;print_r($lSql);echo BR.'</pre>';
    CCor_Qry::exec($lSql);
  }

  public function setNewMailState($aLoopId = NULL) {
    $lLoopId = (NULL == $aLoopId) ? $this -> getLastOpenLoop() : intval($aLoopId);

    $lIds = array();
    $lSql = 'SELECT id,pos,status,prefix,dur,ddlchg,inv ';
    $lSql.= 'FROM al_job_apl_states WHERE loop_id='.$lLoopId.' ';
    $lSql.= 'AND del != "Y" ';
    $lSql.= 'ORDER BY prefix,sub_loop,pos';
    $lQry = new CCor_Qry($lSql);

    #$lSql = 'SELECT st.`id`, st.`dur`, st.`ddlchg` FROM `al_job_apl_states` st WHERE st.`del`="N" AND st.`loop_id`='.$lLoopId;
    #$lSql.= ' AND st.`pos`=(SELECT MIN(st2.`pos`) FROM `al_job_apl_states` AS st2 WHERE st2.`loop_id`='.$lLoopId.' AND st2.`pos`>0 AND st2.`del`="N" GROUP BY st2.`loop_id`)';
    #$lSql.= ' ORDER BY st.`dur`  DESC';

    $lMax = 0;
    $lRows = array();
    foreach ($lQry as $lRow) {
      $lRows[] = $lRow;
      if ($lRow['dur'] > $lMax) $lMax = $lRow['dur'];
    }
    $lOldPrefix = 'EEHAHUHAHAHCHINGCHANGWALLAWALLABINGBANG!';
    $lOldSub = null;
    foreach ($lRows as $lRow) {
      $lSub = $lRow['sub_loop'];
      $lPrefix = $lRow['prefix'];
      if ($lSub != $lOldSub) {
        $lMinPos[$lPrefix] = MAX_SEQUENCE;
        $lOldSub = $lSub;
      }
      if ($lPrefix != $lOldPrefix) {
        $lMinPos[$lPrefix] = MAX_SEQUENCE;
        $lOldPrefix = $lPrefix;
      }
      $lPos = $lRow['pos'];
      $lIds[$lRow['id']] = $lRow;
      if (0 == $lRow['status'] AND $lMinPos[$lPrefix] > $lPos AND $lRow['inv'] == 'Y') {
        $lMinPos[$lPrefix] = $lPos;
      }
    }
    $lDurationInDates = CCor_Date::getWorkdays($lRow['dur']); //aufgrund der Sortierung kommt die groesste Durationtime als erstes
    foreach ($lIds as $lId => $lRow) {
      $lMin = $lMinPos[$lRow['prefix']];
      if ( ($lMin >= $lRow['pos']) && ('N' == $lRow['ddlchg']) ) {
        // setze den neuen eMail-Status
        $lItm = new CApi_Mail_Item('1','2','3','4');
        $lItm -> setNewMailState($lId);
        $lNewUsrDdl = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') + $lRow['dur'], date('Y')));
        $this -> setNewUsrDdl($lId, $lNewUsrDdl);
      }
    }
    //@TODO: Do we really need the below section?
    /*
    $lIsNowActive = $this->getActiveStateIds($lLoopId);
    foreach ($lIsNowActive as $lIndex => $lKey) {
      $lItm = new CApi_Mail_Item('1','2','3','4');
      $lItm -> setNewMailState($lKey);
    }
    */
  }

  /**
   * set new User Deadline Date
   * update fuer diese User mit ddl= "date('Y-m-d') + dur"; fallen somit die naechsten dur-tage aus dem reminder raus.
   *
   * @param $aStatesId
   * @param $aNewDdl New Deadline(Duration time) for user to do their apl task
   */
  public function setNewUsrDdl($aStatesId = 0, $aNewDdl = '') {
    if (0 < $aStatesId AND !empty($aNewDdl)) {
      $lSql = 'UPDATE `al_job_apl_states`';
      $lSql.= ' SET `ddlchg`="Y", `ddl`='.esc($aNewDdl);
      $lSql.= ' WHERE `id` ='.esc($aStatesId);
      CCor_Qry::exec($lSql);
      $this -> dbg(MID.', '.$this -> mJobId.', '.$this -> mTyp.': Deadline has changed to '.$aNewDdl.' for StatesNo: '.$aStatesId);
    }
  }

  /**
   * update Deadline Date
   * @param $aDdl
   */
  public function setDdl($aDdl = '') {
    // If Deadline exist, update Deadline
    if ($aDdl != '') {
      $lSql = 'UPDATE al_job_apl_loop';
      $lSql.= ' SET ddl='.esc($aDdl);
      $lSql.= ' WHERE id ='.esc($this -> mId);
      CCor_Qry::exec($lSql);
      $this -> dbg(MID.', '.$this -> mJobId.', '.$this -> mTyp.': Deadline has changed.');
    }
  }

  public function setAddData($aAdd) {
    if ($this -> testJobInApl()) {
      $lLid = $this -> getLastOpenLoop();
      if (empty($lLid)) {
        return;
      }
      if (is_array($aAdd)) {
        $aAdd = serialize($aAdd);
      }
      $lSql = 'UPDATE al_job_apl_loop SET ';
      $lSql.= 'add_data="'.addslashes(trim($aAdd)).'" ';
      $lSql.= 'WHERE id='.esc($lLid);
      CCor_Qry::exec($lSql);
    }
  }

  public function getAddData() {
    if ($this -> testJobInApl()) {
      $lLid = $this -> getLastOpenLoop();
      if (empty($lLid)) {
        return;
      }
      $lSql = 'SELECT add_data FROM al_job_apl_loop WHERE id='.$lLid;
      $lRet = CCor_Qry::getStr($lSql);
      if (empty($lRet)) return;
      return unserialize($lRet);
    }
  }

  public function getOverallState() {
    $lRet = self::APL_STATE_UNKNOWN;
    $lLid = $this -> getLastOpenLoop();
    if (empty($lLid)) {
      return $lRet;
    }
    $lSql = 'SELECT MIN(status) FROM al_job_apl_states WHERE loop_id='.$lLid;
    #echo '<pre>getOverallState---loop.php---'.get_class().'---';var_dump($lSql,'#############');echo '</pre>';
    return CCor_Qry::getInt($lSql);
  }

  public function getOverallFlagState() {
    $lRet = self::APL_STATE_UNKNOWN;
    $lLid = $this -> getLastOpenFlags($this -> mTyp);
    if (empty($lLid)) {
      return $lRet;
    }
    $lSql = 'SELECT MIN(status) FROM al_job_apl_states WHERE loop_id='.$lLid;
    return CCor_Qry::getInt($lSql);
  }

  public function getAllComments() {
    $lRet = array();
    $lLid = $this -> getLastOpenLoop();
    if (empty($lLid)) return $lRet;
    $lSql = 'SELECT * FROM al_job_apl_states WHERE loop_id='.$lLid;
    $lSql.= ' AND `inv`="Y"';
    $lSql.= ' ORDER BY user_id';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lRet[] = $lRow;
    }
    return $lRet;
  }

  public function getCurrentUserComment($aUid) {
    $lRet = '';
    $lLid = $this -> getLastOpenLoop();
    if (empty($lLid)) return '';
    $lSql = 'SELECT `comment` FROM al_job_apl_states WHERE loop_id='.$lLid;
    $lSql.= ' AND `inv`="Y"';
    // Deleted User sort out.
    $lSql.= ' AND `del`="N"';
    $lSql.= ' AND `user_id`='.intval($aUid);
    // $this -> dbg('getCurrentUserComment:'.$lSql);
    $lRet = CCor_Qry::getStr($lSql);
    
    //check if APL type has 'Action Separate' ticked
    $lAplType = CCor_Res::extract('code', 'flags', 'apltypes'); //apl type flags
    $lType = CCor_Qry::getStr('SELECT typ FROM al_job_apl_loop WHERE id='.$lLid);//get current apl type
    
    //check against type if flag is set bitset
    $lFlags = bitset($lAplType[$lType], atAction);
    $lRet = ($lFlags) ? '' : $lRet;
    
    return $lRet;
  }

  public function getCurrentUserDate($aUid) {
    $lRet = '';
    $lLid = $this -> getLastOpenLoop();
    if (empty($lLid)) return '';
    $lSql = 'SELECT `datum` FROM al_job_apl_states WHERE loop_id='.$lLid;
    $lSql.= ' AND `inv`="Y"';
    $lSql.= ' AND `user_id`='.intval($aUid);
    // $this -> dbg('getCurrentUserDate:'.$lSql);
    $lRet = CCor_Qry::getStr($lSql);
    $lDat = new CCor_Datetime();
    $lDat -> setSql($lRet);
    $lRet = $lDat -> getFmt(lan('lib.datetime.short'));
    return $lRet;
  }

  public function addToComment($aUid, $aComment) {
    $lRet = '';
    if ($this -> testJobInApl()) {
      $lLid = $this -> getLastOpenLoop();
      $lActiveIds = $this -> getActiveStateIds();
      $lSql = 'SELECT id,comment FROM al_job_apl_states WHERE loop_id='.$lLid;
      $lSql.= ' AND inv="Y"';
      $lSql.= ' AND user_id='.intval($aUid);
      $lSql.= ' AND id IN ('.implode(',', $lActiveIds).');';

      $lQry = new CCor_Qry($lSql);
      if ($lRow = $lQry -> getDat()) {
        $lCom = trim($lRow['comment']);
        if (!empty($lCom)) {
          $lCom.= LF.LF;
        }
        $lCom.= $aComment;
        $lSql = 'UPDATE al_job_apl_states SET comment='.esc($lCom).' ';
        $lSql.= 'WHERE id='.intval($lRow['id']).' ';
        $lQry -> query($lSql);
      }
    }
  }

  public function getCurrentUserFiles($aUid) {
    $lRet = '';
    if ($this -> testJobInApl()) {
      $lLid = $this -> getLastOpenLoop();
      if (empty($lLid)) return '';
      $lSql = 'SELECT files FROM al_job_apl_states WHERE loop_id='.$lLid.' ';
      $lSql.= 'AND user_id='.intval($aUid);
      $lRet = CCor_Qry::getStr($lSql);
    }
    return $lRet;
  }

  public function getCurrentUserFlagFiles($aUid) {
    $lRet = '';
    $lLastId = $this -> getLastOpenFlags($this -> mTyp);
    if (0 == $lLastId) return '';
    $lSql = 'SELECT files FROM al_job_apl_states WHERE loop_id='.$lLastId;
    $lSql.= ' AND user_id='.intval($aUid);
    $lRet = CCor_Qry::getStr($lSql);

    return $lRet;
  }


  public function addToFlagFiles($aUid, $aFile) {// wird in supload aufgerufen
    $lRet = '';
    $lLastId = $this -> getLastOpenFlags($this -> mTyp);
    if (0 == $lLastId) return '';
    $lSql = 'SELECT id,files FROM al_job_apl_states WHERE loop_id='.$lLastId;
    $lSql.= ' AND user_id='.intval($aUid);
    $this -> dbg('addToFlagFiles:'.$lSql);
    $lQry = new CCor_Qry($lSql);
    if ($lRow = $lQry -> getDat()) {
      $lFile = trim($lRow['files']);
      $this -> dbg('addToFlagFiles:'.$lFile);
      if (!empty($lFile)) {
        $lFile.= LF;
      }
      $lFile.= $aFile;
      $lSql = 'UPDATE al_job_apl_states SET files='.esc($lFile);
      $lSql.= ' WHERE id='.intval($lRow['id']);
      $this -> dbg('addToFlagFiles:'.$lSql);
      $lQry -> query($lSql);
    }
  }

  public function setFlagFiles($aUid, $aFile) {//vorher getCurrentUserFlagFiles, deswegen KEIN add...
    $lRet = '';
    $lLastId = $this -> getLastOpenFlags($this -> mTyp);
    if (0 == $lLastId OR empty($aFile)) return '';
    $lSql = 'SELECT id,files FROM al_job_apl_states WHERE loop_id='.$lLastId;
    $lSql.= ' AND user_id='.intval($aUid);
    // $this -> dbg('setFlagFiles:'.$lSql);
    $lQry = new CCor_Qry($lSql);
    if ($lRow = $lQry -> getDat()) {
      $lSql = 'UPDATE al_job_apl_states SET files='.esc($aFile);
      $lSql.= ' WHERE id='.intval($lRow['id']);
      // $this -> dbg('setFlagFiles:'.$lSql);
      $lQry -> query($lSql);
    }
  }

  public function addToFiles($aUid, $aFile) {
    $lRet = '';
    if ($this -> testJobInApl()) {
      $lLid = $this -> getLastOpenLoop();
      $lSql = 'SELECT id,files FROM al_job_apl_states WHERE loop_id='.$lLid;
      $lSql.= ' AND user_id='.intval($aUid);
      // $this -> dbg('addToFiles:'.$lSql);
      $lQry = new CCor_Qry($lSql);
      if ($lRow = $lQry -> getDat()) {
        $lFile = trim($lRow['files']);
        // $this -> dbg('addToFiles:'.$lFile);
        if (!empty($lFile)) {
          $lFile.= LF.LF;
        }
        $lFile.= $aFile;
        $lSql = 'UPDATE al_job_apl_states SET files='.esc($lFile);
        $lSql.= ' WHERE id='.intval($lRow['id']);
        // $this -> dbg('addToFiles:'.$lSql);
        $lQry -> query($lSql);
      }
    }
  }

  public function setToFiles($aUid, $aFile) {
    $lRet = '';
    if ($this -> testJobInApl()) {
      $lLid = $this -> getLastOpenLoop();
      $lSql = 'SELECT id,files FROM al_job_apl_states WHERE loop_id='.$lLid.' ';
      $lSql.= 'AND user_id='.intval($aUid);
      $lQry = new CCor_Qry($lSql);
      if ($lRow = $lQry -> getDat()) {
        $lFile = $aFile;
        $lSql = 'UPDATE al_job_apl_states SET files='.esc($lFile).' ';
        $lSql.= 'WHERE id='.intval($lRow['id']);
        $lQry -> query($lSql);
      }
    }
  }

  protected function testJobInApl() {
    if (empty($this -> mWebStatus)) {
      $lFac = new CJob_Fac($this -> mSrc, $this -> mJobId);
      $this -> dbg('testJobInApl:'.$this -> mSrc.' '.$this -> mJobId);
      $lJob = $lFac -> getDat();
      $this -> mWebStatus = $lJob['webstatus'];
    }
    $lSql = 'SELECT apl FROM `al_crp_status` s, `al_crp_master` m where m.mand='.MID.' AND s.mand=m.mand';
    $lSql.= ' AND m.code='.esc($this -> mSrc).' AND m.id=s.crp_id AND s.status='.$this -> mWebStatus.' LIMIT 0,1';
    // $this -> dbg('testJobInApl:'.$lSql);
    $lQry = new CCor_Qry($lSql);
    if ($lRow = $lQry -> getDat()) {
      $lApl = $lRow['apl'];
    }
    if(isset($lApl) AND 1 == $lApl) {
      return TRUE;
    } else {
      return FALSE;
    }
  }

  public function isInAplLoop() {
    return $this -> testJobInApl();
  }

  protected function getUserEmail($aUid) {
    $lRet = '';
    if (isset($this -> mUsrEmail[$aUid]) AND !empty($this -> mUsrEmail[$aUid])) {
      $lRet = $this -> mUsrEmail[$aUid];
    }
    return $lRet;
  }

  public function getAplUserlist() {
    // $this -> dbg('getAplUserlist');
    $lRet = array();
    $lLid = $this -> getLastOpenLoop();
    if (empty($lLid)) return $lRet;
    $lSql = 'SELECT * FROM al_job_apl_states WHERE 1';
    $lSql.= ' AND `inv`="Y"';
    // Deleted User sort out.
    $lSql.= ' AND `del`="N"';
    $lSql.= ' AND loop_id='.$lLid.' order by pos';
    $lAplUserArr = array();
    $lDat = new CCor_Date();
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      if (!isset($lAplUserArr[ $lRow['user_id'] ])) {//need distinct user
        $lAplUserArr[ $lRow['user_id'] ] = $lRow;

        $lus = array();
        $lus['uid'] = $lRow['user_id'];
        $lus['name'] = $lRow['name'];
        $lus['email'] = $this -> getUserEmail($lRow['user_id']);
        $lRet[ $lRow['user_id'] ] = $lus;
      }
    }
    return $lRet;
  }

  public static function cmpLines($a, $b) {
    $lA = $a[0]['prefix'];
    $lB = $b[0]['prefix'];
    if ($lA != $lB) {
      return ($lA > $lB) ? +1 : -1;
    }
    $lA = $a[0]['position'];
    $lB = $b[0]['position'];
    return ($lA > $lB) ? +1 : -1;
  }

  /*
   * Anzeige der AplFlags in den Listen: Jobliste
  * Bunte Striche
  */
  public function getAplCommitList($aLoopId, $aLink) {
    $lGruArr = CCor_Res::extract('id', 'name', 'gru');
    $lImgtemp = '';

    $lTbl = '<table cellpadding="2" cellspacing="0" border="0" class="w100p">';
    $lSql = 'SELECT user_id,name,status,position,gru_id,confirm,typ,prefix,sub_loop FROM al_job_apl_states WHERE loop_id='.$aLoopId;
    $lSql.= ' AND `inv`="Y"';
    // Deleted User is sorted out
    $lSql.= ' AND `del`="N"';
    $lSql.= ' ORDER BY prefix,sub_loop,`status` DESC, pos, gru_id';

    $lRows = array();
    $lTmpRows = array();
    $lQry = new CCor_Qry($lSql);
    $lOldSub = '';
    $lOldPrefix = '';
    foreach ($lQry as $lRow) {
      $lSub = $lRow['sub_loop'];
      $lPrefix = $lRow['prefix'];
      $lTyp = $lRow['typ'];
      if (($lSub != $lOldSub) && ($lPrefix == $lOldPrefix)) {
        unset($lTmpRows[$lPrefix]);
      }
      $lPos = $lRow['position'];
      $lGroupId = $lRow['gru_id'];
      if (0 == $lGroupId OR 'all' == $lRow['confirm']) {
        $lTmpRows[$lPrefix][$lRow['user_id'].'.'.$lPos][] = array('name' => $lRow['name'], 'status' => $lRow['status'], 'position' => $lRow['position'], 'prefix' => $lRow['prefix']);
      } elseif (0 < $lGroupId) {
        $lGruName = $lGruArr[$lGroupId];
        $lTmpRows[$lPrefix][$lGroupId.'.'.$lPos][] = array('name' => $lGruName, 'status' => $lRow['status'], 'position' => $lRow['position'], 'prefix' => $lRow['prefix']);
      }
      $lOldPrefix = $lPrefix;
      $lOldSub = $lSub;
    }
    foreach ($lTmpRows as $lPrefix => $lCurRows) {
      foreach ($lCurRows as $lKey => $lSubRows) {
        $lRows[$lKey.'_'.$lPrefix] = $lSubRows;
      }
    }

    $lComplete = true;
    $lAplTypes = CCor_Res::extract('code', 'name', 'apltypes');
    $lAplType = $lAplTypes[$lTyp];
    
    foreach ($lRows as $lRow) {
      // Farbe des jeweiligen Commit-Status wird generiert.
      $lSta = (isset($lRow['status']) ? $lRow['status'] : $lRow[0]['status']);
      if (self::APL_STATE_UNKNOWN == $lSta) $lComplete = false;
      $lImgtemp.= img('img/ico/16/flag-0'.$lSta.'_5px.gif');
    }
    if ($lComplete) {
      $lImgtemp.= img('img/ico/16/lock.png');
    }

    uasort($lRows, array(__CLASS__, 'cmpLines'));

    $lOld = NULL;
    foreach ($lRows as $lRow) {
      $lPrefix = $lRow[0]['prefix'];
      $lHeader = $lPrefix;
      if ($lOld != $lPrefix) {
        if (substr($lPrefix,0,3) == 'apl') {
          if (!isset($lTyp)) {
            $lTyp = CCor_Qry::getStr('SELECT typ FROM al_job_apl_loop WHERE id='.esc($aLoopId));
          }
          $lHeader = lan('job-apl.'.$lTyp.'.menu');
        } else {
		  $lGruName = CCor_Qry::getStr('SELECT name FROM al_gru WHERE id=' . esc($lPrefix));
		  if (!empty($lGruName)) {
		    $lHeader = $lGruName;
		  }
		}
        $lTbl.= '<tr><td class="th2" colspan="3">'.htm($lHeader).'</td></tr>';
        $lOld = $lPrefix;
      }
      $lTbl.= '<tr>';
      $lSta = (isset($lRow['status']) ? $lRow['status'] : $lRow[0]['status']);
      $lPosition = (isset($lRow['position']) ? $lRow['position'] : $lRow[0]['position']);
      $lTbl.= '<td class="w16">'.img('img/ico/16/flag-0'.$lSta.'.gif').'</td>';
      $lTbl.= '<td class="nw b">'.htm($lPosition).'</td>';
      $lTbl.= '<td class="nw">'.htm(addslashes($lRow[0]['name'])).'</td>';
      $lTbl.= '</tr>';
    }
    $lTbl.= '</table>';

    $lRet = '<div class="fl nw" data-toggle="tooltip" data-tooltip-head="'.$lAplType.'" data-tooltip-body="'.htm($lTbl).'">';

    $lRet.= '<a href="'.$aLink.'">';
    $lSql = 'SELECT num FROM al_job_apl_loop WHERE id='.intval($aLoopId);
    $lRet.= CCor_Qry::getStr($lSql).NB;

    // Farben der jeweiligen Commit-Status werden angezeigt.
    $lRet.= $lImgtemp;
    $lRet.= '</a></div>';

    return $lRet;
  }

  /*
   * Anzeige der AplFlags in den Listen: Jobliste
  * Bunte Striche
  */
  public function getFlagCommitList($aLoopId, $aFlagEve) {
    $lGruArr = CCor_Res::extract('id', 'name', 'gru');
    $lRows = array();

    $lTbl = '<table cellpadding="2" cellspacing="0" border="0">';
    $lSql = 'SELECT `name`,`status`,`position`,`gru_id`,`confirm` FROM al_job_apl_states WHERE `loop_id`='.$aLoopId;
    $lSql.= ' AND `inv`="Y"';
    $lSql.= ' AND `del`="N"'; // Deleted User is sorted out
    $lSql.= ' ORDER BY status DESC, pos, gru_id';
    #echo '<pre>---loop.php---'.get_class().'---';var_dump($lSql,'#############');echo '</pre>';

    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      if (0 == $lRow['gru_id'] OR 'all' == $lRow['confirm']) {
        $lRows[$lRow['name']] = array('name' => $lRow['name'], 'status' => $lRow['status'], 'position' => $lRow['position']);
      } elseif (0 < $lRow['gru_id']) {
        $lGruName = $lGruArr[$lRow['gru_id']];
        $lRows[$lGruName][] = array('name' => $lRow['name'], 'status' => $lRow['status'], 'position' => $lRow['position']);
      }
    }
    foreach ($lRows as $lName => $lRow) {
      $lSta = (isset($lRow['status']) ? $lRow['status'] : $lRow[0]['status']);
      $lImg = 'img/flag/';
      switch($lSta) {
        case self::APL_STATE_AMENDMENT :
          $lImg.= $aFlagEve['amend_ico'];
          BREAK;
        case self::APL_STATE_CONDITIONAL :
          $lImg.= $aFlagEve['condit_ico'];
          BREAK;
        case self::APL_STATE_APPROVED :
          $lImg.= $aFlagEve['approv_ico'];
          BREAK;
        default:
          $lImg = 'img/flag/flag-00';
      }
      $lImg.= '.gif';
      #echo '<pre>---'.get_class().'---  '.$aLoopId.BR;echo($lImg.BR);print_r($lRow);echo '###########</pre>';
      $lTbl.= '<tr>';

      $lPosition = (isset($lRow['position']) ? $lRow['position'] : $lRow[0]['position']);
      $lTbl.= '<td class="w16">'.img($lImg).'</td>';
      $lTbl.= '<td class="nw">'.htm($lPosition).'</td>';
      $lTbl.= '<td class="nw">'.htm($lName).'</td>';
      $lTbl.= '</tr>';

    }
    $lTbl.= '</table>';

    return $lTbl;
  }

  /**
   * Del User or Group from APL.
   * @param $aStatesId
   * @return unknown_type
   */
  public function delAplUser($aStatesId, $aUsrId, $aGruId, $aLoopId){
    $lStatesId = $aStatesId;
    $lUsrId = $aUsrId;
    $lGruId = $aGruId;
    $lLoopId = $aLoopId;

    // 23482/1 APL closure procedure: habe hier ,`pos`=0 rausgelassen, da es zu unerwünschten Seiteneffekten kommen kann. Muss immer del=N in den Abfragen haben!
    $lSql = 'UPDATE `al_job_apl_states` SET `del`="Y" WHERE 1 ';
    if ($lGruId == 0){
      // Delete User from APL
      $lSql.= ' AND `id`='.$lStatesId;
    } else {
      // Delete Memeber of Group
      $lSql.= ' AND `gru_id`='.$lGruId;
      $lSql.= ' AND `loop_id`='.$lLoopId;
    }
    CCor_Qry::exec($lSql);

    // Send Email to deleted User or Groups
    $this -> sendMailToDeletedAplUser($lStatesId, $lUsrId,$lGruId ,$lLoopId);

    // If Group will be deleted get all StatesId
    $lArrStatesId = Array();
    if ($lGruId != 0){
      $lSql = 'SELECT `id` from `al_job_apl_states` WHERE 1 ';
      $lSql.= ' AND `loop_id`='.$lLoopId;
      $lSql.= ' AND `gru_id`='.$lGruId;
      $lQry = new CCor_Qry($lSql);
      foreach ($lQry as $lRow) {
        $lArrStatesId[] = $lRow['id'];
      }
    }

    // Set Email to mlNoSending
    $lItm = new CApi_Mail_Item('1','2','3','4');
    $lItm -> cancelMailOfDeletedAplUser($lStatesId, $lArrStatesId);

    $this -> setNewGroupState($lUsrId, $lLoopId);
    $this -> setNewMailState($lLoopId);

    $lHis = new CJob_His($this -> mSrc, $this -> mJobId);
    if ($lGruId == 0) {
      $lName = CCor_Qry::getStr('SELECT concat(lastname,", ",firstname) as user FROM al_usr WHERE id='.$lUsrId);
      $lHis -> add(19, lan('his-titel-apl-usr.del'), lan('his-titel-apl-usr.del').': "'.$lName.'"');
    }
    else {
      $lName =  CCor_Res::extract('id', 'name', 'gru', array('id' => $lGruId));
      $lHis -> add(19, lan('his-titel-apl-group.del'), lan('his-titel-apl-group.del').': "'.$lName[$lGruId].'"');
    }
  }

  public function showFlagButtons($aSrc, $aJobId, $aAllFlags) {
    $lUid = CCor_Usr::getAuthId();
    $lShow = array();

    $lSql = 'SELECT `id`,`typ` FROM `al_job_apl_loop` WHERE 1 ';
    $lSql.= ' AND `typ`!="apl"';
    $lSql.= ' AND `jobid`='.esc($aJobId);
    $lSql.= ' AND `status`='.esc(self::APL_LOOP_OPEN);
    $lSql.= ' AND `mand`='.intval(MID);
    $lSql.= ' AND `src`='.esc($aSrc);
    $lQryLoop = new CCor_Qry($lSql);
    foreach ($lQryLoop as $lRow) {
      $lLoopId = $lRow['id'];
      $lFlagTyp = $lRow['typ'];
      #$lStepId = $lRow['step_id'];

      $lIds = array();
      $lSql = 'SELECT COUNT(*) FROM al_job_apl_states WHERE `loop_id`='.$lLoopId;
      $lSql.= ' AND `inv`="Y"';
      $lSql.= ' AND `del`="N"';
      $lAmount = CCor_Qry::getInt($lSql);
      #echo '<pre>showFlagButtons---loop.php---'.get_class().'---';var_dump($lSql,$lAmount,'#############');echo '</pre>';
      // gibt es ueberhaupt einen Eintrag?
      if (FALSE !== $lAmount AND 0 < $lAmount) {
        $lSql = 'SELECT `user_id`,`pos`,`status`,`typ` FROM al_job_apl_states WHERE `loop_id`='.$lLoopId;
        $lSql.= ' AND `inv`="Y"';
        if ( bitset($aAllFlags[$lFlagTyp]['flags_conf'], flagBtnDisplay1) ) {
          $lSql.= ' AND `done`="N"';
        }
        $lSql.= ' AND `del`="N"';
        $lSql.= ' ORDER BY `pos` ASC'; // wichtig, wenn user mehrfach (mit unterschiedl. pos) eingeladen ist
        $lQryStates = new CCor_Qry($lSql);
        #echo '<pre>2showFlagButtons---form.php---'.get_class().'---';var_dump($lSql,'#############');echo '</pre>';

        $lMinPos = MAX_SEQUENCE; // Behelfsvorbelegung
        foreach ($lQryStates as $lRow) {
          #echo '<pre>-showFlagButtons--loop.php---'.get_class().'---';var_dump($lRow,'#############');echo '</pre>';
          //brauche zur Anzeige distinct user_ids => koennen sich ueber backupuser-Fkt aendern und mehrfach vorkommen
          //Angezeigt werden muss die user_id mit der kleineren pos, da die agieren darf: $lSql.= ' ORDER BY pos';
          if (!isset($lIds[$lRow['user_id']])) {
          $lIds[$lRow['user_id']] = $lRow;
        }
        $lPos = $lRow['pos'];
        if (0 == $lRow['status'] AND $lMinPos > $lPos) {
          $lMinPos = $lPos;
        }
        }
        //$lMinPos != $lIds[$lUid]['pos'] zeigt die Buttons nur an, solange man keinen Button bestaetigt hat!
        #$lShow[$lFlagTyp] = !isset($lIds[$lUid]) OR $lMinPos != $lIds[$lUid]['pos'];
        if (!bitset($aAllFlags[$lFlagTyp]['flags_conf'], flagBtnDisplay1) AND (isset($lIds[$lUid]) AND 0 == $lIds[$lUid]['pos'])) {
          $ViewBtnMoreTimes = TRUE;
          $Indx = $lFlagTyp.'add';
          $lShow[$Indx] = $lIds[$lUid]['status'];
        } else {
          $ViewBtnMoreTimes = FALSE;
        }
        $lShow[$lFlagTyp] = (isset($lIds[$lUid]) AND ( $lMinPos == $lIds[$lUid]['pos'] OR $ViewBtnMoreTimes ));
        #echo '<pre>--showFlagButtons-loop.php---'.get_class().'---';var_dump($lShow,$lUid,$lIds,$lMinPos,'#############');echo '</pre>';
      } else {
        $lShow[$lFlagTyp] = '-';
      }
    }
    #echo '<pre>---loop.php---'.get_class().'---';var_dump($lShow,'#############');echo '</pre>';
    return $lShow;
  }

  public function isFlagConfirmed($aSrc, $aJobId, $aFlagTyp = '') {
    $lShow = array();

    $lSql = 'SELECT `id`,`typ` FROM al_job_apl_loop WHERE 1 ';
    $lSql.= (empty($aFlagTyp) ? ' AND typ!="apl"' : ' AND typ='.esc($aFlagTyp));
    $lSql.= ' AND `jobid`='.esc($aJobId);
    $lSql.= ' AND `status`='.esc(self::APL_LOOP_OPEN);
    $lSql.= ' AND `mand`='.intval(MID);
    $lSql.= ' AND `src`='.esc($aSrc);
    #echo '<pre>isFlagConfirmed---loop.php---'.get_class().'--$lLoopId-';var_dump($lSql,'#############');echo '</pre>';
    $lQryLoop = new CCor_Qry($lSql);
    foreach ($lQryLoop as $lRow) {
      $lLoopId = $lRow['id'];
      $lFlagTyp = $lRow['typ'];
      #$lStepId = $lRow['step_id'];

      $lIds = array();
      // gibt es ueberhaupt einen Eintrag?
      $lSql = 'SELECT COUNT(*) FROM al_job_apl_states WHERE loop_id='.$lLoopId;
      $lSql.= ' AND `inv`="Y"';
      $lSql.= ' AND `del`="N"';
      $lAmount = CCor_Qry::getInt($lSql);
      #echo '<pre>-isFlagConfirmed--loop.php---isFlagConfirmed '.get_class().'---';var_dump($lSql,$lAmount,'#############');echo '</pre>';

      if ($lAmount !== FALSE AND 0 < $lAmount) {
        $lSql.= ' AND `done`="N"';
        $lAmount2 = CCor_Qry::getInt($lSql);
        #echo '<pre>2isFlagConfirmed---loop.php---isFlagConfirmed '.get_class().'---';var_dump($lSql,$lAmount2,'#############');echo '</pre>';

        if (0 < $lAmount2) {
          $lShow[$lFlagTyp] = FALSE;
        } else {
          $lShow[$lFlagTyp] = TRUE;
        }
      } else {
        $lShow[$lFlagTyp] = FALSE;
      }
    }
    #echo '<pre>-isFlagConfirmed--loop.php---'.get_class().'---';var_dump($lShow,'#############');echo '</pre>';
    if (empty($aFlagTyp)) {
      return $lShow;
    } else {
      return $lShow[$aFlagTyp];
    }
  }

  public function getAllFlags($aSrc, $aJobId) {
    $lShow = array();

    $lSql = 'SELECT `id`,`typ` FROM al_job_apl_loop WHERE 1 ';
    $lSql.= ' AND `typ`!="apl"';
    $lSql.= ' AND `jobid`='.esc($aJobId);
    $lSql.= ' AND `mand`='.intval(MID);
    $lSql.= ' AND `src`='.esc($aSrc);
    #echo '<pre>isFlagConfirmed---loop.php---'.get_class().'--$lLoopId-';var_dump($lSql,'#############');echo '</pre>';
    $lQryLoop = new CCor_Qry($lSql);
    foreach ($lQryLoop as $lRow) {
      $lLoopId = $lRow['id'];
      $lFlagTyp = $lRow['typ'];
      #$lStepId = $lRow['step_id'];

      $lIds = array();
      // gibt es ueberhaupt einen Eintrag?
      $lSql = 'SELECT COUNT(*) FROM al_job_apl_states WHERE `loop_id`='.$lLoopId;
      $lSql.= ' AND `inv`="Y"';
      $lSql.= ' AND `del`="N"';
      $lAmount = CCor_Qry::getInt($lSql);
      #echo '<pre>-isFlagConfirmed--loop.php---isFlagConfirmed '.get_class().'---';var_dump($lSql,$lAmount,'#############');echo '</pre>';

      if ($lAmount !== FALSE AND 0 < $lAmount) {
        $lSql.= ' AND `done`="N"';
        $lAmount2 = CCor_Qry::getInt($lSql);
        #echo '<pre>2isFlagConfirmed---loop.php---isFlagConfirmed '.get_class().'---';var_dump($lSql,$lAmount2,'#############');echo '</pre>';

        if (0 < $lAmount2) {
          $lShow[$lFlagTyp] = FALSE;
        } else {
          $lShow[$lFlagTyp] = TRUE;
        }
      } else {
        $lShow[$lFlagTyp] = FALSE;
      }
    }
    #echo '<pre>-isFlagConfirmed--loop.php---'.get_class().'---';var_dump($lShow,'#############');echo '</pre>';

    return $lShow;

  }

  /**
   * Send Email to Deleted Apl User
   *
   * @param $aStatesId Apl Status Id.
   * @param $aUsrId User Id.
   * @param $aGruId Group Id.
   * @param $aLoopId Apl Loop Id.
   * @return send Email to deleted User.
   */
  public function sendMailToDeletedAplUser($aStatesId, $aUsrId, $aGruId, $aLoopId){
    $lStatesId = $aStatesId;
    $lArrUser = Array();
    $lArrUser[] = $aUsrId;
    $lGruId = $aGruId;
    $lLoopId = $aLoopId;

    // Get Templatename from Config.
    $lTempName = CCor_Cfg::get('tpl.email.apl.deleted.user', '');

    if ($lTempName == ''){
      $this -> dbg('No Template in config for deleted apl user.',mlInfo);
      return;
    }

    // Get Template Id
    $lSql = 'SELECT `id` FROM `al_eve_tpl` WHERE `mand` IN(0,'.MID.') AND `name`='.esc($lTempName).' AND `lang`='.esc(LAN).' ORDER BY `mand` DESC LIMIT 0,1';
    $lQry = new CCor_Qry($lSql);
    $lTempId = $lQry ->getAssoc();

    // Return if no Template Id.
    if (!$lTempId){
      $this -> dbg('Template:"'.$lTempName.'" can not found',mlError);
      return;
    }

    // If Group deleted, get invited Members
    if($lGruId != '0'){
      $lSql = 'SELECT `user_id` from al_job_apl_states WHERE 1 ';
      $lSql.= ' AND `loop_id`='.$lLoopId;
      $lSql.= ' AND `gru_id`='.$lGruId;
      $lQry = new CCor_Qry($lSql);
      foreach ($lQry as $lRow) {
        $lArrUser[] = $lRow['user_id'];
      }
    }

    // Get Job
    $lFac = new CJob_Fac($this -> mSrc, $this -> mJobId);
    $lJob = $lFac -> getDat();

    // Send Email
    foreach ($lArrUser as $lVal){
      $this -> mParams = Array('sid' => $lVal, 'tpl'=> $lTempId['id']);
      $lSender = new CApp_Sender('usr', $this -> mParams, $lJob);
      $lSender -> execute();
    }
  }

  public function isUserActiveNow($aUid) {
    $lLid = $this -> getLastOpenLoop();
    if (empty($lLid)) return FALSE;

    $lLoop = $this->getLoopById($lLid);
    $lType = $this->getEventType($lLoop['typ']);

    $lUid = intval($aUid);

    $lSql = 'SELECT user_id,status,position,prefix FROM al_job_apl_states WHERE loop_id='.$lLid;
    $lSql.= ' AND inv="Y"';
    if (!$lType->canChangeAfter()) {
      $lSql.= ' AND pos<>0';
    }
    $lSql.= ' AND del<>"Y"';

    $lSql.= ' ORDER BY prefix,position';
    $lRows = array();
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lRows[] = $lRow;
    }
    $lMin = MAX_SEQUENCE;
    $lOldPrefix = -1;
    foreach ($lRows as $lRow) {
      if ($lRow['prefix'] != $lOldPrefix) {
        $lOldPrefix = $lRow['prefix'];
        $lMin = MAX_SEQUENCE;
      }
      $lPos = $lRow['position'];
      if (!$lType -> canChangeAhead()) {
        if ($lPos > $lMin) continue;
      }
      if ($lRow['user_id'] == $lUid) {
        return TRUE;
      }
      if (($lPos < $lMin) && (!empty($lPos))) {
        $lMin = $lPos;
      }
    }
    return FALSE;
  }

  public function getActiveStateIds($aLoopId = NULL) {
    $lRet = array();
    $lLid = (empty($aLoopId)) ? $this -> getLastOpenLoop() : intval($aLoopId);
    if (empty($lLid)) return $lRet;

    $lLoop = $this->getLoopById($lLid);
    $lType = $this->getEventType($lLoop['typ']);

    $lSql = 'SELECT id,status,position,pos,name,prefix FROM al_job_apl_states WHERE loop_id='.$lLid;
    $lSql.= ' AND inv="Y" AND del="N"';
    $lSql.= ' ORDER BY prefix,position';

    $lRows = array();
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lRows[$lRow['prefix']][] = $lRow;
    }

    foreach ($lRows as $lPrefix => $lCurRows) {
      $this->dbg($lPrefix);
      // calculate current active position
      $lMin = MAX_SEQUENCE;
      foreach ($lCurRows as $lRow) {
        $lPos = $lRow['pos'];
        if ($lPos == 0) continue;
        if ($lPos < $lMin) {
          $lMin = $lPos;
        }
      } // foreach curRows

      //we now have the current active position, add enabled ids
      foreach ($lCurRows as $lRow) {
        $lPos = $lRow['pos'];
        $lPosition = $lRow['position'];
        $lRowId = $lRow['id'];
        if ($lPos == 0 &&  !$lType->canChangeAgain()) {
          continue;
        }
        if ($lPosition == $lMin) {
          // current active position is always okay
          $lRet[$lRowId] = $lRowId;
        } elseif ($lPosition < $lMin) {
          // current active state is already after state's position
          // canChangeAgain needs to be set
          if ($lType->canChangeAfter()) {
            $lRet[$lRowId] = $lRowId;
          } else {
          }
        } elseif ($lPosition > $lMin) {
          if ($lType->canChangeAll()) {
            $lRet[$lRowId] = $lRowId;
          } else {
          }
        }
      } // foreach curRows
    } // foreach prefix
    return array_keys($lRet);
  }

  /**
   * Has this job got a loop that was completed?
   *
   * A job is only completed if all revisors/revisor groups have set a status
   *
   * @param string $aType (optional) Check only loops of a specific type
   */
  public function hasCompletedLoop($aType = NULL) {
    $lSql = 'SELECT COUNT(*) FROM al_job_apl_loop ';
    $lSql.= 'WHERE src='.esc($this->mSrc).' ';
    $lSql.= 'AND jobid='.esc($this->mJobId).' ';
    $lSql.= 'AND completed="Y" ';
    if (!is_null($aType)) {
      $lSql.= 'AND typ='.esc($aType).' ';
    }
    $lCount = CCor_Qry::getInt($lSql);
    return ($lCount > 0);
  }

  /**
   *Save Amendment Cause in table 'al_job_apl_loop'
   *@param $aColumn string Columnn name for Amendment cause
   *@param $aVal    string Amendment Cause
   *@param $aLoopId int Loop Id
   *
   */
  public function setAplAmendRoutCause($aColumn,$aVal,$aLoopId){
    $lSql = 'UPDATE `al_job_apl_loop` SET ';
    $lSql.= backtick($aColumn).'='.esc($aVal);
    $lSql.= ' WHERE 1';
    $lSql.= ' AND `id`='.$aLoopId;
    CCor_Qry::exec($lSql);

  }
  /**
   * Check if the lasp apl was closed with an break.
   * Return TRUE if this was closed using break else FALSE
   * @param Int $aLastLoopId
   * @return string|boolean
   */
  public function getIfLastAplHasBreak($aLastLoopId) {
    $lRet = self::APL_STATE_UNKNOWN;
    if (empty($aLastLoopId)) {
      return $lRet;
    }
    $lSql = 'SELECT MAX(status) FROM al_job_apl_states WHERE loop_id='.$aLastLoopId;
    $lMaxState = CCor_Qry::getInt($lSql);
    if ($lMaxState == self::APL_STATE_BREAK) {
      return TRUE;
    }
    else return FALSE;
  }

  public function updateGroupState($aUid, $aState, $aMsg) {
    $lUid = (empty($aUid)) ? $aUid : CCor_Usr::getAuthId();
    $lLid = $this -> getLastOpenLoop();
    if (empty($lLid) OR empty($aState)) return;

    if ($aState == self::APL_STATE_BACKTOGROUP) {
      $lInv = 'Y';
    }
    if ($aState == self::APL_STATE_BLOCKFORTHEGROUP) {
      $lInv = 'N';
    }
    $lStatesArray = $this -> getActiveStateIds($lLid);
    if (empty($lStatesArray)) return;
    $lSql = 'SELECT * FROM al_job_apl_states WHERE id IN ('.implode(',', $lStatesArray).') AND user_id='.esc($lUid);
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
    if ($lRow['gru_id'] != 0) {
        $lSql = 'UPDATE `al_job_apl_states` ';
        $lSql.= 'SET `inv`="'.$lInv.'" ';
        if (!empty($aMsg)) {
#          $lSql.= ', comment='.esc($aMsg);
        }
        $lSql.= ' WHERE gru_id='.$lRow['gru_id'].' AND loop_id='.$lLid.' AND user_id != '.$lUid;
        CCor_Qry::exec($lSql);
      }
    }
  }

  public function addPassiveRolesintoApl() {
    $lLoopId = $this -> getLastOpenLoop();
    if ($lLoopId == 0) return; // If Apl is not open return.
    $lUser = CCor_Usr::getInstance();
    $lUid = $lUser -> getAuthId();
    $lSql = 'SELECT uid FROM al_job_apl_states WHERE loop_id='.$lLoopId.' AND uid='.$lUid;
    if (CCor_Qry::getInt($lSql)) return;

    $lBingo = FALSE;
    $lPassiveAplRoles = implode(',', CCor_Cfg::get('passive-apl-roles'));
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
    // This User is at least in one Role activated.
    if ($lBingo) {
      $lStateInfo = array();
      $lStateInfo['pos'] = -1;
      $lStateInfo['dur'] = 0;
      $lStateInfo['done'] = 'Y';
      $lStateInfo['inv'] = 'Y';
      $lStateInfo['typ'] = 'extended-apl';
      $lStateInfo['prefix'] = 'Role';
      $lStateInfo['status'] = self::APL_STATE_FORWARD;
      $lStateInfo['loop_id'] = $lLoopId;
      $this -> addItem($lUid, $lUser->getFullName(), $lStateInfo);
    }
  }


}
