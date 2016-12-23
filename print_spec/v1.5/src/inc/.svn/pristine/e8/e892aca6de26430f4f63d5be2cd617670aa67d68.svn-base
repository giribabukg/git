<?php
class CInc_Hom_Wel_MyTasks_MyTasks extends CJob_List {

  protected $mAlias;
  protected $mAllFlags;
  protected $mCrp;
  protected $mCrpIdArr;
  protected $mGetjoblist;
  protected $mListAmount = 0;
  protected $mPreDefColumns = Array();
  protected $mStartDateArr = array();
  protected $mDdlsDateArr = array();
  protected $mType = 'apl';

  public function __construct($aGetjoblist, $aIte, $aStartDateArr, $aPreDefColumns, $aType= '', $aDesc = '', $aDDl = array()) {
    if (!empty($aType)) {
      $this -> mType = $aType;
    } else {
      $this -> mType = 'apl';
    }
    $this -> mTooltip = $aDesc;

    $this -> mMod = 'hom-wel-'.$this -> mType;

    if ('apl' == $this -> mType) {
      $lMyTasks = 'my.tasks';
      $lMyUrl = 'hom.wel.mytask.url';
    } else  {
      $lMyTasks = 'my.tasks.'.$this -> mType;
      $lMyUrl = 'hom.wel.mytask.'.$this -> mType.'.url';
    }

    $this -> mAlias = CCor_Cfg::get($lMyTasks, array());

    $this -> mShowColumnMore = false;
    $this -> mShowCopyButton = false;
    $this -> mShowDeleteButton = false;
    $this -> mSourceColumn = false;
    $this -> mShowSubHdr = true;
    $this -> mShowImg = false;

    parent::__construct($this -> mMod);

    $this -> mTitle = lan('lib.my.tasks.'.$this -> mType);

    $this -> mGetjoblist = $aGetjoblist;
    $this -> mIte = $aIte;
    $this -> mStartDateArr = $aStartDateArr;
    $this -> mDdlsDateArr = $aDDl;
    $this -> mPreDefColumns = $aPreDefColumns;

    $this -> mCrpIdArr = CCor_Res::extract('code', 'id', 'crpmaster');
    $this -> lAplstatus = array();
    foreach ($this -> mCrpIdArr as $lCode => $lId) {
      $lSql = 'SELECT status FROM al_crp_status WHERE mand='.MID.' AND crp_id='.$lId.' AND apl=1';
      $lQry = new CCor_Qry($lSql);
      foreach ($lQry as $lRow) {
        if (!empty($lRow['status'])) {
          $this -> lAplstatus[$lCode][] = $lRow['status'];
        }
      }
    }

    $this -> mFie = CCor_Res::get('fie');
    $this-> mFieAliasId  = CCor_Res::extract('alias', 'id', 'fie');

    if (!empty($this -> mIte)) { // Job from Role should be sorted out.
      $lRes  = $this -> mIte;
//       $lRes = $this -> removeStatusReRs($lRes); // Remove Jobs which has STATUS = RE or RS or G
      if ('apl' == $this -> mType) {
        $lRemoveDeadlines = CCor_Cfg::get('hom-wel.removeLinesNotInTimeArea', FALSE);
        if ($lRemoveDeadlines) {
          $lRes = $this -> removeDeadline($lRes); // Remove Jobs, their deadline don't between time area. All deadlines has to be in area!
        }
      }
      $lRes = $this -> AddFieldDatesAndSort($lRes);//sortByDate umbenannt

      $this -> mIte = $lRes;
      $this -> mListAmount = count($this -> mIte);

      if ('apl' != $this -> mType AND 'role' != $this -> mType AND !in_array('flags', $this-> mPreDefColumns)) {
        $this-> mPreDefColumns[] = 'flags';
      }
      if ('role' == $this -> mType) {
        $this-> mPreDefColumns[] = 'roletyp';
      }

      if ($this -> mIte) {
        $lColumns = $this-> mPreDefColumns;
        if (!empty($lColumns)) {
          foreach ($lColumns as $lCol) {
            if ($lCol == 'start_date'){
              // Approval Loop Start Date
              $this -> addColumn('start_date', lan('lib.dates'), true, array('width' => '20'));
              continue;
            }

            if ($lCol == 'dates'){
              // The oldest Deadline.
              $this -> addColumn('dates', lan('lib.dates'), true, array('width' => '20'));
              continue;
            }

            if ($lCol == 'roletyp'){
              // The oldest Deadline.
              $this -> addColumn('roletyp', lan('lib.roletyp'), true, array('width' => '20'));
              continue;
            }

            if (isset($this-> mFieAliasId[$lCol])){
              if (isset($this -> mFie[$this -> mFieAliasId[$lCol]])) {
                $lDef = $this -> mFie[$this -> mFieAliasId[$lCol]];
                $this -> addField($lDef);
              } elseif (FLAG_TYP == $this -> mFieAliasId[$lCol]) {
                $lDef = $this -> mFie[$this -> mFieAliasId['jobnr']];
                $lDef['id'] = FLAG_TYP;
                $lDef['alias'] = FLAG_TYP;
                $lDef['name_'.LAN] = 'Flag';
                $lDef['flags'] = 4; // only ListColumn, not sortable

                $this -> addField($lDef);
              }
            }
          }
        }

        $this -> mShowSubHdr = false;
        $this -> mShowSerHdr = false;

        if ($this -> mUsr -> canRead('hom-wel.opt')) {
          $this -> mShowSubHdr = true;
          $this -> mShowSerHdr = true;

          $this -> addSort('last_status_change');
          $this -> addButton(lan('lib.sort'), $this -> getButtonMenu($this -> mMod));

          $this -> mMaxLines = $this -> mGetjoblist -> getCount();

          $this -> addPanel('nav', $this -> getNavBar());
          $this -> addPanel('vie', $this -> getViewMenu());
        }

        $this -> mStdLnk = 'index.php?act=job-';
        $this -> mMyUrl = CCor_Cfg::get($lMyUrl, 'form');
      } else {
        $this -> mShowSubHdr = true;
        $this -> addPanel('nav', lan('job.no.available'));
      }
    } else { // there are no jobs, therefore we need no my tasks panel
      $this -> mShowSubHdr = true;
      $this -> addPanel('nav', lan('lib.my.tasks.no'));
    }

    $this -> mDefs = CCor_Res::getByKey('alias', 'fie');
    $this -> addFilter('flags', lan('lib.flags'));
  }

  protected function addFilter($aAlias, $aCaption, $aOpt = NULL) {
    $lRet = array();
    $lRet['cap'] = $aCaption;
    $lRet['opt'] = $aOpt;
    $this -> mJobFilFie[$aAlias] = $lRet;

    // can we keep it here for the new Alink
    $lAllJobsFromAlink = CCor_Cfg::get('all-jobs_ALINK');
    $lAllJobsFromAlink[] = 'all';
    if (isset($this -> mSrc) AND in_array($this -> mSrc, $lAllJobsFromAlink)) {
      $this -> addField($this -> mDefs[$aAlias], $this -> mDefs[$aAlias]['native']);
    }
  }

  protected function getIterator() {
    $this -> mIte = $this -> mGetjoblist;
  }

  protected function getTdApl() {
    $lSrc = $this -> getVal('src');
    if (empty($lSrc)) {
      return $this -> tda();
    }

    $lSta = $this -> getInt('webstatus');
    // if jobtype has NO approval loop, don't add it to the array
    if (isset($this -> lAplstatus[$lSrc])) {
      $lAplstatus = $this -> lAplstatus[$lSrc];
    }

    // if apl has been canceled
    $lJobId = $this -> getVal('jobid');
    $lApl = new CApp_Apl_Loop($lSrc, $lJobId, 'apl');
    $lLastLoopId = $lApl -> getLastLoop();
    $lLastWasBreak = $lApl -> getIfLastAplHasBreak($lLastLoopId);
    if ($lLastWasBreak) {
      $lRet = $lApl -> getAplCommitList($lLastLoopId, $this -> mCurLnk);
      return $this -> tdClass($lRet, 'w16 br');
    }

    // is apl open and completed
    $lLoopId = $this -> getInt('loop_id');
    if ($lLoopId != 0) {
      $lIsAplCompleted = $lApl -> isAplOpenAndCompleted($lLoopId);
      if ($lIsAplCompleted) {
        $lRet = CApp_Apl_Loop::getAplCommitList($lLoopId, $this -> mCurLnk);
        return $this -> tdClass($lRet, 'w16 co');
      }
    }

    // if webstatus is not approval loop status
    if (!in_array($lSta, $lAplstatus)) {
      return $this -> tda();
    }

    $lLoopId = $this -> getInt('loop_id');
    if (empty($lLoopId)) {
      return $this -> tda();
    }

    $lRet = CApp_Apl_Loop::getAplCommitList($lLoopId, $this -> mCurLnk);

    return $this -> tdClass($lRet, 'w16 ac');
  }

  protected function getTdDates() {
    $lRes = date(lan('lib.date.long'), strtotime($this -> getVal('dates')));
    return $this -> tda($lRes);
  }

  protected function getTdStart_Date() {
    $lId = $this -> getVal('jobid');
    $lRes = date(lan('lib.date.long'), strtotime($this -> mStartDateArr[$lId]));
    return $this -> tda($lRes);
  }

  protected function getTdRoletyp() {
    $lJid = $this-> getVal('jobid');

    $lRoleTask = $this->getTaskType($lJid);
    switch($lRoleTask){
      case 'user':
        $lRes = img('img/ico/16/usr.gif', array('style' => 'margin-right:3px'));
        break;
      case 'group':
        $lRes = img('img/ico/16/gru.gif', array('style' => 'margin-right:3px'));
        break;
      case 'both':
        $lRes = img('img/ico/16/usr.gif', array('style' => 'margin-right:3px'));
        $lRes.= img('img/ico/16/gru.gif', array('style' => 'margin-right:3px'));
        break;
      default:
        $lRes = '';
        break;
    }

    return $this -> tda($lRes);
  }

  protected function getTdApl_Job_Ddl() {
    $lJid = $this -> getVal('jobid');
    $lDate = strtotime($this -> mDdlsDateArr[$lJid]['job_ddl']);

    $lToday = strtotime(date('Y-m-d'));
    $lCls = $this->mCls;
    if ($lToday > $lDate) {
      $lCls.= ' cr';
    } elseif ($lToday == $lDate) {
      $lCls.= ' cy';
    }
    $lRet = '<td class="'.$lCls.'">';
    $lRet.= $this->a(date(lan('lib.date.long'), $lDate));
    $lRet.= '</td>';
    return $lRet;
  }

  protected function getTdApl_Usr_Ddl() {
    $lJid = $this -> getVal('jobid');
    $lDate = strtotime($this -> mDdlsDateArr[$lJid]['usr_ddl']);

    $lToday = strtotime(date('Y-m-d'));
    $lCls = $this->mCls;
    if ($lToday > $lDate) {
      $lCls.= ' cr';
    } elseif ($lToday == $lDate) {
      $lCls.= ' cy';
    }
    $lRet = '<td class="'.$lCls.'">';
    $lRet.= $this->a(date(lan('lib.date.long'), $lDate));
    $lRet.= '</td>';
    return $lRet;
  }

  protected function getTdWebstatus() {
    $lVal = $this -> getVal('webstatus');
    $lSrc = $this -> getVal('src');

    if (isset($this -> mCrpIdArr[$lSrc])) {
      $lCrpId = $this -> mCrpIdArr[$lSrc];
      $this -> loadCrp($lCrpId);
      if (isset($this -> mCrp)) {
        return $this -> getExtWebstatus($lVal, $this -> mCrp);
      }
      $lDis = $lVal / 10;
	  $lPath = CApp_Crpimage::getSrcPath($lSrc, 'img/crp/'.$lDis.'b.gif');
      $lRet = img($lPath, array('style' => 'margin-right:1px'));
      return $this -> tda($lRet);
    } else {
      return $this -> tda('No CriticalPath');
    }
  }

  protected function getTdSrc() {
    $lSrc = $this->getVal('src');
    $lImg = (THEME === 'default' ? 'job-'.$lSrc : CApp_Crpimage::getColourForSrc($lSrc));
    $lRet = img('ico/16/' . LAN . '/' . $lImg . '.gif');

    return $this->tdClass($lRet, 'w16 ac');
  }

  protected function getExtWebstatus($aState, $aCrp) {
    $lDisplay = CCor_Cfg::get('status.display', 'progressbar');

    $lVal = $aState;
    $lNam = '[unknown]';
    $lSrc = $this->getVal('src');
    $lRet = '';

    foreach ($aCrp as $lRow) {
      if (($lDisplay == 'progressbar' && $lVal >= $lRow['status']) OR ($lDisplay == 'activeonly' && $lVal == $lRow['status'])) {
		$lPath = CApp_Crpimage::getSrcPath($lSrc, 'img/crp/'.$lRow['display'].'b.gif');
        $lRet.= img($lPath, array('style' => 'margin-right:1px'));
        $lNam = $lRow['name_'.LAN];
      } else if (($lDisplay == 'progressbar' && $lVal < $lRow['status']) OR ($lDisplay == 'activeonly' && $lVal != $lRow['status'])) {
		$lPath = CApp_Crpimage::getSrcPath($lSrc, 'img/crp/'.$lRow['display'].'l.gif');
        $lRet.= img($lPath, array('style' => 'margin-right:1px'));
      }
    }
    $lRet.= NB.htm($lNam);
    return $this -> tda($lRet);
  }

  protected function getTdXXX_FLAG_TYP_XXX() {
    $lId = $this -> getVal(FLAG_TYP);
    $lRes = $this-> mAllFlags[$lId]['name_'.LAN];
    return $this -> tda($lRes);
  }

  protected function sort2d($array, $index, $order = 'asc', $natsort = false, $case_sensitive = false) {
    if (is_array($array) && count($array) > 0) {
      foreach (array_keys($array) as $key)
        $temp[$key] = $array[$key][$index];

      if (!$natsort)
        ($order=='asc') ? asort($temp) : arsort($temp);
      else {
        ($case_sensitive) ? natsort($temp) : natcasesort($temp);

        if ($order != 'asc')
          $temp = array_reverse($temp, true);
      }

      foreach (array_keys($temp) as $key)
        (is_numeric($key)) ? $sorted[] = $array[$key] : $sorted[$key] = $array[$key];

      return $sorted;
    }

    return $array;
  }

  protected function AddFieldDatesAndSort($aArray) {
    $lArray = $aArray;

    foreach ($lArray as $lKey => $lVal) {
      $lArray[$lKey]['dates'] = null;

      foreach ($this -> mAlias as $lKey => $lValue) {
        if (isset($lArray[$lKey][$lValue])) {
          $lArray[$lKey]['dates'] = $lArray[$lKey][$lValue];
        }
      }
    }

    $lArray = $this -> sort2d($lArray, $this -> mOrd, $this -> mDir);
    return $lArray;
  }

  /**
   * return Alink Deadline Condition
   *
   * @param date $lStartDaysPast get from Pref 'hom.wel.mytasks.time.past'
   * @param date $lStartDaysFuture get from Pref 'hom.wel.mytasks.time.future'
   * @return array $lRet Joblist
   */
  protected function removeDeadline($aArr = Array()) {
    $lRet = $aArr;
    $lInDeadline = false;

    $lStartDaysPast   = CCor_Cfg::get('my.task.past',   60);
    $lStartDate       = mktime(0, 0, 0, date("m"), date ("d") - $lStartDaysPast,   date("Y"));
    $lStartDate       = strftime("%Y-%m-%d", $lStartDate);

    $lStartDaysFuture = CCor_Cfg::get('my.task.future', 14);
    $lEndDate         = mktime(0, 0, 0, date("m"), date ("d") + $lStartDaysFuture, date("Y"));
    $lEndDate         = strftime("%Y-%m-%d", $lEndDate);

    $this -> dbg('My Tasks: Date Period between '.$lStartDate.' and '.$lEndDate);

    foreach ($lRet as $lRow => $lArr) {
      $lInDeadline = false;
      foreach ($this -> mAlias as $lKey => $lVal) { // das Zeitintervall muss fuer alle Deadlines erfuellt sein!
        if (isset($lArr[$lVal]) && !empty($lArr[$lVal])) {
          if ($lArr[$lVal] > $lStartDate && $lArr[$lVal] < $lEndDate){
            $lInDeadline = true;
            break;
          }
        }
      }

      if (!$lInDeadline) {
        unset($lRet[$lRow]);
      }
    }

    return $lRet;
  }

  /**
   * remove jobs which has Status = RE Or RS or G
   * @param array $aArr jobliste
   * */
  protected function removeStatusReRs($aArr = Array()) {
    $lRet = Array();
    $lRet = $aArr;

    foreach ($lRet as $lRow => $lVal) {
      if (($lVal['status'] == 'RE') or ($lVal['status'] == 'RS') or ($lVal['status'] == 'G')) {
        unset($lRet[$lRow]);
      }
    }

    return $lRet;
  }

  protected function getTitleContent() {
    $lRet = '<table cellpadding="0" cellspacing="0" border="0"><tr>'.LF;
    if ($this -> mShowImg) {
      $lRet.= '<td>'.img($this -> mImg, array('style' => 'float:left', 'alt' => $this -> mTitle)).'</td>';
    }
    $lRet.= '<td class="captxt">'.htm($this -> mTitle);

    $ltheme = THEME == "wave8" ? "-wave8" : "";
    if (!empty($this -> mTooltip)) {
      $lRet .= "<i class='ico-jfl ico-jfl-1024' style='margin-bottom:-4px;' data-toggle='tooltip' data-tooltip-body='".$this -> mTooltip."' data-tooltip-head='".$this->mTitle."'></i>";
      $lRet.= '</td>';
    }

    if ('apl' == $this -> mType && $this -> mUsr ->canRead('home-aplfor.filter')) {
      $lRet.= $this -> getGroupSelectionForApl().LF;
    }
    $lRet.= '</tr></table>'.LF;
    return $lRet;
  }

  protected function getGroupSelectionForApl() {
    $lUsr = CCor_Usr::getInstance();
    $lAplReqUid = $lUsr -> getPref('home.aplfor.filter');
    $lRet= '<td>'.NB.NB.NB.NB.NB.NB.NB.'</td>';
    $lRet.= '<td>';
    $lRet.= lan('home.aplfor.filter.label').': '; // Show Tasks From:
    $lRet.= '</td>'.LF;
    $lRet.= '<td class="w400">';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="hom-wel.std" />'.LF;

    $lRet.= '<select name="aplfor" size="1" onchange="this.form.submit()">'.LF;
    $lRet.= '<option value="'.$lUsr->getAuthId().'"></option>'.LF;
    $lGid = CCor_Cfg::get('home.aplfor.filter.gid');
    $lUsers =CCor_Res::extract('id', 'fullname', 'usr', array('gru' => $lGid));
    asort($lUsers);

    foreach ($lUsers as $lKey => $lValue) {
      $lRet.= '<option value="'.$lKey.'" ';
            if ($lAplReqUid == $lKey) {
                $lRet.= ' selected="selected"';
              }
      $lRet.= '>'.htm($lValue).'</option>'.LF;
    }

    $lRet.= '</select>'.LF;

    $lRet.= '</form>'.LF;
    $lRet.= '</td>'.LF;
    return $lRet;
  }

  protected function loadCrp($aCrpId = '') {
    if (!empty($aCrpId)) {
      $this -> mCrp = CCor_Res::get('crp', $aCrpId);
    }
  }

  protected function getColumnHeaders() {
    $lRet = '';
    if (empty($this -> mCols)) {
      $this -> dbg('No columns specified');
      return '';
    }

    if (0 < $this -> mListAmount) {
      $lRet = '<tr>'.LF;
      foreach ($this -> mCols as & $lCol) {
        if ($lCol -> isHidden()) {
          continue;
        }
        // TODO: getHdrAlias dispatcher
        $lRet.= $this -> getColHdr($lCol);
      }
      $lRet.= '</tr>'.LF;
    }
    return $lRet;
  }

  protected function getLink() {
    $lSrc = $this -> getVal('src');
    $lJobId = $this -> getVal('jobid');

    switch ($this -> mMyUrl) {
      case 'apl':
        $lApl = new CInc_App_Apl_Loop($lSrc, $lJobId);
        $lAplType = $lApl -> getLoopById($this -> mRow['loop_id']);
        if ($lAplType['apl_mode'] == 2) $lRet = $this -> mStdLnk.$lSrc.'.edt&jobid='.$lJobId;
        else $lRet = $this -> mStdLnk.'apl&src='.$lSrc.'&jobid='.$lJobId;
        break;
      case 'det':
        $lRet = $this -> mStdLnk.$lSrc.'.edt&jobid='.$lJobId.'&page=det';
        break;
      case 'fil':
        $lRet = $this -> mStdLnk.$lSrc.'-'.$this -> mMyUrl.'&jobid='.$lJobId;
        break;
      case 'flag':
        $lRet = $this -> mStdLnk.$this -> mMyUrl.'&src='.$lSrc.'&jobid='.$lJobId;
        break;
      case 'his':
      default:
        $lRet = $this -> mStdLnk.$lSrc.'.edt&jobid='.$lJobId;
    }

    return $lRet;
  }

  protected function getRows() {
    $lRet = '';
    if (0 < $this -> mListAmount) {
      $this -> mCtr = $this -> mFirst + 1;
      foreach ($this -> mIte as $this -> mRow) {
        $lRet.= $this -> beforeRow();
        $lRet.= $this -> getRow();
        $lRet.= $this -> afterRow();
      }
    }
    return $lRet;
  }

  protected function onBeforeContent() {
    $lRet = '';
    $lIte = array();
    foreach ($this -> mIte as $lRow) {
      $lIte[$lRow['jobid']] = $lRow;
    }
    $this -> mIte = $lIte;
    $this -> loadApl();
    $this -> loadFlags();

    return $lRet;
  }

  protected function getTaskType($aJid){
    $lFie = CCor_Res::getByKey('alias', 'fie');
    $lUsrToBackupIds = $this -> mUsr -> getAllAbsentUsersIBackup();
    //prepare user field sql part
    if ($lUsrToBackupIds !== FALSE) {
      array_push($lUsrToBackupIds, $this -> mUsr -> getId());
      $lSqlUsrPart = ' IN ('.implode(',', $lUsrToBackupIds).')';
    } else {
      $lSqlUsrPart = ' = '.$this -> mUsr -> getId();
    }

    //prepare group field sql part
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

    $lRet = 'none';
    $lArrCondition = CCor_Res::get('rolmytask');
    if (empty($lArrCondition)) {
      return $lRet;
    } else {
	  $lGrpSql = $lUsrSql = '';
      $lSql = 'SELECT jobid FROM al_job_shadow_'.MID.' WHERE 1 AND ';
      //loop through all roles for mandator
      foreach ($lArrCondition as $lKey => $lVal) {
        if (empty($lVal['webstatus'])) continue;

        $lAli = $lVal['alias'];
        $lFieSql = '(webstatus = '.$lVal['webstatus'].' AND ';
        $lFieSql.= $lAli.' '. ($lFie[$lAli]['typ'] == 'uselect' ? $lSqlUsrPart : $lSqlGrpPart) . ' AND ';
        $lFieSql.= ' src = "'.$lVal['src'].'")';
        $lFieSql.= ' OR ';

          if($lFie[$lAli]['typ'] == 'uselect'){ //add part to usr sql
            $lPos = strpos($lUsrSql, $lFieSql);
            if($lPos === false)
              $lUsrSql.= $lFieSql;
          } else if($lFie[$lAli]['typ'] == 'gselect') { //add part to group sql
            $lPos = strpos($lGrpSql, $lFieSql);
            if($lPos === false)
              $lGrpSql.= $lFieSql;
          }
        }
      }

      if($lUsrSql != ''){ //exists any user roles
		$lTempUsrSql = substr($lUsrSql, 0, -3);
		$lUsrPos = ($lTempUsrSql == 'OR ') ? -3 : -4;
		$lUsrSql = $lSql . substr($lUsrSql, 0, $lUsrPos).' AND jobid='.esc($aJid);
      $lUsr = CCor_Qry::getStr($lUsrSql);
	  } else{
        $lUsr = '';
      }

      if($lGrpSql != ''){ //exists any group roles
		$lTempGrpSql = substr($lGrpSql, 0, -3);
		$lGrpPos = ($lTempGrpSql == 'OR ') ? -3 : -4;
		$lGrpSql = $lSql . substr($lGrpSql, 0, $lGrpPos).' AND jobid='.esc($aJid);
      $lGrp = CCor_Qry::getStr($lGrpSql);
	  } else{
        $lGrp = '';
      }

      //find out if job is group or user or both roles
      if(strlen($lUsr) > 0 && strlen($lGrp) == 0){ //only user task
        $lRet = 'user';
      } else if(strlen($lUsr) == 0 && strlen($lGrp) > 0){ //only group task
        $lRet = 'group';
      } else if(strlen($lUsr) > 0 && strlen($lGrp) > 0){ //both user and group task
        $lRet = 'both';
      }

      return $lRet;
  }
}