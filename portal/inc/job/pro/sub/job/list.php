<?php
/**
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 14606 $
 * @date $Date: 2016-06-17 14:47:19 +0200 (Fri, 17 Jun 2016) $
 * @author $Author: pdohmen $
 */
class CInc_Job_Pro_Sub_Job_List extends CJob_List {

  protected $mShowCopyButton = TRUE;
  protected $mShowDeleteButton = TRUE;
  protected $mSrc = 'pro-sub';
  protected $mWithoutLimit = FALSE;
  protected $mIsArchived = FALSE;

  public function __construct($aJobId, $aWithoutLimit = FALSE, $aIsArchived = FALSE) {
    $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');

    $this -> mWithoutLimit = $aWithoutLimit;
    $this -> mCrpId = $lCrp['art'];  // default TODO: NEED TO FIND OUT DEFAULT

    $this -> mUsr = CCor_Usr::getInstance();
    $this -> mSrcArr = CCor_Cfg::get('all-jobs');

    $lCanDeleteSrcArr = Array();
    foreach ($this -> mSrcArr as $lKey) {
      if ($this -> mUsr -> canDelete('job-'.$lKey)) {
        $lCanDeleteSrcArr[] = $lKey;
      }
    }

    if (empty($lCanDeleteSrcArr)) {
      $this -> mShowDeleteButton = FALSE;
    }

    $this -> mShowCsvExportButton = TRUE;

    $this -> mIsArchived = $aIsArchived;
    $lSrc = ($this -> mIsArchived ? 'arc' : 'job');

    $this -> mJobId = $aJobId;

    parent::__construct($lSrc.'-pro-sub', $this -> mCrpId);

    $this -> mStdLnk = 'index.php?act='.$this -> mMod.'.edt&amp;jobid='.$this -> mJobId.'&amp;id=';
    $this -> mOrdLnk = 'index.php?act='.$this -> mMod.'.ord&amp;jobid='.$this -> mJobId.'&amp;fie=';
    $this -> mDelLnk = 'index.php?act='.$this -> mMod.'.unassign&amp;jobid='.$this -> mJobId.'&amp;id=';

    $this -> mIdField = 'jobid';

    $this -> lAplstatus = array();
    foreach ($lCrp as $lCode => $lId) {
      $lSql = 'SELECT status FROM al_crp_status WHERE mand='.MID.' AND crp_id='.$lId.' AND apl=1';
      $lQry = new CCor_Qry($lSql);
      foreach ($lQry as $lRow) {
        if (!empty($lRow['status'])) {
          $this -> lAplstatus[$lCode][] = $lRow['status'];
        }
      }

      // needed for highlighting late deadlines
      if (isset($this -> mDdl[$lCode])) {
        $lDdl = $this -> mDdl[$lCode];
        foreach ($lDdl as $lddl) {
          $this -> requireAlias($lddl);
        }
        $this -> requireAlias('src');
      }
    }

    $this -> addCtr();

    $lDef = $this -> mDefs['src'];
    $this -> onAddField($lDef);

    $this -> addColumn('src', '', TRUE, array('width' => 16));

    // Show Copy Button
    $this -> mCopyJob = $this -> mUsr -> canCopyJob($this -> mSrcArr);
    if ($this -> mShowCopyButton) {
      if (!empty($this -> mCopyJob)) {
        $this -> addColumn('cpy', '', FALSE, array('width' => 16));
      }
    }
    // End Show Copy Button

    if ($this -> mUsr -> canEdit('job-pro-sub')) {
      $this -> addColumn('unassign', '', FALSE, array('width' => '16', 'id' => 'unassign'));
    }

    $lDef['name_'.LAN] = '';
    $this -> addField($lDef);
    $this -> onAddField($lDef);

    $this -> addColumns();

    $this -> addFilter('webstatus', lan('lib.status'), $this -> mCrpId);
    $this -> getFilterbyAlias(); // default: per_prj_verantwortlich

    // Show New Job Button
    if ($this -> mUsr -> canInsert($this -> mMod)) {
      $this -> addButton(lan('job.new'), $this -> getMenu());
    }
    // End Show New Job Button

    $this -> requireAlias('status');

    $this -> mMaxLines = $this -> mIte -> getCount();
    $this -> addPanel('nav', $this -> getNavBar());
    $this -> addPanel('vie', $this -> getViewMenu());

    $this -> getCriticalPaths();
  }

  protected function getIterator() {
    $lFoundItems = $this -> getProjectJobList();
    if (count($lFoundItems) > 0) {
      $lFoundItems = array_map("esc", $lFoundItems);
    }
    $lFoundItems = implode(',', $lFoundItems);
    
    $lWriter = CCor_Cfg::get('job.writer.default', 'alink');
    if ('portal' == $lWriter) {
      $this -> mIte = new CCor_TblIte('all', $this->mWithoutLimit);
      $this -> mIte -> addField('jobid');
      $this -> mIte -> addField('src');
      $this -> mIte -> addField('webstatus');
      $this -> mIte -> addCnd('jobid IN ('.$lFoundItems.')');
    } else {
      $lFie = CCor_Res::getByKey('alias', 'fie');
      $this -> mIte = new CApi_Alink_Query_Getjoblist('', $this -> mWithoutLimit);
      $this -> mIte -> addField('src', $lFie['src']['native']);
      $this -> mIte -> addField('webstatus', $lFie['webstatus']['native']);
      $this -> mIte -> addCondition('JobId', 'IN', $lFoundItems);
    }
  }

  protected function getProjectJobList(){
    $lFoundItems = array();

    $lQuery = new CCor_Qry('SELECT jobid_art, jobid_rep, jobid_sec, jobid_adm, jobid_mis, jobid_com, jobid_tra FROM al_job_sub_'.MID.' WHERE pro_id='.esc($this -> mJobId));
    foreach ($lQuery as $lOuterKey => $lOuterValue) {
      foreach ($lOuterValue as $lInnerKey => $lInnerValue) {
        if ($lInnerValue) {
          array_push($lFoundItems, $lInnerValue);
        }
      }
    }

    return $lFoundItems;
  }

  protected function getArchiveJobs() {

    $lFoundItems = $this -> getProjectJobList();

    if (count($lFoundItems) > 0) {
      $lFoundItems = array_map("esc", $lFoundItems);
      $lFoundItems = implode(',', $lFoundItems);

      $lFie = CCor_Res::getByKey('alias', 'fie');
      //get all jobs which are archived
      $lIte = new CCor_TblIte('al_job_arc_'.MID, $this -> mWithoutLimit);
      $lIte -> addField('src', $lFie['src']['native']);
      $lIte -> addField('webstatus', $lFie['webstatus']['native']);
      $lIte -> addField('jobid', $lFie['jobid']['native']); //add jobid field to gather correct information when getting array below
      $lIte -> addCondition('JobId', 'IN', $lFoundItems);

      //retrieve columns needed to be displayed in view
      $lUsrPref = $this -> mUsr -> getPref($this -> mMod.'.cols');
      if (empty($lUsrPref)) {
        $lSql = 'SELECT val FROM al_sys_pref WHERE code = "'.$this -> mMod.'.cols'.'" AND mand='.MID;
        $lUsrPref = CCor_Qry::getArrImp($lSql);
      }
      $lCol = explode(',', $lUsrPref);

      foreach ($lCol as $lFid) {
        if (isset($this -> mFie[$lFid])) {
          $lDef = $this -> mFie[$lFid];
          //add columns to iterator
          $lIte -> addField($lDef['alias'], $lDef['native']);
        }
      }

      return $lIte -> getArray('jobid');
    } else {
      return array();
    }
  }

  protected function onAddField($aDef) {
    $this -> mIte -> addDef($aDef);
  }

  protected function getLink() {
    $lSrc = $this -> getVal('src');
    $lJobId = $this -> getVal('jobid');
    return 'index.php?act=job-'.$lSrc.'.edt&amp;jobid='.$lJobId;
  }

  protected function getCriticalPaths() {
    $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');

    $this -> mCrp = array();
    foreach ($lCrp as $lKey => $lValue) {
      $this -> mCrp[$lKey] = CCor_Res::get('crp', $lCrp[$lKey]);
    }
  }

  protected function getTdSrc() {
    $lSrc = $this -> getCurVal();
    $lImg = (THEME === 'default' ? 'job-'.$lSrc : CApp_Crpimage::getColourForSrc($lSrc));
    $lRet = img('img/ico/16/'.$lImg.'.gif');
    return $this -> tdClass($this -> a($lRet), 'w16 ac');
  }

  protected function getTdWebstatus() {
    $lVal = $this -> getCurInt();
    $lSrc = $this -> getVal('src');
    if (isset($this -> mCrp[$lSrc])) {
      return $this -> getExtWebstatus($lVal, $this -> mCrp[$lSrc], $lSrc);
    }
    $lDis = $lVal / 10;
	$lPath = CApp_Crpimage::getSrcPath($lSrc, 'img/crp/'.$lDis.'b.gif');
    $lRet = img($lPath, array('style' => 'margin-right:1px'));
    return $this -> tda($lRet);
  }

  protected function getExtWebstatus($aState, $aCrp, $aSrc) {
    $lDisplay = CCor_Cfg::get('status.display', 'progressbar');

    $lVal = $aState;
    $lNam = '[unknown]';
    $lRet = '';

    foreach ($aCrp as $lRow) {
      if (($lDisplay == 'progressbar' && $lVal >= $lRow['status']) OR ($lDisplay == 'activeonly' && $lVal == $lRow['status'])) {
		$lPath = CApp_Crpimage::getSrcPath($aSrc, 'img/crp/'.$lRow['display'].'b.gif');
        $lRet.= img($lPath, array('style' => 'margin-right:1px'));
        $lNam = $lRow['name_'.LAN];
      } else if (($lDisplay == 'progressbar' && $lVal < $lRow['status']) OR ($lDisplay == 'activeonly' && $lVal != $lRow['status'])) {
		$lPath = CApp_Crpimage::getSrcPath($aSrc, 'img/crp/'.$lRow['display'].'l.gif');
        $lRet.= img($lPath, array('style' => 'margin-right:1px'));
      }
    }
    $lRet.= NB.htm($lNam);
    return $this -> tda($lRet);
  }

  protected function onBeforeContent() {
    $lRet = parent::onBeforeContent();

    $lActive = $this -> mIte -> getArray('jobid'); //get active jobs for project
    $lArchive = $this -> getArchiveJobs(); //get archive jobs for project
    $this -> mIte = array_merge($lActive, $lArchive);

    $this -> loadApl();
    $this -> loadFlags();
    return $lRet;
  }

/*  protected function loadApl() {
    $lArr = array_keys($this -> mIte);

    if (empty($lArr)) return;
    $lSql = 'SELECT id,jobid FROM al_job_apl_loop WHERE 1 ';
    $lSql.= 'AND status="open" ';
    $lSql.= 'AND mand='.intval(MID).' ';
    $lSql.= 'AND jobid IN (';
    foreach ($lArr as $lJid) {
      $lSql.= esc($lJid).',';
    }
    $lSql = strip($lSql).')';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $this -> mIte[$lRow['jobid']]['loop_id'] = $lRow['id'];
    }
  }
*/
  protected function getTdApl() {
    $lSrc = $this -> getVal('src');
    if (empty($lSrc)) {
      return $this -> tda();
    }

    $lSta = $this -> getInt('webstatus');
    $lAplstatus = array();
    // If Jobtype has NO Approval Loop, dont take in Array.
    if (isset($this -> lAplstatus[$lSrc])){
      $lAplstatus = $this -> lAplstatus[$lSrc];
    }
    // if Webstatus is not Approval Loop Status
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

  protected function addSrcConditions() {
    $lAvaSrc = Array();
    $lSrcCnd = '';
    $lAvaSrc = CCor_Cfg::get('menu-aktivejobs');
    if (!empty($lAvaSrc)) {
      foreach ($lAvaSrc as $lRow) {
        if ($this -> mUsr -> canRead($lRow)) {
          $lSrcCnd.= '"'.substr($lRow, 4).'",';
        }
      }
      if ($lSrcCnd != '') {
        $lSrcCnd = substr($lSrcCnd, 0, -1);
        $this -> mIte -> addCondition('src', 'IN', $lSrcCnd);
      }
    } else {
      return;
    }
  }

  public function getMenu() {
    $lJobTypes = CCor_Cfg::get('menu-aktivejobs');
    $lUsr = CCor_Usr::getInstance();

    $lMen = new CHtm_Menu('Button');
    $lMen -> addTh2(lan('job.types'));

    foreach ($lJobTypes as $lKey => $lValue) {
      if ($lUsr -> canInsert($lValue) && $lValue != 'job-all') {
        $lMen -> addItem('index.php?act='.$lValue.'.newsub&amp;pid='.$this -> mJobId, lan($lValue.'.menu'), 'ico/16/'.$lValue.'.gif');
      }
    }

    $lBtn = '<a class="nav w130" id="'.$this -> mLnkId.'" href="javascript:Flow.Std.popMen(\''.$lMen -> mDivId.'\',\''.$lMen -> mLnkId.'\')">';
      $lBtn .= '<div class="al"><table cellpadding="2" cellspacing="0" border="0" class="al w25p"><tr><td>';
      $lBtn .= '<i class="ico-w16 ico-w16-plus"></i>';
      $lBtn .= '</td><td class="nw al" style="text-align:left">'.lan("job.new").'</td></tr></tbody></table>';
      $lBtn .= '</div>';
    $lBtn .= '</a>';
    $lBtn .= $lMen ->getMenuDiv();

    return $lBtn;
  }

  protected function getFilterForm() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="'.$this -> mMod.'.fil" />'.LF;
    $lRet.= '<input type="hidden" name="jobid" value="'.$this -> mJobId.'" />'.LF;

    $lRet.= '<table cellpadding="2" cellspacing="0" border="0"><tr>'.LF;
    $lRet.= '<td class="caption w50 p0">Filter</td>';
    $lRet.= '<td>';
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0"><tr>'.LF;

    if (!empty($this -> mJobFilFie)) {
      foreach ($this -> mJobFilFie as $lAli => $lDef) {
        $lFnc = 'getFilter'.$lAli;
        if ($this -> hasMethod($lFnc)) {
          $lVal = (isset($this -> mFil[$lAli])) ? $this -> mFil[$lAli] : '';
          $lRet.= '<td>'.htm($lDef['cap']).'</td>';
          $lRet.= '<td>'.$this -> $lFnc($lVal, $lDef['opt']).'</td>'.LF;
        }
      }
    }

    $lRet.= '</tr></table></td>';
    $lRet.= '<td valign="top">'.btn(lan('lib.filter'),'','','submit').'</td>';
    if (!empty($this -> mFil)) {
      $lRet.= '<td valign="top">'.btn(lan('lib.show_all'),'go("index.php?act='.$this -> mMod.'.clfil&jobid='.$this -> mJobId.'")','img/ico/16/cancel.gif').'</td>';
    }

    $lRet.= '</tr></table>'.LF;
    $lRet.= '</form>';

    return $lRet;
  }

  protected function getFilterBar() {
    if (empty($this -> mJobFilFie)) {
      $this -> dbg('EMPTY FILTER!');
      return '';
    }
    if ($this -> mHideFil) {
      return '';
    }
    $lRet = '';

    $lRet.= '<tr>'.LF;
    $lRet.= '<td class="sub p0"'.$this -> getColspan().'>';
    $lRet.= $this -> getFilterForm();
    $lRet.= '</td>';
    $lRet.= '</tr>'.LF;
    return $lRet;
  }

  protected function getSearchForm() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="'.$this -> mMod.'.ser" />'.LF;
    $lRet.= '<input type="hidden" name="jobid" value="'.$this -> mJobId.'" />'.LF;

    $lRet.= '<table cellpadding="2" cellspacing="0" border="0"><tr>'.LF;
    $lRet.= '<td class="caption w50">'.htm(lan('job-ser.menu')).'</td>';
    $lRet.= '<td>';
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0"><tr>'.LF;
    $lFie = explode(',', $this -> mSerFie);
    $lFac = new CHtm_Fie_Fac();

    $lIdx = array('col_1');
    $lCnt = 0;
    foreach ($lFie as $lFid) {
      if (isset($this -> mFie[$lFid])) {
        if ($lCnt > 2) {
          $lRet.= '</tr><tr>';
          $lCnt = 0;
        }

        $lDef = $this -> mFie[$lFid];
        $lNam = $lDef['name_'.LAN];
        $lAli = $lDef['alias'];
        $lFlags = $lDef['flags'];
        if (in_array($lAli, $lIdx)) {
          $lNam = substr($lNam, 0, -1);
        }

        if (!bitSet($lFlags, ffRead) || $this -> mUsr -> canRead('fie_'.$lAli)) {
          $lRet.= '<td>'.htm($lNam).'</td>'.LF;
          $lVal = (isset($this -> mSer[$lAli])) ? $this -> mSer[$lAli] : '';
          $lRet.= '<td>';
          $lRet.= $lFac -> getInput($lDef, $lVal, fsSearch);
          $lRet.= '</td>';
        }

        $lCnt++;
      }
    }
    $lRet.= '</tr></table></td>';
    $lRet.= '<td valign="top">'.btn(lan('lib.search'),'','img/ico/16/search.gif','submit').'</td>';
    if (!empty($this -> mSer)) {
      $lRet.= '<td valign="top">'.btn(lan('lib.show_all'),'go("index.php?act='.$this -> mMod.'.clser&jobid='.$this -> mJobId.'")','img/ico/16/cancel.gif').'</td>';
    }

    $lRet.= '</tr></table>'.LF;
    $lRet.= '</form>';

    return $lRet;
  }

  protected function getSearchBar() {
    if (empty($this -> mSerFie)) {
      return '';
    }
    if ($this -> mHideSer) {
      return '';
    }
    $lRet = '';

    $lRet.= '<tr>'.LF;
    $lRet.= '<td class="sub p0"'.$this -> getColspan().'>';
    $lRet.= $this -> getSearchForm();
    $lRet.= '</td>';
    $lRet.= '</tr>'.LF;
    return $lRet;
  }

  protected function & getViewMenuObject() {
    $lUsr = CCor_Usr::getInstance();

    $lMen = new CHtm_Menu(lan('lib.opt'));

    $lMen -> addTh2(lan('lib.opt.view'));
    $lMen -> addItem('index.php?act='.$this -> mMod.'.fpr&amp;jobid='.$this -> mJobId, lan('lib.opt.fpr'), 'ico/16/col.gif');
    $lMen -> addItem('index.php?act='.$this -> mMod.'.spr&amp;jobid='.$this -> mJobId, lan('lib.opt.spr'), 'ico/16/search.gif');

    $lOk = 'ico/16/ok.gif';

    //    $lImg = ($this -> mHideFil) ?  'd.gif' : $lOk;
    //    $lMen -> addItem('index.php?act='.$this -> mMod.'.togfil', 'Show filter bar', $lImg);
    //    $lImg = ($this -> mHideSer) ?  'd.gif' : $lOk;
    //    $lMen -> addItem('index.php?act='.$this -> mMod.'.togser', 'Show search bar', $lImg);

    $lMen -> addTh2(lan('lib.opt.lpp'));
    $lArr = array(25, 50, 100, 200);
    foreach ($lArr as $lLpp) {
      $lImg = ($lLpp == $this -> mLpp) ? $lOk : 'd.gif';
      $lMen -> addItem($this -> mLppLnk.$lLpp.'&amp;jobid='.$this -> mJobId, $lLpp.' '.lan('lib.opt.lines'), $lImg);
    }

    $lMen -> addTh2(lan('lib.opt.savedviews'));
    $lSql = 'SELECT id,name FROM al_usr_view WHERE 1 ';
    $lSql.= 'AND src="usr" AND src_id =0 AND ref="'.$this -> mMod.'" AND mand='.MID.' ';
    $lSql.= 'ORDER BY name';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lMen -> addItem('index.php?act='.$this -> mMod.'.selview&amp;id='.$lRow['id'], '[Global] '.$lRow['name'], 'ico/16/global.gif');
    }

    $lSql = 'SELECT id,name FROM al_usr_view WHERE 1 ';
    $lSql.= 'AND src="usr" AND src_id ='.$lUsr -> getId().' AND ref="'.$this -> mMod.'" AND mand='.MID.' ';
    $lSql.= 'ORDER BY name';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lMen -> addItem('index.php?act='.$this -> mMod.'.selview&amp;id='.$lRow['id'].'&amp;jobid='.$this -> mJobId, $lRow['name'], 'ico/16/col.gif');
    }
    $lMen -> addItem('index.php?act=job-view&amp;src='.$this -> mMod.'&amp;jobid='.$this -> mJobId, lan('lib.view.save'));
    if ($lUsr -> canInsert('view-std')) {
      $lMen -> addItem('index.php?act='.$this -> mMod.'.allview'.'&amp;jobid='.$this -> mJobId, lan('lib.view.save_as_std'), 'ico/16/save.gif');
    }

    $lMen -> addTh2(lan('lib.opt.search_presets'));
    $lSql = 'SELECT id,name FROM al_usr_search WHERE 1 ';
    $lSql.= 'AND mand="'.MID.'" ';
    if ('job-pro' == $this -> mMod) {
      $lSql.= 'AND ref="pro" ';
    } else {
      $lSql.= 'AND ref="job" ';
    }
    $lSql.= 'AND src="usr" AND src_id='.$lUsr -> getId().' ';
    $lSql.= 'ORDER BY name';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lMen -> addItem('index.php?act='.$this -> mMod.'.selsearch&amp;id='.$lRow['id'].'&amp;jobid='.$this -> mJobId, $lRow['name'], 'ico/16/search.gif');
    }
    $lMen -> addItem('index.php?act=job-view-search&amp;src='.$this -> mMod.'&amp;jobid='.$this -> mJobId,  lan('lib.search.save'));

    //    if ($lUsr -> isMemberOf(1)) {
    //      $lMen -> addItem('index.php?act=mba.sview', 'Save as Standard', 'ico/16/save-std.gif');
    //    }

    return $lMen;
  }

 /*
  * CSV Export Button
  *
  */
  protected function setCsvExportButton() {
    $this -> addBtn(lan('csv-exp'), 'go("index.php?act='.$this -> mMod.'.csvexp&src='.$this->mSrc.'&jobid='.$this -> mJobId.'")', 'img/ico/16/excel.gif',TRUE);
    if (CCor_Cfg::get('phpexcel.available', false)) {
      $this -> addBtn(lan('xls-exp'), 'go("index.php?act='.$this -> mMod.'.xlsexp&src='.$this->mSrc.'&jobid='.$this -> mJobId.'")', 'img/ico/16/excel.gif', TRUE);
    }
  }

  protected function getTdUnassign() {
    $lJid = $this -> getVal('jobid');
    $lSrc = $this -> getVal('src');

    $lRet = '<td class="'.$this -> mCls.($this -> mHighlight ? 'r': '').' nw w16 ac">';
    $lRet.= '<a class="nav" href="javascript:Flow.Std.cnf(\''.$this -> mDelLnk.$lJid.'&src='.$lSrc.'\', \'cnfUnassign\')">';
    $lRet.= img('img/ico/16/ml-8.gif');
    $lRet.= '</a>';
    $lRet.= '</td>'.LF;
    return $lRet;
  }
}