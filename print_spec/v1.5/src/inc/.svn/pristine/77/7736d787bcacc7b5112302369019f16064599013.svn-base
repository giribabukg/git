<?php
class CInc_Job_Sku_Mod extends CJob_Mod {

  protected $mJobId;

  public function __construct($aJobId = 0) {
    parent::__construct('sku');

    $this -> mJobId = intval($aJobId);
    $this -> mTbl = 'al_job_sku_'.intval(MID);
    $this -> mInsertId = NULL;

    $lFie = CCor_Res::get('fie');
    foreach ($lFie as $lDef) {
      $this -> addField($lDef);
    }
  }

  protected function doInsert() {
    $this -> mVal['webstatus'] = 10;
    $lNow = date('Y-m-d H:i:s');
    $this -> mVal['last_status_change'] = $lNow;

    $lSql = 'INSERT INTO '.$this -> mTbl.' SET ';
    foreach ($this -> mVal as $lKey => $lVal) {
      if(!empty($lVal))
        $lSql.= $lKey.'='.esc($lVal).',';
    }
    $lSql = strip($lSql, 1);
    if ($this -> mTest) {
      $this -> dbg($lSql);
      return TRUE;
    } else {
      $lQry = new CCor_Qry();
      $lRet = $lQry -> query($lSql);
      if ($lRet) {
        $this -> mInsertId = $lQry -> getInsertId();
        $lMod = new CJob_His($this -> mSrc, $this -> mInsertId);
        $lMod -> add(htStatus, 'SKU created', '', '', '', '', 10);
      } else {
        $this -> mInsertId = NULL;
      }
      return $lRet;
    }
  }

  protected function doUpdate() {
    foreach ($this -> mOld as $lKey => $lVal) {
      if ($this -> fieldHasChanged($lKey)) {
        $this -> mUpd[$lKey] = $this -> getVal($lKey);
      }
    }
    if (empty($this -> mUpd)) {
      return TRUE;
    }
    return $this -> forceUpdate($this -> mUpd);
  }

  public function forceUpdate($aArr = array()) {
    if (empty($aArr)) return TRUE;
    $lSql = 'UPDATE '.$this -> mTbl.' SET ';
    foreach ($aArr as $lKey => $lVal) {
      if ($this -> fieldHasChanged($lKey)) {
        $lSql.= $lKey.'='.esc($lVal).',';
      }
    }
    $lSql = strip($lSql, 1);
    $lSql.= ' WHERE id='.$this -> mJobId.' LIMIT 1';
    return CCor_Qry::exec($lSql);
  }

  protected function doDelete($aId) {
    $lSql = 'DELETE FROM '.$this -> mTbl.' ';
    $lSql.= 'WHERE id='.$this -> mJobId.' LIMIT 1';
    return CCor_Qry::exec($lSql);
  }

  public function getInsertId() {
    return $this -> mInsertId;
  }

  protected function checkProtocol() {
    parent::checkProtocol();
    if ($this -> fieldHasChanged('sku_briefing')) {
      $lSub = 'SKU Briefing changed';
      $this -> addHistory(htComment, $lSub);
      $lDat = new CJob_Sku_Dat();
      $lDat -> load($this -> mJobId);
      $lMsg = array('subject' => $lSub);
      $lEve = new CJob_Event(1, $lDat, $lMsg);
      $lEve -> execute();
    }
  }

  public static function reportDraft($aSKUID, $aProID) {
    $lSKUID = $aSKUID;
    $lProID = $aProID;

    $lAdd = array('sku' => array('src' => 'pro', 'proid' => $lProID));
    $lHis = new CJob_His('sku', $lSKUID);
    $lHis -> add(htStatus, 'SKU created', '', $lAdd);
  }

  protected function beforePost($aNew = FALSE) {
    $this -> setKeyword();
  }

  protected function setKeyword() {
    $lArr = CCor_Cfg::get('job-sku.keyw');
    $lRet = '';
    if ($this -> hasValues($lArr)) {
      foreach ($lArr as $lAlias) {
        $lVal = trim($this -> getVal($lAlias));
        $lRet = cat($lRet, $lVal, ' ');
      }
      $this -> mUpd['stichw'] = $lRet;
      $this -> setVal('stichw', $lRet);
    }
  }

}