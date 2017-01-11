<?php
class CInc_App_Event_Action_Move_Jobtype extends CApp_Event_Action {

  public $mSrcArr = Array();

  public function execute() {
    $this -> dbg('Move Job Type');

    $lNewSrc = $this -> mParams['move'];
    $lWebStatus = $this -> mParams['status'];

    $lMove = new CJob_Utl_Move();
    $lJob = $this -> mContext['job'];

    // For the move to jobtype the upcoming line is without meaning, for the email to (user|group|role) this means that correct links are created
    $this -> mContext['job']['src'] = $lNewSrc;

    return $lMove -> moveJob($this -> mContext['job'] -> jobid, $this -> mContext['job'] -> src, $lJob, $lNewSrc, $lWebStatus);
  }

  public static function getParamDefs($aType) {
    $lArr = array();
    $lSrcArr = Array(); // job types (codes) from variablr 'all-jobs'
    $lSrcMenuArr = array(); // job types (names) from variablr 'all-jobs'
    $lSrcArr = CCor_Cfg::get('all-jobs'); // array('art', 'rep', 'sec', 'mis', 'adm', 'com', 'tra');
    foreach ($lSrcArr as $lVal) {
      $lSrcMenuArr[$lVal] = lan('job-'.$lVal.'.menu');
    }
    $lFie = fie('move', 'Move to', 'select', $lSrcMenuArr);
    $lArr [] = $lFie;
    $lFie = fie('status', 'At webstatus', 'input');
    $lArr [] = $lFie;
    return $lArr;
  }

  public static function paramToString($aParams) {
    $lRet = '';
    $lSrcArr = Array(); // job types (codes) from variablr 'all-jobs'
    $lSrcMenuArr = array(); // job types (names) from variablr 'all-jobs'
    if (isset($aParams['move'])) {
      $lCopy = $aParams['move'];
      $lSrcArr = CCor_Cfg::get('all-jobs'); // array('art', 'rep', 'sec', 'mis', 'adm', 'com', 'tra');
      foreach ($lSrcArr as $lVal) {
        $lSrcMenuArr[$lVal] = lan('job-'.$lVal.'.menu');
      }
      if (isset($lSrcMenuArr [$lCopy])) {
        $lRet .= 'Move to: '.$lSrcMenuArr[$lCopy];
      } else {
        $lRet .= 'unknown';
      }
    }
    if (isset($aParams['status'])) {
      $lWebStatus = $aParams['status'];
      if (! empty($lWebStatus)) {
        $lRet .= ' at stage ['.$lWebStatus.']';
      } else {
        $lRet .= ' unknown';
      }
    }
    return $lRet;
  }
}