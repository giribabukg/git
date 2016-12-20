<?php
class CHom_Wel_APLList extends CHtm_List {

  public function __construct() {
    parent::__construct('hom_apl');

    $this -> mStdLnk = 'index.php?act=job-';
    $this -> mColCnt = 0;
    $this -> setAtt('width', '100%');
    $this -> mTitle = lan('lib.my.tasks.apl');

    $lUsr = CCor_Usr::getInstance();
    $this -> mOrd  = $lUsr -> getPref('hom_apl.ord', 'ddl');
    $this -> mDir  = 'asc';
    if (substr($this -> mOrd, 0, 1) == '-') {
      $this -> mOrd = substr($this -> mOrd, 1);
      $this -> mDir = 'desc';
    }

    $lUid = CCor_Usr::getAuthId();
    $this -> mIte = new CCor_TblIte('al_job_apl_loop as p, al_job_apl_states as q');
    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);
    $this -> mIte -> addCnd('p.mand='.MID);
    $this -> mIte -> addCnd('p.status=\'open\'');
    $this -> mIte -> addCnd('p.typ=\'apl\'');
    $this -> mIte -> addCnd('p.id=q.loop_id');
    $this -> mIte -> addCnd('q.user_id='.$lUid);
    $this -> mIte -> addCnd('q.status<3'); // hier muß eine Konstante gesetzt werden! das darf so nicht bleiben!

    $this -> mShowSubHdr = false;

    if ($this -> mIte -> getArray()) {
      $this -> addCtr();
      $this -> addColumn('src',         lan('lib.src'),         true, array('width' => '20%'));
      $this -> addColumn('start_date',  lan('lib.start_date'),  true, array('width' => '20%'));
      $this -> addColumn('ddl',         lan('lib.ddl'),         true, array('width' => '20%'));
      $this -> addColumn('datum',       lan('lib.datum'),       true, array('width' => '20%'));
      $this -> addColumn('status',      lan('lib.status'),      true, array('width' => '20%'));
    } else {
      $this -> mShowSubHdr = true;
    	$this -> addPanel('nav', lan('lib.my.tasks.apl.no'));
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

  protected function getTdStart_date() {
    $lVal = $this -> getVal('start_date');
    $lDat = new CCor_Date($lVal);
    $lRes = $lDat -> getFmt(lan('lib.date.week'));
    return $this -> tda($lRes);
  }

  protected function getTdDdl() {
    $lVal = $this -> getVal('ddl');
    $lDat = new CCor_Date($lVal);
    $lRes = $lDat -> getFmt(lan('lib.date.week'));
    return $this -> tda($lRes);
  }

  protected function getTdDatum() {
    $lVal = $this -> getVal('datum');
    $lDat = new CCor_Date($lVal);
    $lRes = $lDat -> getFmt(lan('lib.date.week'));
    return $this -> tda($lRes);
  }

  protected function getTdStatus() {
    $lVal = $this -> getVal('status');
    switch ($lVal) {
    case 3:
      $lRes = lan('apl.approval');
      break;
    case 2:
      $lRes = lan('apl.conditional');
      break;
    case 1:
      $lRes = lan('apl.amendment');
      break;
    case 0:
      $lRes = lan('apl.unknown');
      break;
    }
    return $this -> tda($lRes);
  }

  protected function getTdCtr($aStat= FALSE) {
    $lStat = $this -> getVal('status');

    $lBD = $this -> getVal('id_bestelldatum');
    $lBD = strtotime($lBD);
    $lNow = time();
    $lRes = $lBD - $lNow;
    $lRes = $lRes / (60 * 60 * 24);
    if (!empty($lBD) && ($lRes < 1)) {
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