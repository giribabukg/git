<?php
class CInc_Eve_Type_Mod extends CCor_Mod_Table {

  public function __construct() {
    parent::__construct('al_eve_types', 'code,mand');

    $this -> addField(fie('code'));
    $this -> addField(fie('mand'));
    $this -> addField(fie('name'));
    $this -> addField(fie('fields'));
  }

  protected function beforePost($aNew = FALSE) {
    if ($aNew) {
      $this -> setVal('mand', MID);
    }
  }

  protected function doInsert() {
    $lSql = 'INSERT INTO '.$this -> mTbl.' SET ';

    foreach ($this -> mVal as $lKey => $lVal) {
      if (!empty($lVal)) {
        $lSql.= $lKey.'='.esc($lVal).',';
      }
    }
    $lSql = strip($lSql, 1);

    $lQry = new CCor_Qry();
    $lRet = $lQry -> query($lSql);
    return $lRet;
  }

  public function saveFields($aCode, $aFields) {
    $this->setOld('code', $aCode);
    $this->setVal('code', $aCode);
    $this->setOld('mand', MID);
    $this->setVal('mand', MID);
    $this->forceVal('fields', $aFields);
    return $this->update();

  }

  public static function clearCache() {
    $lCkey = 'cor_res_eve_type';
    CCor_Cache::clearStatic($lCkey);
  }

  protected function afterChange() {
    self::clearCache();
  }

}