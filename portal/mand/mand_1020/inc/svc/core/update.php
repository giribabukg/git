<?php
class CSvc_Core_Update extends CCust_Svc_Core_Update {

  protected function handleRow($aRow) {
    if ($this->canIgnore($aRow)) {
      $this->update($aRow, array('status' => 'processed', 'action' => 'ignore'));
      return true;
    }
    unset($aRow['jobid']);
    unset($aRow['src']);

    $lJob = $this->findJobByServiceOrder($aRow['service_order_id'], $aRow['core_system']);
    if ($lJob) {
      $aRow['src'] = $lJob['src'];
      $aRow['jobid'] = $lJob['jobid'];
    }
    if ($this->canUpdateJob($aRow)) {
      $this->updateJob($aRow);
    } elseif ($this->canInsertJob($aRow)) {
      $this->insertJob($aRow);
    } else {
      $this->update($aRow, array('status' => 'processed', 'action' => 'ignore'));
    }
  }

  protected function findJobByServiceOrder($aServiceOrderId, $aSystem = '') {
    // find in all jobs
    $lSvcOrd = substr('000000000000'.intval($aServiceOrderId), -12);
    $lSql = 'SELECT jobid FROM al_job_art_1020 WHERE service_order_id ='.esc($lSvcOrd);
    $lJid = CCor_Qry::getInt($lSql);
    if (false == $lJid) return false;
    $lRet['src'] = 'art';
    $lRet['jobid'] = $lJid;
    return $lRet;
  }

  protected function beforePost(&$aValues) {
    // populate csf search field
    $lRes = array();
    if (isset($aValues['csf1_value'])) {
      for ($i = 1; $i <= 21; $i++) {
        $lField = 'csf' . $i . '_value';
        $lVal = isset($aValues[$lField]) ? trim($aValues[$lField]) : '';
        if (!empty($lVal)) {
          $lRes[] = $lVal;
        }
      }
      $lRes = array_unique($lRes);
      $aValues['csf_surch_field'] = implode(', ', $lRes);
    }

    // populate code search field
    if (isset($aValues['code1'])) {
      $lRes = array();
      for ($i = 1; $i <= 6; $i++) {
        $lField = 'code' . $i;
        $lVal = isset($aValues[$lField]) ? trim($aValues[$lField]) : '';
        if (!empty($lVal)) {
          $lRes[] = $lVal;
        }
      }
      $lRes = array_unique($lRes);
      $aValues['code_search_field'] = implode(', ', $lRes);
    }

    // populate cliche number search field
    if (isset($aValues['cliche_number_01'])) {
      $lRes = array();
      for ($i = 1; $i <= 20; $i++) {
        $lField = 'cliche_number_' . substr('0'.$i, -2);
        $lVal = isset($aValues[$lField]) ? trim($aValues[$lField]) : '';
        if (!empty($lVal)) {
          $lRes[] = $lVal;
        }
      }
      $lRes = array_unique($lRes);
      $aValues['cliche_number_search'] = implode(', ', $lRes);
    }

    // strip prefix and leading zeros from party_id fields
    $lArr = explode(',', 'soldto_party_id,shipto_party_id,bill_to_party_id,printer_id');
    foreach ($lArr as $lField) {
      if (!isset($aValues[$lField])) {
        continue;
      }
      $lVal = $aValues[$lField];
      if (strlen($lVal) == 18) {
        $aValues[$lField] = intval(substr($lVal,2));
      }
    }
  }

  protected function beforeInsert(&$aValues) {
    // do insert specific aggregation/reformating stuff in cust/mand here
  }

  protected function beforeUpdate(&$aValues) {
    // do update specific aggregation/reformating stuff in cust/mand here
  }

}
