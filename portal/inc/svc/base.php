<?php
class CInc_Svc_Base extends CCor_Obj {

  public function __construct($aRow) {
    $this -> mRow = $aRow;
    $this -> mId  = intval($aRow['id']);
    $this -> mParam = toArr($aRow['params']);
  }

  protected function getPar($aKey, $aDefault = NULL) {
    return (isset($this -> mParam[$aKey])) ? $this -> mParam[$aKey] : $aDefault;
  }

  public function run() {
    $this -> beforeRun();

    $lRet = FALSE;
    try {
      $lRet = $this -> doExecute();
    } catch (Exception $lExc) {
      $this -> addLog($lExc -> getMessage());
      echo $lExc->getMessage();
    }

    $this -> afterRun($lRet);
    return $lRet;
  }

  protected function doExecute() {
    return TRUE;
  }

  protected function beforeRun() {
    $lSql = 'UPDATE al_sys_svc SET last_run=NOW(),running="Y" WHERE id='.$this -> mId;
    CCor_Qry::exec($lSql);
    $this->progressTick('started');
  }

  protected function afterRun($aOkay = TRUE) {
    $lSql = 'UPDATE al_sys_svc SET ';
    if ($aOkay) {
      $lSql.= 'last_run=NOW(), ';
    }
    $lSql.= 'running="N" WHERE id='.$this -> mId;
    CCor_Qry::exec($lSql);
    if ($aOkay) {
      $this->progressTick('finished');
    } else {
      $this->progressTick('stopped');
    }
  }
  
  protected function progressTick($aAction = '') {
    $lSql = 'UPDATE al_sys_svc SET last_progress=NOW(),';
    $lSql.= 'last_action='.esc($aAction).' ';
    $lSql.= 'WHERE id='.$this -> mId;
    CCor_Qry::exec($lSql);
  }
  
  protected function canContinue() {
    $lSql = 'SELECT running FROM al_sys_svc WHERE id='.$this->mId;
    $lRes = CCor_Qry::getStr($lSql);
    return ($lRes == 'Y');
  }

  public static function addLog($aTxt, $aLvl = mlInfo) {
    $lTxt = '['.date(lan('lib.datetime.long')).'] ';
    if (defined('SVC_RUN')) {
      $lTxt.= '[RUN '.SVC_RUN.'] ';
    }
    if (defined('MID')) {
      $lTxt.= '[MID '.MID.'] ';
    }
    $lTxt.= $aTxt;

    $lSvcDir = CCor_Cfg::get('svc.dir', '');
    $lSvcFileName = CCor_Cfg::get('svc.filename', 'services_');
    $lSvcFileAdd = CCor_Cfg::get('svc.fileadd', 'Y.m');
    $lSvcFileExt = CCor_Cfg::get('svc.fileext', 'txt');

    error_log($lTxt.LF, 3, $lSvcDir.$lSvcFileName.date($lSvcFileAdd).'.'.$lSvcFileExt);
  }
}
