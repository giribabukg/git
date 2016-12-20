  <?php
class CInc_Job_Sku_Sur_List extends CJob_List {

  public function __construct($aJobId) {
    $this -> mJobId = $aJobId;

    $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');
    $this -> mCrpId = $lCrp['pro'];//und wenn's keine projekte gibt???

    $this -> mShowColumnMore = TRUE; // to do: it's called column, needs to be changed all the way through...
    $this -> mShowCopyButton = FALSE;
    $this -> mShowDeleteButton = FALSE;
    $this -> mShowCsvExportButton = FALSE;

    parent::__construct('job-sku-sur', $this -> mCrpId);

    $this -> mStdLnk = 'index.php?act='.$this -> mMod.'.edt&amp;jobid='.$this -> mJobId.'&amp;id=';
    $this -> mOrdLnk = 'index.php?act='.$this -> mMod.'.ord&amp;jobid='.$this -> mJobId.'&amp;fie=';
    $this -> mDelLnk = 'index.php?act='.$this -> mMod.'.del&amp;jobid='.$this -> mJobId.'&amp;id=';

    $this -> mImg = 'img/ico/40/'.LAN.'/job-sku.gif';

    $this -> addFilter('webstatus', 'Status', $this -> mCrpId);
    $this -> getFilterbyAlias(); //default 'per_prj_verantwortlich'

    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);
    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);

    $this -> addButton(lan('job.new'), $this -> getMenu());

    $this -> addPanel('nav', $this -> getNavBar());
    $this -> addPanel('vie', $this -> getViewMenu());

    $this -> addJs();
  }

  protected function getIterator() {
    $lAllJobFields = CCor_Res::extract('id', 'alias', 'fie');
    $lUsr = CCor_Usr::getInstance();
    $lSurJobFieldIDs = $lUsr -> getPref('job-sku-sur.cols');
    $lSurJobFields = explode(',', $lSurJobFieldIDs);

    $this -> mIte = new CCor_TblIte('al_job_pro_'.intval(MID).' AS pro, al_job_sku_sur_'.intval(MID).' AS sku');
    foreach ($lSurJobFields as $lFie) {
      if (!empty($lAllJobFields[$lFie])) {
        $this -> mIte -> addField('pro.'.$lAllJobFields[$lFie]);
      }
    }
    $this -> mIte -> addField('pro.id');
    $this -> mIte -> addField('pro.src');
    $this -> mIte -> addField('pro.webstatus');
    $this -> mIte -> addCnd('pro.id=sku.pro_id');
    $this -> mIte -> addCnd('sku.sku_id='.esc($this -> mJobId));
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
    $lJs.= 'Flow.Std.ajxUpd({act:"job-pro.sub",jid:aJid,div:lDiv});';
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

  protected function getLink() {
    $lJid = $this -> getVal('id');
    $lSrc = $this -> getVal('src');
    return 'index.php?act=job-pro.edt&amp;jobid='.$lJid;
  }

}