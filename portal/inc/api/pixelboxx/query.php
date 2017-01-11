<?php
class CInc_Api_Pixelboxx_Query extends CCor_Obj {
  
  public function __construct($aClient = null) {
    $this->mClient = $aClient;
    $this->init();
  }
  
  protected function init() {
  }
  
  public function setClient($aClient) {
    $this->mClient = $aClient;
  }
  
  public function getClient() {
    if (!isset($this->mClient)) {
      $this->mClient = new CApi_Pixelboxx_Client();
      $this->mClient->loadAuthFromUser();
    }
    return $this->mClient;
  }
  
  public function setParam($aKey, $aVal) {
    $this -> mParam[$aKey] = $aVal;
  }
  
  public function getParam($aKey, $aStd = NULL) {
    return (isset($this -> mParam[$aKey])) ? $this -> mParam[$aKey] : $aStd;
  }
  
  protected function hasPath($aRes, $aPath) {
    $lPath = (is_array($aPath)) ? $aPath : explode('.', $aPath);
    if (!$aRes) return false;
    $lRoot = $aRes;
    foreach ($lPath as $lKey) {
      if (!$lRoot || !$lRoot->$lKey)  {
        $this->dbg('Result does not have '.$lKey, mlError);
        return false;
      }
      $lRoot = $lRoot->$lKey;
    } 
    return true;
  }
  
  public function query() {
    $lClient = $this->getClient();
    $lRet = $lClient->query($this->mMethod, $this->mParam);
    return $lRet;
  }
 
}