<?php
/**
 * Webcenter Client
 *
 * Description
 *
 * @package    API
 * @subpackage Webcenter
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 */

class CInc_Api_Wec_Client extends CCor_Obj {

  /**
   * Transport Object
   *
   * @var Zend_Http_Client
   */
  private $mHtp;

  private $mUrl;
  private $mUsr;
  private $mPwd;

  /**
   * Set url and authentication data
   *
   * @param string $aUrl   Base-Url of Webcenter (with trailing slash)
   * @param string $aUser  Webcenter Username used for authentication
   * @param string $aPass  Webcenter Password used for authentication
   */
  public function setConfig($aUrl, $aUser, $aPass) {
    $this -> mUrl = $aUrl;
    $this -> mUsr = $aUser;
    $this -> mPwd = $aPass;
  }

  public function getUsedURL() {
     return $this -> mUrl;
  }

  /**
   * Set url and authentication data based on main configuration
   */
  public function loadConfig($aExt = FALSE, $aUsr = FALSE) {
    $lCfg = CCor_Cfg::getInstance();

    if ($aExt == FALSE) {
      $this -> setConfig(
          $lCfg -> getVal('wec.host'),
          $lCfg -> getVal('wec.user'),
          $lCfg -> getVal('wec.pass')
      );
    } else {
      $this -> setConfig(
          $lCfg -> getVal('wec.hostext'),
          $lCfg -> getVal('wec.user'),
          $lCfg -> getVal('wec.pass')
      );
    }

    if ($aUsr == TRUE) {
      $lWecFileListCurrentUser = CCor_Cfg::get('wec.filelist.currentuser', FALSE);
      if ($lWecFileListCurrentUser) {
        $lUsr = CCor_Usr::getInstance();

        if ($lUsr -> getAuthId() > 0) {
          $lWecUsr = $lUsr -> getInfo('wec_usr', NULL);
          $lWecPwd = $lUsr -> getInfo('wec_pwd', NULL);

          if (!is_null($lWecUsr) && !is_null($lWecPwd)) {
            $this -> setUser($lWecUsr, $lWecPwd);
          }
        }
      }
    }
  }

  /**
   * Set authentication data only (when switching users)
   *
   * @param string $aUser  Webcenter Username used for authentication
   * @param string $aPass  Webcenter Password used for authentication
   */
  public function setUser($aUser, $aPass) {
    $this -> mUsr = $aUser;
    $this -> mPwd = $aPass;
  }

  /**
   * If not done previously, create a valid object for communication transport
   */
  private function getTransport() {
    if (isset($this -> mHtp)) {
      return;
    }
    $this -> mHtp = new Zend_Http_Client();
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
    $this -> getTransport();
    $this -> mHtp -> setUri($this -> mUrl.$aJsp);
    $this -> mHtp -> setParameterGet('username', $this -> mUsr);
    $this -> mHtp -> setParameterGet('password', base64_encode($this -> mPwd));

    if (!empty($aParams)) {
      $this -> mHtp -> setParameterGet($aParams);
    }
    # print_r($this -> mHtp);
    try {
      $lRes = $this -> mHtp -> request();
      $this->msg('WEC REQUEST '.$this->mHtp->getLastRequest(), mtApi, mlInfo);
      $this->msg('WEC RESPONSE '.$this->mHtp->getLastResponse()->asString(), mtApi, mlInfo);
      if ($lRes -> isError()) {
        $this -> msg($lRes -> getMessage(), mtApi, mlError);
        return FALSE;
      } else {
        $lRet = trim($lRes -> getBody());
        $this->msg($lRet, mtApi, mlInfo);
        return $lRet;
      }
    } catch (Exception $lExc) {
      $this -> msg($lExc -> getMessage(), mtApi, mlError);
    }
    return FALSE;
  }

  public function getUrl($aJsp, $aParams = array()) {
    $lUTF8 = CCor_Cfg::get('wec.utf8', FALSE);
    $lRet = $this -> mUrl.$aJsp;
    $lRet.= (FALSE === strpos($lRet, '?')) ? '?' : '&';
    $lRet.= 'username='.urlencode($this -> mUsr);
    $lRet.= '&password='.urlencode(base64_encode($this -> mPwd));
    if (!empty($aParams))
    foreach ($aParams as $lKey => $lVal) {
      if ($lUTF8) {
        $lRet.= '&'.$lKey.'='.urlencode(utf8_encode($lVal));
      } else {
        $lRet.= '&'.$lKey.'='.urlencode($lVal);
      }
    }
    return $lRet;
  }
}