<?php
class CInc_Crp_Import extends CCor_Obj {
  
  public function __construct($aCrpId, $aMand = null) {
    $this->mCid = intval($aCrpId);
    $this->mMid = (is_null($aMand)) ? MID : intval($aMand);
  }
  
  public function importXml($aXml) {
    $this->mOkay = true;
    $this->mXml = simplexml_load_string($aXml);
    $this->loadStates();
    $this->loadSteps();
    if (!$this->mOkay) return;
    
    $this->insertStates();
    $this->insertSteps();
  }
  
  protected function loadStates() {
    $lRoot = $this->mXml->states->state;
    $lRet = array();
    foreach ($lRoot as $lRow) {
      $lItm = array();
      foreach($lRow as $lKey => $lVal) {
        $lItm[$lKey] = (string)$lVal;
      }
      $lRet[(string)$lRow->id] = $lItm;
    }
    //var_dump($lRet);
    $this->mStates = $lRet;
  }
  
  protected function loadSteps() {
    $lRoot = $this->mXml->steps->step;
    $lRet = array();
    foreach ($lRoot as $lRow) {
      $lItm = array();
      foreach($lRow as $lKey => $lVal) {
        $lItm[$lKey] = (string)$lVal;
      }
      $lFrom = (string)$lRow->from_id;
      $lTo = (string)$lRow->to_id;
      $lOkay = true;
      if (!isset($this->mStates[$lFrom])) {
        $this->msg('Preset State ID '.$lFrom.' in Step '.$lRow['id'].' not found!', mtUser, mlError);
        $lOkay = false;
      }
      if (!isset($this->mStates[$lTo])) {
        $this->msg('Postset State ID '.$lFrom.' in Step '.$lRow['id'].' not found!', mtUser, mlError);
        $lOkay = false;
      }
      if ($lOkay) {
        $lRet[(string)$lRow->id] = $lItm;
      } else {
        $this->mOkay = false;
      }
    }
    //var_dump($lRet);
    $this->mSteps = $lRet;
  }
  
  protected function insertStates() {
    if (empty($this->mStates)) return;
    $lQry = new CCor_Qry();
    foreach ($this->mStates as $lId => $lRow) {
      unset($lRow['id']);
      $lRow['crp_id'] = $this->mCid;
      $lRow['mand'] = $this->mMid;
      $lSql = 'INSERT INTO al_crp_status SET ';
      foreach ($lRow as $lKey => $lVal) {
        $lSql.= '`'.$lKey.'`='.esc($lVal).',';
      }
      $lSql = strip($lSql);
      $lQry->exec($lSql);
      $lNewId = $lQry->getInsertId();
      $this->mStates[$lId]['new_id'] = $lNewId;
      //echo $lSql.BR;
    }
    //var_dump($this->mStates);
  }
  
  protected function insertSteps() {
    if (empty($this->mSteps)) return;
    $lQry = new CCor_Qry();
    foreach ($this->mSteps as $lId => $lRow) {
      unset($lRow['id']);
      $lRow['crp_id'] = $this->mCid;
      $lRow['mand'] = $this->mMid;
      
      $lFrom = $lRow['from_id'];
      if (!isset($this->mStates[$lFrom])) {
        continue;
      }
      $lRow['from_id'] = $this->mStates[$lFrom]['new_id'];
      
      $lTo = $lRow['to_id'];
      if (!isset($this->mStates[$lTo])) {
        continue;
      }
      $lRow['to_id'] = $this->mStates[$lTo]['new_id'];
      
      $lSql = 'INSERT INTO al_crp_step SET ';
      foreach ($lRow as $lKey => $lVal) {
        $lSql.= '`'.$lKey.'`='.esc($lVal).',';
      }
      $lSql = strip($lSql);
      $lQry->exec($lSql);
      //echo $lSql.BR;
    }
  }
  

}