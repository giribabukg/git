<?php
/**
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 14375 $
 * @date $Date: 2016-06-06 15:55:30 +0200 (Mon, 06 Jun 2016) $
 * @author $Author: ahanslik $
 */
class CInc_Job_List extends CHtm_List {

  protected $mAllFlags = array();
  protected $mCrpId = 0;
  /*
   * needed for highlighting the deadlines nearby
   */
  protected $mStartDate = '';
  protected $mEndDate   = '';
  protected $mDdl = array();
  protected $mColCnt = 0;
  protected $mCol;
  protected $mShowCopyButton = TRUE;
  protected $mSourceColumn = FALSE;
  protected $mShowDeleteButton = TRUE;
  protected $mShowColumnMore = FALSE;
  protected $mExtendedWebstatusFilter = FALSE;
  protected $mHighlightLine = TRUE; // if True : Highlight in all Line,
                                    // if False : Highlight only in the DDL coloumn

  protected $mShowCsvExportButton = FALSE;

  protected $mSrcArr; // Current Jobarts
  protected $mUsr; // Current User
  protected $mSrc = 'rep'; // Current Jobart
  protected $mCopyJob; // Jobart which can be inserted from current user.
  /*
   * Master-Variant Bundle aktiv?
   * @var boolean default: FALSE
   */
  public $mMasterVariantBundleActiv = FALSE;
  /*
   * Master-Variant Bundle
   * Check if column 'is_master' defined in al_job_sub_X
   */
  protected $mColumnIsMasterDefined = FALSE;

  protected $mSvcFile;
  protected $mJobImage;

  public function __construct($aMod, $aCrp = 0, $aAttr = '', $aAnyUsrID = NULL) {
    parent::__construct($aMod);

    $this -> mCapCls = (strpos($aMod, "hom") !== FALSE ? 'cap': 'cap2');

    if (is_null($aAnyUsrID)) {
      $this -> mUsr = CCor_Usr::getInstance();
    } else {
      $this -> mUsr = new CCor_Anyusr($aAnyUsrID);
    }

    $this -> mMod = $aMod;
    $this -> mStdLnk = 'index.php?act='.$aMod.'.edt&amp;jobid=';
    $this -> setAtt('width', '100%');
    $this -> mTitle = lan($aMod.'.menu');
    $this -> mImg = 'img/ico/40/job-list.gif';
    $this -> mCrpId = intval($aCrp);
    $this -> loadCrp();
    $this -> mFie = CCor_Res::get('fie');
    $this -> mDefs = CCor_Res::getByKey('alias', 'fie');
    $this -> mCnd = new CCor_Cond();
    // Highlight the whole Line OR only DDL Coloumn
    $this -> mHighlightLine = CCor_Cfg::get('job.list.highlight.line');
    $this -> mCol = $this -> getColumns();
    $this -> mAllFlags = CCor_Res::get('fla');
    $this -> mDefaultFilterField = CCor_Cfg::get('job.field.defaultFilter', 'per_prj_verantwortlich');

    $this -> getPrefs(NULL, $aAnyUsrID);

    $this -> getIterator();
    
    $this -> mWriter = CCor_Cfg::get('job.writer.default', 'alink');
    if (('mop' == $this->mWriter) && is_object($this->mIte)) {
      if (isset($this -> mSrc) && ('pro' != $this->mSrc)) {
        $this->mIte->addField('is_job', 'Job#quote2job_flag');
      }
    }
    if (!empty($this -> mIte)) {
      $this -> addGlobalSearchConditions();
      $this -> addFilterConditions();
      $this -> addSearchConditions();
      $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);
      $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);

      $lCrp = CCor_Res::extract('id', 'code', 'crpmaster');
      $lCrpCode = ((!empty($lCrp) AND isset($lCrp[$this -> mCrpId])) ? $lCrp[$this -> mCrpId] : '' );
      if (!in_array($lCrpCode, array('pro','sku'))) {
        $this -> getRequiredFlagFields();
      }
    }
    
    $this -> mJobFilFie = array();
    $this -> mDefaultFilterField = CCor_Cfg::get('job.field.defaultFilter', 'per_prj_verantwortlich');

    $this -> mHideFil = ($this -> mUsr -> getPref($aMod.'.hidefil') == 1);
    $this -> mHideSer = ($this -> mUsr -> getPref($aMod.'.hideser') == 1);

    if ($aAttr != '') {
      $this -> setAtt('id', $aAttr);
    }

    $this -> mSrcArr = CCor_Cfg::get('all-jobs'); // array('art', 'rep', 'sec', 'mis', 'adm', 'com', 'tra');
    
    if (0 < $this -> getColCnt()) {
      if ($this -> mUsr -> canEdit('job.multiple-edit')) {
        $this -> addChk();
      }
      
      // Add counter
      $this -> addCtr();
      
      // Show Coloumn More
      if ($this -> mShowColumnMore) {
        $this -> addMor();
      }
      
      // Show copy button
      $this -> mCopyJob = $this -> mUsr -> canCopyJob($this -> mSrcArr);
      if ($this -> mShowCopyButton) {
        if (!empty($this -> mCopyJob) AND (substr($this -> mMod, -3) != 'sku')) {
          $this -> addCpy();
        }
      }
      
      // Show delete button
      if ($this -> mShowDeleteButton) {
        $this -> addDel();
      }
      
      // Show job source
      if ($this -> mSourceColumn) {
        $this -> addSrc();
      }

      $this -> addColumns();
    }
    
    // needed for highlighting the deadlines nearby
    $lStartDaysPast = CCor_Cfg::get('ddl.past', -1); // -1: no limit in the past
    if (-1 < $lStartDaysPast) {
      $lStartDate = mktime(0, 0, 0, date("m"), date ("d") - $lStartDaysPast, date("Y"));
      $this -> mStartDate = strftime("%Y-%m-%d" , $lStartDate);
    }
    $lStartDaysFuture = CCor_Cfg::get('ddl.future', 1);
    $lEndDate = mktime(0, 0, 0, date("m"), date ("d") + $lStartDaysFuture, date("Y"));
    $this -> mEndDate = strftime("%Y-%m-%d" , $lEndDate);
    $this -> mDdl = CCor_Res::get('ddl');
    
    if (!empty($this -> mIte)) {
      $lAllJobsFromAlink = CCor_Cfg::get('all-jobs_ALINK');
      if (isset($this -> mSrc) AND in_array($this -> mSrc, $lAllJobsFromAlink) AND isset($this -> mDdl[$this -> mSrc])) {
        // not available for job-all
        $lDdl = $this -> mDdl[$this -> mSrc];
        foreach ($lDdl as $lddl) {
          $this -> requireAlias($lddl);
        }
      }
      
      // INSERT CSV EXPORT BUTTON
      if ($this -> mShowCsvExportButton) {
        if ($this-> mUsr -> canRead('csv-exp')) {
          $this -> setCsvExportButton();
        }
      }

      // INSERT REPORTING EXPORT BUTTON
      if ($this-> mUsr -> canRead('rep-exp') && CCor_Cfg::get('extended.reporting', FALSE)) {
        $this -> setRepExportButton();
      }

      // INSERT MULTIPLE EDIT BUTTON
      $this -> mJobFields = CCor_Res::extract('id', 'name_'.LAN, 'fie', array('flags' => 4096));
      if (!empty($this -> mJobFields) && $this-> mUsr -> canEdit('job.multiple-edit')) {
        $this -> setMultipleEditButton();
      }
    }
    $this -> mShowFlaWithFlags = CCor_Cfg::get('show.flawithflags', TRUE);
    $this -> mShowDdlWithFlags = CCor_Cfg::get('show.ddlwithflags', TRUE);
    $this -> mShowFlagsWithDdl = CCor_Cfg::get('show.flagswithddl', TRUE);
    
    // Master-Variant Bundle active?
    // If Yes: Load Jobfield 'master_id'.
    if ($this -> mMasterVariantBundleActiv = CCor_Cfg::get('master.varaiant.bundle', FALSE)){
      if ($this -> mColumnIsMasterDefined = $this-> isFieldIsMasterDefined()){
        if (isset($this -> mSrc) AND in_array($this -> mSrc, $this -> mSrcArr)){
          $this -> requireAlias('master_id');
        }
      }
    }

    $this -> mSvcFile = CSvc_Wec::getInstance(); // TODO: rename: Svc_Wec > Svc_File/Cor_File
    $this -> mJobImage = CJob_Image::getInstance(); // TODO: rename: Job_Image > Cor_Image
  }

  public function getCrpId() {
    return $this -> mCrpId;
  }

  public function getColCnt() {
    $this -> mColCnt = count($this -> mCol);
    return $this -> mColCnt;
  }

  public function getStartDate() {
    return $this -> mStartDate;
  }

  public function getEndDate() {
    return $this -> mEndDate;
  }

  protected function loadCrp() {
    $this -> mCrp = CCor_Res::get('crp', $this -> mCrpId);
  }

  protected function getRequiredFlagFields() {
      foreach ($this -> mAllFlags as $lFlag) {
      if (!empty($lFlag['alias'])) {
        $lRequired = $lFlag['alias'];
        $this -> requireAlias($lRequired);
      }
      if (!empty($lFlag['ddl_fie'])) {
        $lRequired = $lFlag['ddl_fie'];
        $this -> requireAlias($lRequired);
      }
    }
  }

  protected function addCondition($aAlias, $aOp, $aValue, $aNative = TRUE) {
    $lWriter = CCor_Cfg::get('job.writer.default', 'alink'); if ('portal' == $lWriter) $aNative = FALSE;
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

  protected function addFilter($aAlias, $aCaption, $aOpt = NULL) {
    $lRet = array();
    $lRet['cap'] = $aCaption;
    $lRet['opt'] = $aOpt;
    $this -> mJobFilFie[$aAlias] = $lRet;

    // can we keep it here for the new Alink
    $lAllJobsFromAlink = CCor_Cfg::get('all-jobs_ALINK');
    $lAllJobsFromAlink[] = 'all';
    if (isset($this -> mSrc) AND in_array($this -> mSrc, $lAllJobsFromAlink)) {
      $this -> mIte -> addField($aAlias, $this -> mDefs[$aAlias]['native']);
    }
  }

  protected function addSort($aAlias) {
    $lRet = array();
    $lDef = $this -> mDefs[$aAlias];
    $lRet['cap'] = $lDef['name_'.LAN];
    $this -> mJobSortFie[$aAlias] = $lRet;
  }

  protected function getIterator() {
    if (CCor_Cfg::get('job.writer.default') == 'portal') {
      $this -> mIte = new CCor_TblIte('al_job_'.$this -> mSrc.'_'.intval(MID), $this->mWithoutLimit);
      $this -> mIte -> addField('jobid');
      $this -> mIte -> addCnd('webstatus >= 10');
    }
    else {
      $this -> mIte = new CApi_Alink_Query_Getjoblist($this -> mSrc, $this -> mWithoutLimit);
      $this -> mIte -> addField('jobnr', 'jobnr');
      $this -> addCondition('src', '=', $this -> mSrc);
      $this -> addUserConditions();
    }
  }

  public function addCndEx($aCnd) {
    $this -> mIte -> addCndEx($aCnd);
  }

  protected function addFilterConditions() {
	if (CCor_Cfg::get('job-fil.combined', FALSE)) {
       return $this->getCombinedFilterConditions();
    }

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
            } else {
              $this -> mIte -> addCondition('jobid', '=', 'NOJOBSFOUND');
            }
          }
        } else {
          $this -> addCondition($lKey, '=', $lValue);
        }
      }
    }
  }

  protected function getCombinedFilterConditions ()
  {
    if (empty($this -> mFil))
      return;

    foreach ($this -> mFil as $lKey => $lValue) {
      if ( ! empty($lValue)) {
        if (is_array($lValue) and $lKey == "webstatus") {
          $lStates = "";

          foreach ($lValue as $lWebstatus => $foo) {
            if ($lWebstatus == 0) {
              break;
            } else {
              $lStates .= '"' . $lWebstatus . '",';
            }
          }

          if ( ! empty($lStates)) {
            $lStates = substr($lStates, 0,  - 1);
            $this -> addCondition('webstatus', 'in', $lStates);
          }
        } elseif (is_array($lValue) and $lKey == "flags") {
          $lStates = "";

          foreach ($lValue as $lKey => $lValue) {
            if ($lKey == 0) {
              break;
            }
            if ($lValue == 1) {
              $lStates .= "((flags & " . $lKey . ") = " . $lKey . ") OR ";
            }
            if ($lValue == 2) {
              $lStates .= "((flags & " . $lKey . ") = 0) AND ";
            }
          }

          $lStates = ( ! empty($lStates)) ? substr($lStates, 0, strlen($lStates) - 4) : '';

          if ( ! empty($lStates)) {

            $lJobIds = '';
            $lSQL = 'SELECT jobid FROM al_job_shadow_' . MID . ' WHERE ' . $lStates . ';';

            $lQry = new CCor_Qry($lSQL);
            foreach ($lQry as $lRow) {
              $lJobId = trim($lRow['jobid']);
              if ( ! empty($lJobId)) {
                $lJobIds .= '"' . $lJobId . '",';
              }
            }
           $lJobIds = strip($lJobIds);

            if ( ! empty($lJobIds)) {
              $this -> mIte -> addCondition('jobid', 'IN', $lJobIds);
            } else {
              $this -> mIte -> addCondition('jobid', '=', 'NOJOBSFOUND');
            }
          }
        } else {
          $this -> addCondition($lKey, '=', $lValue);
        }
      }
    }
  }

  protected function addSearchConditions() {
    /** By extended Webstatus Filter (with Checkbox) Webstatus Value is an Array.
     * 	Therefore must be asked with 'IN' in the Qurey.
    */

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

  protected function addGlobalSearchConditions() {

    $lSid = $this -> mUsr -> getPref('job.ser_id');
    if (empty($lSid)) return;
    $lSer = $this -> mUsr -> getPref('job.ser_ser');
    if (empty($lSer)) return;

    if (($lRes = @unserialize($lSer)) !== FALSE) {
      $lSer = $lRes;
    }

    if (!is_object($this -> mIte)) {
      $this->dbg('Iterator not an object, cannot apply global search criteria', mlWarn);
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

  protected function addColumns() {
    foreach ($this -> mCol as $lFid) {
      if (isset($this -> mFie[$lFid])) {
        $lDef = $this -> mFie[$lFid];
        $this -> addField($lDef);
        $this -> onAddField($lDef);
      }
    }
  }

  protected function onAddField($aDef) {
  }

  protected function requireAlias($aAlias) {
    if (!isset($this -> mDefs[$aAlias])){
      $this->dbg('Field '.$aAlias. ' not defined',mlWarn);
      return;
    }
    $lDef = $this -> mDefs[$aAlias];
    $this -> mIte -> addDef($lDef);
  }

  public function loadFlags() {
    $lArr = array_keys($this -> mIte);
    if (empty($lArr)) return;
    $lSql = 'SELECT jobid,flags FROM al_job_shadow_'.intval(MID).' WHERE jobid IN (';
    foreach ($lArr as $lJid) {
      $lSql.= esc($lJid).',';
    }
    $lSql = strip($lSql).')';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $this -> mIte[$lRow['jobid']]['flags'] = $lRow['flags'];
    }
  }

  protected function isAplType($aType) {
    return substr($aType,0,3) == 'apl';
  }

  public function loadApl() {
    $lArr = array_keys($this -> mIte);
    if (empty($lArr)) return;
    $lSql = 'SELECT id,jobid,typ FROM al_job_apl_loop WHERE 1';
    $lSql.= ' AND status='.esc(CApp_Apl_Loop::APL_LOOP_OPEN);
    $lSql.= ' AND mand='.intval(MID);
    $lSql.= ' AND jobid IN (';
    foreach ($lArr as $lJid) {
      $lSql.= esc($lJid).',';
    }
    $lSql = strip($lSql).') ORDER BY id ASC';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      if ($this->isAplType($lRow['typ'])) {
        $this -> mIte[$lRow['jobid']]['loop_id'] = $lRow['id'];
      } else {
        $this -> mIte[ $lRow['jobid'] ]['loop_typ'][ $lRow['typ'] ] = $lRow['id'];
        if (isset($this -> mAllFlags[ $lRow['typ'] ])) {
          $Flag = $this -> mAllFlags[ $lRow['typ'] ];
          if ( !empty($Flag['alias']) AND !isset($this -> mCrpFlagsAliase[ $Flag['alias'] ]) ) {
            $this -> mCrpFlagsAliase[ $Flag['alias'] ]['alias'] = $lRow['typ'];
            if ( !empty($Flag['ddl_fie']) ) {
              $this -> mCrpFlagsAliase[ $Flag['alias'] ]['ddl_fie'] = $Flag['ddl_fie'];
            }
          }
          if ( !empty($Flag['ddl_fie']) AND !isset($this -> mCrpFlagsDdl[ $Flag['ddl_fie'] ]) ) {
            $this -> mCrpFlagsDdl[ $Flag['ddl_fie'] ]['ddl_fie'] = $lRow['typ'];
            if ( !empty($Flag['alias']) ) {
              $this -> mCrpFlagsDdl[ $Flag['ddl_fie'] ]['alias'] = $Flag['alias'];
            }
          }
        }//end_if (isset($this -> mAllFlags[ $lRow['typ'] ]))
      }
    }
    foreach ($this -> mAllFlags as $lTyp => $lFlag) {
      if (!empty($lFlag['alias'])) {
        $this -> mCrpAllFlagsAli[ $lFlag['alias'] ] = $lTyp;
      }
      if (!empty($lFlag['ddl_fie'])) {
        $this -> mCrpAllFlagsDdl[ $lFlag['ddl_fie'] ] = $lTyp;
      }
    }
  }

  protected function getTitleContent() {
    $lRet = '<table cellpadding="0" cellspacing="0" border="0"><tr>'.LF;
    if(THEME === 'default')
    $lRet.= '<td>'.img($this -> mImg, array('style' => 'float:left','alt' => $this -> mTitle)).'</td>';

    if(strpos($_GET['act'], 'job') !== FALSE || strpos($_GET['act'], 'arc') !== FALSE){
      $lSrc = substr($this -> mMod, 4, 3);
      $lCls = CApp_Crpimage::getColourForSrc($lSrc);
    } else {
      $lCls = '';
    }

    $lRet.= '<td class="captxt p8 '.$lCls.'">'.htm($this -> mTitle).'</td>';
    $lRet.= '</tr></table>'.LF;
    return $lRet;
  }

  protected function & getViewMenuObject() {
    $lMen = new CHtm_Menu(lan('lib.opt'));

    $lMen -> addTh2(lan('lib.opt.view'));
    $lMen -> addItem('index.php?act='.$this -> mMod.'.fpr', lan('lib.opt.fpr'), '<i class="ico-w16 ico-w16-col"></i>');
    $lMen -> addItem('index.php?act='.$this -> mMod.'.spr', lan('lib.opt.spr'), '<i class="ico-w16 ico-w16-search"></i>');

    $lOk = '<i class="ico-w16 ico-w16-ok"></i>';

//     $lImg = ($this -> mHideFil) ?  'd.gif' : $lOk;
//     $lMen -> addItem('index.php?act='.$this -> mMod.'.togfil', 'Show filter bar', $lImg);
//     $lImg = ($this -> mHideSer) ?  'd.gif' : $lOk;
//     $lMen -> addItem('index.php?act='.$this -> mMod.'.togser', 'Show search bar', $lImg);

    $lMen -> addTh2(lan('lib.opt.lpp'));
    $lArr = array(5, 10, 25, 50, 100, 200);
    foreach ($lArr as $lLpp) {
      $lImg = ($lLpp == $this -> mLpp) ? $lOk : img("img/d.gif");
      $lMen -> addItem($this -> mLppLnk.$lLpp, $lLpp.' '.lan('lib.opt.lines'), $lImg);
    }

    $lMen -> addTh2(lan('lib.preview.size'));
    $lArr = array(32, 48, 64, 80, 96);
    foreach ($lArr as $lPvs) {
      $lImg = ($lPvs == $this -> mPvs) ? $lOk : img("img/d.gif");
      $lMen -> addItem($this -> mPvsLnk.$lPvs, $lPvs.' '.lan('lib.preview.px'), $lImg);
    }

    $lMen -> addTh2(lan('lib.opt.savedviews'));
    $lSql = 'SELECT id,name FROM al_usr_view WHERE 1 ';
    $lSql.= 'AND src="usr" AND src_id =0 AND ref="'.$this -> mMod.'" AND mand='.MID.' ';
    $lSql.= 'ORDER BY name';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lMen -> addItem('index.php?act='.$this -> mMod.'.selview&amp;id='.$lRow['id'], '[Global] '.$lRow['name'], '<i class="ico-w16 ico-w16-fie"></i>');
    }

    $lSql = 'SELECT id,name FROM al_usr_view WHERE 1 ';
    $lSql.= 'AND src="usr" AND src_id ='.$this -> mUsr -> getId().' AND ref="'.$this -> mMod.'" AND mand='.MID.' ';
    $lSql.= 'ORDER BY name';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lMen -> addItem('index.php?act='.$this -> mMod.'.selview&amp;id='.$lRow['id'], $lRow['name'], '<i class="ico-w16 ico-w16-col"></i>');
    }
    $lMen -> addItem('index.php?act=job-view&amp;src='.$this -> mMod, lan('lib.view.save'));
    if ($this -> mUsr -> canInsert('view-std')) {
      $lMen -> addItem('index.php?act='.$this -> mMod.'.allview', lan('lib.view.save_as_std'), '<i class="ico-w16 ico-w16-save"></i>');
    }

    $lMen -> addTh2(lan('lib.opt.search_presets'));
    $lSql = 'SELECT id,name FROM al_usr_search WHERE 1 ';
    $lSql.= 'AND mand="'.MID.'" ';
    if ('job-pro' == $this -> mMod) {
      $lSql.= 'AND ref="pro" ';
    } else {
      $lSql.= 'AND ref="job" ';
    }
    $lSql.= 'AND src="usr" AND src_id='.$this -> mUsr -> getId().' ';
    $lSql.= 'ORDER BY name';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lMen -> addItem('index.php?act='.$this -> mMod.'.selsearch&amp;id='.$lRow['id'], $lRow['name'], '<i class="ico-w16 ico-w16-search"></i>');
    }
    $lMen -> addItem('index.php?act=job-view-search&amp;src='.$this -> mMod,  lan('lib.search.save'));
    return $lMen;
  }

  protected function getSearchForm() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="'.$this -> mMod.'.ser" />'.LF;

    $lRet.= '<table cellpadding="2" cellspacing="0" border="0"><tr>'.LF;
    $lRet.= '<td class="caption w50">'.htm(lan('job-ser.menu')).'</td>';
    $lRet.= '<td>';
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0"><tr>'.LF;

    $lSerId = $this -> mUsr -> getPref('job.ser_id');
    $lSerSer = $this -> mUsr -> getPref('job.ser_ser');
    if ($lSerId > 0 && $lSerSer != '') {
      if (($lUnSerSerSer = @unserialize($lSerSer)) !== FALSE) {
        $lSerSer = $lUnSerSerSer;
      }

      $lRes = '';
      foreach ($lSerSer as $lKey => $lValue) {
        $lRes.= $this -> mDefs[$lKey]['name_'.LAN] .' = '.$lValue.', ';
      }

      $lRet.= '<th colspan="6"><b>'.lan('lib.search_presets.active').':</b> '; // the 6 comes from the numbers of (job) fields per line (3) plus their names (3) as each is a separate cell
      $lRet.= $lRes;
      $lRet.= '</th></tr><tr>';
    }

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
        // Bei abhaengigen Auuftragsfeldern wird standard Wert mit Variable 'NoChoice' definiert
        // was aber in der Suche nicht noetig ist.
        if (isset($lDef['NoChoice']) && !empty($lDef['NoChoice'])){
          unset($lDef['NoChoice']);
        }
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
    $lRet.= '<td valign="top">'.btn(lan('lib.search'),'','<i class="ico-w16 ico-w16-search"></i>','submit').'</td>';
    if (!empty($this -> mSer)) {
      $lRet.= '<td valign="top">'.btn(lan('lib.show_all'),'go("index.php?act='.$this -> mMod.'.clser")','<i class="ico-w16 ico-w16-cancel"></i>').'</td>';
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

  protected function getFilterForm() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="'.$this -> mMod.'.fil" />'.LF;

    $lRet.= '<table cellpadding="2" cellspacing="0" border="0"><tr>'.LF;
    $lRet.= '<td class="caption w50 p0">Filter</td>';
    $lRet.= '<td>';
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0"><tr>'.LF;

    if (!empty($this -> mJobFilFie)) {
      foreach ($this -> mJobFilFie as $lAli => $lDef) {
        $lFnc = 'getFilter'.$lAli;
        if ($this -> hasMethod($lFnc)) {

          $lVal = (isset($this -> mFil[$lAli])) ? $this -> mFil[$lAli] : '';
          $lRet.= $this -> $lFnc($lVal, $lDef['opt'], htm($lDef['cap']));
        }
      }
    }

    $lRet.= '</tr></table></td>';
    $lRet.= '<td>'.btn(lan('lib.filter'),'','','submit').'</td>';
    if (!empty($this -> mFil)) {
      $lRet.= '<td >'.btn(lan('lib.show_all'),'go("index.php?act='.$this -> mMod.'.clfil")','<i class="ico-w16 ico-w16-cancel"></i>').'</td>';
    }

    $lRet.= '</tr></table>'.LF;
    $lRet.= '</form>';

    return $lRet;
  }

  // TTS-497: Anzeige Button "Sortierung n. Statuswechsel"
  public function getButtonMenu($aMod) {
    $lSort = $this -> mUsr -> getPref($this -> mPrf.'.ord');

    $lMen = new CHtm_Menu('Button');
    $lUrl = 'index.php?act='.$aMod.'.ord&amp;fie=';
    foreach ($this -> mJobSortFie as $lId => $lRow) {
      if(FALSE !== strpos($lSort, $lId)) {
        if($lId != $lSort) {
          $lImg = '<i class="ico-w16 ico-w16-ord-desc"></i>';
        } else {// == '-'.$lId
          $lImg = '<i class="ico-w16 ico-w16-ord-asc"></i>';
        }
      } else {
          $lImg = '<i class="ico-w16 ico-w16-ord-asc-desc"></i>';
      }

      $lMen -> addTh2($lRow['cap']);
      $lMen -> addItem($lUrl.$lId,     lan('lib.sort.asc'),  '<i class="ico-w16 ico-w16-ord-asc"></i>');
      $lMen -> addItem($lUrl.'-'.$lId, lan('lib.sort.desc'), '<i class="ico-w16 ico-w16-ord-desc"></i>');
    }
    $lLnk = "javascript:gIgn=1;Flow.Std.popMen('".$lMen -> mDivId."')";
    $lBtn = btn(lan('lib.sort'), $lLnk, $lImg, 'button', array('class' => 'btn w130','id' => $lMen -> mLnkId ));
    $lBtn .= $lMen -> getMenuDiv();

    return $lBtn;
  }

  protected function getFilterBar() {
    if (empty($this -> mJobFilFie)) {
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

  protected function getFilterFlags($aVal, $aOpt, $aCap = '') {
    $lRet = '';

    // what job flags are there at all?
    $lJfl = array(0 => '['.lan('lib.all').']');
    $lSql = 'SELECT val, name_'.LAN.' AS name FROM al_jfl WHERE mand IN (0, '.MID.') ORDER BY val;';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lJfl[intval($lRow['val'])] = $lRow['name'];
    }

    // what job flags are currently set (array)?
    $lFlags = Array();
    if (isset($this -> mFil['flags'])) {
      $lFlags = $this -> mFil['flags']; // User preferences filter flags
    }

    // what job flags are currently set (bit)?
    $lJflFiltered = 0;
    foreach ($lFlags as $lKey => $lValue) {
      $lJflFiltered += $lValue;
    }

    // IDs
    $lDiv = getNum('do'); // outer div
    $lDivId = getNum('di'); // inner div
    $lLnkId = getNum('l'); // link

    $lRet.= '<td>'.LF;
    $lRet.= '  <div id="'.$lDiv.'">'.LF;
    $lRet.= '    <a class="nav" id="'.$lLnkId.'" href="javascript:Flow.Std.popMen(\''.$lDivId.'\',\''.$lLnkId.'\')">'.LF;
    $lRet.= '      <b>'.$aCap.NB.NB.'</b>'.LF;

    foreach ($lJfl as $lKey => $lValue) {
      if ($lKey === 0) {
        continue;
      }

      if (bitset($lJflFiltered, $lKey) OR empty($lFlags)) {
        $lRet .= '<i class="ico-jfl ico-jfl-'.$lKey.'" title="'.$lValue.'"></i>';
      } else {
        $lRet .= '<i class="ico-jfl ico-jfl-'.$lKey.'l" title="'.$lValue.'"></i>';
      }
    }

    $lRet.= '    </a>'.LF;
    $lRet.= $this -> getFilterFlagsWithCheckbox($lJfl, $lFlags, $lDivId);
    $lRet.= '  </div>'.LF;
    $lRet.= '</td>'.LF;
    return $lRet;
  }

  protected function getFilterFlagsWithCheckbox($aAllJfl, $aCheckedJfl = array(), $aDivId) {
    $lAllJfl = $aAllJfl;
    $lCheckedJfl = $aCheckedJfl;
    $lDivId = $aDivId;

    $lRet = "";
    $lChkBoxes = array();
    $lAllFlags = FALSE;
    $lValues = array();

    if (!empty($lCheckedJfl)) {
      // what job flags are currently set (array)?
      foreach ($lCheckedJfl as $lKey => $lVal) {
        $lValues[$lKey] = $lKey;
      }

      // what job flags are currently set (bit)?
      $lJflFiltered = 0;
      foreach ($lValues as $lKey => $lValue) {
        $lJflFiltered += $lValue;
      }

      // what job flags are currently set (bit)?
      $lAllJflFiltered = 0;
      foreach ($lAllJfl as $lKey => $lValue) {
        $lAllJflFiltered += $lKey;
      }
    } else {
      $lAllFlags = TRUE;
    }

    if (in_array(0, $lValues)) {
      $lAllFlags = TRUE;
    }

    $lFlagsArr = $aAllJfl;
    $lCount = count($lFlagsArr);
    $lFlags = "";
    foreach ($lFlagsArr as $lKey => $lValue) {
      if ($lKey == 0) continue;
      $lFlags.= "".$lKey.",";
    }
    $lFlags = substr($lFlags, 0, -1);

    foreach ($lAllJfl as $lKey => $lValue) {
      $lChkBox = '<input type="checkbox" name="val[flags]['.$lKey.']" value="'.$lKey.'" ';
      $lChkBox.= 'id="flagcheckbox'.$lKey.'" ';
      if (in_array($lKey, $lValues) OR $lAllFlags === TRUE) {
        $lChkBox.= 'checked="checked"';
      }
      if ($lKey == 0) {
        $lChkBox.= ' onclick="javascript:gIgn=1;checkAllFlags(\'' .$lFlags . '\')"';
      } else {
        $lChkBox.= ' onclick="javascript:gIgn=1;uncheckAllFlags(\'flagcheckbox'.$lKey.'\', \'' .$lFlags . '\');"';
      }
      $lChkBox.= '>&nbsp;';

      //$lPath = getImgPath('img/jfl/'.$lKey.'.gif');
      $lChkBox .= '<i class="ico-jfl ico-jfl-'.$lKey.'" title="'.$lValue.'"></i>';

      $lChkBox.= '&nbsp;'.$lValue;
      $lChkBoxes[] = $lChkBox;
    }

    $lRet = '<div id="'.$aDivId.'" class="smDiv" style="display:none">';
    $lRet.= '  <table border="0" cellspacing="0" cellpadding="2" class="tbl mw200">';

    for ($lDummy = 0; $lDummy < count($lChkBoxes); $lDummy++) {
      $lRet.= "    <tr>";
      $lRet.= '      <td class="td1 nw">'.$lChkBoxes[$lDummy].'</td>';
      $lRet.= "    </tr>";
    }

    $lRet.= '  </table>';
    $lRet.= '</div>';

    return $lRet;
  }

  protected function getFilterWebstatus($aVal, $aOpt, $aCap = '') {
    if (CCor_Cfg::get('job-fil.combined', false)) {
      return $this->getNewFilterWebstatus($aVal, $aOpt, $aCap = '');
      exit();
    }
	$lRet = '';
    $lCrp = CCor_Res::get('crp', $aOpt);
    //If empty Critical Path, return
    if (empty($lCrp)) {
      return $lRet;
    }

    // Get Archive Webstatus
    $lSql = 'SELECT c.status FROM al_crp_status c, al_crp_step s WHERE s.mand = '.MID.' AND s.crp_id='.$aOpt.' AND c.id=s.to_id AND s.trans IN ("job2arc","pro2arc")';
    $lSta2Arc = CCor_Qry::getInt($lSql);

    // Sort out the Archive Webstatus
    $lArr = array(0 => '['.lan('lib.all').']');
    foreach ($lCrp as $lRow) {
      if ($lRow['status'] != $lSta2Arc){
        $lArr[intval($lRow['status'])] = $lRow['display'].' - '.$lRow['name_'.LAN];
      }
    }

    // Check Configuration Variable ''extended.webstatus.filter''
    // If True, show extended Webstatuslist in Popup
    // If False, show in selectbox
    $this -> mExtendedWebstatusFilter = CCor_Cfg::get('extended.webstatus.filter');

    if (!$this -> mExtendedWebstatusFilter){ // Show standart Selectbox
      // Show in selectbox
      $lRet = '<td><b>'.$aCap.'</b></td>';
      $lRet.= '<td>';
      $lRet.= getSelect('val[webstatus]', $lArr, $aVal);
    } else {
      // Show extended Webstatuslist
      // Load User pref.
      $lFilterWebstatus = Array();
      if (isset($this -> mFil['webstatus'])){
        $lFilterWebstatus = $this -> mFil['webstatus']; // User Pref Filter Webstatus
      }

      $lDiv = getNum('d'); // surrounding div
      $lDivId = getNum('w'); // div of poupmenu
      $lLnkId = getNum('l');
      $lRet.= '<td>';
      $lRet.= '<div id="'.$lDiv.'">';
      $lRet.= '<a class="nav" id="'.$lLnkId.'" href="javascript:Flow.Std.popMen(\''.$lDivId.'\',\''.$lLnkId.'\')">';
      $lRet.= '<b>'.$aCap.NB.NB.'</b>';

      /**
       * JobId # 22922
       * Selected Webstatus are be showed with a small Webstatus icon.
       */
      foreach ($lCrp as $lRow) {
        // If Archive Webstatus, continue
        if ($lRow['status'] == $lSta2Arc){
          continue;
        }
        if (array_key_exists($lRow['status'],$lFilterWebstatus) OR empty($lFilterWebstatus)) {
          $lRet .= CApp_Crpimage::getCrpIco($this->mSrc, $lRow['display'].'b');
        } else{
          $lRet .= CApp_Crpimage::getCrpIco($this->mSrc, $lRow['display'].'l');
        }
      }
      $lRet.= '</a>';
      $lRet.= $this -> getFilterWebstatusWithCheckbox($lArr, $lFilterWebstatus,$lDivId);
      $lRet.= '</div></td>';
      $lRet.= '<td>';

    }
    $lRet.= '</td>'.LF;
    return $lRet;
  }

  protected function getNewFilterWebstatus($aVal, $aOpt, $aCap = '') {

    $lRet = '';
    $lCrp = CCor_Res::get('crp', $aOpt);
    //If empty Critical Path, return
    if (empty($lCrp)) {
      return $lRet;
    }

    // Get Archive Webstatus
    $lSql = 'SELECT c.status FROM al_crp_status c, al_crp_step s WHERE s.mand = '.MID.' AND s.crp_id='.$aOpt.' AND c.id=s.to_id AND s.trans IN ("job2arc","pro2arc")';
    $lSta2Arc = CCor_Qry::getInt($lSql);

    // Sort out the Archive Webstatus
    $lArr = array(0 => '['.lan('All Status').']');
    foreach ($lCrp as $lRow) {
      if ($lRow['status'] != $lSta2Arc){
        $lArr[intval($lRow['status'])] = $lRow['display'].' - '.$lRow['name_'.LAN];
      }
    }

    // Check Configuration Variable ''extended.webstatus.filter''
    // If True, show extended Webstatuslist in Popup
    // If False, show in selectbox
    $this -> mExtendedWebstatusFilter = CCor_Cfg::get('extended.webstatus.filter');

    if (!$this -> mExtendedWebstatusFilter){ // Show standart Selectbox
      // Show in selectbox
      $lRet = '<td><b>'.$aCap.'</b></td>';
      $lRet.= '<td>';
      $lRet.= getSelect('val[webstatus]', $lArr, $aVal);
    } else {
      // Show extended Webstatuslist
      // Load User pref.
      $lFilterWebstatus = Array();
      if (isset($this -> mFil['webstatus'])){
        $lFilterWebstatus = $this -> mFil['webstatus']; // User Pref Filter Webstatus
      }

      $lDiv = getNum('d'); // surrounding div
      $lDivId = getNum('w'); // div of poupmenu
      $lLnkId = getNum('l');
      $lRet.= '<td>';
      $lRet.= '<div id="'.$lDiv.'">';
      $lRet.= '<a class="nav" id="'.$lLnkId.'" href="javascript:Flow.Std.popMen(\''.$lDivId.'\',\''.$lLnkId.'\')">';
      $lRet.= '<b>'.lan('job.combined.filter.options').NB.NB.'</b>';

      $lRet.= '</a>';
      $lRet.= $this -> getFilterCombinedStatusWithCheckbox($lArr, $lFilterWebstatus,$lDivId);
      $lRet.= '</div></td>';
      $lRet.= '<td>';
    }
    $lRet.= '</td>'.LF;
    return $lRet;
  }

  protected function getFilterPer_prj_verantwortlich($aVal, $aOpt, $aCap='') {
    $lRet = '<td><b>'.$aCap.'</b></td>';
    $lRet.= '<td>';
    $lUsr = CCor_Res::extract('id', 'fullname', 'usr', $aOpt);
    asort($lUsr);
    $lArr = array(0 => '['.lan('lib.all').']');
    foreach ($lUsr as $lUid => $lNam) {
      $lArr[intval($lUid)] = $lNam;
    }
    $lRet.= getSelect('val[per_prj_verantwortlich]', $lArr, $aVal);
    $lRet.= '</td>'.LF;
    return $lRet;
  }

  protected function getFilterPer_brand_manager($aVal, $aOpt, $aCap='') {
    $lRet = '<td><b>'.$aCap.'</b></td>';
    $lRet.= '<td>';
    $lUsr = CCor_Res::extract('id', 'fullname', 'usr', $aOpt);
    asort($lUsr);
    $lArr = array(0 => '['.lan('lib.all').']');
    foreach ($lUsr as $lUid => $lNam) {
      $lArr[intval($lUid)] = $lNam;
    }
    $lRet.= getSelect('val[per_brand_manager]', $lArr, $aVal);
    $lRet.= '</td>'.LF;
    return $lRet;
  }

  protected function getFilterbyAlias($aAlias = NULL) {
    $lAlias = (is_null($aAlias)) ? $this -> mDefaultFilterField : $aAlias;
    $lGruArr = CCor_Res::extract('alias', 'param', 'fie');
    asort($lGruArr);

    // Check whether alias is defined or not
    if (isset($lGruArr[$lAlias])) {
      $lGruArr = unserialize($lGruArr[$lAlias]);
      $lDef = $this -> mDefs[$lAlias];
      $this -> addFilter($lAlias, $lDef['name_'.LAN], array('gru' => $lGruArr['gru']));
    } else {
      // Alias is not defined
      $this -> dbg('Alias '.$aAlias.' is not defined');
      return;
    }
  }

  protected function getFilterProd_art($aVal, $aOpt, $aCap = '') {
    $lRet = '<td><b>'.$aCap.'</b></td>';
    $lRet.= '<td>';
    $lArr = CCor_Res::get('htb', 'sit');
    $lArr = array_merge(array(0 => '[all]'), $lArr);
    $lRet = getSelect('val[prod_art]', $lArr, $aVal);
    $lRet.= '</td>'.LF;
    return $lRet;
  }

  protected function getTdJobnr() {
    $lJid = $this -> getVal('jobid');
    return $this -> tda(jid($lJid));
  }

  protected function getTdPreview() {
    $lSrc = $this -> getVal('src');
    $lJobId = $this -> getVal('jobid');

    $lAttributes = $this -> mSvcFile -> getAttributes($lJobId);
    $this -> mJobImage -> setAttributes(array('src' => $lSrc, 'jobid' => $lJobId, 'width' => $this -> mPvs, 'height' => $this -> mPvs));
    $lImage = $this -> mJobImage -> getImage($lAttributes['preview.path'].$lAttributes['preview.filename']);

    return $this -> td($lImage);
  }

  protected function getTdWebstatus($aAddInfo = '') {
    $lDisplay = CCor_Cfg::get('status.display', 'progressbar');

    $lVal = $this -> getCurInt();
    $lNam = '[unknown]';
    $lRet = '';

    foreach ($this -> mCrp as $lRow) {
      if (($lDisplay == 'progressbar' && $lVal >= $lRow['status']) OR ($lDisplay == 'activeonly' && $lVal == $lRow['status'])) {
        $lRet .= CApp_Crpimage::getCrpIco($this->mSrc, $lRow['display'].'b');
        $lNam = $lRow['name_'.LAN];
      } else if (($lDisplay == 'progressbar' && $lVal < $lRow['status']) OR ($lDisplay == 'activeonly' && $lVal != $lRow['status'])) {
        $lRet .= CApp_Crpimage::getCrpIco($this->mSrc, $lRow['display'].'l');
      }
    }
    if (!empty($aAddInfo)) {
      $lRet.= NB.$aAddInfo;
    }
    $lRet.= NB.htm($lNam);
    return $this -> tda($lRet);
  }

  protected function getTdFlags() {
    $lJobId = $this -> getVal('jobid');
    $lCur = $this -> getCurInt();
    $lFla = CCor_Res::extract('val', 'name_'.LAN, 'jfl');
    $lRet = '';
    foreach ($lFla as $lKey => $lVal) {
      if (bitSet($lCur, $lKey)) {
        $lMsg = '';

        if ($lKey == jfOnhold) {
          $lSql = 'SELECT flag_onhold_reason FROM al_job_shadow_'.MID.' WHERE jobid='.esc($lJobId);
          $lRes = CCor_Qry::getStr($lSql);
          if (!empty($lRes)) {
            $lRes = str_replace("\r\n", "<br>", $lRes);
            $lMsg = ': '.$lRes;
          }
        }

        if ($lKey == jfOnhold) {
          $lSql = 'SELECT flag_cancel_reason FROM al_job_shadow_'.MID.' WHERE jobid='.esc($lJobId);
          $lRes = CCor_Qry::getStr($lSql);
          if (!empty($lRes)) {
            $lRes = str_replace("\r\n", "<br>", $lRes);
            $lMsg = ': '.$lRes;
          }
        }

        $lRet.= img('img/jfl/'.$lKey.'.gif', array('style' => 'margin-right:1px', 'data-toggle' => 'tooltip', 'data-tooltip-head' => lan('lib.flags'), 'data-tooltip-body' => $lMsg));
        $lRet.= '</span>';
      }
    }

    // View CRP Flags
    if ($this -> mShowFlaWithFlags AND isset($this -> mIte[$lJobId]['loop_typ'])) {
      foreach ($this -> mIte[$lJobId]['loop_typ'] as $lTyp => $lLoopId) {
        if (isset($this -> mAllFlags[$lTyp])) {
          $lFlagEve = $this -> mAllFlags[$lTyp];
          $lName = $lFlagEve['name_'.LAN];
          $lAlias = $lFlagEve['alias'];
          if (isset($this -> mCrpFlagsAliase[$lAlias]['ddl_fie'])) {
            $lDdl = $this -> mCrpFlagsAliase[$lAlias]['ddl_fie'];
            $lVal = $this -> getVal($lDdl);
            $lDat = new CCor_Date($lVal);
            $lRetDate = $lDat -> getFmt(lan('lib.date.long'));
            $lRetDate = htm($lRetDate);
          }

          $lRetFlagActions = CApp_Apl_Loop::getFlagCommitList($lLoopId, $lFlagEve);

          $lHeadline = (!empty($lRetDate) ? $lName.': '.$lRetDate : $lName);
          if (isset($this -> mIte[$lJobId][$lAlias]) AND isset($lFlagEve['eve_'.$this -> mIte[$lJobId][$lAlias].'_ico'])) {
            $lImg = $lFlagEve['eve_'.$this -> mIte[$lJobId][$lAlias].'_ico'];
            $lRet.= img('img/flag/'.$lImg.'.gif', array('style' => 'margin-right:1px', 'data-toggle' => 'tooltip', 'data-tooltip-head' => $lHeadline, 'data-tooltip-body' => $lRetFlagActions));
          }
          $lRet.= '</span>';
        }
      }
    }
    return $this -> tdClass($lRet, 'w16');
  }

  protected function getCrpFlagTd($aColKey) {
    $lRet = '';
    $lRetDate = '';
    $lJobId = $this -> getVal('jobid');
    if (!isset($this -> mCrpFlagsAliase[$aColKey])) {
      return $this->tda($lRet);
    }
    $lTyp = $this -> mCrpFlagsAliase[$aColKey]['alias'];

    // View CRP Flags
    if (isset($this -> mIte[$lJobId]['loop_typ'])) {
      if (isset($this -> mIte[$lJobId]['loop_typ'][$lTyp])) {

        if (isset($this -> mCrpFlagsAliase[$aColKey]['ddl_fie'])) {
          $lDdl = $this -> mCrpFlagsAliase[$aColKey]['ddl_fie'];
          $lVal = $this -> getVal($lDdl);
          $lDat = new CCor_Date($lVal);
          $lRetDate = $lDat -> getFmt(lan('lib.date.long'));
          $lRetDate = htm($lRetDate);
        }
        $lLoopId = $this -> mIte[$lJobId]['loop_typ'][$lTyp];
        $lFlagEve = $this -> mAllFlags[$lTyp];
        $lName = $lFlagEve['name_'.LAN];
        $lAlias = $lFlagEve['alias'];

        $lRetFlagActions = CApp_Apl_Loop::getFlagCommitList($lLoopId, $lFlagEve);

        $lHeadline = (!empty($lRetDate) ? $lName.': '.$lRetDate : $lName);
        if (isset($this -> mIte[$lJobId][$lAlias]) AND isset($lFlagEve['eve_'.$this -> mIte[$lJobId][$lAlias].'_ico'])) {
          $lImg = $lFlagEve['eve_'.$this -> mIte[$lJobId][$lAlias].'_ico'];
          $lRet.= img('img/flag/'.$lImg.'.gif', array('style' => 'margin-right:1px', 'data-toggle' => 'tooltip', 'data-tooltip-body' => $lRetFlagActions, 'data-tooltip-head' => $lHeadline));
        }
        $lRet.= '</span>';
      }
    }

    if ($this -> mShowFlagsWithDdl AND !empty($lRet)) {
      $lRet.= '<div class="di vas">'.$lRetDate.'</div>';
    }
    return $this -> tda($lRet);
  }

  protected function getCrpFlagDdlTd($aColKey) {
    $lRet = '';
    $lRetDate = '';
    $lJobId = $this -> getVal('jobid');
    if (!isset($this -> mCrpFlagsDdl[$aColKey])) {
      return $this->tda($lRet);
    }
    $lTyp = $this -> mCrpFlagsDdl[$aColKey]['ddl_fie'];
    $lVal = $this -> getVal($aColKey);
    $lDat = new CCor_Date($lVal);
    $lRetDate = $lDat -> getFmt(lan('lib.date.long'));
    $lRetDate = htm($lRetDate);

    $lRet.= '<div class="di vas">'.$lRetDate.' </div>';

    // View CRP Flags
    if ($this -> mShowDdlWithFlags AND isset($this -> mIte[$lJobId]['loop_typ'])) {
      if (isset($this -> mIte[$lJobId]['loop_typ'][$lTyp])) {
        $lLoopId = $this -> mIte[$lJobId]['loop_typ'][$lTyp];
        $lFlagEve = $this -> mAllFlags[$lTyp];
        $lName = $lFlagEve['name_'.LAN];
        $lAlias = $lFlagEve['alias'];

        $lRetFlagActions = CApp_Apl_Loop::getFlagCommitList($lLoopId, $lFlagEve);

        $lHeadline = (!empty($lRetDate) ? $lName.': '.$lRetDate : $lName);
        if (isset($this -> mIte[$lJobId][$lAlias]) AND isset($lFlagEve['eve_'.$this -> mIte[$lJobId][$lAlias].'_ico'])) {
          $lImg = $lFlagEve['eve_'.$this -> mIte[$lJobId][$lAlias].'_ico'];
          $lRet.= img('img/flag/'.$lImg.'.gif', array('style' => 'margin-right:1px', 'data-toggle' => 'tooltip', 'data-tooltip-body' => $lRetFlagActions, 'data-tooltip-head' => $lHeadline));
        }
        $lRet.= '</span>';
      }
    }

    return $this -> tda($lRet);
  }

  protected function getTdCtr() {
    $lStat = $this -> getVal('status');
    if (($lStat == 'RE') or ($lStat == 'RS') or ($lStat == 'G')) return parent::getTdCtr($aStat = TRUE);
    return parent::getTdCtr();
  }

  protected function loadProjectMembership() {
    if (isset($this->mProjectMembership)) {
      return;
    }
    $lRet = array();
    $this->mProjectMembership = array();
    $lArr = collectVal($this->mIte, 'jobid', 'src');
    if (empty($lArr)) {
      return;
    }
    foreach ($lArr as $lKey=>$lVal) {
      $lArrFlip[$lVal][] = $lKey;
    }
    $lUnion = array();
    foreach ($lArrFlip as $lKey => $lRows) {
      $lSql = '';
      $lSql.= 'SELECT pro_id,jobid_'.$lKey.' AS jid FROM al_job_sub_'.MID.' ';
      $lSql.= 'WHERE jobid_'.$lKey.' IN (';
      $lEsc = array_map('esc', $lRows);
      $lSql.= implode(',', $lEsc);
      $lSql.= ')';
      $lUnion[] = $lSql;
    }
    $lSql = implode(' UNION ', $lUnion);
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lRet[$lRow['jid']] = $lRow['pro_id'];
    }
    $this->mProjectMembership = $lRet;
  }

  protected function getRelatedProject($aJobId) {
    $this->loadProjectMembership();
    if (empty($this->mProjectMembership[$aJobId])) {
      return null;
    }
    return $this->mProjectMembership[$aJobId];
  }

  protected function getTdCpy() {
    $lJid = $this -> getVal('jobid');
    $lSrc = $this -> getVal('src');
    $lMen = new CHtm_Menu(img('img/ico/16/copy.gif'), '', FALSE);
    $lMen->addTh2(lan('lib.copy_to'));
    foreach ($this -> mCopyJob as $lKey) {
      $lMen -> addItem('index.php?act=job-'.$lKey.'.cpy&amp;jobid='.$lJid.'&amp;src='.$lSrc.'&amp;target='.$lKey, lan('lib.copy_to').' '.lan('job-'.$lKey.'.menu'), 'ico/16/'.LAN.'/job-'.$lKey.'.gif');
    }
    $lPro = $this->getRelatedProject($lJid);
    if (!empty($lPro)) {
      $lMen->addTh2(lan('lib.copy_in_project'));
      foreach ($this -> mCopyJob as $lKey) {
        $lMen -> addItem('index.php?act=job-'.$lKey.'.cpy&amp;jobid='.$lJid.'&amp;src='.$lSrc.'&amp;target='.$lKey.'&pid='.$lPro.'&proid='.$lPro, lan('lib.copy_to').' '.lan('job-'.$lKey.'.menu'), 'ico/16/'.LAN.'/job-'.$lKey.'.gif');
      }
    }
    return $this -> td($lMen -> getContent());
  }

  protected function addUserConditions() {
    // wird in der getjoblist aufgerufen
    // bleibt hier als Erinnerung f. Jobtypen, die i.d. PD gespeichert werden
    return TRUE;
  }

  protected function getTdChk() {
    $lSrc = $this -> getVal('src');
    $lJobId = $this -> getVal('jobid');
    $lChkBox = '<input type="checkbox" name="job" data-src="'.$lSrc.'"  data-jobid="'.$lJobId.'" />';
    return $this -> tdc($lChkBox);
  }

  protected function getTdDel() {
    $lJid = $this -> getVal('jobid');
    $lSrc = $this -> getVal('src');
    if (!$this -> mUsr -> canDelete('job-'.$lSrc)) return $this -> td();
    if ($this->isJob()) {
      if (!$this -> mUsr -> canDelete('job-act')) {
        return $this -> td();
      }
    }
    return parent::getTdDel();
  }

  protected function isJob() {
    if ('mop' == $this->mWriter) {
      $lRet = (1 == $this->getVal('is_job'));
    } else {
      $lJid = $this->getVal('jobid');
      $lRet = ('A' != substr($lJid, 0, 1));
    }
    return $lRet;
  }

  protected function getTdApl() {
    $lSta = $this -> getInt('webstatus');
    if (!isset($this->lAplstatus)) {
      return $this->tda();
    }
    if (!in_array($lSta, $this -> lAplstatus)) {
      return $this -> tda();
    }

    $lLoopId = $this -> getInt('loop_id');
    if (empty($lLoopId)) {
      return $this -> tda();
    }
    $lLnk = 'index.php?act=job-apl&src='.$this->mSrc.'&jobid='.$this -> getVal('jobid');
    $lRet = CApp_Apl_Loop::getAplCommitList($lLoopId, $lLnk);

    return $this -> tdClass($lRet, 'w16 ac');
  }

  protected function getTdQLQuestions() {
    $lRet = CJob_Questions_Cnt::getStatus($this->getVal('jobid'));

    return $this->tdClass($lRet, "w16 ac");
  }

  protected function getTdNotfications() {
    $lJobid = $this -> getVal('jobid');
    $lRet = CApp_Notfications::getJobNotfications($lJobid);
    return $this -> tdClass($lRet, 'w16');
  }

  protected function getFilterCombinedStatusWithCheckbox($aArr, $aPrefWebstatus = array(), $aDivId) {
    $lUserPrefWebstatus = $aPrefWebstatus;
    $lDivId = $aDivId;
    $lRet = "";
    $lCrpArr = $aArr;
    $lValues = array();
    $lStateCheckBoxes = array();
    $lAllStates = FALSE;


    if (!empty($lUserPrefWebstatus)) {
      foreach ($lUserPrefWebstatus as $lKey => $lVal) {
        $lValues[$lKey] = $lKey;
      }
    } else {
      $lAllStates = TRUE;
    }
    if (in_array(0,$lValues)) {
      $lAllStates = TRUE;
    }

    $lCrpArr = $aArr;
    $lCount = count($lCrpArr);
    $lStates = "";
    foreach ($aArr as $lWebstatus => $foo) {
      if ($lWebstatus == 0) continue;
      $lStates.= "".$lWebstatus.",";
    }

    $lStates = substr($lStates,0,-1);
    foreach ($lCrpArr as $lWebstatus => $lName){

      $lStateCheckBox = '<input type="checkbox" name="val[webstatus]['.$lWebstatus.']" value="1" ';
      $lStateCheckBox.= 'id="statecheckbox'.$lWebstatus.'" ';
      if (in_array($lWebstatus,$lValues) OR $lAllStates === TRUE) {
        $lStateCheckBox.= 'checked="checked"';
      }
      if ($lWebstatus == 0) {
        // Javascript parameter gIgn=1, that popup ist not closed after click.
        $lStateCheckBox.= ' onclick="javascript:gIgn=1;checkAllStates(\'' .$lStates . '\')"';
      } else {
        $lStateCheckBox.= ' onclick="javascript:gIgn=1;uncheckAllStates(\'statecheckbox'.$lWebstatus.'\', \'' .$lStates . '\')"';
      }
      $lStateCheckBox.= '>';
      $lStateCheckBox.= $lName;
      $lStateCheckBoxes[] = $lStateCheckBox;
    }

    $lRet .= '<div id="'.$aDivId.'" class="smDiv" style="display:none;" >';
    $lRet.= '<div style="float:left;"><table border="0" cellspacing="0" cellpadding="2" class="tbl mw200" >';

    for ($i=0;$i<count($lStateCheckBoxes);$i++) {
      $lRet.= "<tr>";
      $lRet.= '<td class="td1 nw">'.$lStateCheckBoxes[$i].'</td>';
      $lRet.= "</tr>";
    }
    $lRet.= '</table>';
    $lRet.= '</div>';
    $lRet.= $this -> getCombinedFilterFlags($lDivId);
    $lRet.= '</div>';

    return $lRet;
  }

  protected function getCombinedFilterFlags($lDivId){

    $lRet = '<div style="float:left;">';
    $lJfl = array();
    $lSql = 'SELECT val, name_'.LAN.' AS name FROM al_jfl WHERE mand IN (0, '.MID.') ORDER BY val;';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lJfl[intval($lRow['val'])] = $lRow['name'];
    }
    // what job flags are currently set (array)?
    $lFlags = Array();
    if (isset($this -> mFil['flags'])) {
      $lFlags = $this -> mFil['flags']; // User preferences filter flags
    }
    $lRet.= $this -> getFilterCombinedFlagsWithRadio($lJfl, $lFlags, $lDivId);
    $lRet.= '</div>';
    return $lRet;

  }

  protected function getFilterCombinedFlagsWithRadio($aAllJfl, $aCheckedJfl = array(), $aDivId) {

    $lAllJfl = $aAllJfl;
    $lCheckedJfl = $aCheckedJfl;
    $lDivId = $aDivId;
    $lRet = "";

    $lRet = '<div id="'.$aDivId.'">';
    $lRet.= '<table border="0" cellspacing="0" cellpadding="2" class="tbl mw200">';

    $lRet.= '<tr>';
    $lRet.= '<td class="td1 nw"></td>';
    $lRet.= '<td class="td1 nw">'.lan('jfl.set').'</td>';
    $lRet.= '<td class="td1 nw">'.lan('jfl.unset').'</td>';
    $lRet.= '<td class="td1 nw">'.lan('jfl.menu').'</td>';
    $lRet.= '</tr>'.LF;

    foreach ($lAllJfl as $lKey => $lValue) {

      $lPath = getImgPath('img/jfl/'.$lKey.'.gif');
      $lRet.= '<tr>';

      $lCheckValue = isset($lCheckedJfl[$lKey]) ? $lCheckedJfl[$lKey] : 0;
      $lCheckedFlags = $this -> getCheckedFlags($lCheckValue);
      list($lDefault,$lSet,$lUnset)= $lCheckedFlags;

      $lRet.= '<td class="td1 nw">'.$this->getRadio($lKey, 0, $lDefault).'</td>';
      $lRet.= '<td class="td1 nw">'.$this->getRadio($lKey, 1, $lSet).'</td>';
      $lRet.= '<td class="td1 nw">'.$this->getRadio($lKey, 2, $lUnset).'</td>';
      $lRet.= '<td class="td1 nw">'. img($lPath, array('style' => 'margin:1px')).'&nbsp;'.$lValue;
    }
    $lRet.= '</td>';
    $lRet.= '</table>';
    $lRet.= '</div>';

    return $lRet;
  }

  protected function getRadio($aKey, $aVal, $aChecked) {
   $lRet = '<input type="radio" name="val[flags]['.$aKey.']"';
    if ($aChecked) {
      $lRet.= ' checked="checked"';
    }
    $lRet.= ' value="'.$aVal.'" onclick="javascript:gIgn=1" />';
    return $lRet;
  }

  protected function getCheckedFlags($aCheckValue){

    switch ($aCheckValue) {
      case 0:
        $lDefault = TRUE;
        $lSet = FALSE;
        $lUnset = FALSE;
        return array($lDefault,$lSet,$lUnset);

      case 1:
        $lDefault = FALSE;
        $lSet = TRUE;
        $lUnset = FALSE;
        return array($lDefault,$lSet,$lUnset);

      case 2:
        $lDefault = FALSE;
        $lSet = FALSE;
        $lUnset = TRUE;
        return array($lDefault,$lSet,$lUnset);
    }
  }

  /*
   * Master Column
   *
   */
  protected function getTdIs_master() {
    $lRet = '';
    $lIsMaster = $this -> getVal('is_master');
    $lIsVariant = $this -> getVal('master_id');
    if ($lIsMaster){
      $lRet = '<td class="'.$this -> mCls.($this -> mHighlight ? 'r': '').' nw w16 ac">';
      $lRet.= img('img/ico/16/master.gif');
      $lRet.= '</td>'.LF;
      return $lRet;
    }elseif ($lIsVariant != '') {
      $lRet = '<td class="'.$this -> mCls.($this -> mHighlight ? 'r': '').' nw w16 ac">';
      $lRet.= img('img/ico/16/variant.gif');
      $lRet.= '</td>'.LF;
      return $lRet;
    }
    return $this -> td();
  }

  protected function setCsvExportButton() {
    if (CCor_Cfg::get('csv-exp.bymail', TRUE)) {
      $lResCsv = 'new Ajax.Request("index.php?act='.$this -> mMod.'.csvexp&src='.$this -> mSrc.'&age=job",
        {
          onCreate: function(response) {
            $("pag_ajx").src = "img/pag/ajx2.gif";
          },
          onComplete: function(response) {
            $("pag_ajx").src = "img/d.gif";
            alert("'.lan('csv-exp.oncomplete').'");
          }
        }
      );';
      $lResXls = 'new Ajax.Request("index.php?act='.$this -> mMod.'.xlsexp&src='.$this -> mSrc.'&age=job",
        {
          onCreate: function(response) {
            $("pag_ajx").src = "img/pag/ajx2.gif";
          },
          onComplete: function(response) {
            $("pag_ajx").src = "img/d.gif";
            alert("'.lan('csv-exp.oncomplete').'");
          }
        }
      );';
    } else {
      $lResCsv = 'go("index.php?act='.$this -> mMod.'.csvexp&src='.$this -> mSrc.'&age=job")';
      $lResXls = 'go("index.php?act='.$this -> mMod.'.xlsexp&src='.$this -> mSrc.'&age=job")';
    }

    $this -> addBtn(lan('csv-exp'), $lResCsv, '<i class="ico-w16 ico-w16-excel"></i>', TRUE);
    if (CCor_Cfg::get('phpexcel.available', FALSE)) {
      $this -> addBtn(lan('xls-exp'), $lResXls, '<i class="ico-w16 ico-w16-excel"></i>', TRUE);
    }
  }

  protected function setRepExportButton() {
    if (CCor_Cfg::get('rep-exp.bymail', TRUE)) {
      $lResRep = 'new Ajax.Request("index.php?act='.$this -> mMod.'.repexp&src='.$this -> mSrc.'&age=job",
        {
          onCreate: function(response) {
            $("pag_ajx").src = "img/pag/ajx2.gif";
          },
          onComplete: function(response) {
            $("pag_ajx").src = "img/d.gif";
            alert("'.lan('rep-exp.oncomplete').'");
          }
        }
      );';
    } else {
      $lResRep = 'go("index.php?act='.$this -> mMod.'.repexp&src='.$this -> mSrc.'&age=job")';
    }

    $this -> addBtn(lan('rep-exp'), $lResRep, '<i class="ico-w16 ico-w16-excel"></i>', TRUE);
  }

  protected function setMultipleEditButton() {
    $this -> addBtn(lan('job.multiple-edit'), 'Flow.multiplejobs.run();', 'img/ico/16/multiple-edit.png', TRUE);
  }

  protected function getColumns() {
    $lRet = Array();
    $lUsrPref = $this -> mUsr -> getPref($this -> mMod.'.cols');
    if (empty($lUsrPref)) {
      $lSql = 'SELECT val FROM al_sys_pref WHERE code = "'.$this -> mMod.'.cols'.'" AND mand='.MID;
      $lUsrPref = CCor_Qry::getArrImp($lSql);
    }
    $lRet = explode(',', $lUsrPref);
    return $lRet;
  }

  /**
  * Get Extended WebStatus Filter
  * @param array $aArr Webstaus without Archive Webstatus
  * @param array $aPrefWebstatus current user webstatus pref
  * @param int   $aDivId Divid of Popup-menue
  * @return table with webstatus checkboxes
  */
  protected function getFilterWebstatusWithCheckbox($aArr, $aPrefWebstatus = array(), $aDivId) {
    $lUserPrefWebstatus = $aPrefWebstatus;
    $lDivId = $aDivId;
    $lRet = "";
    $lCrpArr = $aArr;
    $lValues = array();
    $lStateCheckBoxes = array();
    $lAllStates = FALSE;


    if (!empty($lUserPrefWebstatus)) {
     foreach ($lUserPrefWebstatus as $lKey => $lVal) {
        $lValues[$lKey] = $lKey;
      }
    } else {
      $lAllStates = TRUE;
    }
    if (in_array(0,$lValues)) {
      $lAllStates = TRUE;
    }


    $lCrpArr = $aArr;
    $lCount = count($lCrpArr);
    $lStates = "";
    foreach ($aArr as $lWebstatus => $foo) {
      if ($lWebstatus == 0) continue;
      $lStates.= "".$lWebstatus.",";
    }

    $lStates = substr($lStates,0,-1);
    foreach ($lCrpArr as $lWebstatus => $lName){

      $lStateCheckBox = '<input type="checkbox" name="val[webstatus]['.$lWebstatus.']" value="1" ';
      $lStateCheckBox.= 'id="statecheckbox'.$lWebstatus.'" ';
      if (in_array($lWebstatus,$lValues) OR $lAllStates === TRUE) {
        $lStateCheckBox.= 'checked="checked"';
      }
      if ($lWebstatus == 0) {
        // Javascript parameter gIgn=1, that popup ist not closed after click.
        $lStateCheckBox.= ' onclick="javascript:gIgn=1;checkAllStates(\'' .$lStates . '\')"';
      } else {
        $lStateCheckBox.= ' onclick="javascript:gIgn=1;uncheckAllStates(\'statecheckbox'.$lWebstatus.'\', \'' .$lStates . '\')"';
      }
      $lStateCheckBox.= '>';
      $lStateCheckBox.= $lName;
      $lStateCheckBoxes[] = $lStateCheckBox;
    }

    $lRet = '<div id="'.$aDivId.'" class="smDiv" style="display:none">';
    $lRet.= '<table border="0" cellspacing="0" cellpadding="2" class="tbl mw200">';

    for ($i=0;$i<count($lStateCheckBoxes);$i++) {
      $lRet.= "<tr>";
      $lRet.= '<td class="td1 nw">'.$lStateCheckBoxes[$i].'</td>';
      $lRet.= "</tr>";
    }
    $lRet.= '</table>';
    $lRet.= '</div>';

    return $lRet;
  }

  /*
   * Check Jobfield 'is_master' defined
   * @param boolean $lRet
   */
  protected function isFieldIsMasterDefined(){
    $lRet = FALSE;
    if(array_key_exists('is_master', $this -> mCols)){
       $lRet = TRUE;
    }
    return $lRet;
  }

  /*
   * Sort Joblist in Master-Varinat Bundle form.
   *@param Array $aIte Joblist
   *@return Array $lRet	Joblist
   */
  protected function sortByMasterVariant($aIte){
   $lRet = array();
   $lIte = $aIte;

   // Get all master_id from variant Jobs
   $lArrVariantIds = Array();
   foreach ($lIte as $lKey => $lVal){
     if (isset($lVal['master_id'])){
       if ($lVal['master_id'] != ''){
         $lArrVariantIds[$lKey] = $lVal['master_id'];
       }
     }
   }

   if (empty($lArrVariantIds)){
     return $lIte;
   }else{
     $lSql = 'SELECT id,pro_id,jobid_rep,jobid_art,jobid_sec,jobid_mis,jobid_adm,is_master,master_id  FROM al_job_sub_'.MID;
     $lSql.= ' WHERE id IN (';
     foreach ($lArrVariantIds as $lKey=>$lVal) {
       $lSql.= esc($lVal).',';
     }
     $lSql = strip($lSql).')';
     $lQry = new CCor_Qry($lSql);
     $lArrMaster = $lQry->getAssocs('id');

     // Replace Project Item Id with JobId in jobfield 'is_master'
     foreach ($lArrVariantIds as $lKey => $lVal){
       if (isset($lVal, $lArrMaster)){
         if ($lArrMaster[$lVal]['is_master'] == 'X'){
           $lMasterJobId = $lArrMaster[$lVal]['jobid_'.$lIte[$lKey]['src']];
           if ($lMasterJobId != ''){
             $lIte[$lKey]['master_id'] = $lArrMaster[$lVal]['jobid_'.$lIte[$lKey]['src']];
           }
         }
       }
     }
     // Sort Joblist by Master-Variant Bundle
     //If there is a Master Job and his Variants, show first master-job and after the variant-job
     foreach ($lIte as $lKey => $lVal){
       if ($lVal['is_master'] == 'X'){
         $lRet[$lKey] = $lIte[$lKey];
         foreach ($lIte as $lKey2 => $lVal2){
           if ($lVal2['master_id'] == $lVal['jobid'] ){
             if (isset($lRet[$lKey2])){
               unset($lRet[$lKey2]);
             }
             $lRet[$lKey2] = $lIte[$lKey2];
             unset ($lIte[$lKey2]);
           }
         }
       }else {
         if (!empty($lIte[$lKey])){
           $lRet[$lKey] = $lIte[$lKey];
         }
       }
     }
   }
   return $lRet;
  }

  protected function getTdPdf_available() {
    $lSrc = $this -> getVal('src');
    $lJID = $this -> getVal('jobid');

    $lLnk = CCor_Cfg::get('job.writer.default', 'alink');
    if ('alink' == $lLnk) {
      $lCount = $this -> getCurInt();
    } elseif ('mop' == $lLnk) {
      $lSQL = 'SELECT count(id) FROM al_job_files WHERE mand='.esc(MID).' AND src='.esc($lSrc).' AND jobid='.esc($lJID).' AND sub='.esc('pdf').';';
      $lCount = CCor_Qry::getInt($lSQL);
    }

    $lURL = 'index.php?act=arc-'.$lSrc.'-fil&jobid='.$lJID;

    $lRet = '<a href="'.htm($lURL).'">';
    if ($lCount < 1) {
      $lRet.= NB;
    } else {
      $lRet.= img('img/ico/16/pdf.png');
    }
    $lRet.= '</a>';

    return $this -> tdClass($lRet, 'w16 ac', TRUE);
  }

  protected function getMultipleEdit() {
    $lFFL = CCor_Cfg::get('job.multiple-edit', 4096);

    $lJobFields = CCor_Res::extract('id', 'alias', 'fie', array('flags' => $lFFL));
    $lJobFieldsJSONEnc = json_encode($lJobFields);

    $lJobFieldsCSV = CCor_Qry::getStr("SELECT val FROM al_sys_pref WHERE code='job.multiple-edit.ord' AND mand=".MID.";");
    if (!empty($lJobFieldsCSV)) {
      $lJobFieldsArr = explode(',', $lJobFieldsCSV);
      $lJobFieldsArr = array_flip($lJobFieldsArr);
      foreach ($lJobFields as $lKey => $lValue) {
        $lJobFieldsArr[$lKey] = $lValue;
      }
      $lJobFields = $lJobFieldsArr;
    }

    $this -> mFac = new CHtm_Fie_Fac();

    $lRet = '<div class="dn" id="multipleedit" title="Edit multiple jobs">';
    $lRet.= '    <input type="hidden" name="mid" value="'.MID.'"/>';
    $lRet.= '    <input type="hidden" name="src" value="'.$this -> mSrc.'"/>';
    $lRet.= '    <input type="hidden" name="act" value="job-'.$this -> mSrc.'.sedts" />';
    $lRet.= '    <input type="hidden" name="fields" value="'.htmlspecialchars($lJobFieldsJSONEnc).'"/>';
    $lRet.= '    <input type="hidden" name="jobids"/>';
    $lRet.= '    <input type="hidden" name="values"/>';
    $lRet.= '    <table style="width: 100%">';

    foreach ($lJobFields as $lKey => $lValue) {
      $lJobField = $this -> mFie[$lKey];
      $lName = $lJobField['name_'.LAN];
      $lDescription = $lJobField['desc_'.LAN];
      $lAvailability = $lJobField['avail'];

      if (bitSet($lAvailability, $this -> mAva)) {
        $lRet.= '        <tr>';
        $lRet.= '            <td class="p4" style="width: 16px;">';
        $lRet.= $lName;
        $lRet.= '            </td>';
        $lRet.= '            <td class="p4">';
        $lRet.= $this -> mFac -> getInput($lJobField);
        $lRet.= '            </td>';
        $lRet.= '            <td class="p4" style="width: 16px;">';

        if (!empty($lDescription)) {
          $lToolTipDescription = preg_replace("/[\n\r\'|&#0*39;]/", " ", $lDescription);
          $lToolTipIcon = img('img/jfl/1024.gif', array('class' => 'info_button', 'alt' => 'Info', 'data-toggle' => 'tooltip', 'data-tooltip-head' => lan('lib.info'),  'data-tooltip-body' => $lToolTipDescription));
          $lRet.= $lToolTipIcon;
        }

        $lRet.= '            </td>';
        $lRet.= '        </tr>';
      }
    }

    $lRet.= '    </table>';
    $lRet.= '    <br>';
    $lRet.= '    <div style="color:red;">'.lan('multiple.jobs.edit.l01').'</div>'.lan('multiple.jobs.edit.l02');
    $lRet.= '    <br>';
    $lRet.= '</div>';

    $lRet.= '<div class="dn" id="multipleedit_progress">';
    $lRet.= '    <p>';
    $lRet.= '        <div style="text-align: center; vertical-align: middle;">';
    $lRet.= lan('job.multiple-edit.wait');
    $lRet.= '        </div>';
    $lRet.= '    </p>';
    $lRet.= '    <p>';
    $lRet.= '        <div id="multipleedit_progress_value">';
    $lRet.= '            <div id="multipleedit_progress_text">';
    $lRet.= '            </div>';
    $lRet.= '        </div>';
    $lRet.= '    </p>';
    $lRet.= '    <p>';
    $lRet.= '        <div id="multipleedit_messages_value">';
    $lRet.= '            <div id="multipleedit_messages_text" style="height: 90px; text-align: left; vertical-align: top; overflow: scroll;">';
    $lRet.= '            </div>';
    $lRet.= '        </div>';
    $lRet.= '    </p>';
    $lRet.= '</div>';

    return $lRet;
  }

  protected function getCont() {
    $lRet = parent::getCont();
    $lRet.= $this -> getMultipleEdit();
    return $lRet;
  }

  protected function getTrTag() {
    $lSrc = $this -> getVal('src');
    $lJID = $this -> getVal('jobid');

    return '<tr class="hi" data-src="'.$lSrc.'" data-jobid="'.$lJID.'">';
  }
}