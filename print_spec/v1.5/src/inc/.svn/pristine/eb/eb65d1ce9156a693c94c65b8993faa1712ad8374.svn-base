<?php
class CInc_Job_Assign_List extends CHtm_List {

  protected $mSrc;
  protected $mJobId;
  protected $mPrjId;
  protected $mSKU;

  public function __construct($aSrc, $aJobId, $aPrjId, $aSKU = FALSE) {
    $this -> mSrc = $aSrc;
    $this -> mJobId = $aJobId;
    $this -> mPrjId = $aPrjId;
    $this -> mSKU = $aSKU;

    $this -> mMod = 'job-assign';

    parent::__construct($this -> mMod);

    $this -> setAtt('width', '100%');
    $this -> mTitle = lan('lib.pro.sel');

    $this -> mUsr = CCor_Usr::getInstance();
    $this -> mStdLnk = 'index.php?act=job-'.$this -> mSrc.'.sassignprj&amp;jobid='.$this -> mJobId.'&amp;fromid='.$this -> mPrjId.'&amp;pid=';
    $this -> mOrdLnk = 'index.php?act=job-assign.ord&amp;src='.$this -> mSrc.'&amp;jobid='.$this -> mJobId.'&amp;prjid='.$this -> mPrjId.'&amp;fie=';
    $this -> mFie = CCor_Res::get('fie');
    $this -> mDefs = CCor_Res::getByKey('alias', 'fie');
    $this -> mCrps = CCor_Res::extract('code', 'id', 'crpmaster');
    $this -> mCrpId = $this -> mCrps['pro'];
    $this -> mCrpArr = CCor_Res::get('crp', $this -> mCrpId);

    $this -> mCnd = new CCor_Cond();
    $this -> mArcStatus = $this -> getArcStatus();

    $this -> addCtr();
    $this -> addColumns();

    $this -> getPrefs($this -> mMod);

    $this -> getIterator();
    $this -> addSearchConditions();

    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);
    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);

    $this -> mMaxLines = $this -> mIte -> getCount();

    $this -> addPanel('nav', $this -> getNavBar());
    $this -> addPanel('vie', $this -> getViewMenu());

    $this -> mHideFil = ($this -> mUsr -> getPref($aSrc.'.hidefil') == 1);
    $this -> mHideSer = ($this -> mUsr -> getPref($aSrc.'.hideser') == 1);
  }

  protected function getIterator() {
    $this -> mIte = new CCor_TblIte('al_job_pro_'.MID, FALSE);
    $this -> mIte -> addCnd('del != "Y"');
    if ($this -> mArcStatus !== '') {
      $this -> mIte -> addCnd('webstatus<'.$this -> mArcStatus);
    }
  }

  protected function addColumns() {
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
      }
    }
  }

  protected function & getViewMenuObject() {
    $lThis = '&amp;src='.$this -> mSrc.'&amp;jobid='.$this -> mJobId.'&amp;prjid='.$this -> mPrjId;
    $lMen = new CHtm_Menu(lan('lib.opt'));

    $lMen -> addTh2(lan('lib.opt.view'));
    $lMen -> addItem('index.php?act='.$this -> mMod.'.fpr'.$lThis, lan('lib.opt.fpr'), 'ico/16/col.gif');
    $lMen -> addItem('index.php?act='.$this -> mMod.'.spr'.$lThis, lan('lib.opt.spr'), 'ico/16/search.gif');

    $lOk = 'ico/16/ok.gif';

    $lMen -> addTh2(lan('lib.opt.lpp'));
    $lArr = array(5, 10, 25, 50, 100, 200);
    foreach ($lArr as $lLpp) {
      $lImg = ($lLpp == $this -> mLpp) ? $lOk : 'd.gif';
      $lMen -> addItem($this -> mLppLnk.$lLpp.$lThis, $lLpp.' '.lan('lib.opt.lines'), $lImg);
    }

    return $lMen;
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
        foreach ($lCnd as $lItm) {
          $this -> mIte -> addCondition($lItm['field'], $lItm['op'], $lItm['value']);
        }
      }
    }
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

  protected function getSearchForm() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '  <input type="hidden" name="act" value="'.$this -> mMod.'.ser" />'.LF;
    $lRet.= '  <input type="hidden" name="src" value="'.$this -> mSrc.'" />'.LF;
    $lRet.= '  <input type="hidden" name="jobid" value="'.$this -> mJobId.'" />'.LF;
    $lRet.= '  <input type="hidden" name="prjid" value="'.$this -> mPrjId.'" />'.LF;

    $lRet.= '  <table cellpadding="2" cellspacing="0" border="0">'.LF;
    $lRet.= '    <tr>'.LF;
    $lRet.= '      <td class="caption w50">'.htm(lan('lib.search')).'</td>'.LF;
    $lRet.= '      <td>'.LF;
    $lRet.= '        <table cellpadding="2" cellspacing="0" border="0">'.LF;
    $lRet.= '          <tr>'.LF;
    $lFie = explode(',', $this -> mSerFie);
    $lFac = new CHtm_Fie_Fac();

    $lIdx = array('col_1');
    $lCnt = 0;
    foreach ($lFie as $lFid) {
      if (isset($this -> mFie[$lFid])) {
        if ($lCnt > 2) {
          $lRet.= '</tr><tr>'.LF;
          $lCnt = 0;
        }

        $lDef = $this -> mFie[$lFid];
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
          $lRet.= '<td>'.LF;
          $lRet.= $lFac -> getInput($lDef, $lVal, fsSearch);
          $lRet.= '</td>'.LF;
        }

        $lCnt++;
      }
    }

    $lRet.= '          </tr>'.LF;
    $lRet.= '        </table>'.LF;
    $lRet.= '      </td>'.LF;
    $lRet.= '<td valign="top">'.btn(lan('lib.search'),'','img/ico/16/search.gif','submit').'</td>'.LF;
    if (!empty($this -> mSer)) {
      $lRet.= '<td valign="top">'.btn(lan('lib.show_all'),'go("index.php?act='.$this -> mMod.'.clser&src='.$this -> mSrc.'&jobid='.$this -> mJobId.'&prjid='.$this -> mPrjId.'")','img/ico/16/cancel.gif').'</td>'.LF;
    }

    $lRet.= '    </tr>'.LF;
    $lRet.= '  </table>'.LF;
    $lRet.= '</form>'.LF;

    return $lRet;
  }

  protected function getArcStatus() {
    $lRet = '';
    $lArcStatus = '';

    if ($this -> mCrpId) { // if (is_numeric($this -> mCrpId))
      $lSql = "SELECT s.status FROM al_crp_status AS s LEFT JOIN al_crp_step AS x ON s.id=x.to_id WHERE x.mand=".MID;
      $lSql.= " AND x.trans LIKE 'pro2arc' AND x.crp_id=".$this -> mCrpId." AND  x.to_id IS NOT NULL LIMIT 0,1";
      $lArcStatus = CCor_Qry::getInt($lSql);
    }

    if ($lArcStatus == '') {
      $this -> dbg('Missing Archive Event in CRP', mlWarn);
    } else {
      $lRet = $lArcStatus;
    }

    return $lRet;
  }

  protected function getTdWebstatus() {
    $lVal = $this -> getCurInt();
    $lSrc = 'pro';

    if (is_int($this -> mCrpId) && isset($this -> mCrpArr)) {
      return $this -> getExtWebstatus($lVal, $this -> mCrpArr);
    }

    if (is_int($lVal)) {
      $lDis = $lVal / 10;
      $lPath = CApp_Crpimage::getSrcPath($lSrc, 'img/crp/'.$lDis.'b.gif');
      $lRet = img($lPath, array('style' => 'margin-right:1px'));
      return $this -> tda($lRet);
    } else {
      return $this -> tda('['.lan('lib.unknown').']');
    }
  }

  protected function getExtWebstatus($aWebStatus, $aCrp) {
    $lVal = $aWebStatus;
    $lSrc = 'pro';
    $lNam = '['.lan('lib.unknown').']';

    $lRet = '';
    foreach ($aCrp as $lRow) {
      if ($lVal >= $lRow['status']) {
        $lPath = CApp_Crpimage::getSrcPath($lSrc, 'img/crp/'.$lRow['display'].'b.gif');
        $lRet.= img($lPath, array('style' => 'margin-right:1px'));
        $lNam = $lRow['name_'.LAN];
      } else if ($lVal < $lRow['status']) {
        $lPath = CApp_Crpimage::getSrcPath($lSrc, 'img/crp/'.$lRow['display'].'l.gif');
        $lRet.= img($lPath, array('style' => 'margin-right:1px'));
      }
    }
    $lRet.= NB.htm($lNam);
    return $this -> tda($lRet);
  }
  
  protected function getNavBar() {
    if (!$this -> mNavBar) {
      return '';
    }
    if (isset($this -> mJobId)){
      $lJobId = $this -> mJobId;
    }else {
      $lJobId = '';
    }
  
    $lNav = new CHtm_NavBar($this -> mMod, $this -> mPage, $this -> mMaxLines, $this -> mLpp, $lJobId);
    $lNav -> setParam('src', $this -> mSrc);
    $lNav -> setParam('jobid', $this -> mJobId);
    $lNav -> setParam('prjid', $this -> mPrjId);
    return $lNav -> getContent();
  }
  
}