<?php
class CInc_Api_Pixelboxx_Client extends CCor_Obj {
  
  public function __construct() {
    $this->resetOptions();
  }
  
  public function getTransport() {
    if (!isset($this->mTrans)) {
      $this->mTrans = $this->createTransport();
    }
    return $this->mTrans;
  }
  
  protected function createTransport() {
    $lWsdl = $this->getWsdlLocation();
    $lClient = new Zend_Soap_Client($lWsdl, $this->mOpt);
    #$lClient = new CApi_Pixelboxx_Stub();
    return $lClient;
  }
  
  public function getWsdlLocation() {
    #$lRet = CCor_Cfg::get('pbox.wsdl', 'https://elements.5flow.net/servlet/ws/WebServiceInterface?WSDL');
    
    $lFile = pathinfo(__FILE__);
    $lRet = $lFile['dirname'].DS.'data'.DS.'WebServiceInterface.wsdl';
    return $lRet;
  }
  
  public function resetOptions() {
    $this->mOpt = array();
    $this->setOption('soap_version', SOAP_1_1);
    return $this;
  }
  
  
  public function setOption($aOption, $aValue) {
    if (empty($aOption)) {
      throw new InvalidArgumentException('Cannot set empty option');
    }
    $this->mOpt[$aOption] = $aValue;
    $this->mTrans = NULL;
    return $this;
  }
  
  public function getOption($aOption, $aDefault = NULL) {
    return (isset($this->mOpt[$aOption])) ? $this->mOpt[$aOption] : $aDefault;
  }
  
  public function setLogin($aUsername, $aPassword) {
    $this->setOption('login', $aUsername);
    $this->setOption('password', $aPassword);
    return $this;
  }
  
  public function loadAuthFromConfig() {
    $lUser = CCor_Cfg::get('pbox.user', 'gemmans');
    $lPass = CCor_Cfg::get('pbox.pass', 'r2d2c3p0');
    $this->setLogin($lUser, $lPass);
    return $this;
  }
  
  public function loadAuthFromUser() {
    $this->loadAuthFromConfig(); // no individual login for now
    return $this;
    $lUsr = CCor_Usr::getInstance();
    $lUser = $lUsr->getInfo('pbox_user');
    $lPass = $lUsr->getInfo('pbox_pass');
    $this->setLogin($lUser, $lPass);
    return $this;
  }
  
  public function __call($aMethod, $aParams) {
    return $this->query($aMethod, $aParams);
  }
  
  public function query($aMethod, $aParams) {
    try {
      $lTrans = $this->getTransport();
      $lRet = $lTrans->$aMethod($aParams);
      $this->msg('Pixelboxx: REQ '.$aMethod.' / '.$lTrans->getLastRequest(), mtApi, mlInfo);
      $this->msg('Pixelboxx: RES '.$aMethod.' / '.$lTrans->getLastResponse(), mtApi, mlInfo);
      return $lRet;
    } catch (Exception $ex) {
      $this->msg('Pixelboxx: REQ '.$aMethod.' / '.$lTrans->getLastRequest(), mtApi, mlInfo);
      $this->msg('Pixelboxx: '.$aMethod.' / '.$ex->getMessage(), mtApi, mlError);
      return false;
    }
  }

}