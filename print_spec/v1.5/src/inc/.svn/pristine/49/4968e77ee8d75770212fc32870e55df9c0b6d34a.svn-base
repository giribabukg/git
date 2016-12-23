<?php
class CInc_Htm_Select extends CHtm_Tag {

  public function __construct($aName, $aOpt = array(), $aSel = NULL) {
    parent::__construct('select');
    $this -> setAtt('name', $aName);
    $this -> mSel = $aSel;
    $this -> mOpt = array();
    if (!empty($aOpt)) {
      $this -> setOptions($aOpt);
    }
  }

  public function setOpt($aKey, $aVal) {
    $this -> mOpt[$aKey] = $aVal;
  }

  public function setOptions($aArr) {
    $this -> mOpt = array();
    foreach ($aArr as $lKey => $lVal) {
      $this -> setOpt($lKey, $lVal);
    }
  }

  public function getContent() {
    $lRet = parent::getTag();
    
    if (!empty($this -> mOpt))
    foreach ($this -> mOpt as $lKey => $lVal) {
      $lRet.= '<option value="'.$lKey.'"';
      if ($lKey == $this -> mSel) {
        $lRet.= ' selected="selected"';
      }
      $lRet.= '>';
      $lRet.= htm($lVal);
      $lRet.= '</option>'.LF;
    }
    
    $lRet.= parent::getEndTag();
    return $lRet;
  }

}