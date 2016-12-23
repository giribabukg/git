<?php
/**
* Can a user access a specified mandator ?
*
* @category   Core
* @package    User
* @copyright  5Flow GmbH
*/

class CInc_Cor_Usr_Mand extends CCor_Obj {

  private $mId;
  private $mAccess;
  private $mLoaded = false;

  public function __construct($aId, $aMandId = NULL) {
    $this->mId = intval($aId);
    if (is_null($aMandId)) {
      $lSql = 'SELECT mand FROM al_usr_mand WHERE uid='.$this->mId;
      $this->mMandId = CCor_Qry::getInt($lSql);
    } else {
      $this->mMandId = $aMandId;
    }
  }

  private function loadAccess() {
    if ($this->mLoaded) return;
    $this->mLoaded = true;
    $this->mAccess = array();

    $lSql = 'SELECT mand FROM al_usr_mand ';
    $lSql.= 'WHERE uid='.$this -> mId.' ';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $this->mAccess[$lRow['mand']] = true;
    }
  }

  public function canAccess($aMid) {
    if (0 == $this->mMandId) return true;
    $this->loadAccess();
    return (!empty($this->mAccess[$aMid]));
  }

}