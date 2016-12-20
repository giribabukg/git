<?php
class CInc_App_Condition_Aplstates extends CApp_Condition_Base {

  protected $mWhich = array('last', 'any', 'all');
  
  protected $mStates = array(
      CApp_Apl_Loop::APL_STATE_APPROVED => 'approved (green)',
      CApp_Apl_Loop::APL_STATE_UNKNOWN => 'unset',
      CApp_Apl_Loop::APL_STATE_FORWARD => 'forward',
      
      CApp_Apl_Loop::APL_STATE_CONDITIONAL => 'conditional (orange)',
      CApp_Apl_Loop::APL_STATE_AMENDMENT => 'amendment (red)',
      CApp_Apl_Loop::APL_STATE_BREAK => 'break (black)'
  );

  public function isMet() {
    $lAplType = $this->mParams['apl-type'];
    $lWhich = $this->mParams['which'];
    $lFunc = 'check'.$lWhich;
    
    if ($this->hasMethod($lFunc)) {
      return $this->$lFunc($lAplType);
    }
    return false;
  }

  protected function checkLast($aAplType = '') {
    $lAplType = (empty($aAplType) || $aAplType == 'all') ? '' : $aAplType;
    
    $lJid = $this->get('jobid');
    $lSql = 'SELECT * FROM al_job_apl_loop ';
    $lSql.= 'WHERE jobid='.esc($lJid).' ';
    $lSql.= (!empty($lAplType)) ? 'AND typ = '.esc($lAplType).' ' : 'AND typ LIKE "apl%" ';
    $lSql.= 'ORDER BY id desc LIMIT 1;';
    $lQry = new CCor_Qry($lSql);
    if ($lRow = $lQry->getDat()) {
      return $this->checkLoop($lRow['id']);
    }
    return false;
  }

  protected function checkAny($aAplType = '') {
    $lAplType = (empty($aAplType) || $aAplType == 'all') ? '' : $aAplType;
    $lJid = $this->get('jobid');
    $lSql = 'SELECT id FROM al_job_apl_loop ';
    $lSql.= 'WHERE jobid='.esc($lJid).' ';
    $lSql.= (!empty($lAplType)) ? 'AND typ = '.esc($lAplType).' ' : 'AND typ LIKE "apl%" ';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      if ($this->checkLoop($lRow['id'])) {
        return true;
      }
    }
    return false;
  }

  protected function checkAll($aAplType = '') {
    $lAplType = (empty($aAplType) || $aAplType == 'all') ? '' : $aAplType;
    $lJid = $this->get('jobid');
    $lSql = 'SELECT id FROM al_job_apl_loop ';
    $lSql.= 'WHERE jobid='.esc($lJid).' ';
    $lSql.= (!empty($lAplType)) ? 'AND typ = '.esc($lAplType).' ' : 'AND typ LIKE "apl%" ';
    $lQry = new CCor_Qry();
    $lRet = false;
    if ($lQry->query($lSql)) {
      foreach ($lQry as $lRow) {
        $lRet = true;
        if (!$this->checkLoop($lRow['id'])) {
          return false;
        }
      }
    }
    return $lRet;
  }
  
  public function getStateCount($aLoopId) {
    $lSql = 'SELECT prefix,sub_loop,status,position,gru_id FROM al_job_apl_states ';
    $lSql.= 'WHERE loop_id='.intval($aLoopId).' ';
    $lSql.= 'AND inv="Y" ';
    $lSql.= 'AND del="N" ';
    $lSql.= 'AND done<>"-" ';
    $lSql.= 'ORDER BY prefix,sub_loop,position';
  
    // init to zero
    $lStates = array();
    foreach ($this->mStates as $lKey => $lName) {
      $lStates[intval($lKey)] = 0;
    }
    $lRows = array();
  
    // ignore previous subloops
    $lOldPrefix = '';
    $lOldSub = 0;
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lPrefix = $lRow['prefix'];
      $lSub = $lRow['sub_loop'];
      if (($lOldSub != $lSub) && ($lPrefix == $lOldPrefix)) {
        unset($lRows[$lPrefix]);
      }
      $lRows[$lPrefix][] = $lRow;
      $lOldPrefix = $lPrefix;
      $lOldSub = $lSub;
    }
  
    // count apl states
    $lGruZero = array();
    foreach ($lRows as $lPref => $lStateRows) {
      foreach ($lStateRows as $lRow) {
        $lStatus = intval($lRow['status']);
        $lGid = $lRow['gru_id'];
        $lPos = $lRow['pos'];
        if ((0 == $lStatus) && !empty($lGid)) {
          $lGruZero[$lGid.'.'.$lPos] = 1;
        } else {
          $lStates[$lStatus]++;
        }
      }
    }
    $lStates[0] += count($lGruZero);
    return $lStates;
  }
  
  protected function checkLoop($aLoopId) {
    $lStates = $this->getStateCount($aLoopId);
    //$this->dump($lStates, "STATES FOR LOOP $aLoopId");
    $lRet = true;
    foreach ($this->mStates as $lKey => $lName) {
      $lCur = (isset($lStates[$lKey])) ? $lStates[$lKey] : 0; // amount of rows in this apl having this state (red, orange, green etc.) 
      $lPar = (isset($this->mParams[$lKey])) ? $this->mParams[$lKey] : array(); // get entered min/max numbers for this state
      $lMin = (isset($lPar['min'])) ? $lPar['min'] : '';
      if (!empty($lMin)) { // zero and empty string are ignored
        if ($lCur < $lMin) {
          $this->dbg('Only '.$lCur.' of '.$lName.' (min '.$lMin.')');
          //$lRet = false; // use this instead if you want to debug all condition lines
          return false; // join all conditions with AND, immediately return false if any min is not satisfied
        }
      }
      $lMax = (isset($lPar['max'])) ? $lPar['max'] : '';
      if ('' == $lMax) { // zero is NOT ignored, only empty string
        continue;
      }
      if ($lCur > $lMax) {
        $this->dbg($lCur.' of '.$lName.' (max '.$lMax.')');
        //$lRet = false;
        return false;
      }
    }
    return $lRet;
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
    $lRet.= '<td style="vertical-align:top">Loop has</td>';
    
    $lRet.= '<td>';
    $lRet.= '<table cellpadding="2">';
    $lRet.= '<tr><td class="b">min</td><td class="b">max</td><td class="b">of state</td></tr>';
    $lAtt = array('class' => 'imp w50 ar');
    $lVal = '';
    foreach ($this->mStates as $lKey => $lName) {
      $lRow = (isset($lPar[$lKey])) ? $lPar[$lKey] : array(); 
      $lFac -> mValPrefix = 'par['.$lKey.']';
      $lRet.= '<tr>';
      $lRet.= '<td>';
      $lVal = (empty($lRow['min'])) ? '' : $lRow['min']; // min of zero ignored
      $lDef = fie('min', '', 'string', null, $lAtt);
      $lRet.= $lFac->getInput($lDef, $lVal);
      $lRet.= '</td>';
      $lRet.= '<td>';
      $lVal = (isset($lRow['max'])) ? $lRow['max'] : '';
      $lDef = fie('max', '', 'string', null, $lAtt);
      $lRet.= $lFac->getInput($lDef, $lVal);
      $lRet.= '</td>';
      $lRet.= '<td>'.htm($lName).'</td>';
      $lRet.= '</tr>';
    }
    $lRet.= '</table>';
    $lRet.= '</td>';
    $lRet.= '</tr>';
    
    $lRet.= '</tr>';
    return $lRet;
  }

  public function paramToString() {
    $lRet = '';
    if (empty($this->mParams)) {
      return '';
    }
    $lRet = $this->mParams['which'].' APL ';
    if (isset($this->mParams['apl-type'])) {
      $lType = $this->mParams['apl-type'];
      if ($lType != 'all') {
        $lRet.= 'of type '.$lType.' ';
      }
    }
    $lRet.= 'has ';
    $lAdded = false;
    foreach ($this->mStates as $lKey => $lName) {
      $lAdd = false;
      $lPar = (isset($this->mParams[$lKey])) ? $this->mParams[$lKey] : array();
      $lMin = (isset($lPar['min'])) ? $lPar['min'] : '';
      if (!empty($lMin)) { // zero and empty string are ignored
        $lRet.= 'min '.$lMin;
        $lAdd = true;
        $lAdded = true;
      }
      $lMax = (isset($lPar['max'])) ? $lPar['max'] : '';
      if ('' != $lMax) { // zero is NOT ignored, only empty string
        if ($lAdd) {
          $lRet.= '/';
        }
        $lRet.= 'max '.$lMax;
        $lAdd = true;
        $lAdded = true;
      }
      if ($lAdd) {
        $lRet.= ' of '.$lName.', ';
        #$lRet.= ' of '.$lName.' ('.$lKey.'), ';
      }
    }
    if ($lAdded) {
      $lRet = strip($lRet, 2);
    } else {
      $lRet.= 'arbitrary states???';
    }
    return htm($lRet);
  }
}