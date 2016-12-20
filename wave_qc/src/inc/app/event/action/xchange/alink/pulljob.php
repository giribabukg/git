<?php
class CInc_App_Event_Action_Xchange_Alink_Pulljob extends CApp_Event_Action {

  public function execute() {
    $lJob = $this->mContext['job'];
    $lMap = $this->mParams['map'];
    $lJid = $lJob['jobid'];
  
    // get jobid /job ref of third party system
    $lRefField = $this->mParams['ref_jid'];
    $lTheirJid = $lJob[$lRefField];
  
    if (empty($lTheirJid)) {
      return true;
    }
  
    // get field mapping
    $lSql = 'SELECT * FROM al_fie_map_items WHERE map_id='.$lMap;
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $this->mMap[$lRow['alias']] = $lRow['native'];
    }
  
    // init alink client
    $lConfKey = $this->mParams['client'];
    $lClient = new CApi_Alink_Anyclient($lConfKey);
  
    $lQry = new CApi_Alink_Query_Getjobdetails($lTheirJid);
    $lQry->setClient($lClient);
    foreach ($this->mMap as $lAlias => $lNative) {
      $lQry->addField($lAlias, $lNative);
      $lLastAlias = $lAlias;
    }
    $lRes = $lQry->query();
    $lSuccess = false;
    if (false !== $lRes) {
      $lTheirJob = $lQry->getDat();
      if ($lRes && $lTheirJob['jobid'] && $lTheirJob->offsetExists($lLastAlias)) {
        // update our job with the data pulled from other system
        $lSrc = $lJob['src'];
        $lFac = new CJob_Fac($lSrc);
        $lMod = $lFac->getMod($lJid);
        foreach ($lTheirJob as $lAlias => $lValue) {
          if ($lAlias == 'jobid') continue; // jobid is always there, even if not requested
          $lMod->forceVal($lAlias, $lValue);
        }
        $lSuccess = $lMod->update();
      }
    }
    
    // trigger success / error events?
    if ($lSuccess) {
      $lEventOk = $this->mParams['event_ok'];
      $this->trigger($lEventOk);
    } else {
      $lEventError = $this->mParams['event_error'];
      $this->trigger($lEventError);
    }
    return $lSuccess; // quick typecast to boolean
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