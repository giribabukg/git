<?php
class CInc_Arc_Pro_List extends CArc_List {

  protected $mSrc = 'pro';
  protected $mWithoutLimit = FALSE;
  protected $mArcStatus;

  public function __construct($aWithoutLimit = FALSE) {
    $this -> mWithoutLimit = $aWithoutLimit;
    $this -> mShowCsvExportButton = TRUE;

    $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');

    $this -> mCrpId = $lCrp[$this -> mSrc];
    $this -> mArcStatus = $this -> getArcStatus();

    parent::__construct($this -> mSrc);

    $this -> mImg = 'img/ico/40/'.LAN.'/job-'.$this -> mSrc.'.gif';
    $this -> mIdField = 'id';

    $this -> addColumn('mor', '', FALSE, array('width' => '16'));
    $this -> addCtr();
    $this -> addColumns();
    $this -> getFilterbyAlias('produktgruppe'); // default: 'per_prj_verantwortlich'

    $this -> mMaxLines = $this -> mIte -> getCount();
    $this -> addPanel('nav', $this -> getNavBar());
    $this -> addPanel('vie', $this -> getViewMenu());

    $this -> addJs();
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

  protected function getIterator() {
    $this -> mIte = new CCor_TblIte('al_job_pro_'.intval(MID), $this -> mWithoutLimit);
    $this -> mIte -> addCnd('del="N"');
    $this -> mIte -> addCnd('webstatus>='.$this -> mArcStatus);
  }

  protected function addCondition($aAlias, $aOp, $aValue) {
    $this -> mIte -> addCondition($aAlias, $aOp, $aValue);
  }

  protected function getTdMor() {
    $lPid = $this -> getInt('id');
    $this -> mMoreId = getNum('t');
    $lRet = '<a class="nav" onclick="loadSub(\''.$this -> mMoreId.'\','.$lPid.')">';
    $lRet.= '...</a>';
    return $this -> tdc($lRet);
  }

  protected function afterRow() {
    $lRet = parent::afterRow();
    $lRet.= '<tr style="display:none" id="'.$this -> mMoreId.'"><td class="td1 tg" id="'.$this -> mMoreId.'l">&nbsp;</td>';
    $lCol = $this -> mColCnt - 1;
    $lRet.= '<td class="td1 p16" id="'.$this -> mMoreId.'r" colspan="'.$lCol.'"><div><img src="img/pag/ajx.gif" alt="" /></div></td>';
    $lRet.= '</tr>'.LF;
    return $lRet;
  }

  protected function getArcStatus() {
    $lRet = '';
    $lArcStatus = '';
    $lSql = "Select s.status from al_crp_status as s left join al_crp_step as x ON s.id= x.to_id where x.mand=".MID;
    $lSql.= " AND x.trans LIKE 'pro2arc' AND x.crp_id=".$this -> mCrpId." AND x.to_id IS NOT NULL LIMIT 0,1";
    $lArcStatus = CCor_Qry::getInt($lSql);
    if ($lArcStatus == '') {
      $this -> dbg('Missing Archive Event in CRP', mlWarn);
    } else {
      $lRet = $lArcStatus;
    }
    return $lRet;
  }
}