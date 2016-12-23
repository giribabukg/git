<?php
class CInc_App_Condition_Aplstate extends CApp_Condition_Base {

  protected $mStates = array('open', 'completed', 'not completed', 'closed', 'closed or completed', 'open and not completed');
  protected $mWhich = array('last', 'any', 'all');
  protected $mValues = NULL;

  public function isMet() {
    $lWhich = $this->mParams['which'];
    $lState = $this->mParams['state'];
    $lAPlType = $this->mParams['apl-type'];
    $lFunc = 'check'.$lWhich;
    
    if ($this->hasMethod($lFunc)) {
      return $this->$lFunc($lState, $lAPlType);
    }
    return false;
  }

  public function getValues() {
    return $this -> mValues;
  }

  protected function checkLast($aState, $aAplType = '') {
    $lAplType = (empty($aAplType) || $aAplType == 'all') ? '' : $aAplType;
    
    $lJid = $this->get('jobid');
    $lSql = 'SELECT * FROM al_job_apl_loop ';
    $lSql.= 'WHERE jobid='.esc($lJid).' ';
    $lSql.= (!empty($lAplType)) ? 'AND typ = '.esc($lAplType).' ' : 'AND typ LIKE "apl%" ';
    $lSql.= 'ORDER BY id desc LIMIT 1;';
    $lQry = new CCor_Qry($lSql);
    if ($lRow = $lQry->getDat()) {
      $this -> mValues = $lRow;
      return $this->checkRow($lRow, $aState);
    }
    return false;
  }

  protected function checkAny($aState, $aAplType = '') {
    $lAplType = (empty($aAplType) || $aAplType == 'all') ? '' : $aAplType;
    $lJid = $this->get('jobid');
    $lSql = 'SELECT * FROM al_job_apl_loop ';
    $lSql.= 'WHERE jobid='.esc($lJid).' ';
    $lSql.= (!empty($lAplType)) ? 'AND typ = '.esc($lAplType).' ' : 'AND typ LIKE "apl%" ';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $this -> mValues[] = $lRow;
      if ($this->checkRow($lRow, $aState)) {
        return true;
      }
    }
    return false;
  }

  protected function checkAll($aState, $aAplType = '') {
    $lAplType = (empty($aAplType) || $aAplType == 'all') ? '' : $aAplType;
    $lJid = $this->get('jobid');
    $lSql = 'SELECT * FROM al_job_apl_loop ';
    $lSql.= 'WHERE jobid='.esc($lJid).' ';
    $lSql.= (!empty($lAplType)) ? 'AND typ = '.esc($lAplType).' ' : 'AND typ LIKE "apl%" ';
    $lQry = new CCor_Qry();
    $lRet = false;
    if ($lQry->query($lSql)) {
      foreach ($lQry as $lRow) {
        $lRet = true;
        $this -> mValues[] = $lRow;
        if (!$this->checkRow($lRow, $aState)) {
          return false;
        }
      }
    }
    return $lRet;
  }

  protected function checkRow($aRow, $aState) {
    if ('completed' == $aState) {
      return $aRow['completed'] == 'Y';
    }
    if ('not completed' == $aState) {
      return ($aRow['completed'] == 'N');
    }
    if ('closed or completed' == $aState) {
      return ($aRow['completed'] == 'Y' OR 'closed' == $aRow['status']);
    }
    if ('open and not completed' == $aState) {
      return ($aRow['completed'] == 'N' AND 'open' == $aRow['status']);
    }
    return ($aState == $aRow['status']);
  }
  
  protected function getAplTypes() {
    $lSql = 'SELECT code, name FROM al_apl_types WHERE mand='.MID;
    $lQry = new CCor_Qry($lSql);
    $lAplTypes = array('all'=>'All APL Types');
    foreach ($lQry as $lKey => $lVal) {
      $lAplTypes[$lVal['code']] = $lVal['name'];
    }
    return $lAplTypes;
  }

  public function getSubForm($aPar = NULL) {
    $lPar = toArr($aPar);
    $lFac = new CHtm_Fie_Fac();
    $lFac -> mOld = false;
    $lFac -> mValPrefix = 'par';

    $lRet = '<tr>';
    $lRet.= '<td>'.htm(lan('lib.which')).'</td>';
    $lVal = isset($lPar['which']) ? $lPar['which'] : '';
    $lArr = array();
    foreach ($this->mWhich as $lRowVal) {
      $lArr[$lRowVal] = $lRowVal;
    }
    $lRet.= '<td>';
    $lDef = fie('which', '', 'select', $lArr);
    $lRet.= $lFac->getInput($lDef, $lVal);
    $lRet.= '</td></tr>';
    
    $lRet.= '<tr>';
    $lRet.= '<td>Apl Type</td>';
    $lRet.= '<td>';
    $lVal = isset($lPar['apl-type']) ? $lPar['apl-type'] : '';
    $lDef = fie('apl-type', '', 'select', $this -> getAplTypes());
    $lRet.= $lFac->getInput($lDef, $lVal);
    $lRet.= '</td></tr>';

    $lRet.= '<tr>';
    $lRet.= '<td>'.htm(lan('lib.state')).'</td>';
    $lRet.= '<td>';
    $lVal = isset($lPar['state']) ? $lPar['state'] : '';
    $lArr = array();
    foreach ($this->mStates as $lRowVal) {
      $lArr[$lRowVal] = $lRowVal;
    }
    $lDef = fie('state', '', 'select', $lArr);
    $lRet.= $lFac->getInput($lDef, $lVal);
    $lRet.= '</td></tr>';
    return $lRet;
  }

  public function paramToString() {
    $lRet = '';
    if (!empty($this->mParams)) {
      $lRet = $this->mParams['which'].' APL is '.$this->mParams['state'];
    }
    return htm($lRet);
  }
}