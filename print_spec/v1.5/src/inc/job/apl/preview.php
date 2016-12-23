<?php
/**
 * Jobs APL Preview
 *
 * Given a job and a country, find the corresponding approval loop event to trigger
 *
 * @package    JOB
 * @copyright  Copyright (c) 2012 5Flow GmbH (http://www.5flow.eu)
 * @version $Rev: 3 $
 * @date $Date: 2012-02-21 09:50:56 +0100 (Di, 21 Feb 2012) $
 * @author $Author: g.emmans $
 */

class CInc_Job_Apl_Preview extends CCor_Ren {

  protected $mEventId;
  protected $mActions;
  protected $mPreviousStates;

  static $mJsInserted = false;
  protected static $mLoadedActions = array();

  public function __construct($aJid, $aDiv = null) {
    $this->mGroups = CCor_Res::extract('id', 'name', 'gru');
    $this->mJid = $aJid;
    $this->mKey = 'apl.dlg.'.strtolower($this->mJid);
    $this->mDiv = $aDiv;
    $this->mCheck = false;
    $this->mConditions = array();
    $this -> mPreventEmptyGroup = true;
    if (!self::$mJsInserted) {
      self::$mJsInserted = true;
      $lJs = $this->getJs();
      $lPag = CHtm_Page::getInstance();
      $lPag->addJs($lJs);
    }
    $this -> mEmailTemplate = CCor_Cfg::get('tpl.email');

  }

  public function setJob($aJobData) {
    $this->mJob = $aJobData;
  }

  public function setCheck($aFlag = true) {
    $this->mCheck = (bool)$aFlag;
  }

  public function getJs() {
    $lRet = 'jQuery(function(){'.LF;
    $lRet.= 'jQuery("form").submit(function() {'.LF;
    $lRet.= 'if (jQuery(".apl-tpl").length != jQuery(".bc-apl table").length) {'.LF;
    $lRet.= 'alert("Please select a workflow!"); return false;';
    $lRet.= '};';

    if ($this->mPreventEmptyGroup) {
      $lRet.= 'if (jQuery(".apl-norev").size() > 0) {'.LF;
      $lRet.= 'alert("Cannot start with empty revisor group!"); return false;'.LF;
      $lRet.= '}'.LF;
    }

    $lRet.= 'return true;';
    $lRet.= '});'.LF;

    $lRet.= '});'.LF;
    return $lRet;
  }

  private function debug($aText) {
    #echo $aText.BR;
  }

  /**
   * Set the event ID to display
   * @param int $aEventId The primary key (ID) of the event to display
   */
  public function setEventId($aEventId) {
    $this->mEventId = intval($aEventId);
    $this->mActions = NULL;
    return $this;
  }

  public function setActions($aArr) {
    $this->mActions = $aArr;
  }

  public function getActions() {
    return $this->mActions;
  }

  protected function getSession($aPrefix) {
    $lSes = CCor_Ses::getInstance();
    $lRes = $lSes[$this->mKey];
    $this->debug('getSession '.$this->mKey.' '.serialize($lRes));
    return $lRes[$aPrefix];
  }

  protected function setSession($aPrefix, $aValue) {
    $lSes = CCor_Ses::getInstance();
    $lRes = $lSes[$this->mKey];
    $lRes[$aPrefix] = $aValue;
    $this->debug('setSession '.$this->mKey.' '.serialize($aValue));
    #var_dump($lKey,$aPrefix,$aValue);
    $lSes[$this->mKey] = $lRes;
    $lSes->offsetSet($this->mKey, $lRes);
  }

  protected function unsetSession($aPrefix) {
    $lSes = CCor_Ses::getInstance();
    $lRes = $lSes[$this->mKey];
    $this->debug('unsetSession '.$this->mKey.' '.serialize($lRes));
    unset($lRes[$aPrefix]);
    $lSes[$this->mKey] = $lRes;
  }

  public function saveToSession($aPrefix) {
    $this->debug('Save '. $aPrefix.' '.serialize($this->mActions));
    $lActions = array();
    foreach ($this->mActions as $lAct) {
      $lAct['prefix'] = $aPrefix;
      ksort($lAct);
      $lActions[] = $lAct;
    }
    $this->mActions = $lActions;
    $this->setSession($aPrefix, $this->mActions);
  }

  public function loadFromSession($aPrefix) {
    $this->mActions = $this->getSession($aPrefix);
    #$this->debug('Load '. $aPrefix.' '.serialize($this->mActions));
    #$this->dump($this->mActions);
    return $this;
  }

  public function clearSession($aPrefix) {
    $this->unsetSession($aPrefix);
  }

  public function killSession() {
    $lSes = CCor_Ses::getInstance();
    $this->debug('killSession '.$this->mKey);
    unset($lSes['apl.dlg.'.$this->mJid]);
  }

  public function deleteAction($aPrefix, $aHash) {
    if (empty($this->mActions)) {
      $this->loadFromSession($aPrefix);
    }
    $lRes = array();
    foreach ($this->mActions as $lRow) {
      $lHash = md5(serialize($lRow));
      if ($lHash != $aHash) {
        $lRes[] = $lRow;
      }
    }
    $this->mActions = $lRes;
    $this->renumberPositions();
  }

  public function addUsers($aPrefix, $aUids, $aPos, $aDays, $aTyp = 'email_usr') {
    if (empty($this->mActions)) {
      $this->loadFromSession($aPrefix);
    }
    $this->debug('Adding '. $aPrefix.' '.serialize($this->mActions));

    $lPar = array();
    $lPar['tpl'] = $this -> mEmailTemplate['apl'];
    $lPar['inv'] = 'Y';
    $lPar['confirm'] = 'one';

    if (substr($aPos,0,1) == 'a') {
      $lMode = 'add';
    } else {
      $lMode = 'insert';
    }
    $lPos = (substr($aPos, 1))-1;

    $lRow['typ'] = $aTyp;
    $lRow['pos'] = $lPos;
    $lRow['days'] = $aDays;
    $lRow['prefix'] = $aPrefix;
    $lRow['active'] = 1;
    $lRow['param'] = $lPar;
    $lRow['del'] = TRUE;
    $lAct = array();
    $lIns = FALSE;
    $lOffs = 0;

    if ($lMode == 'add') {
      foreach ($this->mActions as $lRowAct) {
        if (!$lIns && ($lRowAct['pos'] == $lPos)) {
          foreach ($aUids as $lUid) {
            $lPar['sid'] = $lUid;
            ksort($lPar);
            $lRow['param'] = $lPar;
            $lRow['pos'] = $lPos;
            $lAct[] = $lRow;
          }
          $lIns = TRUE;
        }
        if (EVENT_DEFER_POSITION != $lRowAct['pos']) {
          $lRowAct['pos'] = $lRowAct['pos']+$lOffs;
        }
        $lAct[] = $lRowAct;
      }
    } else {
      if (!empty($this->mActions))
        foreach ($this->mActions as $lRowAct) {
        if (!$lIns && ($lRowAct['pos'] >= $lPos)) {
          foreach ($aUids as $lUid) {
            $lPar['sid'] = $lUid;
            $lRow['param'] = $lPar;
            $lAct[] = $lRow;
          }
          $lIns = TRUE;
          $lOffs++;
        }
        if (EVENT_DEFER_POSITION != $lRowAct['pos']) {
          $lRowAct['pos'] = $lRowAct['pos']+$lOffs;
        }
        $lAct[] = $lRowAct;
      }
    }
    if (!$lIns) {
      foreach ($aUids as $lUid) {
        $lPar['sid'] = $lUid;
        $lRow['param'] = $lPar;
        $lAct[] = $lRow;
      }
    }

    $this->mActions = $lAct;
    $this->renumberPositions();
  }

  protected function renumberPositions() {
    $lCur = -1;
    $lOld = null;
    $lAct = array();
    foreach ($this->mActions as $lRow) {
      if ($lRow['pos'] == EVENT_DEFER_POSITION) {
        $lAct[] = $lRow;
        continue;
      }
      if ($lRow['pos'] !== $lOld) {
        $lCur++;
        $lOld = $lRow['pos'];
      }
      $lRow['pos'] = $lCur;
      $lAct[] = $lRow;
    }
    $this->mActions = $lAct;
  }

  protected function loadActionsFor($aEventId) {
    if (isset(self::$mLoadedActions[$aEventId])) {
      return self::$mLoadedActions[$aEventId];
    }
    $lRet = array();

    $lSql = 'SELECT * FROM al_eve_act WHERE eve_id='.$aEventId.' ';
    $lSql.= 'ORDER BY pos,id';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      try {
        $lParam = unserialize($lRow['param']);
        $lParam['inv'] = (array_key_exists('inv', $lParam) ? strtoupper($lParam['inv']) : strtoupper($lParam['def']));
        unset($lParam['def']);
        $lRow['param'] = $lParam;
      } catch (Exception $lExc) {
        $this->dbg($lExc->getMessage(), mlError);
      }
      $lAct = $lRow->toArray();
      $lRet[] = $lAct;
    }
    self::$mLoadedActions[$aEventId] = $lRet;
    return $lRet;
  }

  public function loadActions() {
    $this->mActions = array();
    $lActions = $this->loadActionsFor($this->mEventId);
    // even though we have cached the event actions, some actions may depend
    // on conditions using the dynamic field / country
    foreach ($lActions as $lRow) {
      if (!empty($lRow['cond_id'])) {
        $lMet = $this->isConditionMet($lRow['cond_id']);
        if (!$lMet) continue;
      }
      $this->mActions[] = $lRow;
    }
    $this->renumberPositions(); // necessary if a position's condition is false
    return $this->mActions;
  }

  protected function isConditionMet($aCondId) {
    if (isset($this->mConditions[$aCondId])) {
      return $this->mConditions[$aCondId];
    }
    $lFac = new CInc_App_Condition_Registry();
    $lObj = $lFac->loadFromDb($aCondId);
    $lObj->setContext('data', $this->mJob);
    $lMet = $lObj->isMet();
    $this->mConditions[$aCondId] = $lMet;
    return $lMet;
  }

  public function loadPreviousAplStates($aType, $aPrefix) {
    $lSrc = $this->mJob['src'];
    $lLoop = new CApp_Apl_Loop($lSrc, $this->mJid, $aType);

    $lLid = $lLoop->getLastOpenLoop();

    $lRet = array();

    $lSql = 'SELECT status,prefix,user_id,gru_id ';
    $lSql.= 'FROM al_job_apl_states ';
    $lSql.= 'WHERE loop_id='.$lLid.' ';
    $lSql.= 'AND done="Y" ';
    $lSql.= 'AND prefix='.esc($aPrefix).' ';
    $lSql.= 'ORDER BY datum'; // later states will overwrite older ones
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      if (!empty($lRow['gru_id'])) {
        $lRet['G'.$lRow['gru_id']] = $lRow['status'];
      } else {
        $lRet['U'.$lRow['user_id']] = $lRow['status'];
      }
    }
    $this->mPreviousStates = $lRet;
    return $lRet;
  }

  protected function getPreviousGroupStateTd($aGroupId, $aRow, $aParam) {
    $lRet = $this->tdPlain(NB);
    if ($aParam['inv'] == 'N') return $lRet;
    if (EVENT_DEFER_POSITION == $aRow['pos']) return $lRet;

    $lState = 0;
    if (isset($this->mPreviousStates['G'.$aGroupId])) {
      $lState = $this->mPreviousStates['G'.$aGroupId];
    }
    $lRet = img('img/ico/16/flag-0'.$lState.'.gif');
    return $this->tdPlain($lRet, ' ac');
  }

  protected function getPreviousUserStateTd($aUserId, $aRow, $aParam) {
    $lRet = $this->tdPlain(NB);
    if ($aParam['inv'] == 'N') return $lRet;
    if (EVENT_DEFER_POSITION == $aRow['pos']) return $lRet;

    $lState = 0;
    if (isset($this->mPreviousStates['U'.$aUserId])) {
      $lState = $this->mPreviousStates['U'.$aUserId];
    }
    $lRet = img('img/ico/16/flag-0'.$lState.'.gif');
    return $this->tdPlain($lRet, ' ac');
  }

  public function getCont() {
    if (is_null($this->mActions)) {
      $this->loadActions();
    }
    if (empty($this->mActions)) {
      return '';
    }

    $lRet = '<table class="tbl w400" cellpadding="2"><tr>';
    if ($this->mPreviousStates) {
      $lRet.= '<th class="th2 w16">&nbsp;</th>';
    }
    if ($this->mCheck) {
      $lRet.= '<th class="th2 w16">&nbsp;</th>';
    }
    $lRet.= '<th class="th2 w16">POS</th>';
    $lRet.= '<th class="th2">Who?</th>';
    $lRet.= '<th class="th2 ac">Days</th>';
    $lRet.= '<th class="th2">Role</th>';
    $lRet.= '<th class="th2">&nbsp;</th>';
    $lRet.= '</tr>';
    $this->mCls = 'td1';
    $lOld = 0;
    $lSum = 0;
    $lMax = 0;

    foreach ($this->mActions as $lRow) {
      if($lRow['active'] == 0) continue;
      $lPos = $lRow['pos'];
      $lArr = $lRow['param'];

      if ($lOld != $lPos) {
        $this->mCls = ($this->mCls == 'td1') ? 'td2' : 'td1';
        $lOld = $lPos;
        $lSum+= $lMax;
        $lMax = 0;
      }

      $lTyp = $lRow['typ'];
      $lDays = isset($lRow['dur']) ? $lRow['dur'] : 2;
      if ($lArr['inv'] != 'N') {
        if ($lDays > $lMax) $lMax = $lDays;
      }
      if ('email_usr' == $lTyp) {
        $lRet.= $this->getEmail_usr($lRow, $lArr);
      }
      if ('email_gru' == $lTyp) {
        $lRet.= $this->getEmail_gru($lRow, $lArr);
      }
      if ('email_rol' == $lTyp) {
        $lRet.= $this->getEmail_rol($lRow, $lArr);
      }
      if('email_gruasrole' == $lTyp){
        $lRolFie = $lArr['sid'];
        $lArr['sid'] = $this->mJob[$lRolFie];

    	$lRet .= $this->getEmail_gru($lRow, $lArr);
      }
    }
    $lSum+= $lMax;
    $lRet.= '<tr>';

    if ($this->mCheck) {
      $lRet.= '<td class="th3 w16">&nbsp;</td>';
    }
    if ($this->mPreviousStates) {
      $lRet.= '<td class="th3 w16">&nbsp;</td>';
    }
    $lRet.= '<td class="th3">&nbsp;</td>';
    $lRet.= '<td class="th3">Total Leadtime</td>';
    #$lRet.= '<th class="th2">Action</th>';
    #$lRet.= '<th class="th2">&nbsp;</th>';

    $lRet.= '<td class="th3 ac">'.$lSum.'</td>';
    $lRet.= '<th class="th3">&nbsp;</th>';
    $lRet.= '<th class="th3">&nbsp;</th>';
    $lRet.= '</tr>';

    $lRet.= '</table>';
    #$lRet.= var_export($this->mActions, true);

    return $lRet;
  }

  protected function encKey($aCtr) {
    #return htm("'".$aCtr."'");
    if (empty($aCtr)) $aCtr = ' ';
    return base64_encode($aCtr);
  }

  protected function getCheck($aCtr, $aNum, $aType = 'usr', $aId = null) {
    if (!$this->mCheck) return '';
    $lRet = '<input type="checkbox" name="apl_chk['.$this->encKey($aCtr).']['.$aNum.']" checked="checked" />';
    return $this->tdPlain($lRet);
  }

  protected function getEmail_gru($aRow, $aParam) {
    $lRet = '';
    $lChkId = getNum();
    $lGid = intval($aParam['sid']);
    $lNum = getNum('tr').uniqid();
    $lMem = $this->getMembers($lGid);

    $lRet.= '<tr class="pc">';
    $lCtr = $aRow['prefix'];
    $lRet .= $this->getCheck($lCtr, $lChkId, 'gru', $lGid);

    if ($this->mPreviousStates) {
      $lRet.= $this->getPreviousGroupStateTd($lGid, $aRow, $aParam);
    }

    $lPos = $aRow['pos'];
    if (EVENT_DEFER_POSITION == $lPos) {
      $lRet.= $this->td(lan('lib.eve.deferred'), ' ac');
    } else {
      $lRet.= $this->td($lPos+1, ' ac');
    }
    #$lRet.= '<td class="'.$this->mCls.'">';
    #$lRet.= img('img/ico/16/gru.gif');
    #$lRet.= '</td>';
    $lGroup = $this->getGroup($lGid);
    $lGroupShort = substr(strrchr($lGroup, '/'),2);

    $lCont = htm($lGroup);
    $lCls = '';
    if (empty($lMem)) {
      $lCont.= ' - no members!';
      $lCls = ' cr apl-norev';
    } else {
      #$lCont = (count($lMem)).' x '.$lCont;
      $lCont.= ' ... ';
    }
    $lCont = '<a href="#" onClick="Flow.Std.togTr(\''.$lNum.'\'); return true;">'.$lCont.'</a>';

    $lSer = serialize($aRow);
    $lInp = '<input type="hidden" name="apl_eve['.$this->encKey($lCtr).']['.$lChkId.']" value="'.htm($lSer).'" />';
    $lCont.= $lInp;
    #$lCont.= htm($lSer);

    $lRet.= $this->tdPlain($lCont, $lCls);
    $lDays = isset($aRow['dur']) ? $aRow['dur'] : 2;

    if ($aParam['inv'] == 'N') {
      $lRet.= $this->td(' ', ' tg');
      $lRet.= $this->td('Reader');
    } else {
      $lRet.= $this->td($lDays, ' ac');
      $lRet.= $this->td('Revisor');
    }

    $lDel = isset($aRow['del']) ? $aRow['del'] : FALSE;
    if ($lDel) {
      $lHash = md5(serialize($aRow));
      $lImg = img('ico/16/del.gif');
      $lSrc = (empty($this -> mJob['src'])) ? 'rep' : $this -> mJob['src'];
      $lCont = '<a onclick="javascript:Flow.apl.deleteAction(this,\'job-'.$lSrc.'\',\''.$this->mJid.'\',\''.$lCtr.'\',\''.$lHash.'\'); return true;" class="cp nav">'.$lImg.'</a>';
      $lRet.= $this->tdPlain($lCont, ' ac');
    } else {
      $lRet.= $this->tdPlain('&nbsp;');
    }

    $lRet.= '</tr>';

    $lRet.= '<tr class="togtr" id="'.$lNum.'" style="display:none">'.LF;
    $lRet.= '<td class="td1 tg">&nbsp;</td>';

    $lRet.= '<td class="td1" colspan="6" style="color:#666">';
    #$lRet.= htm($lGroup).' has '.(count($lMem)).' members'.BR;
    $lRet.= '<ol style="margin:1em; padding:0.2em">';
    if (empty($lMem)) {
      $lRet.= '<a href="index.php?act=gru-mem&id='.$lGid.'" target="_blank" class="nav">Add members</a>';
    } else {
      $lRet.= implode(LF, $lMem);
    }
    $lRet.= '</ol>';
    $lRet.= '</td>';

    $lRet.= '</tr>';
    return $lRet;

  }

  protected function getEmail_usr($aRow, $aParam) {
    $lRet = '';

    $lChkId = getNum();
    $lUid = intval($aParam['sid']);
    $lRet.= '<tr class="pc">';
    $lCtr = $aRow['prefix'];
    $lRet .= $this->getCheck($lCtr, $lChkId, 'usr', $lUid);

    if ($this->mPreviousStates) {
      $lRet.= $this->getPreviousUserStateTd($lUid, $aRow, $aParam);
    }

    $lPos = $aRow['pos'];
    if (EVENT_DEFER_POSITION == $lPos) {
      $lRet.= $this->td(lan('lib.eve.deferred'), ' ac');
    } else {
      $lRet.= $this->td($lPos+1, ' ac');
    }
    #$lRet.= '<td class="'.$this->mCls.'">';
    #$lRet.= img('img/ico/16/usr.gif');
    #$lRet.= '</td>';

    $lAllUsr = CCor_Res::extract('id', 'fullname', 'usr');
    $lName = (isset($lAllUsr[$lUid])) ? $lAllUsr[$lUid] : 'unknown';
    $lCont = htm($lName);

    $lSer = serialize($aRow);
    if (EVENT_DEFER_POSITION != $lPos) {
      $lInp = '<input type="hidden" name="apl_eve['.$this->encKey($lCtr).']['.$lChkId.']" value="'.htm($lSer).'" />';
      $lCont.= $lInp;
    }
    $lRet.= $this->tdPlain($lCont);
    $lDays = isset($aRow['dur']) ? $aRow['dur'] : 2;

    if ($aParam['inv'] == 'N') {
      $lRet.= $this->tdPlain(NB, ' tg');
      $lRet.= $this->td('Reader');
    } else {
      $lRet.= $this->td($lDays, ' ac');
      $lRet.= $this->td('Revisor');
    }

    $lDel = isset($aRow['del']) ? $aRow['del'] : FALSE;
    if ($lDel) {
      $lHash = md5(serialize($aRow));
      $lImg = img('ico/16/del.gif');
      $lSrc = (empty($this -> mJob['src'])) ? 'rep' : $this -> mJob['src'];
      $lCont = '<a onclick="javascript:Flow.apl.deleteAction(this,\'job-'.$lSrc.'\',\''.$this->mJid.'\',\''.$lCtr.'\',\''.$lHash.'\'); return true;" class="cp nav">'.$lImg.'</a>';
      $lRet.= $this->tdPlain($lCont, ' ac');
    } else {
      $lRet.= $this->tdPlain('&nbsp;');
    }


    $lRet.= '</tr>';
    return $lRet;
  }

  protected function getEmail_rol($aRow, $aParam) {
    $lRoleField = $aParam['sid'];
    $lUsrName = CCor_Res::extract('id', 'fullname', 'usr', array('id' => $this->mJob[$lRoleField]));
    $aParam['sid'] = $this->mJob[$lRoleField];

    $lRet = '';
    $lChkId = getNum();

    $lRet.= '<tr class="pc">';
    $lCtr = $aRow['prefix'];
    $lUid = $aParam['sid'];
    $lRet.= $this->getCheck($lCtr, $lChkId, 'rol', $lUid);

    if ($this->mPreviousStates) {
    	$lRet.= $this->getPreviousUserStateTd($lUid, $aRow, $aParam);
    }

    $lPos = $aRow['pos'];
    if (EVENT_DEFER_POSITION == $lPos) {
      $lRet.= $this->td(lan('lib.eve.deferred'), ' ac');
    } else {
      $lRet.= $this->td($lPos+1, ' ac');
    }

    $lAllUsr = CCor_Res::extract('alias', 'name', 'rol');
    $lName = (isset($lAllUsr[$lRoleField])) ? $lAllUsr[$lRoleField] : 'unknown';
    $lName.= ' ('.$lUsrName[$this -> mJob[$lRoleField]].')';

    $lSer = serialize($aRow);
    if (EVENT_DEFER_POSITION != $lPos) {
      $lInp = '<input type="hidden" name="apl_eve['.$this->encKey($lCtr).']['.$lChkId.']" value="'.htm($lSer).'" />';
      $lCont.= $lInp;
    }

    $lCont.= $lInp;
    $lRet.= $this->tdPlain('Role '.$lName.$lInp);
    $lDays = isset($aRow['dur']) ? $aRow['dur'] : 2;
    $lRet.= $this->td($lDays, ' ac');
    $lRet.= $this->tdPlain('&nbsp;');

    $lDel = isset($aRow['del']) ? $aRow['del'] : FALSE;
    if ($lDel) {
      $lHash = md5(serialize($aRow));
      $lImg = img('ico/16/del.gif');
      $lSrc = (empty($this -> mJob['src'])) ? 'rep' : $this -> mJob['src'];
      $lCont = '<a onclick="javascript:Flow.apl.deleteAction(this,\'job-'.$lSrc.'\',\''.$this->mJid.'\',\''.$lCtr.'\',\''.$lHash.'\'); return true;" class="cp nav">'.$lImg.'</a>';
      $lRet.= $this->tdPlain($lCont, ' ac');
    } else {
      $lRet.= $this->tdPlain('&nbsp;');
    }

    $lRet.= '</tr>';
    return $lRet;
  }

  protected function getGroup($aGroupId) {
    $lGid = intval($aGroupId);
    $lRet = '';
    if (empty($lGid)) return '';

    if (isset($this->mGroups[$lGid])) {
      return $this->mGroups[$lGid];
    }
    return '';
  }

  protected function getMembers($aGroupId) {
    $lGid = intval($aGroupId);
    if (empty($lGid)) return '';
    $lMem = CCor_Res::extract('id', 'fullname', 'usr', array('gru' => $lGid));
    if (empty($lMem)) return array();

    $lRet = array();
    foreach ($lMem as $lName) {
      $lRet[] = '<li style="color:#999">'.htm($lName).'</li>';
    }
    return $lRet;
  }

  protected function td($aContent, $aAddClass = '') {
    $lRet = '<td class="'.$this->mCls.$aAddClass.'">';
    $lRet.= htm($aContent);
    $lRet.= '</td>';
    return $lRet;
  }

  protected function tdPlain($aContent, $aAddClass = '') {
    $lRet = '<td class="'.$this->mCls.$aAddClass.'">';
    $lRet.= $aContent;
    $lRet.= '</td>';
    return $lRet;
  }

}
