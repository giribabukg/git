<?php
/**
 * Additional Persistent User Infos
 *
 * Retrieves and stores arbitrary user information fields, e.g. user credentials
 * for third-party tools etc.
 *
 * @category   Core
 * @package    User
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 */

class CInc_Cor_Usr_Info extends CCor_Dat {

  private $mId;
  private $mLoaded = false;
  private $mInfos;

  public function __construct($aId) {
    $this -> mId = intval($aId);
    $this -> loadInfos();
  }

  private function loadInfos() {
    $this -> mVal = array();
    $lSql = 'SELECT iid,val FROM al_usr_info WHERE uid='.esc($this -> mId);
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $this -> mVal[$lRow['iid']] = $lRow['val'];
    }
    $this -> dump($this -> mVal);
  }

  public function get($aKey, $aStd = NULL) {
    return (isset($this -> mVal[$aKey])) ? $this -> mVal[$aKey] : $aStd;
  }

  public function set($aKey, $aVal) {
    $this -> mVal[$aKey] = $aVal;
    $lSql = 'REPLACE INTO al_usr_info SET ';
    $lSql.= 'uid='.esc($this -> mId).',';
    $lSql.= 'iid='.esc($aKey).',';
    $lSql.= 'val='.esc($aVal).';';
    CCor_Qry::exec($lSql);
  }

}