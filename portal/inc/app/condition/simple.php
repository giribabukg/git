<?php
class CInc_App_Condition_Simple extends CApp_Condition_Base {

  public function isMet() {
    $lField = $this -> mParams['alias'];
    $lOp    = $this -> mParams['op'];
    $lValue = $this -> mParams['val'];

    if (!empty($this -> mParams['value_of_field'])) {
      $lFieldAlias = $this -> mParams['value_of_field'];
      $lValue = $this -> get($lFieldAlias);
    }
    return $this -> isValidTerm($lField, $lOp, $lValue);
  }

  public function getSubForm($aPar = NULL) {
    $lPar = toArr($aPar);
    $lFac = new CHtm_Fie_Fac();
    $lFac -> mOld = false;
    $lFac -> mValPrefix = 'par';

    $lRet = '<tr>';
    $lRet.= '<td>'.htm(lan('lib.field')).'</td>';
    $lVal = isset($lPar['alias']) ? $lPar['alias'] : '';
    $lRet.= '<td>';
    $lDef = fie('alias', '', 'resselect', array('res'=>'fie', 'key' => 'alias', 'val' => 'name_'.LAN));
    $lRet.= $lFac -> getInput($lDef, $lVal);
    $lRet.= '</td></tr>';

    $lRet.= '<tr>';
    $lRet.= '<td>'.htm(lan('lib.op')).'</td>';
    $lValue = isset($lPar['op']) ? $lPar['op'] : '';
    $lRet.= '<td>';

    $lOps = array();
    foreach ($this -> mOps as $lKey => $lVal) {
      $lOps[$lKey] = lan('lib.'.$lVal);
    }
    $lDef = fie('op', '', 'select', $lOps);
    $lRet.= $lFac -> getInput($lDef, $lValue);
    $lRet.= '</td></tr>';

    $lRet.= '<tr>';
    $lRet.= '<td>'.htm(lan('lib.value')).'</td>';
    $lVal = isset($lPar['val']) ? $lPar['val'] : '';
    $lRet.= '<td>';
    $lDef = fie('val', '');
    $lRet.= $lFac -> getInput($lDef, $lVal);
    $lRet.= '</td></tr>';

    $lRet.= '<tr>';
    $lRet.= '<td>Value of Field</td>';
    $lVal = isset($lPar['value_of_field']) ? $lPar['value_of_field'] : '';
    $lRet.= '<td>';
    $lDef = fie('value_of_field', '', 'resselect', array('res'=>'fie', 'key' => 'alias', 'val' => 'name_'.LAN));
    $lRet.= $lFac -> getInput($lDef, $lVal);
    $lRet.= '</td></tr>';

    return $lRet;
  }

  public function paramToString() {
    return $this -> termToString($this -> mParams);
  }

  public function paramToSQL() {
    return $this -> termToSQL($this -> mParams);
  }
}