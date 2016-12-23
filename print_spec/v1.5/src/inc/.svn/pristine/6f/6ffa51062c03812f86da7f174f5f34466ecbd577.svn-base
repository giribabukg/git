<?php
class CInc_Rep_Chart extends CCor_Obj {

  public function __construct($aSrc) {
    $this -> mSrc = $aSrc;
  }
  
  public function getContent() {
    $lCls = 'CRep_'.$this -> mSrc.'_Res';
    if (CCor_Loader::loadClass($lCls)) {
      $lRes = new $lCls($this -> mSrc);
      
      return $lRes -> getResult();
    }
    
    return FALSE; 
  }

  public function getExcel($aKey) {
    $lCls = 'CRep_'.$this -> mSrc.'_Excel';
    if (CCor_Loader::loadClass($lCls)) {
      return new $lCls($this -> mSrc, $aKey);
    }
    
    return FALSE;
  }
}