<?php
class CInc_App_Condition_Complex extends CApp_Condition_Base {

  public function isMet() {
    $this -> mReg = new CApp_Condition_Registry();
    $lConj = $this -> mParams['conjunction'];
    if ('or' == $lConj) {
      return $this -> isMetOr();
    }
    return $this -> isMetAnd();
  }

  protected function isMetOr() {
    $this -> dbg('isMetOr');
    $lPar = $this -> mParams;
    unset($lPar['conjunction']);
    foreach ($lPar as $lCond) {
      $lRet = $this -> isSubConditionValid($lCond['c']);
      if ($lRet) return TRUE;
    }
    return FALSE;
  }

  protected function isMetAnd() {
    $this -> dbg('isMetAnd');
    $lPar = $this -> mParams;
    unset($lPar['conjunction']);
    foreach ($lPar as $lCond) {
      $lRet = $this -> isSubConditionValid($lCond['c']);
      if (!$lRet) return FALSE;
    }
    return TRUE;
  }

  protected function isSubConditionValid($aConditionId) {
    $this -> dbg('Loading condition '.$aConditionId);
    $lCnd = $this -> mReg -> loadFromDb($aConditionId);
    $lCnd -> setContext('data', $this -> getContext('data'));
    return $lCnd -> isMet();
  }

  public function getSubForm($aPar = NULL) {
    $lPar = toArr($aPar);
    $this -> mFac = new CHtm_Fie_Fac();
    $this -> mFac -> mOld = FALSE;

    $lRet = '<tr><td>AND/OR</td><td>';
    $this -> mFac -> mValPrefix = 'par';
    $lVal = (isset($lPar['conjunction'])) ? $lPar['conjunction'] : 'and';
    $lDef = fie('conjunction', '', 'select', array('and' => 'AND', 'or' => 'OR'));
    $lRet.= $this -> mFac -> getInput($lDef, $lVal);
    $lRet.= '</td></tr>';

    $lFirst = TRUE;
    for ($i = 0; $i < self::MAX_LINES; $i++) {
      $this -> mFac -> mValPrefix = 'par['.$i.']';
      $lRow = (isset($lPar[$i])) ? $lPar[$i] : array();
      $lRet.= $this -> getRow($lRow, $i);
      $lFirst = FALSE;
    }
    return $lRet;
  }

  protected function getRow($aPar, $aIndex) {
    $lPar = toArr($aPar);
    $lRet.= '<tr><td class="ar">'.($aIndex+1).'.</td><td>';
    $lVal = isset($lPar['c']) ? $lPar['c'] : '';
    $lDef = fie('c', '', 'resselect', array('res'=>'cond', 'key' => 'id', 'val' => 'name'));
    $lRet.= $this -> mFac -> getInput($lDef, $lVal);
    $lRet.= '</td></tr>';
    return $lRet;
  }

  public function requestToArray($aParams) {
    $lPar = toArr($aParams);
    $this -> dump($lPar, 'PARAM');
    $lRet = array();
    $lRet['conjunction'] = $lPar['conjunction'];
    unset($lPar['conjunction']);
    foreach ($lPar as $lKey => $lRow) {
      if ( empty($lRow['c']) ) continue;
      $lRet[] = $lRow;
    }
    return $lRet;
  }

  public function paramToString() {
    $lArr = array();
    $lRet = '';
    $lPar = $this -> mParams;
    $lConj = (isset($lPar['conjunction'])) ? $lPar['conjunction'] : 'and';
    unset($lPar['conjunction']);

    $lCondRes = CCor_Res::extract('id', 'name', 'cond');

    foreach($lPar as $lCond) {
      $lCid = $lCond['c'];
      $lName = (isset($lCondRes[$lCid])) ? $lCondRes[$lCid] : lan('lib.unknown');
      $lArr[] = $lName;
    }
    if (!empty($lArr)) {
      $lRet = implode(' <b>'.strtoupper($lConj).'</b><br />', $lArr);
    }
    return $lRet;
  }

  public function paramToSQL() {
    $lSupportedTypes = array('simple', 'and', 'or', 'complex');

    $lArr = array();
    $lRet = '';
    $lParams = $this -> mParams;
    $lConjunction = (isset($lParams['conjunction'])) ? $lParams['conjunction'] : 'and';
    unset($lParams['conjunction']);

    $lCondIdToParams = CCor_Res::extract('id', 'params', 'cond');
    $lCondIdToTyp = CCor_Res::extract('id', 'type', 'cond');

    foreach ($lParams as $lRow) {
      $lConditionId = $lRow['c'];
      $lConditionParams = $lCondIdToParams[$lConditionId];
      $lConditionTyp = $lCondIdToTyp[$lConditionId];

      if (in_array($lConditionTyp, $lSupportedTypes)) {
        $lAppConditionRegistry = new CApp_Condition_Registry();
        $lAppConditionRegistryObject = $lAppConditionRegistry -> loadFromDb($lConditionId);
        $lConditionsSQL = $lAppConditionRegistryObject -> paramToSQL();
        $lArr[] = '('.$lConditionsSQL.')';
      }
    }

    if (!empty($lArr)) {
      $lRet = implode(' '.strtoupper($lConjunction).' ', $lArr);
    }

    return $lRet;
  }

  public function getWebstatus() {
    $lSupportedTypes = array('simple', 'and', 'or', 'complex');

    $lArr = array();
    $lRet = '';
    $lConjunction = (isset($this -> mParams['conjunction'])) ? $this -> mParams['conjunction'] : 'and';
    unset($this -> mParams['conjunction']);

    $lCondIdToParams = CCor_Res::extract('id', 'params', 'cond');
    $lCondIdToTyp = CCor_Res::extract('id', 'typ', 'cond');

    foreach ($this -> mParams as $lRow) {
      $lConditionId = $lRow['c'];
      $lConditionParams = (isset($lCondIdToParams[$lConditionId])) ? $lCondIdToParams[$lConditionId] : '1';
      $lConditionTyp = (isset($lCondIdToTyp[$lConditionId])) ? $lCondIdToTyp[$lConditionId] : NULL;

      if (in_array($lConditionTyp, $lSupportedTypes)) {
        $lAppConditionRegistry = new CApp_Condition_Registry();
        $lAppConditionRegistryObject = $lAppConditionRegistry -> loadFromDb($lConditionId);
        $lAppConditionRegistryParams = $lAppConditionRegistryObject -> getParams();

        return $this -> getWebStatus($lAppConditionRegistryParams);
      }
    }

    return FALSE;
  }
}