<?php
class CInc_Api_Xchange_Xml_Base extends CApi_Xchange_Base {
  
  public function parse($aFile) {
    // we can pass a file or an in-memory string
    $lXml = $aFile;
    if (file_exists($aFile)) {
      $this->logDebug('loading from file');
      $lXml = file_get_contents($aFile);
    }
    $this->logDebug('Start Parsing XML');
    try {
      $lRet = $this->doParse($lXml);
    } catch (Exception $ex) {
      $this->msg($ex->getMessage(), mtApi, mlError);
      $lRet = false;
    }
    $this->logDebug('End Parsing XML');
    return $lRet;
  }
  
  protected function doParse($aXml) {
    $this->logError('Abstract doParse used');
    return false;
  }

}