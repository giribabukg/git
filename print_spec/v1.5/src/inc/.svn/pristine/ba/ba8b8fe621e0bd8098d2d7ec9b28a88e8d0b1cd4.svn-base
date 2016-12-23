<?php
class CInc_Job_Writer_Mop extends CCor_Obj implements IInc_Job_Writer_Intf {

  public function __construct($aFields, $aSrc = "rep") {
    $this->mFields = $aFields;
    $this->db = CInc_Job_Writer_Mop_Db::getInstance();
    $this->mDefaultMig = CCor_Cfg::get('mop.companyid', 20);
    $this->mClientId   = CCor_Cfg::get('mop.clientid', MID);
    $this->mMopLibPath = CCor_Cfg::get('mop.library');
    $this->mSynchMig   = array(); // which MIGs need synch triggered at end of request?
    $this->mDoSynch    = CCor_Cfg::get('mop.synch', true);
    $this->mDelayedSynch = CCor_Cfg::get('mop.synch.delayed', false);
  }

  public function __destruct() {
    if (!$this->mDoSynch) return;
    if (!empty($this->mSynchMig)) {
      // will therefore not be called if mDelayedSynch is on 
      require_once $this->mMopLibPath.'MOP/Replication.php';
      MOP_Replication::triggerSync(array_keys($this->mSynchMig));
    }
  }


  /**
   * Update an existing job
   *
   * @param string $aJobId Id of job to update
   * @param array $aValues Key/Value hash of alias => value for jobfields
   * @return boolean Update successful?
   */

  public function update($aJobId, $aValues) {
    $this->mJid = intval($aJobId);
    $lIsJob = $this->isJob($this->mJid);

    $lJobValues = array();
    $lZusValues = array();
    foreach ($aValues as $lKey => $lValue) {
      if (!isset($this -> mFields[$lKey])) continue;
      $lFie = $this -> mFields[$lKey];
      $lNat = strtolower($lFie['nat']);
      if (empty($lNat)) continue;
      if ($this->isJobField($lNat)) {
        $lField = substr(strrchr($lNat, '#'), 1);
        $lJobValues[$lField] = $lValue;
      } else {
        $lField = substr(strrchr($lNat, '#'),1);
        $lZusValues[$lNat] = $lValue;
      }
    }

    if (empty($lJobValues) && empty($lZusValues)) {
      return TRUE;
    }
    
    if (empty($lJobValues['migcompanyid'])) {
      $lMig = $this->getMigFromJob($this->mJid);
      if (!$lMig) {
        $lMig = $this->mDefaultMig;
      }
      $this->mMig = $lMig;
      $lJobValues['migcompanyid'] = $lMig;
    } else {
      $this->mMig = $lJobValues['migcompanyid'];
    }
    
    if ($lIsJob) {
      $lJobValues['sync_web_loc'] = 1;
      $lJobValues['sync_loc_erp'] = 1;
    } 
    //$this->dump($lJobValues);
    $this->updateJobValues($lJobValues);
    if (!empty($lZusValues)) {
      $this->updateZusValues($lZusValues);
    }

    if ( ($lIsJob) && $this->mDoSynch ) {
      $this->addMigToSynch($this->mMig);
    }
    return TRUE;
  }
  
  protected function addMigToSynch($aMig) {
    $lSql = 'INSERT IGNORE INTO al_wdc_synch SET mig='.intval($aMig);
    CCor_Qry::exec($lSql); // this needs to be done in the Wave DB!
    if ($this->mDelayedSynch) {
      #$lSql = 'INSERT IGNORE INTO al_wdc_synch SET mig='.intval($aMig);
      #CCor_Qry::exec($lSql); // this needs to be done in the Wave DB!
    } else {
      $this->mSynchMig[$this->mMig] = 1;
    }
  }

  protected function getMigFromJob($aJobId) {
    $lSql = 'SELECT migcompanyid FROM job WHERE id='.intval($aJobId);
    $lQry = new CCor_Qry($lSql, $this->db);
    $lRet = false;
    if ($lRow = $lQry->getAssoc()) {
      $lRet = $lRow['migcompanyid'];
    }
    return $lRet;
  }

  protected function isJob($aJobId) {
    $lSql = 'SELECT quote2job_flag FROM job WHERE id='.intval($aJobId);
    $lQry = new CCor_Qry($lSql, $this->db);
    $lRet = false;
    if ($lRow = $lQry->getAssoc()) {
      $lRet = (1 == $lRow['quote2job_flag']);
    }
    return $lRet;
  }

  protected function updateJobValues($aValues) {
    $lSql = 'UPDATE job SET ';
    foreach ($aValues as $lKey => $lVal) {
      $lSql.= $lKey.'='.esc($lVal).',';
    }
    $lSql = substr($lSql, 0 ,-1);
    $lSql.= ' WHERE id='.$this->mJid;
    $this->dbg($lSql);
    $this->db->query($lSql);
  }

  protected function updateZusValues($aValues) {

    $lSql = 'SELECT * FROM jobinfo WHERE job_id='.esc($this->mJid);
    $lQry = new CCor_Qry($lSql, $this->db);
    foreach ($lQry as $lRow) {
      $this->dump($lRow->toArray(), 'ZUSVALUES');
      $lOld[$lRow['native']] = $lRow;
    }
    foreach ($aValues as $lKey => $lVal) {
      $lKey = strtolower($lKey);
      if ('zus.' == substr($lKey,0,4)) {
        $lKey = substr($lKey, 4);
      }
      if ('job#zus#' == substr($lKey,0, 8)) {
        $lKey = substr($lKey, 8);
      }
      if ('' == $lVal) {
        if (isset($lOld[$lKey])) {
          $lRowId = $lOld[$lKey]['sid'];
          $lSql = 'INSERT INTO deletions ';
          $lSql.= 'SET table_name="jobinfo",';
          $lSql.= 'data_id='.esc($lRowId).',';
          $lSql.= 'mytime=NOW();';
          $this->dbg('MOP DEL '.$lSql);
          $lQry->query($lSql);

          $lSql = 'INSERT INTO jobinfo ';
          $lSql.= 'SET `value`='.esc($lVal).', ';
          $lSql.= 'native='.esc($lKey).', ';
          $lSql.= 'job_id='.esc($this->mJid).', ';
          $lSql.= 'migcompanyId='.esc($this->mMig).',';
          $lSql.= 'updttime=NOW(), ';
          $lSql.= 'sync_web_loc=1 ';
          $lSql.= 'ON DUPLICATE KEY UPDATE `value`='.esc($lVal).', sync_web_loc=1';
          $lQry->query($lSql);
        }
      } else {
        $lSql = 'INSERT INTO jobinfo ';
        $lSql.= 'SET `value`='.esc($lVal).', ';
        $lSql.= 'native='.esc($lKey).', ';
        $lSql.= 'job_id='.esc($this->mJid).', ';
        $lSql.= 'migcompanyId='.esc($this->mMig).',';
        $lSql.= 'updttime=NOW(), ';
        $lSql.= 'sync_web_loc=1 ';
        $lSql.= 'ON DUPLICATE KEY UPDATE `value`='.esc($lVal).', sync_web_loc=1';
        $this->dbg('MOP UPD '.$lSql);
        $lQry -> query($lSql);
        // Nice : mysql will return 1 affected rows on insert and 2 on update
        $lAffectedRows = $lQry->getAffectedRows();
        if (1 == $lAffectedRows) {
          $lSid = $lQry->getInsertId();
          $lQry->query('UPDATE jobinfo SET id=sid WHERE sid='.esc($lSid));
        }
      }
    }
  }

  private function isJobField($aNative) {
    $aNative = strtolower($aNative);
    if ('job#zus#' == substr($aNative,0,8)) return false;
    return (substr($aNative,0,4) == 'job#');
  }

  /**
   * Insert a Job
   *
   * @param array $aValues Key/Value hash of alias => value for jobfields
   * @return string|FALSE Return new JobId on success, FALSE otherwise
   */
  public function insert($aValues) {

    $lJobValues = array(
        'clientId' => $this->mClientId,
        'sync_web_loc' => 0,
        'sync_loc_erp' => 0,
        #'translationId' => MID
    );
    $lZusValues = array();
    foreach ($aValues as $lKey => $lValue) {
      if (!isset($this -> mFields[$lKey])) continue;
      $lFie = $this -> mFields[$lKey];
      $lNat = $lFie['nat'];
      if (empty($lNat)) continue;
      if ($this->isJobField($lNat)) {
        $lField = substr(strrchr($lNat, '#'), 1);
        $lJobValues[$lField] = $lValue;
      } else {
        $lField = substr(strrchr($lNat, '#'), 1);
        $lZusValues[$lNat] = $lValue;
      }
    }
    if (empty($lJobValues['migcompanyid'])) {
      $this->mMig = $this->mDefaultMig;
      $lJobValues['migcompanyid'] = $this->mDefaultMig;
    } else {
      $this->mMig = $lJobValues['migcompanyid'];
    }

    //$this->dump($lJobValues);
    $this->mJid = $this->insertJobValues($lJobValues);
    if (!empty($lZusValues)) {
      $this->updateZusValues($lZusValues);
    }
    #require_once $this->mMopLibPath.'MOP/Replication.php';
    #MOP_Replication::triggerSync(array($this->mMig));
    return $this->mJid;
  }

  protected function insertJobValues($aValues) {
    #$aValues['auftraggeberId'] = '132113';

    $lSql = 'INSERT INTO job SET ';
    foreach ($aValues as $lKey => $lVal) {
      $lSql.= $lKey.'='.esc($lVal).',';
    }
    $lSql = substr($lSql, 0 ,-1);
    $this->dbg($lSql);
    $this->db->query($lSql);
    $lInsertId = $this->db->getInsertId();
    if ($lInsertId) {
      $lSql = 'UPDATE job SET id=sid WHERE sid='.esc($lInsertId).' LIMIT 1';
      $this->db->query($lSql);
    }
    return $lInsertId;
  }

  public function copyAnfToJob($aJobId) {
    $lSql = 'UPDATE job SET quote2job_flag=1 WHERE id='.intval($aJobId);
    $lQry = new CCor_Qry($lSql, $this->db);
    return $aJobId;
  }

}
