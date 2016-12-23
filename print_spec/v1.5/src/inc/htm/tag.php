<?php
class CInc_Htm_Tag extends CCor_Ren {

  public function __construct($aTag) {
    $this -> mTag = $aTag;
    $this -> mAtt = array();
    $this -> mCnt = '';
  }

  public function setAtt($aKey, $aVal) {
    $this -> mAtt[$aKey] = $aVal;
  }

  public function addAtt($aKey, $aVal, $aSep = ' ') {
    $lVal = $this -> getAtt($aKey);
    if (NULL !== $lVal) {
      $lVal.= $aSep;
    }
    $lVal.= $aVal;
    $this -> setAtt($aKey, $lVal);
  }

  public function addAttributes($aArr) {
    if (empty($aArr)) {
      return;
    }
    foreach ($aArr as $lKey => $lVal) {
      $this -> setAtt($lKey, $lVal);
    }
  }

  public function getAtt($aKey, $aStd = NULL) {
    return (isset($this -> mAtt[$aKey])) ? $this -> mAtt[$aKey] : $aStd;
  }

  public function getAttributeString() {
    if (empty($this -> mAtt)) return '';
    $lRet = '';
    foreach ($this -> mAtt as $lKey => $lVal) {
      $lRet.= ' '.$lKey.'="'.htm($lVal).'"';
    }
    return $lRet;
  }

  function setCnt($aCnt) {
    $this -> mCnt = $aCnt;
  }

  function addCnt($aCnt) {
    $this -> mCnt.= $aCnt;
  }

  public function getTag($aEmpty = FALSE) {
    $lRet = '<'.$this -> mTag;
    $lRet.= $this -> getAttributeString();
    if ($aEmpty) {
      $lRet.= ' /';
    }
    $lRet.= '>';
    return $lRet;
  }

  public function getEndTag() {
    return '</'.$this -> mTag.'>';
  }

  protected function getCont() {
    if (empty($this -> mCnt)) {
      return $this -> getTag(TRUE);
    }
    $lRet = $this -> getTag(FALSE);
    $lRet.= $this -> mCnt;
    $lRet.= $this -> getEndTag();
    return $lRet;
  }
}