<?php
class CInc_Api_Alink_Query_Insertjob extends CApi_Alink_Query {

  protected $mUpd;

  public function __construct($aAnf = TRUE) {
    parent::__construct('insertJob');
    $this -> addParam('sid', MAND);
    if ($aAnf) $this -> addParam('ang', 1);
    $this -> mUpd = array();

    $this -> mDefs = CCor_Res::extract('alias', 'native', 'fie');
  }

  public function addField($aNative, $aValue) {
    $this -> dbg('INS ADDING '.$aNative.' = '.$aValue);
    $this -> mUpd[$aNative] = $aValue;
  }

  public function addVal($aAlias, $aValue) {
    $lFie = (isset($this -> mDefs[$aAlias])) ? $this -> mDefs[$aAlias] : $aAlias;
    $this -> addField($lFie, $aValue);
  }

  public function query() {
    $this -> mLoaded = TRUE;
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