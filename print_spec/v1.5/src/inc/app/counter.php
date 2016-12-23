<?php
/**
 *
 * @author Geoffrey Emmans
 *
 */
class CInc_App_Counter extends CCor_Obj {

  /**
   * Mandator ID
   * @var int
   */
  protected $mMid;

  public function __construct() {
    $this -> mMid = MID;
  }

  public function getMid() {
    return $this->mMid;
  }

  public function setMid($aMid) {
    if ($aMid != intval($aMid)) {
      throw new InvalidArgumentException('Invalid MID '.$aMid.', must be an integer');
    }
    $this->mMid = intval($aMid);
    return $this;
  }

  /**
   *
   * @param string $aCode Unique key/namespace for number sequence
   * @param int $aIncrement How much to add with every call to getNextNumber
   */

  public function getNextNumber($aCode, $aIncrement = 1) {
    $lWhere = 'WHERE code='.esc($aCode).' AND mand='.$this->mMid;
    $lSql = 'UPDATE al_numbers SET num=num+'.intval($aIncrement).' '.$lWhere;
    $lQry = new CCor_Qry($lSql);
    $lSql = 'SELECT num FROM al_numbers '.$lWhere;
    $lRet = CCor_Qry::getInt($lSql);
    return $lRet;
  }
  
  public function getNextJobNumber($aCode, $aIncrement = 1) {
    $lWhere = 'WHERE code='.esc($aCode);
    $lSql = 'UPDATE al_numbers SET num=num+'.intval($aIncrement).' '.$lWhere;
    $lQry = new CCor_Qry($lSql);
    $lSql = 'SELECT num FROM al_numbers '.$lWhere;
    $lRet = CCor_Qry::getInt($lSql);
    return $lRet;
  }

}