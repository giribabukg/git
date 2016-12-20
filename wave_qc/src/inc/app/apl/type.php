<?php
/**
 * Approval Loop Type
 *
 * Utility class to query the settings of a single APL type
 *
 * @package    Application
 * @subpackage Approval Loop
 * @version $Rev: 1565 $
 * @copyright  Copyright (c) 5Flow GmbH (http://www.5flow.eu)
 * @date $Date: 2013-06-21 12:08:31 +0200 (Fr, 21 Jun 2013) $
 * @author $Author: gemmans $
 */
class CInc_App_Apl_Type extends CCor_Obj {

  const FLAG_INVITED_ONCE  = 1;
  const FLAG_CHANGE_ALL    = 2;
  const FLAG_CHANGE_AFTER  = 4;
  const FLAG_CHANGE_AGAIN  = 8;
  const FLAG_CHANGE_AHEAD  = 16;
  const FLAG_USE_SUBLOOPS  = 32;

  const FLAG_BITMASK       = 63;

  /**
   * Mandator id
   * @var int
   */
  protected $mMid;

  /**
   * Code of the APL type, e.g. apl or apl-ord
   * @var string
   */
  protected $mCode;

  /**
   * Definition of the current APL type
   * @var CCor_Dat
   */
  protected $mDef;

  /**
   * Constructor - set unique code and Mandator ID
   * @param string $aCode The APL Type unique code, e.g. apl-ord for Ordering APL
   * @param int|null $aMid The mandator ID. If empty, will use currend MID
   */
  public function __construct($aCode, $aMid = null) {
    $this->mCode = $aCode;
    $this->mMid  = intval($aMid);
    if (empty($this->mMid)) {
      $this->mMid = MID;
    }
    $this->load();
  }
  
  /**
   * Get a raw field value from the APL Type table row
   * @param string $aKey
   * @param string $aDefault
   * @return string
   */
  public function get($aKey, $aDefault = null) {
    return isset($this->mDef[$aKey]) ? $this->mDef[$aKey] : $aDefault;
  }

  /**
   * Load the APL type definition defined by mid and code
   * If entry is not found, return default definition
   */
  protected function load() {
    $lSql = 'SELECT * FROM al_apl_types WHERE code='.esc($this->mCode);
    $lSql.= ' AND mand='.$this->mMid;
    $lQry = new CCor_Qry($lSql);
    $this->mDef = $lQry->getDat();
    if (!$this->mDef) {
      $this->mDef = $this->getDefault();
    }
  }

  /**
   * Return default APL type definition
   * @return CCor_Dat
   */
  protected function getDefault() {
    $lRet = new CCor_Dat();
    $lRet['mid'] = $this->mMid;
    $lRet['code'] = $this->mCode;
    $lRet['flags'] = $this->getDefaultCaps();
    return $lRet;
  }

  /**
   * Return default flags
   * @return string
   */
  protected function getDefaultCaps() {
    $lRet = self::FLAG_BITMASK;
    $lRet = unsetBit($lRet, self::FLAG_CHANGE_AHEAD);
    $lShowUntilConfirmed = CCor_Cfg::get('job.apl.show.btn.untilconfirm');
    if ($lShowUntilConfirmed) {
      $lRet = unsetBit($lRet, self::FLAG_CHANGE_AFTER);
      $lRet = unsetBit($lRet, self::FLAG_CHANGE_AGAIN);
    }
    return $lRet;
  }
  
  /**
   * Helper for Flag query functions below
   * @param int $aFlag
   * @return boolean
   */
  protected function hasFlag($aFlag) {
  	$lFlags = $this->mDef['flags'];
  	return bitSet($lFlags, $aFlag);
  }

  /**
   * Is a user invited only once? If false, user can be invited multiple times
   * @return boolean
   */
  public function userInvitedOnce() {
    return $this->hasFlag(self::FLAG_INVITED_ONCE);
  }

  /**
   * Will an approval affect states in other countries even if they are not yet active?
   * @return boolean
   */
  public function canChangeAll() {
    return $this->hasFlag(self::FLAG_CHANGE_ALL);
  }

  /**
   * Can the user set his APL state after the APL has passed his position?
   * @return boolean
   */
  public function canChangeAfter() {
    return $this->hasFlag(self::FLAG_CHANGE_AFTER);
  }

  /**
   * Can the user change his APL state if he has already set the state once?
   * @return boolean
   */
  public function canChangeAgain() {
    return $this->hasFlag(self::FLAG_CHANGE_AGAIN);
  }

  /**
   * Can the user set his APL state before the APL has reached his position?
   * @return boolean
   */
  public function canChangeAhead() {
    return $this->hasFlag(self::FLAG_CHANGE_AHEAD);
  }
  
  /**
   * Will the APL use subloops (e.g. per country)?
   * @return boolean
   */
  public function usesSubLoops() {
  	return $this->hasFlag(self::FLAG_USE_SUBLOOPS);
  }

  /**
   * Return the event that should be triggered when all positions are completed
   * @return int|null ID of the event, null if not set
   */
  public function getEventCompleted() {
    return $this->mDef['event_completed'];
  }

}