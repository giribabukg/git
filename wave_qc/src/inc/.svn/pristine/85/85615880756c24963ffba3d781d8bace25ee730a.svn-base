<?php
class CInc_Api_Alink_Query_GetPrintSpecs extends CApi_Alink_Query {

  public function __construct($aJobId) {
    parent::__construct('getprintspecs');
  }

  public function query() {
    $this -> mLoaded = TRUE;
    $this -> addParam('sid', MAND);

    $lres = parent::query();
    
    $lVorlage = '';
    $lCount = $lres -> getVal('maxcount');
    if ($lCount>0) {
      $lRows = $lres -> getVal('item');
      $lRet = array();
  
      if (!empty($lRows)) {
        foreach ($lRows as $lRow) {
          $lTmp = array();
          foreach ($lRow as $lKey => $lVal) {
           $lTmp[(string)$lKey] = (string) $lVal;
          }
          // print_r($lTmp);
          $lRet[] = $lTmp;
        }
      }
    }
    
    return $lRet;
  }

}