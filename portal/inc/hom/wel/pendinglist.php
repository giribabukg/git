<?php
class CHom_Wel_PendingList extends CHtm_List {

  public function __construct() {
    parent::__construct('hom_pen');

    $this -> mStdLnk = 'index.php?act=job-';
    $this -> mColCnt = 0;
    $this -> setAtt('width', '100%');
    $this -> mTitle = lan('lib.my.tasks.pending');

    $lUsr = CCor_Usr::getInstance();
    $this -> mOrd  = $lUsr -> getPref('hom_pen.ord', 'id_capproval');
    $this -> mDir  = 'asc';
    if (substr($this -> mOrd, 0, 1) == '-') {
      $this -> mOrd = substr($this -> mOrd, 1);
      $this -> mDir = 'desc';
    }

    $lUid = CCor_Usr::getAuthId();
    $this -> mIte = new CCor_TblIte('al_job_pdb_'.MID.' as p, al_crp_master as q');
    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);
    $this -> mIte -> addCnd('q.mand='.MID);
    $this -> mIte -> addCnd('q.code=p.src');
    $this -> mIte -> addCnd('(NOT ISNULL(p.id_bestelldatum) OR NOT ISNULL(p.id_ccomponent) OR NOT ISNULL(p.id_capproval) OR NOT ISNULL(p.id_translations) OR NOT ISNULL(p.id_translationapproval))');

    $this -> mShowSubHdr = false;

    if ($this -> mIte -> getArray()) {
      $this -> addCtr();
      $this -> addColumn('src',                     lan('lib.src'),                     true, array('width' => '25%'));
      $this -> addColumn('id_bestelldatum',         lan('lib.id_bestelldatum'),         true, array('width' => '25%'));
      $this -> addColumn('id_ccomponent',           lan('lib.id_ccomponent'),           true, array('width' => '25%'));
      $this -> addColumn('id_capproval',            lan('lib.id_capproval'),            true, array('width' => '25%'));
      $this -> addColumn('id_translations',         lan('lib.id_translations'),          true, array('width' => '25%'));
      $this -> addColumn('id_translationapproval',  lan('lib.id_translationapproval'), true, array('width' => '25%'));
    } else {
      $this -> mShowSubHdr = true;
      $this -> addPanel('nav', lan('lib.my.tasks.pending.no'));
    }
  }

  protected function getLink() {
    $lId = $this -> getVal('jobid');
    $lSrc = $this -> getVal('src');

    return $this -> mStdLnk.$lSrc.'.edt&jobid='.$lId;
  }

  protected function getTdSrc() {
    $lRes = $this -> getVal('src');
    return $this -> tda($lRes);
  }

  protected function getTdId_bestelldatum() {
    $lVal = $this -> getVal('id_bestelldatum');
    $lDat = new CCor_Date($lVal);
    $lRes = $lDat -> getFmt(lan('lib.date.week'));
    return $this -> tda($lRes);
  }

  protected function getTdId_ccomponent() {
    $lVal = $this -> getVal('id_ccomponent');
    $lDat = new CCor_Date($lVal);
    $lRes = $lDat -> getFmt(lan('lib.date.week'));
    return $this -> tda($lRes);
  }

  protected function getTdId_capproval() {
    $lVal = $this -> getVal('id_capproval');
    $lDat = new CCor_Date($lVal);
    $lRes = $lDat -> getFmt(lan('lib.date.week'));
    return $this -> tda($lRes);
  }

  protected function getTdId_translations() {
    $lVal = $this -> getVal('id_translations');
    $lDat = new CCor_Date($lVal);
    $lRes = $lDat -> getFmt(lan('lib.date.week'));
    return $this -> tda($lRes);
  }

  protected function getTdId_translationapproval() {
    $lVal = $this -> getVal('id_translationapproval');
    $lDat = new CCor_Date($lVal);
    $lRes = $lDat -> getFmt(lan('lib.date.week'));
    return $this -> tda($lRes);
  }

  protected function getTdCtr($aStat= FALSE) {
    $lStat = $this -> getVal('status');

    $lBD = $this -> getVal('id_bestelldatum');
    $lBD = strtotime($lBD);
    $lNow = time();
    $lRes = $lBD - $lNow;
    $lRes = $lRes / (60 * 60 * 24);
    if (!empty($lBD) && ($lRes <= 1)) {
      $this -> mCls = 'tdr';
    } else {
      $this -> mCls = 'tdo';
    }

    if (($lStat == 'RE') or ($lStat == 'RS')) $aStat = TRUE;
    $lRet = '<td class="'.$this -> mCls.' ar w16">';
    $lRet.= $this -> mCtr.'.';
    if ($aStat) $lRet.= '<b>R<b>';
    $lRet.= '</td>'.LF;
    return $lRet;
  }

}