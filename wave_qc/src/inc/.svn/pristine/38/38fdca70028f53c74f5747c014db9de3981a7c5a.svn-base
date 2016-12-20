<?php
class CInc_Eve_Mod extends CCor_Mod_Table {

  public function __construct() {
    parent::__construct('al_eve','id,mand');

    $this -> addField(fie('id'));
    $this -> addField(fie('mand'));
    $this -> addField(fie('typ'));
    $this -> mAvailLang = CCor_Res::get('languages');
    foreach ($this -> mAvailLang as $lLang => $lName) {
      $this -> addField(fie('name_'.$lLang));
    }
  }

  protected function beforePost($aNew = FALSE) {
    if ($aNew) {
      $this -> setVal('mand', MID);
    }
  }

  public function insert() {
    $lSql = 'INSERT INTO '.$this -> mTbl.' SET ';

    foreach ($this -> mVal as $lKey => $lVal) {
      if (!empty($lVal)) {
        $lSql.= $lKey.'="'.addslashes($lVal).'",';
      }
    }
    $lSql = strip($lSql, 1);

    $lQry = new CCor_Qry();
    $lRet = $lQry -> query($lSql);
    if ($lRet) {
      $this -> mInsertId = $lQry -> getInsertId();
    } else {
      $this -> mInsertId = NULL;
    }
    return $this->mInsertId;
  }

  public function setInfos($aId, $aInfos) {
    $lId = intval($aId);
    $lSql = 'DELETE FROM al_eve_infos WHERE eve_id='.$lId;
    $lQry = new CCor_Qry($lSql);
    if (!empty($aInfos))
    foreach ($aInfos as $lKey => $lVal) {
      $lSql = 'INSERT INTO al_eve_infos SET eve_id='.$lId.',';
      $lSql.= 'alias='.esc($lKey).',';
      $lSql.= 'val='.esc($lVal).';';
      $lQry ->query($lSql);
    }
  }

  public function copyActions($aSourceEventId, $aDestinationEventId) {
    $lSrcId = intval($aSourceEventId);
    $lDstId = intval($aDestinationEventId);
    $lQry = new CCor_Qry('SELECT * FROM al_eve_act WHERE eve_id='.$lSrcId);
    foreach ($lQry as $lRow) {
      $lRow['eve_id'] = $lDstId;
      unset($lRow['id']);
      $lSql = 'INSERT INTO al_eve_act SET ';
      foreach ($lRow as $lKey => $lVal) {
        $lSql.= $lKey.'='.esc($lVal).',';
      }
      $lSql = strip($lSql);
      CCor_Qry::exec($lSql);
    }
  }

  public static function clearCache() {
    $lCkey = 'cor_res_eve';
    CCor_Cache::clearStatic($lCkey);
  }

  protected function afterChange() {
    self::clearCache();
  }

}