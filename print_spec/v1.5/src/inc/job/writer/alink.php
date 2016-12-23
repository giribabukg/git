<?php
class CInc_Job_Writer_Alink extends CCor_Obj implements IInc_Job_Writer_Intf {

  public function __construct($aFields, $aSrc = "rep") {
    $this->mFields = $aFields;
  }

  /**
   * Update an existing job
   *
   * @param string $aJobId Id of job to update
   * @param array $aValues Key/Value hash of alias => value for jobfields
   * @return boolean Update successful?
   */

  public function update($aJobId, $aValues) {
    $lQry = new CApi_Alink_Query_UpdateJob($aJobId);
    $this -> dump($aValues);
    foreach ($aValues as $lKey => $lVal) {
      if (isset($this -> mFields[$lKey])) {
        $lFie = $this -> mFields[$lKey];
        $lNat = $lFie['nat'];
        if (!empty($lNat)) {
          $lQry -> addField($lFie['nat'], $lVal);
        }
      }
    }
    return $lQry -> query();
  }

  /**
   * Insert a Job
   *
   * @param array $aValues Key/Value hash of alias => value for jobfields
   * @return string|FALSE Return new JobId on success, FALSE otherwise
   */
  public function insert($aValues, $aAsQuotation = true) {
    $lQry = new CApi_Alink_Query_Insertjob($aAsQuotation);
    foreach ($aValues as $lKey => $lVal) {
     $lFie = $this -> mFields[$lKey];
      $lNat = $lFie['nat'];
      if (!empty($lNat)) {
        $lQry -> addField($lFie['nat'], $lVal);
      }
    }
    $lRes = $lQry -> query();
    return $lRes ? $lRes->getVal('jobid') : FALSE;
  }


  public function copyAnfToJob($aJobId, $aDest = '') {
    $lQry = new CApi_Alink_Query('copyAngToJob');
    $lQry -> addParam('jobid', $aJobId);
    $lQry -> addParam('sid', MAND);
    $lQry -> addParam('ws_src', 0);
    if (!empty($aDest)) {
      $lQry -> addParam('jobid_dest', $aDest);
    }
    $lRes = $lQry -> query();
    $lRes -> getResult();
    $lJid = $lRes -> getVal('jobid');
    return $lJid;
  }
}