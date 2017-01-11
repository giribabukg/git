<?php
/**
 * Queue Data Object
 *
 * Insert a new action into the queue
 *
 * @package    Application
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 */

class CInc_App_Queue extends CCor_Obj {

  public function __construct($aAction) {
    $this -> reset($aAction);
  }

  public function reset($aAction) {
    $this -> mAct = $aAction;
    $this -> mPar = array('mid' => intval(MID));
  }

  public function setParam($aKey, $aVal) {
    $this -> mPar[$aKey] = $aVal;
  }

  public function insert() {
    self::add($this -> mAct, $this -> mPar);
  }

  public static function add($aAction, $aParams = NULL) {
    $lSql = 'INSERT INTO al_sys_queue SET ';
    $lSql.= 'act='.esc($aAction).',';
    $lSql.= 'create_date=NOW()';
    if (!empty($aParams)) {
      if (!isset($aParams['mid'])) {
      $aParams['mid'] = intval(MID);
      }
      $aParams = serialize($aParams);
      $aParams = str_replace(';}s:',';};s:',$aParams);
      $lSql.=',params='.esc($aParams);
    } else {
      $aParams = array();
      $aParams['mid'] = intval(MID);
      $lSql.=',params='.esc(serialize($aParams));
    }
    CCor_Qry::exec($lSql);
  }

}