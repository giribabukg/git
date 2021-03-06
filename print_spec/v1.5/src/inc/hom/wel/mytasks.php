<?php
/**
 * @copyright  Copyright (c) 2004-2011 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 13626 $
 * @date $Date: 2016-04-26 09:47:52 +0800 (Tue, 26 Apr 2016) $
 * @author $Author: gemmans $
 */
class CInc_Hom_Wel_MyTasks extends CCor_Ren {

  protected $mJobList = Array();
  protected $mUsr;
  protected $mUsrId;
  protected $mAplIte = array();
  protected $mRoleIte = array();
  protected $mFlagIte = array();
  protected $mGetjoblist = array();
  protected $mIsOnHoliday;
  protected $mIteWithJobId = array();
  protected $mAlias;
  protected $mCrp;
  protected $mStartDateArr = array(); // Approval Loop Start Date
  protected $mDdlsDateArr = array();
  protected $mFlagTyp = array();

  protected $mDefaultLpp;
  protected $mDefaultOrder = '';

  /*
   * JobId: #23192
   * Status Change request in My Task List
   */
  protected $mStatusChangeJobList = array();
  protected $mPreDefColumns = Array();

  public function __construct($aUid) {
    $lUid = (is_null($aUid)) ? $lUid = CCor_Usr::getAuthId() : $lUid = $aUid;
    $this -> mUsr = CCor_Usr::getInstance();
    $this -> mJobList = $this -> getJobList($lUid);
    $this -> mAlias = CCor_Cfg::get('my.tasks');

    $this -> mDefs = CCor_Res::getByKey('alias', 'fie');
    $this -> mCnd = new CCor_Cond();
    $this -> mDefaultLpp = 25;
  }

  protected function getJobs($aMod = '') {
    $lAplRes = array();
    $lFlagRes = array();
    $lRoleRes = array();

    $lAplResJobIDs = array();
    $lFlagResJobIDs = array();
    $lRoleResJobIDs = array();

    foreach ($this -> mJobList as $lKey => $lValue) {
      if (isset($this -> mFlagTyp[$lKey])) {
        $lListFound = false;

        if (isset($this -> mFlagTyp[$lKey]['apl'])) {
          $lAplResJobIDs[] = $lKey;
          $lListFound = true;
        }

        if (isset($this -> mFlagTyp[$lKey]['role'])) {
          $lRoleResJobIDs[] = $lKey;
          $lListFound = true;
        }

        if (!$lListFound) {
          foreach ($this -> mFlagTyp[$lKey] as $lTyp) {
            $lFlagResJobIDs[] = $lKey;
          }
        }
      }
    }
    
    if (CCor_Cfg::get('job.writer.default') == 'portal') {
      $this -> mIte = new CCor_TblIte('all', $this->mWithoutLimit);
      $this -> mIte -> addField('jobid');
    } else $this -> mIte = new CApi_Alink_Query_Getjoblist();
    $this -> addFields($aMod);

    switch ($aMod) {
    	case 'apl':
          $lJobIdStr_Temp = array_map("esc", $lAplResJobIDs);
          $this -> mCountResJobIDs = count($lAplResJobIDs);
    	  break;
    	case 'flag':
          $lJobIdStr_Temp = array_map("esc", $lFlagResJobIDs);
          $this -> mCountResJobIDs = count($lFlagResJobIDs);
    	  break;
    	case 'role':
          $lJobIdStr_Temp = array_map("esc", $lRoleResJobIDs);
          $this -> mCountResJobIDs = count($lRoleResJobIDs);
    	  break;
    }
    if(sizeof($lJobIdStr_Temp) < 1){
      $lJobIdStr_Temp = array('"0"');
    }
    $lJobIdStr = implode(',', $lJobIdStr_Temp);

    if (empty($lJobIdStr)) {
      //quick fix to prevent error on Alink
      $this -> mIte -> addCondition('jobid', 'IN', '"-"');
    } else {
    $this -> mIte -> addCondition('jobid', 'IN', $lJobIdStr);
    }


    $this -> addGlobalSearchConditions();
    $this -> addFilterConditions();
    $this -> addSearchConditions();

    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);
    if ($this -> mUsr -> canRead('hom-wel.opt')) {
      $lCount = $this -> mCountResJobIDs;
      $this -> mFirst = $this -> mLpp * $this -> mPage;
      if($lCount <= $this -> mFirst){ //if number of jobs is less than starting job index
        $this -> mFirst = floor($lCount / $this -> mLpp) * $this -> mLpp; //get nearest LPP relative to job count
        $lPage = ($this -> mFirst / $this -> mLpp) - 1;
        
        $this -> mPage = ($lPage > 0) ? $lPage : 0;
        $this -> mUsr -> setPref('hom-wel-'.$aMod.'.page', $this -> mPage); //reset page number for user preference 
        $this -> mFirst = $this -> mPage * $this -> mLpp; //reset first variable
      }
      
      $this -> mIte -> setLimit($this -> mFirst, $this -> mLpp); //use correct starting point for limit
    }

    if (CCor_Cfg::get('job.writer.default') == 'portal') {
      $this -> mIteWithJobId = $this -> mIte -> getArray();
    } else $this -> mIteWithJobId = $this -> mIte -> getArray('jobid');

    $this -> mIte -> addField(FLAG_TYP, FLAG_TYP);

    foreach ($this -> mIteWithJobId as $lJob) {
      if ($aMod == 'apl') {
        $lAplRes[] = $lJob;
      }

      if ($aMod == 'flag') {
        $l_Job = $lJob;
        $l_Job[FLAG_TYP] = $lTyp;
        $lFlagRes[] = $l_Job;
      }

      if ($aMod == 'role') {
        $lRoleRes[] = $lJob;
      }
    }

    $this -> mGetjoblist = $this -> mIte;

    $this -> mAplIte  = $lAplRes;
    $this -> mFlagIte = $lFlagRes;
    $this -> mRoleIte = $lRoleRes;
  }

  /**
   * return Joblist. Alle Jobs denen OFFENE Korrekturumlauf ist der Benutzer beteiligt ist
   * und sein Deadline zwischen bestimmte Zeitraum ist.
   * @return array Joblist
   */
  protected function getJobList($aUid) {
    $lMyTaskJobIds = array();

    $lSql = 'SELECT distinct l.jobid, l.start_date, l.ddl as job_ddl, s.ddl as usr_ddl, s.loop_id, s.typ, s.user_id, s.pos, s.status, s.comment, s.done, s.confirm, s.prefix, s.sub_loop';
    $lSql.= ' FROM al_job_apl_loop l, al_job_apl_states s, al_job_shadow_'.MID.' sh';
    $lSql.= ' WHERE  l.status="open" ';
    $lSql.= ' AND l.mand='.MID;
    $lSql.= ' AND l.id=s.loop_id';
    $lSql.= ' AND l.typ=s.typ';
    //22595: As soon as the User give his Comment,independent of which, the Job will not be appear in the Mytask List.
    $lSql.= ' AND s.status < 1';
    $lSql.= ' AND s.del="N"';
    $lSql.= ' AND s.inv="Y"';
    $lSql.= ' AND l.jobid=sh.jobid';
    $lSql.= ' AND sh.flags != 2';
    $lSql.= ' ORDER BY s.loop_id, s.prefix, s.sub_loop, s.pos';
    $lQryJobsMytasks = new CCor_Qry($lSql);

    $lUid = $aUid;
    $this -> mUsrId = $lUid;
    $lIds = array();
    $lFlagTyp = array();
    $lNoDeny = array();
    $lOldKey = null;
    $lOldSub = null;
    foreach ($lQryJobsMytasks as $lRow) {
      $lRet = '';
      foreach ($lRow as $lKey => $lVal) {
        $lRet.= $lKey.'='.$lVal.' | ';
      }
      $lTyp = $lRow['typ'];
      if (substr($lTyp, 0, 3) == 'apl') $lTyp = 'apl';
      $lJobId = $lRow['jobid'];
      $lI = $lJobId.'.'.$lTyp.'.'.$lRow['prefix'];
      if ($lRow['sub_loop'] != $lOldSub) {
        $lMinPos[$lI] = MAX_SEQUENCE; // reset
        $lOldSub = $lRow['sub_loop'];
        unset($lIds[$lI]);
        unset($lMinPos[$lI]);
        unset($lNoDeny[$lI]);
      }
      if (!isset($lMinPos[$lI])) {
        $lMinPos[$lI] = MAX_SEQUENCE; // Behelfsvorbelegung
      }
      // brauche zur Anzeige distinct user_ids => können sich über backupuser-Fkt ändern und mehrfach vorkommen
      // Angezeigt werden muß die user_id mit der kleineren pos, da die agieren darf: $lSql.= ' ORDER BY pos';
      if (!isset($lIds[$lI][$lRow['user_id']])) {
        $lIds[$lI][$lRow['user_id']] = $lRow;
      }
      $lPos = $lRow['pos'];
      if (0 == $lRow['status'] AND $lMinPos[$lI] > $lPos) {
        $lMinPos[$lI] = $lPos;
      }
      if (("one" == $lRow['confirm'] AND "Y" == $lRow['done'] AND empty($lRow['comment'])) OR "-" == $lRow['done']) { // fuer eine Uebergangszeit, da es vorher "-" nicht gab.
        $lNoDeny[$lI][$lRow['user_id']] = TRUE;
      }
    }
    $lShowAplBtnUntilConfirm = CCor_Cfg::get('job.apl.show.btn.untilconfirm');
    $lShowAplBtnUntilConfirm = false;
    foreach ($lQryJobsMytasks as $lRow) {
      $lTyp = $lRow['typ'];
      if (substr($lTyp, 0, 3) == 'apl') $lTyp = 'apl';
      $lJobId = $lRow['jobid'];
      $lI = $lJobId.'.'.$lTyp.'.'.$lRow['prefix'];

      if (!isset($lIds[$lI][$lUid]) OR $lMinPos[$lI] < $lIds[$lI][$lUid]['pos'] OR isset($lNoDeny[$lI][$lUid])) continue;

      $lMyTaskJobIds[$lJobId] = $lJobId;
      $lFlagTyp[$lJobId][$lTyp] = $lTyp;
      $this -> mStartDateArr[$lJobId] = $lRow['start_date'];
      if ($this -> mUsrId == $lRow['user_id']) $lUsrDdl = $lRow['usr_ddl'];
      $this -> mDdlsDateArr[$lJobId] = array('job_ddl' => $lRow['job_ddl'], 'usr_ddl' => $lUsrDdl);
    }

    /*
     * JobId: #23192
    * Status Change request in My Task List
    */
    $this -> mStatusChangeJobList = $this -> getStatusChangeJobList();
    if (!empty($this -> mStatusChangeJobList)){
      foreach ($this -> mStatusChangeJobList as $lJid){
        if (!isset($lMyTaskJobIds[$lJid])){
          $lMyTaskJobIds[$lJid] = $lJid;
          $this -> mStartDateArr[$lJid] = '';
        }
        $lFlagTyp[$lJid]['role'] = 'role';
      }
    }
    $this -> mFlagTyp = $lFlagTyp;
    $lRet = $lMyTaskJobIds;
    return $lRet;

  }

  /**
   * Add job fields
   *
   * @return -
   */
  public function addFields($aMod = '') {
    $lJobFieldsById = CCor_Res::extract('id', 'alias', 'fie'); // as job fields are stored in the system/user preferences by their ids, we need (id, alias) tuples for further handling
    $lJobFieldsByAlias = CCor_Res::extract('alias', 'native', 'fie'); // as job fields are stored in Networker by their natives, we need (alias, native) tuples for further handling

    // START: check for user preferences first, use system preferences when there are none
    $lUsedJobFields = Array();
    $lUsrPref = $this -> mUsr -> getPref('hom-wel-'.$aMod.'.cols');
    $lGenCols = CCor_Cfg::get('hom.wel.mytask.column', array());
    $lModCols = CCor_Cfg::get('hom.wel.mytask.'.$aMod.'.column', array());
    if ($this -> mUsr -> canRead('hom-wel.opt') && !empty($lUsrPref)) {
      $lUsrPrefArr = explode(',', $lUsrPref);
      foreach ($lUsrPrefArr as $lKey => $lValue) {
        $lUsedJobFields[] = $lJobFieldsById[$lValue];
      }
    } elseif ($aMod == 'apl' && count($lGenCols) >= 1) {
      $lUsedJobFields = $lGenCols;
    } elseif ($aMod != 'apl' && count($lModCols) >= 1) {
      $lUsedJobFields = $lModCols;
    } else {
      $lUsedJobFields = array('jobnr', 'stichw', 'apl', 'webstatus');
    }
    // STOP: check for user preferences first, use system preferences when there are none

    $this-> mPreDefColumns = array();
    if (count($lUsedJobFields) >= 1) {
      foreach ($lUsedJobFields as $lKey => $lValue) {
        if (array_key_exists($lValue, $lJobFieldsByAlias)) {
          $this -> mIte -> addField($lValue, $lJobFieldsByAlias[$lValue]);
          $this-> mPreDefColumns[] = $lValue;
        }
      }
    }

    $this -> mIte -> addField('src', $lJobFieldsByAlias['src']);
    $this -> mIte -> addField('jobnr', 'jobnr');
    $this -> mIte -> addField('status', 'status');
    $this -> mIte -> addField('webstatus', 'webstatus');
    $this -> mIte -> addField('flags', $lJobFieldsByAlias['flags']);

    // getRequiredFlagFields
    $lAllFlags = CCor_Res::get('fla');
    foreach ($lAllFlags as $lFlag) {
      $lRequired = $lFlag['alias'];
      if (!empty($lRequired) AND isset($lJobFieldsByAlias[$lRequired])) {
        $this -> mIte -> addField($lRequired, $lJobFieldsByAlias[$lRequired]);
      }
      $lRequired = $lFlag['ddl_fie'];
      if (!empty($lRequired) AND isset($lJobFieldsByAlias[$lRequired])) {
        $this -> mIte -> addField($lRequired, $lJobFieldsByAlias[$lRequired]);
      }
    }

    foreach ($this -> mAlias as $lFie) {
      if (isset($lJobFieldsByAlias[$lFie])){
        $this -> mIte -> addField($lFie, $lJobFieldsByAlias[$lFie]); // 'alias', 'native'
      }
    }
  }

  /**
   * Get content
   *
   * @return -
   */
  protected function getCont() {
    $lRet = '';
    $lRet.= $this -> getAplTaskContent();
    $lRet.= $this -> getFlagTaskContent();
    $lRet.= $this -> getRoleTaskContent();
    return $lRet;
  }

  /**
   * Get approval loop tasks
   *
   * @return -
   */
  protected function getAplTaskContent() {
    $this -> mSer = $this -> mUsr -> getPref('hom-wel-apl.ser');

    $this -> getPrefs('hom-wel-apl');
    $this -> getJobs('apl');

    if (empty($this -> mAplIte)) {
      $lHide = CCor_Cfg::get('hom.wel.mytask.hide.empty.apl');
      if ($lHide) return '';
    }

    $lVie = new CHom_Wel_MyTasks_MyTasks($this -> mGetjoblist, $this -> mAplIte, $this -> mStartDateArr, $this-> mPreDefColumns, 'apl', lan('lib.my.tasks.apl.desc'), $this -> mDdlsDateArr);
    $lRet = $lVie -> getContent().BR.BR;

    return $lRet;
  }

  /**
   * Get flag tasks
   *
   * @return -
   */
  protected function getFlagTaskContent() {
    $this -> mSer = $this -> mUsr -> getPref('hom-wel-flag.ser');

    $this -> getPrefs('hom-wel-flag');
    $this -> getJobs('flag');

    if (empty($this -> mFlagIte)) {
      $lHide = CCor_Cfg::get('hom.wel.mytask.hide.empty.flag');
      if ($lHide) return '';
    }

    $lVie = new CHom_Wel_MyTasks_MyTasks($this -> mGetjoblist, $this -> mFlagIte, $this -> mStartDateArr, $this-> mPreDefColumns, 'flag', lan('lib.my.tasks.flag.desc'));
    $lRet = $lVie -> getContent().BR.BR;

    return $lRet;
  }

  /**
   * Get role tasks
   *
   * @return -
   */
  protected function getRoleTaskContent() {
    $this -> mSer = $this -> mUsr -> getPref('hom-wel-role.ser');

    $this -> getPrefs('hom-wel-role');
    $this -> getJobs('role');

    if (empty($this -> mRoleIte)) {
      $lHide = CCor_Cfg::get('hom.wel.mytask.hide.empty.role');
      if ($lHide) return '';
    }

    $lVie = new CHom_Wel_MyTasks_MyTasks($this -> mGetjoblist, $this -> mRoleIte, $this -> mStartDateArr, $this-> mPreDefColumns, 'role', lan('lib.my.tasks.role.desc'));
    $lRet = $lVie -> getContent().BR.BR;

    return $lRet;
  }

  protected function getStatusChangeJobList() {
    $this -> mDefs = CCor_Res::getByKey('alias', 'fie');
    $lUsrToBackupIds = $this -> mUsr -> getAllAbsentUsersIBackup();
    if ($lUsrToBackupIds !== FALSE) {
      array_push($lUsrToBackupIds, $this -> mUsrId);
      $lSqlUsrPart = ' IN ('.implode(',', $lUsrToBackupIds).')';
    } else {
      $lSqlUsrPart = ' = '.$this -> mUsrId;
    }

    $lUsrGrps = CCor_Usr::getInstance() -> getMemArray();
    if($lUsrToBackupIds !== FALSE) {
      $lGrpBackups = $lUsrGrps;
      foreach($lUsrToBackupIds as $lBackupUsr){
    	$lBackUsr = new CCor_Anyusr($lBackupUsr);
    	$lBackupGrps = $lBackUsr -> getMemArray();
    	$lGrpBackups = array_merge($lGrpBackups, $lBackupGrps);
      }
      $lSqlGrpPart = ' IN ('.implode(',', $lGrpBackups).')';
    } else {
      $lSqlGrpPart = ' IN ('.implode(',', $lUsrGrps).')';
    }

    $lRet = Array();
    $lJobList = Array();
    $lArrCondition = Array();
    $lArrCondition = CCor_Res::get('rolmytask');
    if (empty($lArrCondition)) {
      return $lRet;
    } else {
      $lSql = 'SELECT jobid FROM al_job_shadow_'.MID.' WHERE 1 AND ';
      foreach ($lArrCondition as $lKey => $lVal) {
        if (empty($lVal['webstatus'])) continue;

        $lAli = $lVal['alias'];
        $lFieSql = '(webstatus = '.$lVal['webstatus'].' AND ';

        //include relevant field sql (user or group)
        $lFieSql.= $lAli.' '. ($this -> mDefs[$lAli]['typ'] == 'uselect' ? $lSqlUsrPart : $lSqlGrpPart) . ' AND ';
        $lFieSql.= ' src = "'.$lVal['src'].'")';
        $lFieSql.= ' OR ';

        $lPos = strpos($lSql, $lFieSql);
        if($lPos == false)
          $lSql.= $lFieSql;
      }
      $lTempSql = substr($lSql, 0, -3);
      $lPos = ($lTempSql == 'OR ') ? -3 : -4;

      $lSql = substr($lSql, 0, $lPos);
      $lQry = new CCor_Qry($lSql);
      foreach ($lQry as $lRow) {
        $lJobId = $lRow['jobid'];
        $lJobList[$lJobId] = $lJobId;
      }
      $lRet = $lJobList;
      return $lRet;
    }
  }

  protected function addGlobalSearchConditions() {
    $lSid = $this -> mUsr -> getPref('job.ser_id');
    if (empty($lSid)) return;
    $lSer = $this -> mUsr -> getPref('job.ser_ser');
    if (empty($lSer)) return;
    if (!is_object($this -> mIte)) {
      $this -> dbg('Iterator not an object, cannot apply global search criteria', mlWarn);
      return;
    }
    foreach ($lSer as $lAli => $lVal) {
      if (empty($lVal)) continue;
      if (!isset($this -> mDefs[$lAli])) {
        $this -> dbg('Unknown Field '.$lAli, mlWarn);
        continue;
      }
      $lDef = $this -> mDefs[$lAli];
      $lCnd = $this -> mCnd -> convert($lAli, $lVal, $lDef['typ']);
      if ($lCnd) {
        foreach($lCnd as $lItm) {
          $this -> mIte -> addCondition($lItm['field'], $lItm['op'], $lItm['value']);
        }
      }
    }
  }

  protected function addFilterConditions() {
    if (empty($this -> mFil)) return;

    foreach ($this -> mFil as $lKey => $lValue) {
      if (!empty($lValue)) {
        if (is_array($lValue) AND $lKey == "webstatus") {
          $lStates = "";

          foreach ($lValue as $lWebstatus => $foo) {
            if ($lWebstatus == 0) {
              break;
            } else {
              $lStates.= '"'.$lWebstatus.'",';
            }
          }

          if (!empty($lStates)) {
            $lStates = substr($lStates, 0, -1);

            $this -> addCondition('webstatus', 'in', $lStates);
          }
        } elseif (is_array($lValue) AND $lKey == "flags") {
          $lStates = "";

          foreach ($lValue as $lKey => $lValue) {
            if($lKey == 0){
              break;
            } else {
              $lStates.= "((flags & ".$lKey.") = ".$lKey.") OR ";
            }
          }
          $lStates = (!empty($lStates)) ? substr($lStates, 0, strlen($lStates) - 4) : '';

          if (!empty($lStates)) {
            $lJobIds = '';
            $lSQL = 'SELECT jobid FROM al_job_shadow_'.MID.' WHERE '.$lStates.';';
            $lQry = new CCor_Qry($lSQL);
            foreach ($lQry as $lRow) {
              $lJobId = trim($lRow['jobid']);
              if (!empty($lJobId)) {
                $lJobIds.= '"'.$lJobId.'",';
              }
            }
            $lJobIds = strip($lJobIds);

            if (!empty($lJobIds)) {
              $this -> mIte -> addCondition('jobid', 'IN', $lJobIds);
            }
          }
        } else {
          $this -> addCondition($lKey, '=', $lValue);
        }
      }
    }
  }

  protected function addCondition($aAlias, $aOp, $aValue, $aNative = true) {
    if (!isset($this -> mDefs[$aAlias])) {
      $this -> dbg('Unknown Field '.$aAlias, mlWarn);
      return;
    }
    $lDef = $this -> mDefs[$aAlias];
    if ($aNative) {
      $this -> mIte -> addCondition($lDef['native'], $aOp, $aValue);
    } else {
      $this -> mIte -> addCondition($aAlias, $aOp, $aValue);
    }
  }

  protected function addSearchConditions() {
    if (empty($this -> mSer)) return;
    foreach ($this -> mSer as $lAli => $lVal) {
      if (empty($lVal)) continue;
      if (!isset($this -> mDefs[$lAli])) {
        $this -> dbg('Unknown Field '.$lAli, mlWarn);
        continue;
      }
      $lDef = $this -> mDefs[$lAli];
      $lCnd = $this -> mCnd -> convert($lAli, $lVal, $lDef['typ']);
      $this -> dump($lCnd, 'After Array');
      if ($lCnd) {
        foreach($lCnd as $lItm) {
          $this -> mIte -> addCondition($lItm['field'], $lItm['op'], $lItm['value']);
        }
      }
    }
  }

  protected function getPrefs($aKey = NULL) {
    $lKey = (NULL === $aKey) ? $this -> mPrf : $aKey;

    $lUsr = CCor_Usr::getInstance();
    $this -> mLpp  = $lUsr -> getPref($lKey.'.lpp', $this -> mDefaultLpp);
    $this -> mPage = $lUsr -> getPref($lKey.'.page');
    $this -> mOrd  = $lUsr -> getPref($lKey.'.ord', $this -> mDefaultOrder);
    $this -> mDir  = 'asc';
    if (substr($this -> mOrd, 0, 1) == '-') {
      $this -> mOrd = substr($this -> mOrd, 1);
      $this -> mDir = 'desc';
    }
    $lGrp = $lUsr -> getPref($lKey.'.grp');
    if (NULL !== $lGrp) {
      $this -> setGroup($lGrp);
    }
    $this -> mSerFie = $lUsr -> getPref($lKey.'.sfie');
    $this -> mFilFie = $lUsr -> getPref($lKey.'.ffie');

    $this -> mSer = $lUsr -> getPref($lKey.'.ser');
    $this -> mFil = $lUsr -> getPref($lKey.'.fil');
  }
}
