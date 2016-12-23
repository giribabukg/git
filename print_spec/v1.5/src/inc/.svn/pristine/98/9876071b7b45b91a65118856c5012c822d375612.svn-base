<?php
class CInc_App_Event_Action_Alink_Insertsubjob extends CApp_Event_Action {

  public function execute() {
    $lJob = $this->mContext['job'];
    $lJid = $lJob->getId();
    
    $lType = $this->mParams['type'];
    $lDesc = isset($this->mParams['desc']) ? $this->mParams['desc'] : '';
    $lComm = isset($this->mParams['comment']) ? $this->mParams['comment'] : '';
    
    $lMsg = isset($this->mContext['msg']) ? $this->mContext['msg'] : '';
    $lMsg = isset($lMsg['body']) ? $lMsg['body'] : '';
    
    $lDesc = strtr($lDesc, array('{msg}' => $lMsg));
    $lComm = strtr($lComm, array('{msg}' => $lMsg));
    
    $lQry = new CInc_Api_Alink_Query_Insertak($lJid, $lType, $lDesc, $lComm);
    return $lQry->query();
  }

  public static function getParamDefs($aType) {
    $lArr = array();
    $lArr[] = fie('type', 'Sub Job Type (A,N...)');
    $lArr[] = fie('desc', 'Description');
    $lArr[] = fie('comment', 'Comment');
    return $lArr;
  }

  public static function paramToString($aParams) {
    $lRet = array();
    if (!empty($aParams['type'])) {
      $lRet[] = 'Type '.$aParams['type'];
    }
    if (!empty($aParams['desc'])) {
      $lRet[] = 'Description '.shortStr($aParams['desc'], 20);
    }
    if (!empty($aParams['comment'])) {
      $lRet[] = 'Comment '.shortStr($aParams['comment'], 20);
    }
    return implode(', ', $lRet);
  }

}