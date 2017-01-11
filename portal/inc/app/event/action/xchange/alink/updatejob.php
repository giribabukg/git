<?php
class CInc_App_Event_Action_Xchange_Alink_Updatejob extends CApp_Event_Action {
  
  public function execute() {
    $lJob = $this->mContext['job'];
    $lMap = $this->mParams['map'];
    $lJid = $lJob['jobid'];
    
    $lRefField = $this->mParams['ref_jid'];
    $lTheirJid = $lJob[$lRefField];
    
    if (empty($lTheirJid)) {
      return true;
    }
      
    $lSql = 'SELECT * FROM al_fie_map_items WHERE map_id='.$lMap;
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $this->mMap[] = $lRow;
    }
  
    // init alink client and query
    $lConfKey = $this->mParams['client'];
    $lClient = new CApi_Alink_Anyclient($lConfKey);
  
    $lQry = new CApi_Alink_Query_Updatejob($lTheirJid);
    $lQry->setClient($lClient);
    foreach ($this->mMap as $lRow) {
      $lAlias = $lRow['alias'];
      $lNative = $lRow['native'];
      if (!empty($lRow['default_value'])) {
        $lVal = $lRow['default_value'];
      } else {
        $lVal = $lJob[$lAlias];
      }
      $lQry->addField($lNative, $lVal);
    }
    $lSid = MANDATOR;
    $lQry -> addParam('sid', $lSid);
    $lRes = $lQry->query();
    
    $lSuccess = false;
    if ($lRes) {
      $lStatus = $lRes->getVal('errmsg');
      $lSuccess = $lStatus == 'OK';
    }
    
    // trigger success / error events?
    if ($lSuccess) {
      $lEventOk = $this->mParams['event_ok'];
      $this->trigger($lEventOk);
    } else {
      $lEventError = $this->mParams['event_error'];
      $this->trigger($lEventError);
    }
    return $lSuccess; 
  }
  
  protected function trigger($aEventId) {
    $lEve = intval($aEventId);
    if (empty($lEve)) return;
    $lJob = $this->mContext['job'];
    $lEve = new CJob_Event($lEve, $lJob);
    $lEve -> execute();
  }
  
  public static function getParamDefs($aType) {
    $lArr = array();
    $lSql = 'SELECT id,name FROM al_fie_map_master ORDER BY name';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lMap[$lRow['id']] = $lRow['name'];
    }
    $lFie = fie('map', lan('fie-map.menu'), 'select', $lMap);
    $lArr[] = $lFie;
    
    $lFie = fie('client', 'Config Prefix');
    $lArr[] = $lFie;
    
    $lResDef = array('res' => 'fie', 'key' => 'alias', 'val' => 'alias');
    $lFie = fie('ref_jid', 'JobReference Field', 'resselect', $lResDef);
    $lArr[] = $lFie;
    
    $lResDef = array('res' => 'eve', 'key' => 'id', 'val' => 'name_'.LAN);
    $lFie = fie('event_ok', 'Event onSuccess', 'resselect', $lResDef);
    $lArr[] = $lFie;
    
    $lResDef = array('res' => 'eve', 'key' => 'id', 'val' => 'name_'.LAN);
    $lFie = fie('event_error', 'Event onError', 'resselect', $lResDef);
    $lArr[] = $lFie;
  
    return $lArr;
  }
  
  public static function paramToString($aParams) {
    $lRet = '';
    if (isset($aParams['map'])) {
      $lMap = $aParams['map'];
      $lRet.= 'Map '. $lMap;
    } 
    if (!empty($aParams['client'])) {
      $lRet.= ' Config '.$aParams['client'];
    }
    if (!empty($aParams['ref_jid'])) {
      $lRet.= ' JobRef Field '.$aParams['ref_jid'];
    }
    return $lRet;
  }

}