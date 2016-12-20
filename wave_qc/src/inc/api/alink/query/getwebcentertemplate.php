<?php
class CInc_Api_Alink_Query_GetWebcenterTemplate extends CApi_Alink_Query {

  public function __construct($aJobId) {
    parent::__construct('getCustomerList');
    $this -> addParam('jobid', $aJobId);
    
    $lFie = array();  
    $lDef = array();
    $lDef['alias'] = 'WebcenterTemplate';
    $lDef['native'] = 'WebcenterTemplate';
    $lFie[] = $lDef;

    $lCnd = array();
    $lTmp = array();
    $lTmp['field'] = 'jobid';
    $lTmp['op']    = 'bnr';    // das kann evtl konfigurierbar sein
    $lTmp['value'] = $aJobId;
    $lCnd[] = $lTmp;
    
    $this -> addParam('fields', $lFie);
    $this -> addParam('where', $lCnd);
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
          $lVorlage = $lTmp['WebcenterTemplate'];
        }
      }                
    }
    
    return $lVorlage;
  }

}