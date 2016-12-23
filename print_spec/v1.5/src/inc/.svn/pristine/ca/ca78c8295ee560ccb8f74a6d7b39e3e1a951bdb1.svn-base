<?php
class CInc_Job_Pro_Sub_Wizfac extends CHtm_Fie_Fac {

  public function __construct() {
    parent::__construct('pro');
    $this -> mIndex = 0;
  }

  public function setIndex($aIndex) {
    $this -> mIdx = $aIndex;
  }

  public function getId($aAlias = NULL) {
    $lAlias = (NULL == $aAlias) ? $this -> getDef('alias') : $aAlias;
    if (isset($this -> mIds[$lAlias])) {
      return $this -> mIds[$lAlias].'_'.$this -> mIdx;
    }
    $lId = getNum('in');
    $this -> mIds[$lAlias] = $lId;
    return $lId.'_'.$this -> mIdx;
  }

  protected function getOldName() {
    $lRet = 'old['.$this -> getDef('alias').']['.$this -> mIdx.']';
    return $lRet;
  }

  protected function getName() {
    $lRet = 'val['.$this -> getDef('alias').']['.$this -> mIdx.']';
    return $lRet;
  }

}