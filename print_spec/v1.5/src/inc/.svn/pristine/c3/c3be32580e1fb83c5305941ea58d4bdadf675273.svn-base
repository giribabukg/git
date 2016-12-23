<?php
class CInc_Api_Xchange_Base extends CCor_Obj {
  
  public function __construct($aParams) {
    $this->mParams = $aParams;
    $this->getPrefs();
  }
  
  protected function getPrefs() {
    if (PHP_SAPI == 'cli') {
      return;
    }
    $lUsr = CCor_Usr::getInstance();
    
    $lPref = $lUsr->getPref('sys.msg.mt'.mtApi);
    $this->mShowInfo  = bitSet($lPref, mlInfo);
    $this->mShowError = bitSet($lPref, mlError);
    
    $lPref = $lUsr->getPref('sys.msg.mt'.mtDebug);
    $this->mShowDebug = bitSet($lPref, mlInfo);
  }
  
  protected function getParam($aKey, $aDefault = null) {
    return (isset($this->mParams[$aKey])) ? $this->mParams[$aKey] : $aDefault;
  }
  
  protected function setParam($aKey, $aValue) {
    $this->mParams[$aKey] = $aValue;
    return (isset($this->mParams[$aKey])) ? $this->mParams[$aKey] : $aDefault;
  }
  
  protected function hasParam($aKey) {
    return isset($this->mParams[$aKey]);
  }
  
  protected function getPrefixParams($aPrefix) {
    $lRet = array();
    $lPrefix = $aPrefix;
    $lLen = strlen($aPrefix);
    foreach ($this->mParams as $lKey => $lVal) {
      #$this->logDebug('key '. $lKey.'  is '.substr($lKey, 0, $lLen).'='.$aPrefix);
      if (substr($lKey, 0, $lLen) == $aPrefix) {
        $lSubKey = substr($lKey, $lLen);
        $lRet[$lSubKey] = $lVal;
      }
    }
    return $lRet;
  }
  
  public function execute() {
    $this->logDebug('Start');
    try {
      $lRet = $this->doExec();
    } catch (Exception $ex) {
      $this->msg($ex->getMessage(), mtApi, mlError);
      $lRet = false;
    }
    $this->logDebug('End');
    return $lRet;
  }
  
  protected function doExec() {
    $this->logError('Abstract doExec used');
    return false;
  }
  
  protected function logDebug($aText) {
    if (PHP_SAPI != 'cli') {
      $this->msg($aText, mtApi, mlInfo);
      if ($this->mShowDebug) {
        echo '[DEBUG] '.$aText.BR.LF;
      }
    }
    CSvc_Base::addLog('[DEBUG] '.$aText, mlInfo);
  }
  
  protected function logInfo($aText) {
    if (PHP_SAPI != 'cli') {
      $this->msg($aText, mtApi, mlInfo);
      if ($this->mShowInfo) {
        echo '[INFO ] '.$aText.BR.LF;
      }
    }
    CSvc_Base::addLog('[INFO ] '.$aText, mlInfo);
  }
  
  protected function logError($aText) {
    if (PHP_SAPI != 'cli') {
      $this->msg($aText, mtApi, mlError);
      if ($this->mShowError) {
        echo '[ERROR] '.$aText.BR.LF;
      }
    }
    CSvc_Base::addLog('[ERROR]'.$aText, mlError);
  }
  
  
  
}