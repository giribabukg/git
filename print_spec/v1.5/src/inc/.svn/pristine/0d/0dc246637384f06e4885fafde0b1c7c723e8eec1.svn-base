<?php
class CInc_Job_Sku_List extends CJob_List {

  protected $mAva = fsSku;
  protected $mSrc = 'sku';
  protected $mWithoutLimit = FALSE; // SQL LIMIT for csv export
  protected $mShowColumnMore = TRUE;

  public function __construct($aWithoutLimit = FALSE, $aAnyUsrID = NULL) {
    $this -> mWithoutLimit = $aWithoutLimit;

    $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');
    $this -> mCrpId = $lCrp['sku'];


    $this -> mShowCopyButton = FALSE;
    $this -> mShowDeleteButton = FALSE;
    $this -> mShowCsvExportButton = FALSE;

    parent::__construct('job-sku', $this -> mCrpId, '', $aAnyUsrID);

    $this -> mImg = 'img/ico/40/'.LAN.'/job-sku.gif';

    $this -> addFilter('webstatus', 'Status', $this -> mCrpId);
    $this -> getFilterbyAlias(); //default 'per_prj_verantwortlich'

    $lUsr = CCor_Usr::getInstance();
    if ($lUsr -> canInsert('job-sku')) {
      $this -> addBtn(lan('job-sku.new'), 'go("index.php?act=job-sku.new")', 'img/ico/16/plus.gif');
    }

    $this -> addSort('last_status_change');
    $this -> addButton(lan('lib.sort'), $this -> getButtonMenu($this -> mMod));

    $this -> addPanel('nav', $this -> getNavBar());
    $this -> addPanel('vie', $this -> getViewMenu());

    $this -> addJs();
  }

  protected function getIterator() {
    $this -> mIte = new CCor_TblIte('al_job_sku_'.intval(MID), $this -> mWithoutLimit);
    $lSql = "SELECT DISTINCT `to_id` FROM `al_crp_step` WHERE `mand`=".intval(MID);
    $lSql.= " AND `trans` LIKE 'sku2arc' AND `crp_id`=".esc($this -> mCrpId)." LIMIT 0,1";
    $lSKUArcStatus = CCor_Qry::getInt($lSql);
    if ($lSKUArcStatus) {
      $this -> mIte -> addCondition('webstatus', '<', $lSKUArcStatus);
    }
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

  protected function & getViewMenuObject() {
    $lUsr = CCor_Usr::getInstance();

    $lMen = new CHtm_Menu(lan('lib.opt'));

    $lMen -> addTh2(lan('lib.opt.view'));
    $lMen -> addItem('index.php?act='.$this -> mMod.'.fpr', lan('lib.opt.fpr'), 'ico/16/col.gif');
    $lMen -> addItem('index.php?act='.$this -> mMod.'.spr', lan('lib.opt.spr'), 'ico/16/search.gif');

    $lOk = 'ico/16/ok.gif';

//    $lImg = ($this -> mHideFil) ?  'd.gif' : $lOk;
//    $lMen -> addItem('index.php?act='.$this -> mMod.'.togfil', 'Show filter bar', $lImg);
//    $lImg = ($this -> mHideSer) ?  'd.gif' : $lOk;
//    $lMen -> addItem('index.php?act='.$this -> mMod.'.togser', 'Show search bar', $lImg);

    $lMen -> addTh2(lan('lib.opt.lpp'));
    $lArr = array(25, 50, 100, 200);
    foreach ($lArr as $lLpp) {
      $lImg = ($lLpp == $this -> mLpp) ? $lOk : 'd.gif';
      $lMen -> addItem($this -> mLppLnk.$lLpp, $lLpp.' '.lan('lib.opt.lines'), $lImg);
    }

    $lMen -> addTh2(lan('lib.opt.savedviews'));
    $lSql = 'SELECT id,name FROM al_usr_view WHERE 1 ';
    $lSql.= 'AND src="usr" AND src_id=0 AND ref="'.$this -> mMod.'" AND mand='.intval(MID).' ';
    $lSql.= 'ORDER BY name';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lMen -> addItem('index.php?act='.$this -> mMod.'.selview&amp;id='.$lRow['id'], '[Global] '.$lRow['name'], 'ico/16/global.gif');
    }

    $lSql = 'SELECT id,name FROM al_usr_view WHERE 1 ';
    $lSql.= 'AND src="usr" AND src_id='.$lUsr -> getId().' AND ref="'.$this -> mMod.'" AND mand='.intval(MID).' ';
    $lSql.= 'ORDER BY name';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lMen -> addItem('index.php?act='.$this -> mMod.'.selview&amp;id='.$lRow['id'].'&amp;jobid='.$this -> mJobId, $lRow['name'], 'ico/16/col.gif');
    }
    $lMen -> addItem('index.php?act=job-view&amp;src='.$this -> mMod, lan('lib.view.save'));
    if ($lUsr -> canInsert('view-std')) {
      $lMen -> addItem('index.php?act='.$this -> mMod.'.allview', lan('lib.view.save_as_std'), 'ico/16/save.gif');
    }

    $lMen -> addTh2(lan('lib.opt.search_presets'));
    $lSql = 'SELECT id,name FROM al_usr_search WHERE 1 ';
    $lSql.= 'AND mand="'.intval(MID).'" ';
    if ('job-sku' == $this -> mMod) {
      $lSql.= 'AND ref="sku" ';
    } else {
      $lSql.= 'AND ref="job" ';
    }
    $lSql.= 'AND src="usr" AND src_id='.$lUsr -> getId().' ';
    $lSql.= 'ORDER BY name';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lMen -> addItem('index.php?act='.$this -> mMod.'.selsearch&amp;id='.$lRow['id'], $lRow['name'], 'ico/16/search.gif');
    }
    $lMen -> addItem('index.php?act=job-view-search&amp;src='.$this -> mMod, lan('lib.search.save'));

//    if ($lUsr -> isMemberOf(1)) {
//      $lMen -> addItem('index.php?act=mba.sview', 'Save as Standard', 'ico/16/save-std.gif');
//    }

    return $lMen;
  }

}