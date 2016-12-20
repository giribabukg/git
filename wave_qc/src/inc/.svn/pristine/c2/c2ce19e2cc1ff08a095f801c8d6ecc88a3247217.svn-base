<?php
class CInc_Api_Alink_Query_Gettimesheet extends CApi_Alink_Query {

  protected $mSql;
  protected $mCnd;

  public function __construct() {
    parent::__construct('getTimeSheet');
    $this->mSql = CCor_Sqlmig::getInstance();
  }

  public function getKalkNr($aJobId) {
    $lSql = 'SELECT kalknr FROM kalkulat WHERE jobid='.esc($aJobId);
    #echo $lSql.BR;
    $lQry = new CCor_Qry($lSql, $this->mSql);
    if ($lRow = $lQry->getAssoc()) {
      return $lRow['kalknr'];
    }
    return null;
  }

  public function getByJob($aJobId) {
    $lRet = array();
    $lKalkNr = $this->getKalkNr($aJobId);
    if (!$lKalkNr) return array();

    $lSql = 'SELECT * FROM artkalk WHERE kalknr='.$lKalkNr;
    #echo $lSql;
    $lQry = new CCor_Qry($lSql, $this->mSql);
    foreach ($lQry as $lRow) {
      $lDat = new CCor_Dat();
      $lDat->setLoCase(true);
      $lDat -> assign($lRow);
      $lRet[] = $lDat->toArray();
    }
    return $lRet;
  }

  /**
   * Get the timesheet record for one job
   *
   * @param string $aPnr Employee number
   * @param string $aDate MySQL formatted date
   * @param string $aJobId JobID of Networker job if necessary
   */
  public function get($aPnr = null, $aDate = null, $aDate2 = null, $aJobId = null) {
    $lRet = array();

    $lSql = 'SELECT ak.id,ak.kalknr,ak.datum,ak.pnr,ak.pos,ak.artnr,ak.bezeichnung,ak.zeit,ak.bemerkung,ak.menge,job.jobid,job.stichw1 ';
    $lSql.= 'FROM artkalk ak, auftrag job WHERE ak.typ="S" AND job.kalk=ak.kalknr ';

    //apply filters
    if (!is_null($aPnr)) {
      $lSql.= 'AND pnr='.esc($aPnr).' ';
    }
    if (!is_null($aDate)) {
      if (is_null($aDate2)) { 
        $lSql.= 'AND datum='.esc($aDate).' ';
      } else {
        $lSql.= 'AND datum BETWEEN '.esc($aDate).' AND '.esc($aDate2).' ';
        
      }
    }
    if (!is_null($aJobId)) {
      $lKalkNr = $this->getKalkNr($aJobId);
      if (!$lKalkNr) return $lRet;
      $lSql.= 'AND kalknr='.esc($lKalkNr).' ';
    }
    $lSql.= 'ORDER BY ak.datum,job.jobid,ak.pos ';
    #echo $lSql.BR;

    // get result
    $lQry = new CCor_Qry($lSql, $this->mSql);
    foreach ($lQry as $lRow) {
      $lDat = new CCor_Dat();
      $lDat->setLoCase(true);
      $lDat -> assign($lRow);
      $lRet[] = $lDat->toArray();
    }
    return $lRet;
  }
  
  public function getByKalkPos($aKalkNr, $aPos) {
    $lRet = null;
    $lKalkNr = intval($aKalkNr);
    $lPos = intval($aPos);
    if (!$lKalkNr) return array();
  
    $lSql = 'SELECT * FROM artkalk WHERE kalknr='.$lKalkNr;
    $lSql.= ' AND pos='.$lPos;
    //echo $lSql;
    $lQry = new CCor_Qry($lSql, $this->mSql);
    if ($lRow = $lQry->getAssoc()) {
      $lDat = new CCor_Dat();
      $lDat->setLoCase(true);
      $lDat -> assign($lRow);
      $lRet = $lDat->toArray();
    }
    return $lRet;
  }
  

}