<?php
class CInc_Cor_Cond extends CCor_Obj {

  public function convert($aField, $aValue, $aType = 'string') {
    $lVal = trim($aValue);
    $lFnc = 'cnv'.$aType;
    if ($this -> hasMethod($lFnc)) {
      return $this -> $lFnc($aField, $lVal);
    }
    return $this -> cnvString($aField, $lVal);
  }

  protected function isQuoted($aValue) {
    return (preg_match('/^(\'|").+(\'|")$/', $aValue));
  }

  protected function isList($aValue) {
    return (preg_match('/^.+,.+$/', $aValue));
  }

  protected function isWildchar($aValue) {
    return (preg_match('/^.*(\*|\?).*$/', $aValue));
  }

  protected function replaceWildchar($aValue) {
    return strtr($aValue, array('*' => '%', '?' => '_', '%' => '\%', '_' => '\_'));
  }

  public function test() {
    $lArr[] = '"Dixan"';
    $lArr[] = '"Dixan';
    $lArr[] = 'Dixan"';
    $lArr[] = "'Dixan\"";
    foreach ($lArr as $lVal) {
      $lTxt = ($this -> isQuoted($lVal)) ? 'yes' : 'no';
      echo htm($lVal.' '.$lTxt).BR;
    }
  }

  // helper funcs

  protected function getCond($aField, $aOp, $aValue) {
    $lRet = array();
    $lRet['field'] = $aField;
    $lRet['op']    = $aOp;
    $lRet['value'] = $aValue;
    return array($lRet);
  }

  // some fields may require two conditions like date1 >= field >= date2

  protected function getSingleCond($aField, $aOp, $aValue) {
    $lRet = array();
    $lRet['field'] = $aField;
    $lRet['op']    = $aOp;
    $lRet['value'] = $aValue;
    return $lRet;
  }

  public function eqInt($aField, $aValue) {
    return $this -> getCond($aField, '=', intval($aValue));
  }

  // field type funcs

  public function cnvString($aField, $aValue) {
    if ($this -> isQuoted($aValue)) {
      return $this -> getCond($aField, 'like', substr($aValue,1,-1));
    }
    if ($this -> isList($aValue)) {
      $lLis = explode(',', $aValue);
      $lTmp = '';
      foreach ($lLis as $lVal) {
        $lTmp.= '"'.addslashes(trim($lVal)).'",';
      }
      $lTmp = strip($lTmp);
      return $this -> getCond($aField, 'in', $lTmp);
    }
    if ($this -> isWildchar($aValue)) {
      return $this -> getCond($aField, 'like', $this -> replaceWildchar($aValue));
    }
    return $this -> getCond($aField, 'like', '%'.$this -> replaceWildchar($aValue).'%');
  }

  public function cnvTselect($aField, $aValue) {
    return $this -> getCond($aField, 'like', '%'.$this -> replaceWildchar($aValue).'%');
  }
  
  public function cnvUselect($aField, $aValue) {
    return $this -> eqInt($aField, $aValue);
  }

  public function cnvGselect($aField, $aValue) {
    return $this -> eqInt($aField, $aValue);
  }
  
  /**
   *  Get Search Condition for Typ Date
   *  @param $aField array SearchFields
   *  @param $aValue string Current Value
   *  return Set Condition
   */
  public function cnvDate($aField, $aValue) {
    $lDat = new CCor_Date($aValue);
    $lVal = $lDat -> getFmt('Y-m-d');
    return $this -> getCond($aField, '=', $lVal);
    
  }
  
  public function cnvBoolean($aField, $aValue) {
    $lVal = ($aValue == 'on') ? 'X' : $aValue;
    return $this -> getCond($aField, '=', $lVal);
    
  }

}