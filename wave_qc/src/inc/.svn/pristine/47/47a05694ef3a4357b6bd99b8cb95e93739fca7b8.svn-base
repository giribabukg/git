<?php
class CInc_Arc_Sec_Mod extends CJob_Mod {

  protected $mJobId;

  public function __construct($aJobId = 0) {
    parent::__construct('sec');

    $this -> mJobId = $aJobId;
    $this -> mInsertId = NULL;

    $lFie = CCor_Res::get('fie');
    foreach ($lFie as $lDef) {
      if (in_array($lDef['alias'], CCor_Cfg::get('arc.edt.fie'))) {
        $this -> addField($lDef);
      }
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
    $lRet = $this -> forceUpdate($this -> mUpd);
    if ($lRet) {
      CJob_Utl_Shadow::reflectUpdate($this -> mSrc, $this -> mJobId, $this -> mUpd);
    }
    return $lRet;
  }

  public function forceUpdate($aArr = array()) {
    if (empty($aArr)) return TRUE;
    $lQry = new CCor_Qry();
    $lSQL = 'UPDATE al_job_arc_'.intval(MID).' SET ';
    foreach ($aArr as $lKey => $lVal) {
      $lFie = $this -> mFie[$lKey];
      $lNat = $lFie['nat'];
      if (!empty($lNat)) {
        $lSQL.= $lKey.'='.esc($lVal).',';
      }
    }
    $lSQL = substr($lSQL, 0, -1);
    $lSQL.= ' WHERE jobid='.esc($this -> mJobId);
    return $lQry -> query($lSQL);
  }

  protected function beforePost($aNew = FALSE) {
    $this -> setKeyword();
  }

  protected function afterPost($aNew) {
    parent::afterPost($aNew);
    if ($aNew) {
    }
  }

  protected function doInsert() {
    return false;
  }

  protected function doDelete($aId) {
    return false;
  }

}