<?php
class CInc_Api_Alink_Query_Setworkitem extends CApi_Alink_Query {

  public function __construct() {
    parent::__construct('setworkitem');
    $this -> clearParams();
  }

  // possible params :
  // sid,id,[mode],[jobid],[jobnr],[datum],pnr,artnr,
  // zeit,mengeformat,[kst],[hk],[bemerkung],[bezeichnung],[ak]
  
  // BEWARE: id is not the field ID in artkalk, it is actually "kalknr,pos"

  public function clearParams() {
    $this -> mParam  = new CApi_Alink_Param($this -> mMethod);
    $this -> addParam('sid', MAND);
  }

  /**
   * Insert a new time worksheet item
   *
   * @param string $aJobId The job's ID
   * @param string $aPnr Employee number
   * @param string $aArtNr Article number
   * @param string $aTime Time spent
   * @param string $aAmount Amount/format
   * @param string $aComment An optional comment
   * @param string $aDate Date of entry
   */
  public function insert($aJobId, $aPnr, $aArtNr, $aTime = '', $aAmount = '', $aComment = '', $aDate = '') {
    $this -> clearParams();
    $this->addParam('id', '');
    $this->addParam('jobid', $aJobId);
    $this->addParam('pnr', $aPnr);
    $this->addParam('artnr', $aArtNr);
    $this->addParam('zeit', $aTime);
    $this->addParam('mengeformat', $aAmount);
    $this->addParam('bemerkung', $aComment);
    $this->addParam('datum', $aDate);
    return $this->query();
  }

  /**
   * Update an existing time worksheet item
   *
   * @param string $aId The workitem ID
   * @param string $aPnr Employee number
   * @param string $aArtNr Article number
   * @param string $aTime Time spent
   * @param string $aAmount Amount/format
   * @param string $aComment An optional comment
   * @param string $aDate Date of entry
   */
  public function update($aId, $aJobId, $aPnr, $aArtNr, $aTime = '', $aAmount = '', $aComment = '', $aDate = '') {
    $this -> clearParams();
    $this->addParam('id', $aId);
    $this->addParam('mode', 'edit');
    $this->addParam('jobid', $aJobId);
    $this->addParam('pnr', $aPnr);
    $this->addParam('artnr', $aArtNr);
    $this->addParam('zeit', $aTime);
    $this->addParam('mengeformat', $aAmount);
    $this->addParam('bemerkung', $aComment);
    $this->addParam('datum', $aDate);
    return $this->query();
  }
  
  /**
   * Delete an existing time worksheet item
   *
   * @param string $aId The workitem ID
   * @param string $aPnr Employee number
   * @param string $aJid JobId
   */
  public function delete($aId, $aPnr, $aJid) {
    $this->addParam('id',  $aId);
    $this->addParam('jobid', $aJid);
    
    $this->addParam('mode', 'delete');
    $this->addParam('pnr', $aPnr);
    $this->addParam('artnr', '');
    $this->addParam('zeit', '');
    $this->addParam('mengeformat', '');
    return $this->query();
  }

}