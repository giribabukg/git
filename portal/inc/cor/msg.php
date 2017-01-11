<?php
/**
 * Core: Message
 *
 *  SINGLETON and Subject pattern
 *
 * @package    COR
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 6 $
 * @date $Date: 2012-02-21 09:50:56 +0100 (Tue, 21 Feb 2012) $
 * @author $Author: gemmans $
 */
class CCor_Msg extends CCor_Obj implements ICor_Sub {

  protected $mMsg = array();
  protected $mObs = array();

  private static $mInstance = NULL;

  private function __construct() {
    $this -> mMsg = array();
    $this -> mObs = array();
    $this -> mSes = CCor_Sys::getInstance();
    $lMsg = $this -> mSes['msg'];
    if (!empty($lMsg)) {
      $this -> mMsg = $lMsg;
    }
  }

  private function __clone() {}

  public static function getInstance() {
    if (NULL === self::$mInstance) {
      self::$mInstance = new self();
    }
    return self::$mInstance;
  }

  public function addMsg($aText, $aTyp = mtUser, $aLvl = mlInfo, $aSrc = NULL, $aRef = NULL) {
    $lMsg['txt'] = $aText;
    $lMsg['typ'] = intval($aTyp);
    $lMsg['lvl'] = intval($aLvl);
    $lMsg['src'] = $aSrc;
    $lMsg['ref'] = $aRef;
    $this -> mMsg[] = & $lMsg;
    $this -> mSes['msg'] = $this -> mMsg;
    $this -> dispatch($lMsg);
  }

  public static function add($aText, $aTyp = mtUser, $aLvl = mlInfo, $aSrc = NULL, $aRef = NULL) {
    $lRef = self::getInstance();
    $lRef -> addMsg($aText, $aTyp, $aLvl, $aSrc, $aRef);
  }

  public function subscribe(ICor_Obs $aObserver, $aTyp = mtAll, $aLvl = mlAll) {
    $lObs['obj'] = & $aObserver;
    $lObs['typ'] = intval($aTyp);
    $lObs['lvl'] = intval($aLvl);
    $this -> mObs[] = $lObs;
  }

  public function unsubscribe(ICor_Obs $aObserver) {
    if (empty($this -> mObs)) return;
    foreach ($this -> mObs as $lKey => & $lVal) {
      if ($lVal === $aObserver) {
        unset($this -> mObs[$lKey]);
      }
    }
  }

  private function dispatch($aMsg) {
    if (empty($this -> mObs)) return;
    $lMsgLvl = $aMsg['lvl'];
    $lMsgTyp = $aMsg['typ'];
    foreach ($this -> mObs as $lKey => $lVal) {
      if (!bitSet($lVal['typ'], $lMsgTyp)) {
        continue;
      }
      if (!bitSet($lVal['lvl'], $lMsgLvl)) {
        continue;
      }
      $lObsObj = & $lVal['obj'];
      #echo "<br />Observer:"; print_r($lObsObj);
      $lObsObj -> onEvent($this, $aMsg);
    }
  }

  public function getMsg($aTyp = mtAll, $aLvl = mlAll) {
    $lRet = array();
    if (empty($this -> mMsg)) return $lRet;
    foreach ($this -> mMsg as $lMsg) {
      if (!bitSet($aTyp, $lMsg['typ'])) continue;
      if (!bitSet($aLvl, $lMsg['lvl'])) continue;
      $lRet[] = $lMsg;
    }
    return $lRet;
  }

  public function clear() {
    $this -> mMsg = array();
    $lSys = CCor_Sys::getInstance();
    unset($lSys['msg']);
  }

}