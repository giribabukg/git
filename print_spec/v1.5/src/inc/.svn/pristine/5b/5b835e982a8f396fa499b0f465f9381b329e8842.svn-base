<?php
class CInc_Api_Xchange_Export_Xml extends CCor_Ren {
  
  public function __construct($aJob, $aParam = null) {
    $this->mJob = $aJob;
    $this->mParam = $aParam;
    $this->mRootTag = 'job';
    $this->mFields = array();
    //$this->mJobFields  = CCor_Res::get('fie');
    $this->initXml();
    $this->init();
  }
  
  protected function initXml() {
    $lRoot = '<?xml version="1.0" encoding="UTF-8"?><'.$this->mRootTag.' />';
    $this->mXml = simplexml_load_string($lRoot);
  }
  
  protected function init() {
    $this->addTag('method', $this->mParam);
  }
  
  protected function getFields() {
    if (empty($this->mFields)) {
      $this->createFields();
    }
    return $this->mFields;
  }
  
  protected function createFields() {
    $this->add('jobid');
  }
    
  protected function add($aAlias, $aTagname = null, $aType = 'string') {
    if (empty($aAlias)) return;
    $lRec = array();
    $lRec['tag']  = (is_null($aTagname)) ? $aAlias : $aTagname;
    $lRec['type'] = $aType;
    $this->mFields[$aAlias] = $lRec;
  }
  
  protected function getCont() {
    $lFields = $this->getFields();
    foreach ($lFields as $this->mCurKey => $lRec) {
      $this->addChild($this->mCurKey, $lRec);
    }
    if (true) {
      $dom = new DOMDocument("1.0");
      $dom->preserveWhiteSpace = false;
      $dom->formatOutput = true;
      $dom->loadXML($this->mXml->asXML());
      return $dom->saveXML();
    }
    return $this->mXml->asXML();
  }
  
  protected function addTag($aTagname, $aValue) {
    $this->dbg('Adding '.$aTagname.': '.$aValue);
    $this->mXml->addChild($aTagname, $aValue);
  }
  
  protected function getVal($aAlias, $aDefault = null) {
    return (isset($this->mJob[$aAlias])) ? $this->mJob[$aAlias] : $aDefault;
  }
  
  protected function addChild($aAlias, $aFieldRec) {
    $lType = (isset($aFieldRec['type'])) ? $aFieldRec['type'] : '';
    $lTag  = (isset($aFieldRec['tag']) ) ? $aFieldRec['tag']  : $aAlias;
    $lVal  = $this->getVal($aAlias);
    
    $lFnc = 'addAlias'.$aAlias;
    if ($this -> hasMethod($lFnc)) {
      return $this -> $lFnc($lTag, $lVal);
    }
    
    $lType = (isset($aFieldRec['type'])) ? $aFieldRec['type'] : '';
    $lFnc = 'addType'.$lType;
    if ($this -> hasMethod($lFnc)) {
      return $this -> $lFnc($aAlias, $lTag, $lVal);
    }
    $this->addTag($lTag, (string)$lVal);
    return $lVal;
  }
  
}