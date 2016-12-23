<?php
class CInc_Api_Alink_Query_Updatejob extends CApi_Alink_Query {

  protected $mUpd;

  public function __construct($aJobId) {
    parent::__construct('updateJob');
    $this -> addParam('jobid', $aJobId);
    $this -> addParam('recalc', 1);
    $this -> addParam('ExprOnChange', 'webstatus');
    $this -> mUpd = array();
  }

  public function addField($aNative, $aValue) {
    $this -> dbg('UPD ADDING '.$aNative.' = '.$aValue);
    $this -> mUpd[$aNative] = $aValue;
  }

  public function query() {
    $this -> mLoaded = TRUE;
    $this -> addParam('sid', MAND);
    if (empty($this -> mUpd)) {
      return TRUE;
    }
    $lUpd = array();
    foreach ($this -> mUpd as $lKey => $lVal) {
      $lItm = array();
      $lItm['name'] = $lKey;
      $lItm['value'] = $lVal;
      $lUpd[] = $lItm;
    }
    $this -> addParam('fields', $lUpd);

    parent::query();
    return $this -> mResponse;
  }

}