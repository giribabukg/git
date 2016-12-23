<?php
class CInc_Htm_Column extends CCor_Dat {

  protected $mAlias;
  protected $mCaption;
  protected $mFieAtt;
  protected $mHidden;
  protected $mSortable;

  public function __construct($aAlias = '', $aCaption = '', $aSortable = FALSE, $aHtmAtt = array(), $aFieAtt = array()) {
    $this -> mAlias     = $aAlias;
    $this -> mCaption   = $aCaption;
    $this -> mSortable  = $aSortable;
    $this -> assign($aHtmAtt);
    $this -> mFieAtt = new CCor_Dat();
    if (!empty($aFieAtt)) {
      $this -> mFieAtt -> assign($aFieAtt);
    }
    $this -> mHidden = FALSE;
  }

  public function getAlias() {
    return $this -> mAlias;
  }

  public function getCaption() {
    return $this -> mCaption;
  }

  public function setCaption($aCaption) {
    $this -> mCaption = $aCaption;
  }

  public function isSortable() {
    return $this -> mSortable;
  }

  public function setSortable($aFlag = TRUE) {
    $this -> mSortable = $aFlag;
  }

  public function getAttributes() {
    return $this -> mVal;
  }

  public function getFieldAttr($aKey) {
    if (!isset($this -> mFieAtt[$aKey])) {
      return '';
    }
    return $this -> mFieAtt[$aKey];
  }

  public function isHidden() {
    return $this -> mHidden;
  }

  public function setHidden($aFlag = TRUE) {
    $this -> mHidden = $aFlag;
  }
}