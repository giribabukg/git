<?php
/**
 * Webcenter Client Stub
 *
 * For testing purposes only
 *
 * @package    API
 * @subpackage Webcenter
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 */

class CInc_Api_Wec_Stub extends CCor_Obj {

  /**
   * XML-string to return instead of real Webcenter Server response
   *
   * @var string XML-string response
   */
  private $mReply;

  /**
   * Set url and authentication data. Dummy to retain public interface
   *
   * @param string $aUrl   Base-Url of Webcenter (with trailing slash)
   * @param string $aUser  Webcenter Username used for authentication
   * @param string $aPass  Webcenter Password used for authentication
   */
  public function setConfig($aUrl, $aUser, $aPass) {
  }

  public function setResponse($aXml) {
    $this -> mReply = $aXml;
  }

  public function readFile($aFilename) {
    $lRet = file_get_contents($aFilename);
    $this -> setResponse($lRet);
  }

  /**
   * Execute query and return Webcenter's response as string
   *
   * @param string $aJsp     JSP-Page to call
   * @param array  $aParams  Optional parameter array
   *
   * @return string Webcenter Response Body
   */
  public function query($aJsp, $aParams = array()) {
    return $this -> mReply;
  }

}
