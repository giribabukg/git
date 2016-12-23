<?php
class CInc_Api_Pixelboxx_Stub extends SoapClient {
  
  public function __construct() {
    
  }
  
  protected function getServer() {
    if (isset($this->mServer)) return $this->mServer;
    $lFile = pathinfo(__FILE__);
    $lWsdl = $lFile['dirname'].DS.'data'.DS.'WebServiceInterface.wsdl';
    $this->mServer = new SoapServer($lWsdl);
    $this->mServer->setClass(get_class($this));
  }

  /*
  public function __call($aName, $aParams) {
    return $this->_doRequestcallRequest($aName, $aParams);
  }
  */
  
  public function __doRequest($aRequest, $aLocation, $aAction, $aVersion) {
    echo "jay";
    $this->mLastRequest = 'STUB '.$aRequest.'('.var_export($aParams, true).')';
    $this->mLastResponse = '';
    
    $this->getServer();
    ob_start();
    #$this->mLastRequest = $aRequest;
    $this->mServer->handle($aRequest);
    $this->mLastResponse = ob_get_contents();
    ob_end_clean();
    return $this->mLastResponse();
    
    /*
    $lFile = pathinfo(__FILE__);
    $lXml = $lFile['dirname'].DS.'data'.DS.$aMethod.'Response.xml';
    if (file_exists($lXml)) {
      $lRet = file_get_contents($lXml);
    } else {
      $lXml = $lFile['dirname'].DS.'data'.DS.'faultResponse.xml';
      $lRet = file_get_contents($lXml);
    }
    $this->mLastResponse = $lRet;
    return $lRet;
    //$lRes = simplexml_load_string($lRet);
    return $lRes->Body;
    */
  }
  
  public function getLastRequest2($aMethod, $aParams) {
    return $this->__getLastRequest();
    return $this->mLastRequest;
  }
  
  public function getLastResponse2() {
    return $this->__getLastResponse();
    return $this->mLastResponse;
  }

  # Soap Request Method 
  
  public function getFunctions() {
    $this->mLastRequest = 'getFunctions';
    $lRet = array();
    $lRet[] = 'Here comes a list of functions';
    $this->mLastResponse = var_export($lRet, true);
    return $lRet;
    
    
  }
  
  public function getFolderStructure($aParams) {
    $this->mLastRequest = 'getFolderStructure '.var_export($aParams, true);
    $lRet = new stdClass();
    
    $lF = array();
    $lFolder = new stdClass();
    $lFolder->name = 'Sub 1';
    $lFolder->doi = 'pboxx-pixelboxx-101';
    $lF[] = $lFolder;
    
    $lFolder = new stdClass();
    $lFolder->name = 'Sub 2';
    $lFolder->doi = 'pboxx-pixelboxx-102';
    
    $lFolder2 = new stdClass();
    $lFolder2->name = 'SubSub 1';
    $lFolder2->doi = 'pboxx-pixelboxx-103';
    $lFolder->F = array($lFolder2);
    
    $lF[] = $lFolder;
    
    $lFs->name= 'Root';
    $lFs->doi = 'pboxx-pixelboxx-123';
    $lFs->F = $lF;
    
    $lRet->FolderStructure = $lFs;
    
    $this->mLastResponse = var_export($lRet, true);
    return $lRet;
  }
  
  public function getFolder($aParams) {
    $this->mLastRequest = 'getFolder '.var_export($aParams, true);
    #$lFolderId = $aParams[0]['FolderId'];
    $lRet = new stdClass();
    
    $lRet->Folder['doi'] = $lFolderId;
    $lRet->Folder['name'] = 'Test';
    $this->mLastResponse = var_export($lRet, true);
    return $lRet;
  }
  
  
  
}