<?php
/**
 * Webcenter Response Object
 *
 * Can be used for all XML-Replies for a given Webcenter query. Provides uniform
 * utility functions like isSuccess() etc.
 *
 * @package    API
 * @subpackage Webcenter
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 */

class CInc_Api_Wec_Response extends CCor_Obj {

  public function __construct($aXml){
    $this -> mXml = $aXml;
    $this -> mDoc = simplexml_load_string($this -> mXml);
  }

  public function getVal($aKey) {
    return (isset($this -> mDoc -> $aKey)) ? $this -> mDoc -> $aKey : NULL;
  }

  public function isSuccess() {
    if (empty($this -> mXml)) return false;
    if ($this->mDoc->getName() == 'error') {
      $this->msg('Webcenter Error: '.$this->mXml, mtApi, mlError);
      return false;
    }
    return true;
  }

  public function getDoc() {
    return $this -> mDoc;
  }

}