<?php
/**
 * Services: Runner
 *
 * SINGLETON
 *
 * @package    SVC
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 14615 $
 * @date $Date: 2016-06-18 12:49:58 +0200 (Sat, 18 Jun 2016) $
 * @author $Author: gemmans $
 */
class CInc_Svc_Runner extends CCor_Obj {

  private static $mInstance;

  private function __construct() {
  }

  private function __clone() {
  }

  public function __destruct() {
    self::$mInstance = NULL;
  }

  public static function getInstance() {
    if (NULL === self::$mInstance) {
      self::$mInstance = new self();
    }
    return self::$mInstance;
  }

  /**
   * Define constant SVC_RUN (autoinc) for better logging
   */
  public static function defineRun() {
    // get a unique number for better logging
    if (!defined('SVC_RUN')) {
      $lCounter = new CApp_Counter();
      $lCounter->setMid(0);
      $lNum = $lCounter->getNextNumber('svc.run');
      define('SVC_RUN', $lNum);
    }
  }

  public static function resetRunning($aSvcId) {
    $lSql = 'UPDATE al_sys_svc SET running="N" WHERE id='.intval($aSvcId);
    $lReset = CCor_Qry::exec($lSql);
    if ($lReset) {
      CSvc_Base::addLog('['.$aSvcId.'] stopped.');
    }
  }

  public function run() {
    self::defineRun();

    $lDat = new CCor_Date();
    $this -> mDow = $lDat -> getDow();
    $this -> mDowFlag = 1 << $this -> mDow;
    CSvc_Base::addLog('------------------------');
    CSvc_Base::addLog(date('D '.lan('lib.datetime.long')));
    CSvc_Base::addLog('------------------------');

    $lSql = 'SELECT * FROM al_sys_svc WHERE 1';
    $lSql.= ' AND mand='.intval(MID);
    $lSql.= ' AND (flags & '.sfActive.')';
    $lSql.= ' ORDER BY pos';

    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $this -> check($lRow);
    }
  }

  private function check($aRow) {
    $lName = '[ID'.$aRow['id'].'] '.'['.$aRow['act'].'] '.$aRow['name'].' ';

    $lFla = intval($aRow['flags']);
    if (!bitSet($lFla, sfActive)) {
      CSvc_Base::addLog($lName.'not active.');
      return;
    }

    if ('Y' == $aRow['running']) {
      CSvc_Base::addLog($lName.'already running.');
      return;
    }

      $lDow = intval($aRow['dow']);
      if (!bitSet($lDow, $this -> mDowFlag)) {
        CSvc_Base::addLog($lName.'not due on a '.date('l').'.');
        return;
      }

      $lTim = date('H:i:s');
      if ($lTim < $aRow['from_time']) {
        CSvc_Base::addLog($lName.'not due before '.$aRow['from_time'].'.');
        return;
      }
      if ($lTim > $aRow['to_time']) {
        CSvc_Base::addLog($lName.'not due after '.$aRow['to_time'].'.');
        return;
      }

      $lLast = $aRow['last_run'];
      $lDat = new CCor_Datetime($lLast);
      $lTick = intval($aRow['tick']);
      $lNow = mktime();
      $lCmp = $lDat -> getTime() + $lTick;
      if ($lNow < $lCmp) {
        CSvc_Base::addLog($lName.'not due yet.');
        return;
      }

    $lCls = 'CSvc_'.ucfirst($aRow['act']);
    if (class_exists($lCls)) {
      CSvc_Base::addLog($lName.'started...');

      $lCls = new $lCls($aRow);
      if ($lCls -> run()) {
        CSvc_Base::addLog($lName.'okay.');
      } else {
        CSvc_Base::addLog($lName.'not okay.');
        $this -> msg('Service '.$aRow['name'].' ['.$aRow['id'].'] reported problem.', mtAdmin, mlError);
      }
    } else {
      $this -> msg('Service class '.$lCls.' not found.', mtAdmin, mlFatal);
      CSvc_Base::addLog($lName.'class not found.');
    }
  }
}
