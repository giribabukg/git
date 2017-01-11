<?php
class CInc_Job_Event extends CCor_Obj {

  /**
   * Registry/factory for Event Action Objects
   *
   * @var CApp_Event_Action_Registry
   */
  protected $mReg;
  protected $mFlagItemsClass;
  // There is only different between CopyJobTO And CopyTaskTo
  // that CopyTaskTo copy to Job mit Projectassigment.
  public    $mCopyJobTo = ''; // If Event 'Copy_Job' defined, set the target Jobtyp for Copy
  public    $mCopyTaskTo = ''; // If Event 'Copy_Task' defined, set the target Jobtyp for Copy
  public    $mMoveJobTo = '';
  protected $mUsrArr = array();
  protected $mAdd = array();
  protected $mConditions = array();

  public function __construct($aEventId, $aJob, $aMsg = array(), $aIgn = array(), $aHisInsertId = 0) {
    $this -> mId = $aEventId;
    $this -> mJob = $aJob;
    $this -> setContext('job', $aJob);
    $this -> setContext('msg', $aMsg);
    $this -> mAct = array();
    $this -> mHisInsertId = $aHisInsertId;
    $this -> mIgn = $aIgn;
    $this -> loadActions();
  }

  public function setContext($aKey, & $aVal) {
    $this -> mContext[$aKey] = $aVal;
  }

  protected function loadActions() {
    $this -> mAllActions = CCor_Res::get('action');
    if (isset($this -> mAllActions[$this -> mId])) {
      $lActionArr = $this -> mAllActions[$this -> mId];
      foreach ($lActionArr as $lRow) {
        $this -> mAct[] = $lRow;
      }
    }
  }

  public function execute() {
    if (empty($this -> mAct)) {
      return TRUE;
    }
    foreach ($this -> mAct as $lAct) {
      if ($lAct['active']) {
        // ignore this notification?
        $lId = $lAct['id'];
        if (in_array($lId, $this -> mIgn)) {
          continue;
        }
        // If Event is 'copy_job', set the target Jobtype to $this ->mCopyJobTo
        if ($lAct['typ'] == 'copy_job'){
          $this -> setCopyJobTo($lAct);
          continue;
        }
        if ($lAct['typ'] == 'copy_task'){
          $this -> setCopyTaskTo($lAct);
          continue;
        }
        if ($lAct['typ'] == 'move_jobtype'){
          $this -> setMoveJobTo($lAct);
        }
        if (!empty($lAct['cond_id'])) {
          $lMet = $this->isConditionMet($lAct['cond_id']);
          if (!$lMet) continue;
        }
        $this -> doAction($lAct);
      }
    }
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

  /**
   * Create the Action Registry (if not already done)
   *
   * @return CApp_Event_Action_Registry
   */

  protected function getRegistry() {
    if (!isset($this -> mReg)) {
      $this -> mReg = new CApp_Event_Action_Registry();
    }
  }

  /**
   * Actually perform the specified action (e.g. send an email notification)
   * This function will be called by execute()
   *
   * @return boolean Successful?
   */

  protected function doAction($aAct) {
    $lTyp = $aAct['typ'];
    $lPar = toArr($aAct['param']);
    $lPar['hisId'] = $this -> mHisInsertId;
    $lPar['tablekeyid'] = $aAct['id'];
    if (isset($this -> mMailType) AND !empty($this -> mMailType)) $lPar['mailtype'] = $this -> mMailType;

    $this -> getRegistry();
    $lObj = $this -> mReg -> factory($lTyp, $this -> mContext, $lPar);
    if (!$lObj) {
      $this -> dbg('Failed to create '.$lTyp);
      return FALSE;
    }
    return $lObj -> execute();
  }

  /*
   * If Type = job_copy ,this function will be called
   * @param aAct Array Event Infos
   * */
  protected function setCopyJobTo($aAct){
    $lPar = toArr($aAct['param']);
    if (!empty($lPar['copy'])){
      $this-> mCopyJobTo = $lPar['copy'];
    }
  }

   /*
   * If Type = job_task ,this function will be called
   * @param aAct Array Event Infos
   * */
  protected function setCopyTaskTo($aAct){
    $lPar = toArr($aAct['param']);
    if (!empty($lPar['copy'])){
      $this-> mCopyTaskTo = $lPar['copy'];
    }
  }
  public function getCopyJobTo(){
    $lRet = $this -> mCopyJobTo;
    return $lRet;
  }

  public function getCopyTaskTo(){
    $lRet = $this -> mCopyTaskTo;
    return $lRet;
  }
  
  protected function setMoveJobTo($aAct){
    $lPar = toArr($aAct['param']);
    if (!empty($lPar['move'])){
      $this-> mMoveJobTo = $lPar['move'];
    }
  }
  
  public function getMoveJobTo(){
    $lRet = $this -> mMoveJobTo;
    return $lRet;
  }

  protected function getUsrNames() {
    if (empty($this -> mUsrArr)) {
      $this -> mUsrArr = CCor_Res::extract('id', 'departm_fullname', 'usr');
    }
    return $this -> mUsrArr;
  }

  protected function getFlagItemsClass($aFlagId, $aStepId) {
    if (!isset($this -> mFlagItemsClass[$aFlagId])) {
      $lAllFlags = CCor_Res::get('fla');
      $lFlagEve = $lAllFlags[$aFlagId];
      $lFlagId  = $lFlagEve['id'];
      $lFlagDdl = $lFlagEve['ddl_fie'];

	  $this -> mFlagClass = new CApp_Apl_Loop($this -> mJob['src'], $this -> mJob['jobid'], $lFlagId, MID, $this -> mJob['webstatus']);
      $lDatum = new CCor_Date();
      $lDatum -> setInp($this -> mJob[$lFlagDdl]);
      if (!$lDatum -> isEmpty()) {
        $lAdd['apl_date'] = $lDatum -> getFmt(lan('lib.date.long'));
      }
      #$lLastId = $this -> mFlagClass -> getLastOpenLoop();
      $lLastId = $this -> mFlagClass -> getLastOpenFlags($lFlagId);
      #$lLastId = (!empty($lLast_Id) ? $lLast_Id[$aFlagId] : 0);
      if (0 == $lLastId) {
        $this -> mAdd['apl_id'] = $this -> mFlagClass -> createLoop($lDatum -> getSql(), $aStepId);
        $this -> mFlagClass -> mId = $this -> mAdd['apl_id'];
      } else {
        $this -> mAdd['apl_id'] = $lLastId;
        $this -> mFlagClass -> mId = $this -> mAdd['apl_id'];
      }
      $this -> mFlagItemsClass[$aFlagId] = &$this -> mFlagClass;
    }
    return $this -> mFlagItemsClass[$aFlagId];
  }

  public function addFlagItems($aFlagId = '', $aStepId = '') {
    if (empty($aFlagId) OR empty($aStepId)) {
      return TRUE;
    }
    $this -> mFlagClass = $this -> getFlagItemsClass($aFlagId, $aStepId); //auch ohne Events Eintrag in die _apl_loop

    if (!empty($this -> mAct)) {
      $lUsrArr = $this -> getUsrNames();
      $i = 0;// dadurch können user, die in mehreren Gruppen sind, mehrfach im apl_states u. sys_mails eingetragen werden

      foreach ($this -> mAct as $lAct) {
        if ($lAct['active']) {
          $lEveActId = $lAct['id'];

          if (in_array($lEveActId, $this -> mIgn)) {
            continue;
          }

          $lTyp = explode('_', $lAct['typ']);
          if ('email' == $lTyp[0]) {
            $lPar = toArr($lAct['param']);
            $lTpl = $lPar['tpl'];
            $l1Invite = array();
            $lInfoArr = array('pos' => $lAct['pos']);

            switch ($lTyp[1]) {
              case 'rol' :
                $lRoleAli = $lPar['sid'];
                $lUid = $this -> mJob[$lRoleAli];
                if (!empty($lUid)){
                  $lNam = $lUsrArr[$lUid];
                  if (isset($lPar['inv'])) {
                    $lInfoArr['inv'] = $lPar['inv'];
                  }
                  $lAplStatesId = $this -> mFlagClass -> addItem($lUid, $lNam, $lInfoArr);

                  $l1Invite[$lUid] = $lInfoArr;
                  $l1Invite[$lUid]['id'] = $lUid;
                  $l1Invite[$lUid]['apl_id'] = $lAplStatesId;
                  $this -> mAdd['special_apl_usr'][$lEveActId.'.'.$lTpl][] = $l1Invite;
                }
                BREAK;
              case 'usr' :
                $lUid = $lPar['sid'];
                if (!empty($lUid) AND isset($lUsrArr[$lUid])){
                  $lNam = $lUsrArr[$lUid];
                  #if (isset($lPar['inv'])) {
                  #  $lInfoArr['inv'] = $lPar['inv'];
                  #}
                  $lAplStatesId = $this -> mFlagClass -> addItem($lUid, $lNam, $lInfoArr);

                  $l1Invite[$lUid] = $lInfoArr;
                  $l1Invite[$lUid]['id'] = $lUid;
                  $l1Invite[$lUid]['apl_id'] = $lAplStatesId;
                  $this -> mAdd['special_apl_usr'][$lEveActId.'.'.$lTpl][] = $l1Invite;
                }
                BREAK;
              case 'gru' :
                $lGid = $lPar['sid'];
                if (!empty($lGid)) {
                  $lSql = 'SELECT m.uid FROM al_usr u, al_usr_mem m WHERE u.id=m.uid AND u.del="N" AND m.gid='.$lGid;
                  $lQry = new CCor_Qry($lSql);
                  foreach ($lQry as $lRow) {
                    $lUid = $lRow['uid'];
                    if (!isset($lUsrArr[$lUid])) continue;
                    $lNam = $lUsrArr[$lUid];
                    $lInfoArr['gru'] = $lGid;
                    $lInfoArr['confirm'] = $lPar['confirm'];
                    $lAplStatesId = $this -> mFlagClass -> addItem($lUid, $lNam, $lInfoArr);

                    $l1Invite = $lInfoArr;
                    $l1Invite['id'] = $lUid;
                    $l1Invite['apl_id'] = $lAplStatesId;
                    $this -> mAdd['special_apl_usr'][$lEveActId.'.'.$lTpl]['G'.$i][$lUid] = $l1Invite;
                  }
                  $i++;
                }
                BREAK;
              case 'gruasrole' :
                $lRoleAli = $lPar['sid'];
                $lGid = $this -> mJob[$lRoleAli];
                if (!empty($lGid)) {
                  $lSql = 'SELECT m.uid FROM al_usr u, al_usr_mem m WHERE u.id=m.uid AND u.del="N" AND m.gid='.$lGid;
                  $lQry = new CCor_Qry($lSql);
                  foreach ($lQry as $lRow) {
                    $lUid = $lRow['uid'];
                    if (!isset($lUsrArr[$lUid])) continue;
                    $lNam = $lUsrArr[$lUid];
                    $lInfoArr['gru'] = $lGid;
                    $lInfoArr['confirm'] = $lPar['confirm'];
                    $lAplStatesId = $this -> mFlagClass -> addItem($lUid, $lNam, $lInfoArr);

                    $l1Invite = $lInfoArr;
                    $l1Invite['id'] = $lUid;
                    $l1Invite['apl_id'] = $lAplStatesId;
                    $this -> mAdd['special_apl_usr'][$lEveActId.'.'.$lTpl]['G'.$i][$lUid] = $l1Invite;
                  }
                  $i++;
                }
                BREAK;
              #case 'apl' :
              #  BREAK;
              #case 'gpm' :
              #  BREAK;
            }//end_switch
          }
          $this -> mContext['msg']['add'] = $this -> mAdd;
        }
      }
    }
  }

  public function closeFlags ($aFlagId = '', $aStepId = '') {
    if (empty($aFlagId) OR empty($aStepId)) {
      return TRUE;
    }

    $this -> mFlagClass = $this -> getFlagItemsClass($aFlagId, $aStepId);
    $this -> mFlagClass -> closeLoops();
    unset($this -> mFlagItemsClass[$aFlagId]);
  }
  
  public function setMailType($aType) {
    $this -> mMailType = $aType;
  }
}