<?php
/**
 * Core: Systemlogger
 *
 *  Description
 *
 * @package    COR
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 10245 $
 * @date $Date: 2015-09-03 15:18:50 +0200 (Thu, 03 Sep 2015) $
 * @author $Author: pdohmen $
 */
class CCor_Log extends CCor_Obj implements ICor_Obs {

  public function __construct() {
    $this -> sub(mtUser);
    $this -> sub(mtDebug);
    $this -> sub(mtPhp);
    $this -> sub(mtSql);
    $this -> sub(mtApi);
    $this -> sub(mtAdmin);
  }

  protected function sub($aType) {
    ob_start();
    $lLvl = CCor_Cfg::get('msg.log.mt'.$aType);
    ob_end_clean();
    if (mlNone == $lLvl) {
      return;
    }
    $lMsg = CCor_Msg::getInstance();
    $lMsg -> subscribe($this, $aType, $lLvl);

  }

  public function log($lMsg, $lLvl, $lTyp) {
    $lUid = CCor_Usr::getAuthId();
    if (false !== $lUid) {
      $lDb = CCor_Sql::getInstance();
      $lSql = 'INSERT INTO al_sys_log SET datum=NULL,'
      .'uid='.$lUid.',typ='.$lTyp.',lvl='.$lLvl.','
      .'msg="'.addslashes($lMsg).'",'
      .'act="'.addslashes($_REQUEST['act']).'";';
      $lDb -> dbgQuery($lSql);
    }
  }
  public function onEvent(ICor_Sub $aSender, $aInfo = NULL) {
    $lUid = CCor_Usr::getAuthId();
    if (false !== $lUid) {
      $lDb = CCor_Sql::getInstance();
      $lSql = 'INSERT INTO al_sys_log SET datum=NULL,'
      .'uid='.$lUid.',typ='.$aInfo['typ'].',lvl='.$aInfo['lvl'].','
      .'msg="'.addslashes($aInfo['txt']).'",'
      .'act="'.addslashes($_REQUEST['act']).'";';
      $lDb -> dbgQuery($lSql);

    }
  }

  public function onClickTracker($aUrl, $aJobid, $aUid=NULL) {
    $lUid = (is_null($aUid)) ? CCor_Usr::getAuthId() : $aUid;
    $lSql = 'INSERT INTO al_links_log SET uid='.$lUid.", ";
    $lSql.= 'mand='.MID.", ";
    $lSql.= 'jobid='.esc($aJobid).', ';
    $lSql.= 'act='.esc($_REQUEST['act']).', ';
    $lSql.= 'session_id='.esc($_COOKIE['PHPSESSID']).", ";
    $lSql.= 'url='.esc($aUrl);
    CCor_Qry::exec($lSql);
  }

}