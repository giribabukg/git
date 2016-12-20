<?php
class CInc_App_Condition_Or extends CApp_Condition_Base {

  public function isMet() {
    $lConditions = $this -> mParams;
    if (empty($lConditions)) {
      return FALSE;
    }

    foreach ($lConditions as $lCond) {
      $lField = $lCond['alias'];
      $lOp    = $lCond['op'];
      $lValue = $lCond['val'];
      $lRet = $this -> isValidTerm($lField, $lOp, $lValue);
      if ($lRet) return TRUE;
    }

    return FALSE;
  }

  public function getSubForm($aPar = NULL) {
    $lPar = toArr($aPar);
    $this -> mFac = new CHtm_Fie_Fac();
    $this -> mFac -> mOld = FALSE;

    $lRet = '<tr><td></td><td>';

    $lFirst = TRUE;
    for ($i = 0; $i < self::MAX_LINES; $i++) {
      $this -> mFac -> mValPrefix = 'par['.$i.']';
      $lRow = (isset($lPar[$i])) ? $lPar[$i] : array();
      $lRet.= $this -> getRow($lRow, $lFirst);
      $lFirst = FALSE;
    }
    $lRet.= '</td></tr>';
    return $lRet;
  }

  protected function getRow($aPar, $aIsFirst) {
    $lPar = toArr($aPar);

    $lRet = '<div class="p2" style="float:left; margin-right:1em;">';
    if ($aIsFirst) {
      $lId = getNum('l');
      $lRet.= '<label for="'.$lId.'">'.lan('lib.field').'</label>'.BR;
    }
    $lVal = isset($lPar['alias']) ? $lPar['alias'] : '';
    $lDef = fie('alias', '', 'resselect', array('res'=>'fie', 'key' => 'alias', 'val' => 'name_en'));
    $lRet.= $this -> mFac -> getInput($lDef, $lVal);
    $lRet.= '</div>';


    $lRet.= '<div class="p2" style="float:left; margin-right:1em;">';
    if ($aIsFirst) {
      $lId = getNum('l');
      $lRet.= '<label for="'.$lId.'">'.lan('lib.op').'</label>'.BR;
    }
    $lValue = isset($lPar['op']) ? $lPar['op'] : '';
    $lOps = array();
    foreach ($this -> mOps as $lKey => $lVal) {
      $lOps[$lKey] = lan('lib.'.$lVal);
    }
    $lDef = fie('op', '', 'select', $lOps);
    $lRet.= $this -> mFac -> getInput($lDef, $lValue);
    $lRet.= '</div>';

    $lRet.= '<div class="p2" style="float:left; margin-right:1em;">';
    if ($aIsFirst) {
      $lId = getNum('l');
      $lRet.= '<label for="'.$lId.'">'.lan('lib.value').'</label>'.BR;
    }
    $lVal = isset($lPar['val']) ? $lPar['val'] : '';
    $lDef = fie('val');
    $lRet.= $this -> mFac -> getInput($lDef, $lVal);
    $lRet.= '</div>';

    $lRet.= '<div style="clear:both">';

    return $lRet;
  }

  public function requestToArray($aParams) {
    $this -> dump($aParams);
    $lPar = toArr($aParams);
    $lRet = array();
    foreach ($lPar as $lRow) {
      if (empty($lRow['alias']) && empty($lRow['val'])) continue;
      $lRet[] = $lRow;
    }
    return $lRet;
  }

  public function paramToString() {
    $lArr = array();
    $lRet = '';
    if (!empty($this -> mParams)) {
      foreach ($this -> mParams as $lRow) {
        if (empty($lRow['alias'])) continue;
        $lArr[] = $this -> termToString($lRow);
      }
    }
    if (!empty($lArr)) {
      $lRet = implode(' OR <br />', $lArr);
    }
    return $lRet;
  }

  public function paramToSQL() {
    $lArr = array();
    $lRet = '';
    if (!empty($this -> mParams)) {
      foreach ($this -> mParams as $lRow) {
        if (empty($lRow['alias'])) continue;
        $lArr[] = $this -> termToSQL($lRow);
      }
    }
    if (!empty($lArr)) {
      $lRet = implode(' OR ', $lArr);
    }
    return $lRet;
  }
}