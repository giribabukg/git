<?php
/**
 * Archiv: Liste
 *
 * @package    ARC
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 12356 $
 * @date $Date: 2016-02-01 16:34:03 +0800 (Mon, 01 Feb 2016) $
 * @author $Author: gemmans $
 */
class CInc_Arc_List extends CHtm_List {
  protected $mShowCsvExportButton = FALSE;

  public function __construct($aSrc) {
    parent::__construct('arc-'.$aSrc);
    $this -> mSrc = $aSrc;
    $this -> mUsr = CCor_Usr::getInstance();
    $this -> mMod = 'arc-'.$aSrc;
    $this -> mStdLnk = 'index.php?act='.$this -> mMod.'.edt&amp;jobid=';
    $this -> setAtt('width', '100%');
    $this -> mTitle = lan($this -> mMod.'.menu');
    $this -> mImg = 'img/ico/40/job-list.gif';
    $this -> mFie = CCor_Res::get('fie');
    $this -> mIdField = 'jobid';
	$this -> mCapCls = (THEME === 'default' ? 'cap': 'cap2');


    $this -> mDefs = CCor_Res::getByKey('alias', 'fie');
    $this -> mCndMaster = CCor_Res::extract('id', 'aliased', 'cndmaster');

    $this -> mCnd = new CCor_Cond();

    $this -> getPrefs('arc-'.$this -> mSrc);

    $this -> getIterator();
    $this -> addFilterConditions();
    $this -> addSearchConditions();

    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);
    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);

    $this -> mJobFilFie = array();

    // INSERT CSV EXPORT BUTTON
    if ($this -> mShowCsvExportButton) {
      if ($this-> mUsr -> canRead('csv-exp')) {
        $this -> setCsvExportButton();
      }
    }

    // INSERT REPORTING EXPORT BUTTON
    if ($this-> mUsr -> canRead('rep-exp') && CCor_Cfg::get('extended.reporting', false)) {
    	$this -> setRepExportButton();
    }

    $lUsr = CCor_Usr::getInstance();
    $this -> mHideFil = ($lUsr -> getPref($aSrc.'.hidefil') == 1);
    $this -> mHideSer = ($lUsr -> getPref($aSrc.'.hideser') == 1);
  }

  protected function addCopy() {
    $lUsr = CCor_Usr::getInstance();
    $lFla = FALSE;
    $this -> mSrcArr = CCor_Cfg::get('all-jobs'); // array('art', 'rep', 'sec', 'mis', 'adm', 'com', 'tra');
    foreach ($this -> mSrcArr as $lKey) {
      $lFla = TRUE;
      $this -> mCanIns[$lKey] = $lUsr -> canInsert('job-'.$lKey);
    }
    if ($lFla) {
      $this -> addColumn('cpy', '', FALSE, array('width' => 16));
    }
  }

  protected function addCondition($aAlias, $aOp, $aValue) {
    if (!isset($this -> mDefs[$aAlias])) {
      $this -> dbg('Unknown Field '.$aAlias, mlWarn);
      return;
    }
    $this -> mIte -> addCondition($aAlias, $aOp, $aValue);
  }

  public function addCndEx($aCnd) {
    $this -> mIte -> addCndEx($aCnd);
  }

  protected function addFilter($aAlias, $aCaption, $aOpt = NULL) {
    $lRet = array();
    $lRet['cap'] = $aCaption;
    $lRet['opt'] = $aOpt;
    $this -> mJobFilFie[$aAlias] = $lRet;
  }

  protected function getIterator() {
  }

  protected function addFilterConditions() {
    if (empty($this -> mFil)) return;
    foreach ($this -> mFil as $lAli => $lVal) {
      if (empty($lVal)) continue;
      $this -> addCondition($lAli, '=', $lVal);
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

  protected function addColumns() {
    $lUsr = CCor_Usr::getInstance();
    $lUsrPref = $this -> mUsr -> getPref($this -> mMod.'.cols');
    if (empty($lUsrPref)) {
      $lSql = 'SELECT val FROM al_sys_pref WHERE code = "'.$this -> mMod.'.cols'.'" AND mand='.MID;
      $lUsrPref = CCor_Qry::getArrImp($lSql);
    }
    $lCol = explode(',', $lUsrPref);

    foreach ($lCol as $lFid) {
      if (isset($this -> mFie[$lFid])) {
        $lDef = $this -> mFie[$lFid];
        $this -> addField($lDef);
        $this -> onAddField($lDef);
      }
    }
  }

  protected function onAddField($aDef) {
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

  protected function getTitleContent() {
    $lRet = '<table cellpadding="0" cellspacing="0" border="0"><tr>'.LF;
    if(THEME === 'default')
		$lRet.= '<td>'.img($this -> mImg, array('style' => 'float:left')).'</td>';

    if(strpos($_GET['act'], 'job') !== FALSE || strpos($_GET['act'], 'arc') !== FALSE){
      $lSrc = substr($this -> mMod, 4, 3);
      $lCls = CApp_Crpimage::getColourForSrc($lSrc);
    } else {
      $lCls = '';
    }

    $lRet.= '<td class="captxt '.$lCls.'">'.htm($this -> mTitle).'</td>';
    $lRet.= '</tr></table>'.LF;
    return $lRet;
  }

  protected function & getViewMenuObject() {
    $lUsr = CCor_Usr::getInstance();

    $lMen = new CHtm_Menu(lan('lib.opt'));

    $lMen -> addTh2(lan('lib.opt.view'));
    $lMen -> addItem('index.php?act='.$this -> mMod.'.fpr', lan('lib.opt.fpr'), '<i class="ico-w16 ico-w16-col"></i>');
    $lMen -> addItem('index.php?act='.$this -> mMod.'.spr', lan('lib.opt.spr'), '<i class="ico-w16 ico-w16-search"></i>');

    $lOk = '<i class="ico-w16 ico-w16-ok"></i>';

    #$lImg = ($this -> mHideFil) ?  'd.gif' : $lOk;
    #$lMen -> addItem('index.php?act='.$this -> mMod.'.togfil', 'Show filter bar', $lImg);
    #$lImg = ($this -> mHideSer) ?  'd.gif' : $lOk;
    #$lMen -> addItem('index.php?act='.$this -> mMod.'.togser', 'Show search bar', $lImg);

    $lMen -> addTh2(lan('lib.opt.lpp'));
    $lArr = array(25, 50, 100, 200);
    foreach ($lArr as $lLpp) {
      $lImg = ($lLpp == $this -> mLpp) ? $lOk : 'd.gif';
      $lMen -> addItem($this -> mLppLnk.$lLpp, $lLpp.' '.lan('lib.opt.lines'), $lImg);
    }

    $lMen -> addTh2(lan('lib.opt.savedviews'));

    $lSql = 'SELECT id,name FROM al_usr_view WHERE 1 ';
    $lSql.= 'AND src="usr" AND src_id =0 AND ref="'.$this -> mMod.'" AND mand='.MID.' ';
    $lSql.= 'ORDER BY name';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lMen -> addItem('index.php?act='.$this -> mMod.'.selview&amp;id='.$lRow['id'], '[Global] '.$lRow['name'], '<i class="ico-w16 ico-w16-global"></i>');
    }

    $lSql = 'SELECT id,name FROM al_usr_view WHERE ref="'.$this -> mMod.'" ';
    $lSql.= 'AND src="usr" AND src_id='.$lUsr -> getId().' AND mand='.MID.' ';
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
    $lSql = 'SELECT id,name FROM al_usr_search WHERE ref="'.$this -> mMod.'" ';
    $lSql.= 'AND src="usr" AND src_id='.$lUsr -> getId().' ';
    $lSql.= 'AND mand="'.MID.'" ';
    $lSql.= 'ORDER BY name';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lMen -> addItem('index.php?act='.$this -> mMod.'.selsearch&amp;id='.$lRow['id'], $lRow['name'], '<i class="ico-w16 ico-w16-search"></i>');
    }
    $lMen -> addItem('index.php?act=job-view-search&amp;src='.$this -> mMod, lan('lib.search.save'));
    /*
    if ($lUsr -> isMemberOf(1)) {
      $lMen -> addItem('index.php?act=mba.sview', 'Save as Standard', 'ico/16/save-std.gif');
    }
    */
    return $lMen;
  }

  protected function getSearchForm() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="'.$this -> mMod.'.ser" />'.LF;

    $lRet.= '<table cellpadding="2" cellspacing="0" border="0"><tr>'.LF;
    $lRet.= '<td class="caption w50">'.htm(lan('lib.search')).'</td>';
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
        // Bei abhaengigen Auuftragsfeldern wird standard Wert mit Variable 'NoChoice' definiert
        // was aber in der Suche nicht noetig ist.
        // JobId # 23588
        if (isset($lDef['NoChoice']) && !empty($lDef['NoChoice'])){
          unset($lDef['NoChoice']);
        }
        $lNam = $lDef['name_'.LAN];
        $lAli = $lDef['alias'];
        if (in_array($lAli, $lIdx)) {
          $lNam = substr($lNam, 0, -1);
        }
        $lRet.= '<td>'.htm($lNam).'</td>'.LF;

        $lVal = (isset($this -> mSer[$lAli])) ? $this -> mSer[$lAli] : '';
        $lRet.= '<td>';
        $lRet.= $lFac -> getInput($lDef, $lVal, fsSearch);
        $lRet.= '</td>';

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
    $lRet.= '<td class="caption w50 p0">'.htm(lan('lib.filter')).'</td>';
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
      $lRet.= '<td valign="top">'.btn(lan('lib.show_all'),'go("index.php?act='.$this -> mMod.'.clfil")','<i class="ico-w16 ico-w16-cancel"></i>').'</td>';
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

  protected function getFilterWebstatus($aVal, $aOpt) {
    $lCrp = CCor_Res::get('crp', $aOpt);
    $lArr = array(0 => '[all]');
    foreach ($lCrp as $lRow) {
      $lArr[intval($lRow['status'])] = $lRow['display'].' - '.$lRow['name_'.LAN];
    }
    $lRet = getSelect('val[webstatus]', $lArr, $aVal);
    return $lRet;
  }

  protected function getFilterPer_prj_verantwortlich($aVal, $aOpt) {
    $lUsr = CCor_Res::extract('id', 'fullname', 'usr', $aOpt);
    $lArr = array(0 => '['.lan('lib.all').']');
    foreach ($lUsr as $lUid => $lNam) {
      $lArr[intval($lUid)] = $lNam;
    }

    $lRet = getSelect('val[per_prj_verantwortlich]', $lArr, $aVal);
    return $lRet;
  }

  protected function getFilterAuftragsart($aVal, $aOpt) {
    #$lArr = array(0 => '[all]');
    $lArr = CCor_Res::get('htb', 'jot');
    $lArr = array_merge(array(0 => '[all]'), $lArr);
    $lRet = getSelect('val[auftragsart]', $lArr, $aVal);
    return $lRet;
  }

  protected function getFilterProduktgruppe($aVal, $aOpt) {
    #$lArr = array(0 => '[all]');
    $lArr = CCor_Res::get('htb', 'prg');
    $lArr = array_merge(array(0 => '[all]'), $lArr);
    $lRet = getSelect('val[produktgruppe]', $lArr, $aVal);
    return $lRet;
  }

  protected function getFilterbyAlias($aAlias = 'per_prj_verantwortlich') {
    $lGruArr = CCor_Res::extract('alias', 'param', 'fie');
    if (isset($lGruArr[$aAlias])) {
      $lGruArr = unserialize($lGruArr[$aAlias]);
      $lDef = $this -> mDefs[$aAlias];
      if (isset($lGruArr['gru'])) {
        $this -> addFilter($aAlias, $lDef['name_'.LAN], array('gru' => $lGruArr['gru']));
      } else {
        $this -> addFilter($aAlias, $lDef['name_'.LAN]);
      }
    }
  }

  protected function getTdJobnr() {
    $lJid = $this -> getVal('jobid');
    return $this -> tda(jid($lJid));
  }

  protected function getTdWec_pi100() {
    return $this -> getTdWec_pi();
  }

  protected function getTdWec_pi300() {
    return $this -> getTdWec_pi();
  }

  protected function getTdWec_pi() {
    $lJobId = $this -> getVal('jobid');

    $lSvcWecInst = CSvc_Wec::getInstance();
    $lStatics = $lSvcWecInst -> getStatics();
    $lDynamics = $lSvcWecInst -> getDynamics($lJobId);

    if (file_exists($lDynamics['thumbnail_dir'].$lDynamics['thumbnail_file'])) {
      $lRet = $this -> printThumbnail($lDynamics['thumbnail_dir'].$lDynamics['thumbnail_file'], $lJobId);
    } else {
      $lRet = $this -> printThumbnail($lStatics['thumbnail_not_found'], $lJobId);
    }

    return $this -> tdClass($lRet, 'w100 ac');
  }

  protected function printThumbnail($aImgPath, $aJobId) {
    $lParams = array(
      'src' => $this -> mSrc,
      'jobid' => $aJobId
    );
    $lParamsJSONEnc = json_encode($lParams);
    $lParamsHTMLSpecChar = htmlspecialchars($lParamsJSONEnc);

    $lImgLarge = "<img src='".$aImgPath."' border='0px' width='300px' height='300px' alt='' />";

    $lRet = '<a id="a'.$aJobId.'" class="a'.$aJobId.'" data-toggle="tooltip" data-tooltip-head="" data-tooltip-body="'.$lImgLarge.'" href="javascript:void(0);" onclick="Flow.thumbnail.update('.$lParamsHTMLSpecChar.');">';
    $lRet.= '<img src="'.$aImgPath.'" id="img'.$aJobId.'" border="0px" width="100px" height="100px" />';
    $lRet.= '</a>';

    return $lRet;
  }

  protected function getTdWebstatus() {
    return $this -> tda('Archive');
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

        $lRet.= img('img/jfl/'.$lKey.'.gif', array('style' => 'margin-right:1px', 'data-toggle' => 'tooltip', 'data-tooltip-head' => $lVal, 'data-tooltip-body' => $lMsg));
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

  protected function getTdCpy() {
    $lJid = $this -> getVal('jobid');
    $lMen = new CHtm_Menu(img('img/ico/16/copy.gif'), '', false);
    foreach ($this -> mSrcArr as $lKey) {
      if (!$this -> mCanIns[$lKey]) continue;
      $lMen -> addItem('index.php?act=job-'.$lKey.'.cpy&amp;jobid='.$lJid.'&amp;src='.$lKey.'&amp;target='.$lKey.'&amp;arc=', 'Copy to '.lan('job-'.$lKey.'.menu', 'ico/16/job-'.$lKey.'.gif'));
    }
    return $this -> td($lMen -> getContent());
  }

  protected function setCsvExportButton() {
    if (CCor_Cfg::get('csv-exp.bymail', true)) {
      $lResCsv = 'new Ajax.Request("index.php?act='.$this -> mMod.'.csvexp&src='.$this -> mSrc.'&age=arc",
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
      $lResXls = 'new Ajax.Request("index.php?act='.$this -> mMod.'.xlsexp&src='.$this -> mSrc.'&age=arc",
        {
          onCreate: function(response) {
            $("pag_ajx").src = "img/pag/ajx2.gif";
          },
          onComplete: function(response) {
            $("pag_ajx").src = "img/d.gif";
            alert("'.lan('xls-exp.oncomplete').'");
          }
        }
      );';
    } else {
      $lResCsv = 'go("index.php?act='.$this -> mMod.'.csvexp&src='.$this -> mSrc.'&age=arc")';
      $lResXls = 'go("index.php?act='.$this -> mMod.'.xlsexp&src='.$this -> mSrc.'&age=arc")';
    }

    $this -> addBtn(lan('csv-exp'), $lResCsv, '<i class="ico-w16 ico-w16-excel"></i>', true);
    if (CCor_Cfg::get('phpexcel.available', false)) {
      $this -> addBtn(lan('xls-exp'), $lResXls, '<i class="ico-w16 ico-w16-excel"></i>', true);
    }
  }

  protected function setRepExportButton() {
    if (CCor_Cfg::get('rep-exp.bymail', true)) {
      $lResRep = 'new Ajax.Request("index.php?act='.$this -> mMod.'.repexp&src='.$this -> mSrc.'&age=arc",
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
      $lResRep = 'go("index.php?act='.$this -> mMod.'.repexp&src='.$this -> mSrc.'&age=arc")';
    }

    $this -> addBtn(lan('rep-exp'), $lResRep, '<i class="ico-w16 ico-w16-excel"></i>', true);
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
      $lRet.= img('<i class="ico-w16 ico-w16-master"></i>');
      $lRet.= '</td>'.LF;
      return $lRet;
    }elseif ($lIsVariant != '') {
      $lRet = '<td class="'.$this -> mCls.($this -> mHighlight ? 'r': '').' nw w16 ac">';
      $lRet.= img('<i class="ico-w16 ico-w16-variant"></i>');
      $lRet.= '</td>'.LF;
      return $lRet;
    }
    return $this -> td();
  }

  protected function getTdPdf_available() {
    $lCount = $this -> getCurInt();

    $lSrc = $this -> getVal('src');
    $lJID = $this -> getVal('jobid');

    $lLnk = 'index.php?act=arc-'.$lSrc.'-fil&jobid='.$lJID;

    $lRet = '<a href="'.htm($lLnk).'">';
    if (empty($lCount)) {
      $lRet.= NB;
    } else {
      $lRet.= img('<i class="ico-w16 ico-w16-pdf"></i>');
    }
    $lRet.= '</a>';

    return $this -> tdClass($lRet, 'w16 ac', TRUE);
  }
}