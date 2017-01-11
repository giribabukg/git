<?php
class CInc_Job_Sku_Sub_Job_List extends CJob_List {

  public function __construct($aJobId) {
    $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');
    $this -> mCrpId = $lCrp['art'];
    $this -> mJobId = $aJobId;
    parent::__construct('job-sku-sub', $this -> mCrpId);

    $this -> mStdLnk = 'index.php?act='.$this -> mMod.'.edt&amp;jobid='.$this -> mJobId.'&amp;id=';
    $this -> mOrdLnk = 'index.php?act='.$this -> mMod.'.ord&amp;jobid='.$this -> mJobId.'&amp;fie=';
    $this -> mDelLnk = 'index.php?act='.$this -> mMod.'.del&amp;jobid='.$this -> mJobId.'&amp;id=';

    $this -> mImg = 'img/ico/40/'.LAN.'/job-sku.gif';

//    $this -> mFie = CCor_Res::getByKey('id', 'fie');
//    $this -> mDefs = CCor_Res::getByKey('alias', 'fie');
//    $this -> mCnd = new CCor_Cond();
//    $lUsr = CCor_Usr::getInstance();
//
//    $this -> mCanInsArt = $lUsr -> canInsert('job-art');
//    $this -> mCanInsRep = $lUsr -> canInsert('job-rep');
//    $this -> mCanInsSec = $lUsr -> canInsert('job-sec');
//    $this -> mCanInsMis = $lUsr -> canInsert('job-mis');
//    $this -> mCanInsAdm = $lUsr -> canInsert('job-adm');
//    $this -> mCanInsCom = $lUsr -> canInsert('job-com');
//    $this -> mCanInsTra = $lUsr -> canInsert('job-tra');
//    $this -> addCtr();
//
//    $this -> addColumn('src', '', true, array('width' => 16));
//    #$this -> addColumn('is_master', '', false, array('width' => '16'));
//    $this -> addColumns();
//
//    if ($this -> mCanInsert) {
//      $this -> addColumn('cpy', '', false, array('width' => '16'));
//    }
//    $this -> addColumn('assign', '', false, array('width' => '16'));
//    if ($this -> mCanDelete) {
//      $this -> addDel();
//    }

    $this -> getPrefs();

//    $this -> mAli = CCor_Res::getByKey('alias', 'fie');
//
//    $this -> mPlain = new CHtm_Fie_Plain();

    $this -> getIterator();
    #$this -> addGlobalSearchConditions();
    $this -> addFilterConditions();
    $this -> addSearchConditions();
    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);
    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);

//    $this -> mJobFilFie = array();
//    $lUsr = CCor_Usr::getInstance();
//    $this -> mHideFil = ($lUsr -> getPref($this -> mMod.'.hidefil') == 1);
//    $this -> mHideSer = ($lUsr -> getPref($this -> mMod.'.hideser') == 1);
//
//    $this -> mMaxLines = $this -> mIte -> getCount();
//
    $this -> addButton(lan('job.new'), $this -> getMenu());
    $this -> addPanel('nav', $this -> getNavBar());
    $this -> addPanel('vie', $this -> getViewMenu());

    $this -> addFilter('webstatus', 'Status', $this -> mCrpId);
    $this -> getCriticalPaths();

    #$this -> lAplstatus = CCor_Qry::getInt('SELECT status FROM al_crp_status WHERE mand='.MID.' AND crp_id='.$this -> mCrpId.' AND apl=1');
  }

  protected function getIterator() {
    // return the job IDs that are assigned to this sku
    $lQry = new CCor_Qry('SELECT job_id FROM al_job_sku_sub_'.intval(MID).' WHERE sku_id='.esc($this -> mJobId));
    $lJobIDs = array();
    foreach ($lQry as $lKey) {
      array_push($lJobIDs, $lKey['job_id']);
    }
    $lJobIDs = array_map("esc", $lJobIDs);

    // return the column names
    $lUsr = CCor_Usr::getInstance();
    $lCol = $lUsr -> getPref($this -> mMod.'.cols');

    $lQry = new CCor_Qry('SELECT alias FROM al_fie WHERE id IN ('.$lCol.')');
    $lColNames = array();
    foreach ($lQry as $lKey) {
      array_push($lColNames, $lKey['alias']);
    }

    // return the needed job fields only
    $lDefFie = CCor_Res::extract('alias', 'native', 'fie');
    $this -> mIte = new CApi_Alink_Query_Getjoblist();
    foreach ($lColNames as $lFie) {
      if (!empty($lDefFie[$lFie])) {
        $this -> mIte -> addField($lFie, $lDefFie[$lFie]);
      }
    }
    $this -> mIte -> addField('src', $lDefFie['src']);
    $this -> mIte -> addField('webstatus', $lDefFie['webstatus']);
    $this -> mIte -> addCondition('jobid', 'in', implode(',', $lJobIDs));
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
             }else {
              $lStates.= '"'.$lWebstatus.'",';
             }
          }
          if (!empty($lStates)) {
            $lStates = substr($lStates,0,-1);
            $this -> mIte -> addCondition('webstatus', 'in', $lStates);
          }
        } else {
          $this -> mIte -> addCondition($lKey, '=', $lValue);
        }
     }
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
          $lRet.= '<td><b>'.htm($lDef['cap']).'</b></td>';
          $lRet.= '<td>'.$this -> $lFnc($lVal, $lDef['opt']).'</td>'.LF;
        }
      }
    }

    $lRet.= '</tr></table></td>';
    $lRet.= '<td>'.btn(lan('lib.filter'),'','','submit').'</td>';
    if (!empty($this -> mFil)) {
      $lRet.= '<td >'.btn(lan('lib.show_all'),'go("index.php?act='.$this -> mMod.'.clfil&jobid='.$this -> mJobId.'")','img/ico/16/cancel.gif').'</td>';
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
    $lMen = new CHtm_Menu(lan('lib.opt'));

    $lMen -> addTh2(lan('lib.opt.view'));
    $lMen -> addItem('index.php?act='.$this -> mMod.'.fpr&jobid='.$this -> mJobId, lan('lib.opt.fpr'), 'ico/16/col.gif');
    $lMen -> addItem('index.php?act='.$this -> mMod.'.spr&jobid='.$this -> mJobId, lan('lib.opt.spr'), 'ico/16/search.gif');

    $lOk = 'ico/16/ok.gif';

    #$lImg = ($this -> mHideFil) ?  'd.gif' : $lOk;
    #$lMen -> addItem('index.php?act='.$this -> mMod.'.togfil', 'Show filter bar', $lImg);
    #$lImg = ($this -> mHideSer) ?  'd.gif' : $lOk;
    #$lMen -> addItem('index.php?act='.$this -> mMod.'.togser', 'Show search bar', $lImg);

    $lMen -> addTh2(lan('lib.opt.lpp'));
    $lArr = array(25, 50, 100, 200);
    foreach ($lArr as $lLpp) {
      $lImg = ($lLpp == $this -> mLpp) ? $lOk : 'd.gif';
      $lMen -> addItem($this -> mLppLnk.$lLpp.'&jobid='.$this -> mJobId, $lLpp.' '.lan('lib.opt.lines'), $lImg);
    }

    $lMen -> addTh2(lan('lib.opt.savedviews'));
    $lSql = 'SELECT id,name FROM al_usr_view WHERE 1 ';
    $lSql.= 'AND src="usr" AND src_id=0 AND ref='.esc($this -> mMod).' AND mand='.intval(MID).' ';
    $lSql.= 'ORDER BY name';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lMen -> addItem('index.php?act='.$this -> mMod.'.selview&id='.$lRow['id'].'&jobid='.$this -> mJobId, '[Global] '.$lRow['name'], 'ico/16/global.gif');
    }

    $lSql = 'SELECT id,name FROM al_usr_view WHERE 1 ';
    $lSql.= 'AND src="usr" AND src_id='.esc($this -> mUsr -> getId()).' AND ref='.esc($this -> mMod).' AND mand='.intval(MID).' ';
    $lSql.= 'ORDER BY name';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lMen -> addItem('index.php?act='.$this -> mMod.'.selview&id='.$lRow['id'].'&jobid='.$this -> mJobId, $lRow['name'], 'ico/16/col.gif');
    }
    $lMen -> addItem('index.php?act=job-view&src='.$this -> mMod.'&jobid='.$this -> mJobId, lan('lib.view.save'));
    if ($this -> mUsr -> canInsert('view-std')) {
      $lMen -> addItem('index.php?act='.$this -> mMod.'.allview'.'&jobid='.$this -> mJobId, lan('lib.view.save_as_std'), 'ico/16/save.gif');
    }

    $lMen -> addTh2(lan('lib.opt.search_presets'));
    $lSql = 'SELECT id,name FROM al_usr_search WHERE 1 ';
    $lSql.= 'AND mand="'.intval(MID).'" ';
    if ('job-pro' == $this -> mMod) {
      $lSql.= 'AND ref="pro" ';
    } else {
      $lSql.= 'AND ref="job" ';
    }
    $lSql.= 'AND src="usr" AND src_id='.esc($this -> mUsr -> getId()).' ';
    $lSql.= 'ORDER BY name';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lMen -> addItem('index.php?act='.$this -> mMod.'.selsearch&id='.$lRow['id'].'&jobid='.$this -> mJobId, $lRow['name'], 'ico/16/search.gif');
    }
    $lMen -> addItem('index.php?act=job-view-search&src='.$this -> mMod.'&jobid='.$this -> mJobId, lan('lib.search.save'));

    /*
    if ($this -> mUsr -> isMemberOf(1)) {
      $lMen -> addItem('index.php?act=mba.sview', 'Save as Standard', 'ico/16/save-std.gif');
    }
    */

    return $lMen;
  }














  public function getMenu() {
    $lJobTypes = CCor_Cfg::get('menu-skuitems');

    $lMen = new CHtm_Menu('Button');
    $lMen -> addTh2(lan('job.types'));

    foreach ($lJobTypes as $lKey => $lValue) {
      $lJobType = substr($lValue, -3); // art instead of job_art
      $lHyphened = strtr($lValue, '_', '-'); // job-art instead of job_art
      $lMen -> addItem('index.php?act=job-'.$lJobType.'.sur&skuid='.$this -> mJobId, lan($lHyphened.'.menu'), 'ico/16/'.$lHyphened.'.gif');
    }

    $lImg = 'img/ico/16/plus.gif';
    $lLnk = "javascript:gIgn=1;Flow.Std.popMen('".$lMen -> mDivId."')";
    $lBtn = btn(lan('job.new'), $lLnk, $lImg, 'button', array('class' => 'btn w130', 'id' => $lMen -> mLnkId));
    $lBtn .= $lMen -> getMenuDiv();

    return $lBtn;
  }

  protected function loadFlags() {
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
    $lRet.= '<td>'.img($this -> mImg, array('style' => 'float:left')).'</td>';
    $lRet.= '<td class="captxt p8">'.htm($this -> mTitle).'</td>';
    $lRet.= '</tr></table>'.LF;
    return $lRet;
  }

  protected function getTdFlags() {
    $lCur = $this -> getCurInt();
    $lFla = CCor_Res::extract('val', 'name_'.LAN, 'jfl');
    $lRet = '';
    foreach ($lFla as $lKey => $lVal) {
      if (bitSet($lCur, $lKey)) {
        $lRet.= img('img/jfl/'.$lKey.'.gif', array('style' => 'margin-right:1px', 'data-toggle' => 'tooltip', 'data-tooltip-body' => $lVal));
        $lRet.= '</span>';
      }
    }
    return $this -> tdClass($lRet, 'w16');
  }

  protected function getTdCtr($aStat= FALSE) {
    $lStat = $this -> getVal('webstatus');

    if (($lStat == 'RE') or ($lStat == 'RS')) $aStat = TRUE;
    $lRet = '<td class="'.$this -> mCls.($this -> mHighlight ? 'r': '').' ar w16">';
    $lRet.= $this -> mCtr.'.';
    if ($aStat) $lRet.= '<b>R<b>';
    $lRet.= '</td>'.LF;
    return $lRet;
  }

  protected function getCriticalPaths() {
    $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');

    $this -> mCrp = array();
    foreach ($lCrp as $lKey => $lValue) {
      $this -> mCrp[$lKey] = CCor_Res::get('crp', $lCrp[$lKey]);
    }
  }

  protected function getLink() {
    $lJid = $this -> getVal('jobid');
    $lSrc = $this -> getVal('src');

    return 'index.php?act=job-'.$lSrc.'.edt&amp;jobid='.$lJid;
  }

  protected function getTdCpy() {
    $lJobID = $this -> getVal('jobid');
    $lSrc = $this -> getVal('src');

    $lUsr = CCor_Usr::getInstance();

    $lJobTypes = CCor_Cfg::get('menu-skuitems');
    foreach ($lJobTypes as $lValue) {
      $lJobType = substr($lValue, -3); // art instead of job_art
      $lHyphened = strtr($lValue, '_', '-'); // job-art instead of job_art
      $this -> mCanIns[$lJobType] = $lUsr -> canInsert($lHyphened);
    }

    $lMen = new CHtm_Menu(img('img/ico/16/copy.gif'), '', false);
    foreach ($lJobTypes as $lValue) {
      $lJobType = substr($lValue, -3); // art instead of job_art
      $lHyphened = strtr($lValue, '_', '-'); // job-art instead of job_art
      if (!$this -> mCanIns[$lJobType]) continue;
      $lMen -> addItem('index.php?act=job-'.$lJobType.'.cpy&amp;jobid='.$lJobID.'&amp;src='.$lSrc.'&amp;target='.$lJobType, lan('lib.copy_to').' '.lan('job-'.$lJobType.'.item'), 'ico/16/'.LAN.'/job-'.$lJobType.'.gif');
    }
    return $this -> td($lMen -> getContent());
  }

//  protected function onBeforeContent() {
//    $lRet = parent::onBeforeContent();
//    $this -> mIte = $this -> mIte -> getArray('jobid');
//    $this -> loadFlags();
//    $this -> loadApl();
//    return $lRet;
//  }

  protected function loadApl() {
    $lArr = array_keys($this -> mIte);
    if (empty($lArr)) return;
    $lSql = 'SELECT id,jobid FROM al_job_apl_loop WHERE 1 ';
    $lSql.= 'AND status="open" ';
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

  protected function getTdApl() {
//    $lSta = $this -> getInt('webstatus');
//    if (!in_array($lSta, $this -> lAplstatus)) {
//      return $this -> tda();
//    }

    $lLoopId = $this -> getInt('loop_id');
    if (empty($lLoopId)) {
      return $this -> tda();
    }

    $lRet = CApp_Apl_Loop::getAplCommitList($lLoopId, $this -> mCurLnk);

    return $this -> tdClass($lRet, 'w16 ac');
  }

  protected function getTdJobnr() {
    $lJobID = $this -> getVal('jobid');
    return $this -> tda(jid($lJobID));
  }

  protected function getTdSrc() {
    $lSrc = $this -> getVal('src');
    $lImg = (THEME === 'default' ? 'job-'.$lSrc : CApp_Crpimage::getColourForSrc($lSrc));
    $lRet = img('img/ico/16/'.$lImg.'.gif');
    return $this -> tdClass($this -> a($lRet), 'w16 ac');
  }

  protected function getTdWebstatus() {
    $lVal = $this -> getVal('webstatus');
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
      if (($lDisplay == 'progressbar' && $lVal >= $lRow['status']) OR ($lDisplay == 'individual' && $lVal == $lRow['status'])) {
        $lPath = CApp_Crpimage::getSrcPath($aSrc, 'img/crp/'.$lRow['display'].'b.gif');
        $lRet.= img($lPath, array('style' => 'margin-right:1px'));
        $lNam = $lRow['name_'.LAN];
      } else if (($lDisplay == 'progressbar' && $lVal < $lRow['status']) OR ($lDisplay == 'individual' && $lVal != $lRow['status'])) {
        $lPath = CApp_Crpimage::getSrcPath($aSrc, 'img/crp/'.$lRow['display'].'l.gif');
        $lRet.= img($lPath, array('style' => 'margin-right:1px'));
      }
    }
    $lRet.= NB.htm($lNam);
    return $this -> tda($lRet);
  }

}