<?php
class CInc_Health_Runner extends CCor_Obj {

  public function __construct () {
    $this->init();
  }

  protected function init () {
    $this->loadSystems();
    $this->loadServices();
  }

  protected function loadSystems () {
    $this->mSystems = array();
    $lSql = 'SELECT * FROM pf_systems ORDER BY name';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $this->mSystems[$lRow['id']] = $lRow;
    }
  }

  protected function loadServices () {
    $this->mServices = array();
    $this->mSysServices = array();
    $lSql = 'SELECT * FROM pf_services ORDER BY system_id,sort_order';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $this->mServices[$lRow['id']] = $lRow;
      $this->mSysServices[$lRow['system_id']][$lRow['id']] = $lRow;
    }
  }

  protected function loadDependencies () {
  }
  
  protected function insertResult($aRow, $aRes) {
    $lKey = key($aRes);
    
    if ($aRow['last_status'] != $lKey) {
      $lSubject = $aRes[$lKey];
      $lSql = 'INSERT INTO pf_messages SET ';
      $lSql.= 'service_id='.$aRow['id'].',';
      $lSql.= 'msg_state='.esc($lKey).',';
      $lSql.= 'subject='.esc($lSubject).',';
      $lSql.= 'insert_datetime=NOW()';
      CCor_Qry::exec($lSql);
    }
    $lSql = 'UPDATE pf_services SET last_status=' . esc($lKey) . ',last_update=NOW()';
    $lSql .= ' WHERE id=' . esc($aRow['id']);
    CCor_Qry::exec($lSql);
  }

  public function runAll () {
    $lRet = array();
    // this->loadAll();
    foreach ($this->mServices as $lId => $lRow) {
      $lQueryType = $lRow['query_type'];
      if ('local' == $lQueryType) {
        $lRes = $this->runLocal($lRow);
        $lRet[$lId] = $lRes;
        $this->insertResult($lRow, $lRes);
      } else {
        $lQueries[$lRow['system_id']][$lRow['mid']][$lId] = $lRow;
      }
    }
    if (empty($lQueries)) return $lRet;
    foreach ($lQueries as $lSysId => $lMidRows) {
      $lSys = $this->mSystems[$lSysId];
      $lUrl = $lSys['url'];
      foreach ($lMidRows as $lMid => $lRows) {
        $lResults = $this->queryRemote($lUrl, $lMid, $lRows);
        foreach ($lResults as $lId => $lRes) {
          $lService = $this->mServices[$lId];
          $this->insertResult($lService, $lRes);
        }
      }
    }
    return $lRet;
  }
  
  protected function queryRemote($aUrl, $aMid, $aRows) {
    $lRet = array();
    $lQry = array();
    foreach ($aRows as $lId => $lRow) {
      $lItm = array(
          'method' => $lRow['code'],
          'params' => $lRow['params']
      );
      $lQry[$lId] = $lItm;
      
      $lRes = array('skip' => $lRow['name'].' skipped');
      $lRet[$lId] = $lRes;
    }
    $lQryStr = Zend_Json::encode($lQry);
    $lClient = new Zend_Http_Client();
    $lClient ->setUri($aUrl.'/cli.php?act=webservice-health.test&mid='.$aMid);
    
    CCor_Msg::add('Mid '.$aMid.' URL '.$aUrl.': '.$lQryStr, mtUser, mlError);
    $lClient->setRawData($lQryStr, 'text/json');
    $lClient ->setHeaders('Content-Length: '.strlen($lQryStr));
    $lSuccess = false;
    try {
      $lResponse = $lClient->request('POST');
      $lSuccess = $lResponse->isSuccessful();
      CCor_Msg::add('Response2 '.$aMid.' URL '.$aUrl.': '.$lResponse->getMessage(), mtUser, mlError);
    } catch (Exception $ex) {
      CCor_Msg::add('Response '.$aMid.' URL '.$aUrl.': '.$ex->getMessage(), mtUser, mlError);
    }
    if ($lSuccess) {
      $lResp = $lResponse->getBody();
      CCor_Msg::add('Response '.$aMid.' URL '.$aUrl.': '.$lResp, mtUser, mlError);
      #echo $lResp.LF;
      $lArr = Zend_Json::decode($lResp);
      foreach ($lArr as $lId => $lRow) {
        $lRet[$lId] = $lRow;
      }
    }
    return $lRet;
  }

  public function runSystem ($aSystemId) {
    $lRet = array();
    // this->loadAll();
    foreach ($this->mSysServices[$aSystemId] as $lId => $lRow) {
      $lQueryType = $lRow['query_type'];
      if ('local' == $lQueryType) {
        $lRes = $this->runLocal($lRow);
        $lRet[$lId] = $lRes;
        $lKey = key($lRes);
        
        if ($lRow['last_status'] != $lKey) {
          $lSubject = 'Status changed from '.$lRow['last_status'].' to '.$lKey;
          #if ($lKey == 'error') {
            $lSubject = $lRes[$lKey];
          #}
          $lSql = 'INSERT INTO pf_messages SET ';
          $lSql.= 'service_id='.$lId.',';
          $lSql.= 'msg_state='.esc($lKey).',';
          $lSql.= 'subject='.esc($lSubject).',';
          $lSql.= 'insert_datetime=NOW()';
          CCor_Qry::exec($lSql);
        }
        
        $lSql = 'UPDATE pf_services SET last_status=' . esc($lKey) . ',last_update=NOW()';
        $lSql .= ' WHERE id=' . esc($lId);
        CCor_Qry::exec($lSql);
      }
    }
    return $lRet;
  }

  protected function runLocal ($aRow) {
    $lCode = $aRow['code'];
    $lFunc = 'test' . $lCode;
    if ($this->hasMethod($lFunc)) {
      $lRet = $this->$lFunc($aRow);
    } else {
      $lRet = array(
          'skip' => 'Unknown test ' . $lCode);
    }
    return $lRet;
  }

  protected function testUrl ($aRow) {
    $lUrl = $aRow['params'];
    $lOk = true;
    try {
      $lClient = new Zend_Http_Client($lUrl, array('timeout' => 5));
      $lResponse = $lClient->request();
      $lOk = $lResponse->isSuccessful();
    } catch (Exception $ex) {
      $lOk = false;
    }
    
    if ($lOk) {
      $result = array(
          'ok' => 'URL "' . $lUrl . '" successful');
    } else {
      $result = array(
          'error' => 'Request to "' . $lUrl . '" not successful');
    }
    return $result;
  }

  protected function testPing ($aRow) {
    $host = $aRow['params'];
    $lRetVal = 0;
    $lDummy = null;
    $ts = microtime(true);
    $lRes = exec('ping -c 1 -W 3 ' . $host, $lDummy, $lRetVal);
    if (0 == $lRetVal) {
      $micro = microtime(true) - $ts;
      $result = array(
          'ok' => 'Ping host "' . $host . '" successful');
    } else {
      $result = array(
          'error' => 'Ping error: host "' . $host . '" unreacheable');
    }
    return $result;
    
    var_dump($lRes);
    var_dump($lRetVal);
    $timeout = 1;
    /* ICMP ping packet with a pre-calculated checksum */
    $package = "\x08\x00\x7d\x4b\x00\x00\x00\x00PingHost";
    $socket = socket_create(AF_INET, SOCK_RAW, 0);
    socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array(
        'sec' => $timeout,
        'usec' => 0));
    socket_connect($socket, $host, null);
    $ts = microtime(true);
    socket_send($socket, $package, strLen($package), 0);
    if (socket_read($socket, 255)) {
      $micro = microtime(true) - $ts;
      $result = array(
          'ok' => 'Host ' . $host . ' pinged in ' . $micro . ' microseconds');
    } else {
      $result = array(
          'error' => 'Host ' . $host . ' unreacheable');
    }
    socket_close($socket);
    return array(
        $result);
  }

  public function getStates () {
    $lRet = array();
    $lSql = 'SELECT id,last_status,last_update FROM pf_services';
    $lQry = new CCor_Qry($lSql);
    $lToday = date('Y-m-d');
    foreach ($lQry as $lRow) {
      $lItm['last_status'] = $lRow['last_status'];
      $lLastUpdate = $lRow['last_update'];
      if (substr($lLastUpdate,0,10) == $lToday) {
        $lLastUpdate = substr($lLastUpdate, -8);
      }
      $lItm['last_update'] = $lLastUpdate;
      $lRet[$lRow['id']] = $lItm;
    }
    return $lRet;
  }
}