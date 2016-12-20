<?php
class CInc_Api_Dalim_Query extends CCor_Obj {

  /**
   * @var Zend_Http_Client
   */
  protected $mHtp;

  public function __construct() {
    $this->getConfig();
    $this->initParams();
  }

  protected function getConfig() {
    $this->mBase = CCor_Cfg::get('dalim.baseurl');
    $this->mInternalBase = CCor_Cfg::get('dalim.internalurl');
  }

  public function initParams() {
    $this->mParams = array();
  }

  public function getParam($aKey, $aDefault = NULL) {
    $lKey = (string) $aKey;
    if (isset($this->mParams[$lKey])) {
      return $this->mParams[$lKey];
    }
    return $aDefault;
  }

  public function setParam($aKey, $aValue) {
    $lKey = (string) $aKey;
    $this->mParams[$lKey] = (string) $aValue;
    return $this;
  }

  /**
   * If not done previously, create a valid object for communication transport
   */
  protected function getTransport() {
    if (!isset($this -> mHtp)) {
      $this -> mHtp = new Zend_Http_Client();
      $this -> mHtp -> setConfig(array('timeout' => 120));
    }
    return $this->mHtp;
  }

  public function getHttp() {
    return $this->mHtp;
  }

  public function query($aEndpoint, $aIsInternal = true) {
    $this -> getTransport();
    if ($aIsInternal) {
      $lBase = $this->mInternalBase;
    } else {
      $lBase = $this->mBase;
    }
    $this -> mHtp -> setUri($lBase.$aEndpoint);
    if (!empty($this->mParams)) {
      $this -> mHtp -> setParameterGet($this->mParams);
    }
    try {
      $lRes = $this -> mHtp -> request(Zend_Http_Client::GET);
      $this->msg($this->mHtp->getLastRequest(),mtApi, mlInfo);
      $lResp = $this->mHtp->getLastResponse();

      $this->msg($this->mHtp->getLastResponse()->asString(), mtApi, mlInfo);
      if ($lRes -> isError()) {
        $this->msg('Error '.$lRes->getStatus(), mtApi, mlError);
        return false;
      } else {
        $lRet = $lRes -> getBody();
        return $lRet;
      }
      $this->msg($this->mHtp->getLastRequest());
    } catch (Exception $lExc) {
      $this -> msg($lExc -> getMessage(), mtApi, mlError);
    }
    return false;
  }

}