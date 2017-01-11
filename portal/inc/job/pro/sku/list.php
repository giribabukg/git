<?php
class CInc_Job_Pro_Sku_List extends CJob_List {

  public function __construct($aJobId) {
    $this -> mJobId = $aJobId;

    $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');
    $this -> mCrpId = $lCrp['sku'];

    $this -> mShowColumnMore = TRUE; // to do: it's called column, needs to be changed all the way through...
    $this -> mShowCopyButton = FALSE;
    $this -> mShowDeleteButton = FALSE;
    $this -> mShowCsvExportButton = FALSE;
    $this -> mSourceColumn = FALSE;

    parent::__construct('job-pro-sku', $this -> mCrpId);

    $this -> mStdLnk = 'index.php?act='.$this -> mMod.'.edt&amp;jobid='.$this -> mJobId.'&amp;id=';
    $this -> mOrdLnk = 'index.php?act='.$this -> mMod.'.ord&amp;jobid='.$this -> mJobId.'&amp;fie=';
    $this -> mDelLnk = 'index.php?act='.$this -> mMod.'.del&amp;jobid='.$this -> mJobId.'&amp;id=';

    $this -> mImg = 'img/ico/40/'.LAN.'/job-sku.gif';

    $this -> addFilter('webstatus', 'Status', $this -> mCrpId);
    $this -> getFilterbyAlias(); //default 'per_prj_verantwortlich'

    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);
    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);

    $lUsr = CCor_Usr::getInstance();
    if ($lUsr -> canInsert($this -> mMod)) {
      $this -> addBtn(lan($this -> mMod.'.new'), 'go("index.php?act=job-sku.newsur&pid='.$this -> mJobId.'")', 'img/ico/16/plus.gif');
    }

    $this -> addPanel('nav', $this -> getNavBar());
    $this -> addPanel('vie', $this -> getViewMenu());

    $this -> addJs();
  }

  protected function getIterator() {
    $lAllJobFields = CCor_Res::extract('id', 'alias', 'fie');
    $lUsr = CCor_Usr::getInstance();
    $lSKUJobFieldIDs = $lUsr -> getPref('job-pro-sku.cols');
    $lSKUJobFields = explode(',', $lSKUJobFieldIDs);

    $this -> mIte = new CCor_TblIte('al_job_sku_'.intval(MID).' AS main, al_job_sku_sur_'.intval(MID).' AS sur');
    foreach ($lSKUJobFields as $lFie) {
      if (!empty($lAllJobFields[$lFie])) {
        $this -> mIte -> addField('main.'.$lAllJobFields[$lFie]);
      }
    }
    $this -> mIte -> addField('main.id');
    $this -> mIte -> addField('main.src');
    $this -> mIte -> addField('main.webstatus');
    $this -> mIte -> addCnd('main.id=sur.sku_id');
    $this -> mIte -> addCnd('sur.pro_id='.esc($this -> mJobId));
  }

  protected function afterRow() {
    $lRet = parent::afterRow();
    $lRet.= '<tr style="display:none" id="'.$this -> mMoreId.'"><td class="td1 tg" id="'.$this -> mMoreId.'l">&nbsp;</td>';
    $lCol = $this -> mColCnt - 1;
    $lRet.= '<td class="td1 p16" id="'.$this -> mMoreId.'r" colspan="'.$lCol.'"><div><img src="img/pag/ajx.gif" alt="" /></div></td>';
    $lRet.= '</tr>'.LF;
    return $lRet;
  }

  protected function addJs() {
    $lJs = 'function loadSub(aId,aJid){';
    $lJs.= 'Flow.Std.togTr(aId);';
    $lJs.= 'lDiv = aId + "r";';
    $lJs.= 'Flow.Std.ajxUpd({act:"job-sku.sub",jid:aJid,div:lDiv});';
    $lJs.= '}';
    $lPag = CHtm_Page::getInstance();
    $lPag -> addJs($lJs);
  }

  protected function getTdMor() {
    $lSKUID = $this -> getInt('id');
    $this -> mMoreId = getNum('t');
    $lRet = '<a class="nav" onclick="loadSub(\''.$this -> mMoreId.'\','.$lSKUID.')">';
    $lRet.= '...</a>';
    return $this -> tdc($lRet);
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

  protected function getTdDel() {
    $lRet = '<td class="'.$this -> mCls.($this -> mHighlight ? 'r': '').' nw w16 ac">';
    $lRet.= '<a class="nav" href="javascript:Flow.Std.cnf(\''.$this -> getDelLink().'\', \'cnfDel\')">';
    $lRet.= img('img/ico/16/del.gif');
    $lRet.= '</a>';
    $lRet.= '</td>'.LF;
    return $lRet;
  }

  protected function getLink() {
    $lJid = $this -> getVal('id');

    return 'index.php?act=job-sku.edt&jobid='.$lJid;
  }

}