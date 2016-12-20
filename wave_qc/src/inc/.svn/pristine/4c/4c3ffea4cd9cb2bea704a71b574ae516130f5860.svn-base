<?php
class CInc_Job_Writer_Portal extends CCor_Obj implements IInc_Job_Writer_Intf {

  public function __construct($aFields, $aSrc) {
    $this->mFields = $aFields;
    $this->mSrc = $aSrc;
  }

  /**
   * Update an existing job
   *
   * @param string $aJobId Id of job to update
   * @param array $aValues Key/Value hash of alias => value for jobfields
   * @return boolean Update successful?
   */

  public function update($aJobId, $aValues) {
    $lSql = 'UPDATE `al_job_'.$this -> mSrc.'_'.MID.'` SET ';
    $lRows = array();
    $lVals = array();
    foreach ($aValues as $lAlias => $lValue) {
      $lValues[] = '`'.$lAlias.'` = '.esc($lValue);
    }
    $lSql.=  implode(',', $lValues);
    $lSql.= ' WHERE jobid='.esc($aJobId);
    $lRet = CCor_Qry::exec($lSql);
    return $lRet;
  }

  /**
   * Insert a Job
   *
   * @param array $aValues Key/Value hash of alias => value for jobfields
   * @return string|FALSE Return new JobId on success, FALSE otherwise
   */
  public function insert($aValues, $aAsQuotation = true) {
    $lRet = FALSE;
    $lJobid = $this -> getNewPdbId($this -> mSrc);
    $lSql = 'INSERT INTO `al_job_'.$this -> mSrc.'_'.MID.'`';
    $lRows = array();
    $lVals = array();
    foreach ($aValues as $lAlias => $lValue) {
      $lRows[] = '`'.$lAlias.'`';
      $lVals[] = esc($lValue);
    }
    $lSql.=  '(jobid,jobnr,'.implode(',', $lRows).') VALUES ('.$lJobid.','.$lJobid.','.implode(',', $lVals).');';
    $lRes = CCor_Qry::exec($lSql);
    if ($lRes) $lRet = $lJobid;
    return $lRet;
  }
  
  public static function getNewPdbId($aSrc) {
    $lObj = new CApp_Counter();
    $lDefaultMig = CCor_Cfg::get('wave.global.id', 99);
    $lNum = $lObj -> getNextJobNumber('pdb_jobid');
  
    $lRet = $lDefaultMig.$lNum;
    return $lRet;
  }
  
}