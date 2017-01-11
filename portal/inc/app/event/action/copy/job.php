<?php
class CInc_App_Event_Action_Copy_Job extends CApp_Event_Action {
  public $mSrcArr = Array();
  public function execute() {
    $this -> dbg('Copy Job');
    return TRUE;
  }

  public static function getParamDefs($aType) {
    $lArr = array();
    $lSrcArr = Array(); // Jobarts from konfiguration variable 'all-jobs'
    $lSrcMenuArr = array(); // Translate Jobarts.
     
    $lSrcArr = CCor_Cfg::get('all-jobs'); //array('art', 'rep', 'sec', 'mis', 'adm', 'com', 'tra');
    foreach ($lSrcArr as $lVal){
      $lSrcMenuArr[$lVal]= lan('job-'.$lVal.'.menu');
    }
    $lFie = fie('copy', 'Copy to', 'select', $lSrcMenuArr);
   
    $lArr[] = $lFie;
    return $lArr;
  }

  public static function paramToString($aParams) {
    $lRet = '';
    $lSrcArr = Array(); // Jobarts from konfiguration variable 'all-jobs'
    $lSrcMenuArr = array(); // Translate Jobarts.
    
    
    if (isset($aParams['copy'])) {
      $lCopy = $aParams['copy'];
      //$lArr = CCor_Res::get('htb', 'js');
      $lSrcArr = CCor_Cfg::get('all-jobs'); //array('art', 'rep', 'sec', 'mis', 'adm', 'com', 'tra');
      foreach ($lSrcArr as $lVal){
        $lSrcMenuArr[$lVal]= lan('job-'.$lVal.'.menu');
      }
      
      if (isset($lSrcMenuArr[$lCopy])){
        $lRet.= 'Copy to: '.$lSrcMenuArr[$lCopy];
      } else {
       $lRet.= 'unknown';
      }
    }
  
    return $lRet;
  }

}