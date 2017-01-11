<?php
class CInc_App_His extends CCor_Obj {

  protected $mSrc;
  protected $mJobId;

  public function __construct($aSrc, $aJobId) {
    $this->setDefaults();
    $this->setVal('src', $aSrc);
    $this->setVal('src_id', $aJobId);
  }

  public function setDefaults() {
    $this->mVal = array();
    $this->setVal('mand', MID);
    $this->setVal('user_id', CCor_Usr::getAuthId());
    $this->setVal('datum', date('Y-m-d H:i:s'));
  }

  public function setVal($aKey, $aValue) {
    $this->mVal[$aKey] = $aValue;
  }

  public function add($aType, $aSubject, $aMsg = '') {
    $this->setVal('typ', intval($aType));
    $this->setVal('subject', $aSubject);
    $this->setVal('msg', $aMsg);

    $lSql = 'INSERT INTO al_job_his SET ';
    foreach ($this->mVal as $lKey => $lVal) {
      $lSql.= $lKey.'='.esc($lVal).',';
    }
    $lSql = strip($lSql).';';
    CCor_Qry::exec($lSql);
  }

}