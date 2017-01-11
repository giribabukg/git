<?php
class CInc_Svc_Core_Update extends CSvc_Base {

  protected function doExecute() {
    if (empty(MID)) {
      $this->addLog('Empty MID!');
      return false;
    }

    // reset can be used to pull in fields that were not mapped previously
    $this->mReset = $this->getPar('reset', 0);
    if ($this->mReset) {
      if ('cli' == PHP_SAPI) {
        $this->logMsg('Core_update with reset should only be run manually', mlError);
        return false;
      }
      self::resetLatestRecords();
    }

    try {
      $lRet = $this->doImport();
    } catch (Exception $ex) {
      $this->addLog($ex->getMessage());
      $this->msg($ex->getMessage(), mtApi, mlError);
      $lRet = false;
    }
    return $lRet;
  }

  public static function getLatestRecords() {
    $lSql = 'SELECT id,core_system,service_order_id AS sid FROM al_core_xml ORDER BY id DESC';
    $lQry = new CCor_Qry($lSql);
    $lSvcIds = array();
    foreach ($lQry as $lRow) {
      $lKey = $lRow['core_system'].'_'.$lRow['sid'];
      if (!isset($lSvcIds[$lKey])) {
        $lSvcIds[$lKey] = $lRow['id'];
      }
    }
    return $lSvcIds;
  }

  public static function resetLatestRecords() {
    $lArr = self::getLatestRecords();
    if (empty($lArr)) {
      return;
    }
    $lIds = implode(',', $lArr);
    $lSql = 'UPDATE al_core_xml SET `status`="new" WHERE id IN ('.$lIds.')';
    CCor_Qry::exec($lSql);
  }

  protected function doImport() {
    $lSql = 'SELECT * FROM al_core_xml WHERE mand='.MID.' AND `status`="new" ORDER BY id';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $this->handleRow($lRow);
    }
    return true;
  }

  protected function handleRow($aRow) {
    if ($this->canIgnore($aRow)) {
      if (!$this->mReset) {
        $this->update($aRow, array('status' => 'processed', 'action' => 'ignore'));
      }
      return true;
    }
    if (empty($aRow['jobid'])) {
      $lJob = $this->findJobByServiceOrder($aRow['service_order_id'], $aRow['core_system']);
      if ($lJob) {
        $aRow['src'] = $lJob['src'];
        $aRow['jobid'] = $lJob['jobid'];
      }
    }
    if ($this->canUpdateJob($aRow)) {
      $this->updateJob($aRow);
    } elseif ($this->canInsertJob($aRow)) {
      $this->insertJob($aRow);
    } else {
      if (!$this->mReset) {
        $this->update($aRow, array('status' => 'processed', 'action' => 'ignore'));
      }
    }
  }

  protected function canIgnore($aRow) {
    return false;
  }

  protected function canInsertJob($aRow) {
    if ($this->mReset) {
      return false; // the manual reset will probably not contain all necessary fields in the map for an insert
    }
    $lMap = $this->getPar('map.insert');
    if (empty($lMap)) {
      return false;
    }
    if (!empty($aRow['jobid'])) {
      return false;
    }
    // subject to mand specific override - we may be interested in specific jobs only
    return true;
  }

  protected function canUpdateJob($aRow) {
    if ($this->mReset) {
      if (empty($aRow['action'])) {
        return false; // we do not want to touch jobs not processed by the regular service, this would update the status
      }
    }
    $lMap = $this->getPar('map.update');
    if (empty($lMap)) {
      return false;
    }
    if (!empty($aRow['jobid'])) {
      return true;
    }
    // subject to mand specific override - we may be interested in specific jobs only
    return false;
  }

  protected function insertJob($aRow) {
    $lSrc = $this->getSrc($aRow);
    $lFac = new CJob_Fac($lSrc);

    $lMap = $this->getPar('map.insert');
    if (empty($lMap)) {
      return false;
    }
    $lExtract = new CApi_Core_Map($aRow['xml']);
    $lValues = $lExtract->getMappedValues($lMap);
    $this->beforePost($lValues);
    $this->beforeInsert($lValues);

    $lMod = $lFac->getMod();
    $lMod->forceVal('sales_order_id', $aRow['sales_order_id']);
    $lMod->forceVal('service_order_id', $aRow['service_order_id']);
    if (!empty($lValues)) {
      foreach ($lValues as $lKey => $lVal) {
        $lMod->forceVal($lKey, $lVal);
      }
    }
    if ($lMod->insert()) {
      $lJid = $lMod->getInsertId();
      $this->update($aRow, array('src' => $lSrc, 'jobid' => $lJid, 'status' => 'processed', 'action' => 'insert'));
    } else {
      $this->addLog('Insertion failed');
    }
  }

  protected function updateJob($aRow) {
    $lSrc = $aRow['src'];
    $lJid = $aRow['jobid'];
    if (empty($lSrc) || empty($lJid)) {
      $this->addLog('Incorrect Job reference Src '.$lSrc.'/JobId '.$lJid);
      return;
    }
    $lFac = new CJob_Fac($lSrc);

    $lMap = $this->getPar('map.update');
    if (empty($lMap)) {
      return false;
    }
    $lExtract = new CApi_Core_Map($aRow['xml']);
    $lValues = $lExtract->getMappedValues($lMap);
    $this->beforePost($lValues);
    $this->beforeUpdate($lValues);

    try {
      $lMod = $lFac->getMod($lJid);
      unset($lValues['jobid']); // never ever update that
      unset($lValues['src']); // never ever update that
      if (!empty($lValues)) {
        foreach ($lValues as $lKey => $lVal) {
          $lMod->forceVal($lKey, $lVal);
        }
      }
      if ($lMod->update()) {
        $lUpd = array('src' => $lSrc, 'jobid' => $lJid, 'status' => 'processed');
        if ('insert' != $aRow['action']) { // preserve first insert when using reset
          $lUpd['action'] = 'update';
        }
        $this->update($aRow, $lUpd);
      } else {
        $this->addLog('Update failed');
      }
    } catch (Exception $ex) {
      $this->addLog('Update failed: '.$ex->getMessage());
    }
  }

  protected function beforePost(&$aValues) {
    // do general aggregation or reformating stuff for update and insert in cust/mand here
  }

  protected function beforeInsert(&$aValues) {
    // do insert specific aggregation/reformating stuff in cust/mand here
  }

  protected function beforeUpdate(&$aValues) {
    // do update specific aggregation/reformating stuff in cust/mand here
  }

  protected function getSrc($aRow) {
    if (!empty($aRow['src'])) {
      return $aRow['src'];
    }
    $lDefault = $this->getPar('default.src', 'art');
    // subject to mand specific override
    $lRet = $lDefault;
    return $lRet;
  }

  protected function getJobId($aRow, $aSrc = '') {
    if (!empty($aRow['jobid'])) {
      return $aRow['jobid'];
    }
    if (!empty($aRow['service_order_id'])) {
      $lJob = $this->findJobByServiceOrder($aRow['service_order_id'], $aRow['core_system']);
      return $lJob['jobid'];
    }
    return false;
  }

  protected function findJobByServiceOrder($aServiceOrderId, $aSystem = '') {
    if ($lRet = $this->findJobInImportTable($aServiceOrderId, $aSystem)) {
      return $lRet;
    }
    if ($lRet = $this->findJobInIterator($aServiceOrderId, $aSystem)) {
      return $lRet;
    }
    return false;
  }

  protected function findJobInImportTable($aServiceOrderId, $aSystem = '') {
    // find in al_core_xml import table
    $lSql = 'SELECT src,jobid FROM al_core_xml WHERE service_order_id='.esc($aServiceOrderId);
    $lSql.= ' AND jobid<>""';
    if (!empty($aSystem)) {
      $lSql.= ' AND core_system='.esc($aSystem);
    }
    $lSql.= ' ORDER BY id DESC LIMIT 1';
    $lQry = new CCor_Qry($lSql);
    if ($lRow = $lQry->getDat()) {
      $lRet['src'] = $lRow['src'];
      $lRet['jobid'] = $lRow['jobid'];
      return $lRet;
    }
    return false;
  }

  protected function findJobInIterator($aServiceOrderId, $aSystem = '') {
    // find in all jobs
    $lSvcOrd = substr('000000000000'.intval($aServiceOrderId), -12);
    $lIte = new CCor_TblIte('all');
    $lIte->addCondition('service_order_id', '=', $lSvcOrd);
    $lIte->addField('jobid');
    $lIte->addField('src');
    foreach ($lIte as $lJob) {
      $lSrc = $lJob['src'];
      $lJid = $lJob['jobid'];
      if (!empty($lSrc) && !empty($lJid)) {
        $lRet['src'] = $lSrc;
        $lRet['jobid'] = $lJid;
      }
      return $lRet;
    }
    return false;
  }

  protected function update($aRow, $aUpdate) {
    if (empty($aUpdate)) {
      return;
    }
    $lSql = 'UPDATE al_core_xml SET ';
    foreach ($aUpdate as $lKey => $lVal) {
      $lSql.= '`'.$lKey.'`='.esc($lVal).',';
    }
    if ($this->mReset) {
      $lSql = strip($lSql);
    } else {
      $lSql .= 'processed_time=NOW() ';
    }
    $lSql.= 'WHERE id='.intval($aRow['id']);
    CCor_Qry::exec($lSql);
  }

}
