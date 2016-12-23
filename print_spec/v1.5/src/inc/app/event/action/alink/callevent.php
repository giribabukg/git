<?php
class CInc_App_Event_Action_Alink_Callevent extends CApp_Event_Action {

  public function execute() {
    $lJob = $this->mContext['job'];
    $lJid = $lJob->getId();
    $lSrc = $lJob->getSrc();
    
    $lEvent = $this->mParams['event'];
    $lParam = $this->mParams['addparam'];
    
    $lQry = new CApi_Alink_Query_Callevent($lJid, $lEvent, $lParam);
    return $lQry->query();
  }

  public static function getParamDefs($aType) {
    $lArr = array();
    $lArr[] = fie('event', 'Event Name');
    $lArr[] = fie('addparam', 'Parameter');
    return $lArr;
  }

  public static function paramToString($aParams) {
    $lRet = array();
    if (!empty($aParams['event'])) {
      $lRet[] = 'Event '.$aParams['event'];
    }
    if (!empty($aParams['addparam'])) {
      $lRet[] = 'Parameter '.$aParams['addparam'];
    }
    
    return implode(', ', $lRet);
  }

}