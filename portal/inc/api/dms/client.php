<?php
/**
 * Class for sending queries to the DMS system provided by Hansen Consulting.
 *
 * This class only takes care of sending the request and retrieving a reponse.
 * The content of both request and response is not handled here.
 *
 * @package    API
 * @subpackage DMS
 * @copyright  Copyright (c) 5Flow GmbH (http://www.5flow.eu)
 * @version $Rev: 687 $
 * @date $Date: 2013-01-18 03:56:42 +0100 (Fr, 18 Jan 2013) $
 * @author $Author: gemmans $
 */
class CInc_Api_Dms_Client extends CCor_Obj {

  /**
   * The base URL of the DMS API.
   *
   * Final URL is built using base URL, command and parameters.
   * @var string
   */
  protected $mBaseUrl;

  /**
   * Http client talking to the DMS API
   * @var Zend_Http_Client
   */
  protected $mHtp;

  /**
   * Construction - fetch base url from config
   */
  public function __construct() {
    $this->mBaseUrl = CCor_Cfg::get('dms.base.url', 'https://dms.5flow.net/');
  }

  /**
   * Lazy instantiation of Http Transport. Always return the transport object.
   * @return Zend_Http_Client
   */
  protected function getTransport() {
    if (!isset($this -> mHtp)) {
      $this -> mHtp = new Zend_Http_Client();
    }
    return $this->mHtp;
  }

  /**
   * Send a query and return the result
   *
   * @param string $aCommand command to execute (e.g. openfile)
   * @param array|null $aParams Hash array of GET parameters
   * @param string|null $aRawPost If we send a raw POST, send this as POST body
   */
  public function query($aCommand, $aParams = null, $aRawPost = null) {
    $lClient = $this->getTransport();
    $lClient -> setUri($this->mBaseUrl.$aCommand);
    if (!empty($aParams)) {
      $lClient -> setParameterGet($aParams);
    }
    $lMethod = Zend_Http_Client::GET;
    if (!empty($aRawPost)) {
      $lClient->setRawData($aRawPost, 'text/xml');
      #$lMethod = Zend_Http_Client::POST;
    }
    try {
      $lRes = $lClient -> request();
      $this->msg($this->mHtp->getLastRequest(),mtApi, mlInfo);
      $this->msg($lRes->asString(), mtApi, mlInfo);
      if ($lRes -> isError()) {
        $this->msg('Error '.$lRes->getStatus(), mtApi, mlError);
        return false;
      } else {
        $lRet = $lRes -> getBody();
        return $lRet;
      }
    } catch (Exception $lExc) {
      $this -> msg($lExc -> getMessage(), mtApi, mlError);
    }
    return false;
  }

}
