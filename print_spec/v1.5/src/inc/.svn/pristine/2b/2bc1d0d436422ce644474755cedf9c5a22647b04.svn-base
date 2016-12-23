<?php
/**
 * Base Class for Webcenter Queries
 *
 * Basically a data holder for parameters via get/setParam, this object acts as
 * a base class for all Webcenter queries. Its main method query will return the
 * XML reply from Webcenter
 *
 * @package    API
 * @subpackage Webcenter
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 */
class CInc_Api_Wec_Query extends CCor_Obj {

  /**
   * Webcenter Client Object
   *
   * @var CApi_Wec_Client
   */
  protected $mCli;

  /**
   * URL Parameters
   *
   * @var CApi_Wec_Client
   */
  protected $mPar;
  
  protected $mLastResponseString;

  /**
   * Debug Mode
   *
   * @var CApi_Wec_Client
   */
  protected $mDebug;

  public function __construct($aClient, $aDebug=False) {
    $this -> mCli = $aClient;
    $this -> mPar = array();
    $this -> mDebug = $aDebug;
  }

  public function setParam($aKey, $aVal) {
    $this -> mPar[$aKey] = $aVal;
  }

  public function getParam($aKey, $aStd = NULL) {
    return (isset($this -> mPar[$aKey])) ? $this -> mPar[$aKey] : $aStd;
  }
  
  public function getLastResponseAsString() {
    return $this->mLastResponseString;
  }
  
  public function getLastResponse() {
    try {
      $lRet = new CApi_Wec_Response($this->getLastResponseAsString());
    } catch (Exception $lEx) {
      $lRet = new CApi_Wec_Response('<error><code>999</code><message>XML error</message></error>');
    }
    return $lRet;
  }

  public function query($aJsp) {
    #$this -> dump($this -> mPar, $aJsp);
    #$this->dbg($aJsp);

    $lRet = $this -> mCli -> query($aJsp, $this -> mPar);
    $this->mLastResponseString = $lRet;
    return $lRet;
  }

}