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

class CInc_Api_Wec_Robot extends CCor_Obj {

  /**
   * Transport Object
   *
   * @var Zend_Http_Client
   */
  private $mHtp;

  private $mUrl;
  private $mUsr;
  private $mPwd;
  private $mDebug = FALSE;


  public function __construct() {
    $this -> loadConfig();
  }

  public function setDebug($aFlag = TRUE) {
    $this -> mDebug = $aFlag;
  }

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

  /**
   * Set url and authentication data based on main configuration
   */
  public function loadConfig() {
    $lCfg = CCor_Cfg::getInstance();
    $this -> setConfig(
      $lCfg -> getVal('wec.host'),
      $lCfg -> getVal('wec.user'),
      $lCfg -> getVal('wec.pass')
    );
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
    $this -> mHtp -> setCookieJar(true);
  }

  protected function sendDebug($aRes) {
    if (!$this -> mDebug) return;
    $this -> msg($this -> mHtp -> getLastRequest(), mtApi, mlInfo);
    if ($aRes -> isError) {
      $this -> msg($lRes -> getMessage(), mtApi, mlError);
    }
    $this -> msg($this -> mHtp -> getLastResponse() -> getHeadersAsString());
    $this -> msg($this -> mHtp -> getLastResponse() -> getBody(), mtApi, mlInfo);
  }

  /**
   * Execute query and return Webcenter's response as string
   *
   * @param string $aJsp     JSP-Page to call
   * @param array  $aParams  Optional parameter array
   *
   * @return string Webcenter Response Body
   */
  protected function queryGet($aJsp, $aParams = array(), $aTrim = true) {
    $this -> msg($aJsp.' '.var_export($aParams, TRUE), mtApi, mlInfo);
    $this -> getTransport();
    $this -> mHtp -> setUri($this -> mUrl.$aJsp);
    if (!empty($aParams)) {
      $this -> mHtp -> setParameterGet($aParams);
    }
    try {
      $lRes = $this -> mHtp -> request(Zend_Http_Client::GET);
      $this -> sendDebug($lRes);
      if ($lRes -> isError()) {
        return FALSE;
      } else {
        $lRet = $lRes -> getBody();
        if ($aTrim) {
          $lRet = trim($lRet);
        }
        return $lRet;
      }
    } catch (Exception $lExc) {
      $this -> msg($lExc -> getMessage(), mtApi, mlError);
    }
    return FALSE;
  }

  protected function queryPost($aJsp, $aParams = array()) {
    $this -> msg($aJsp.' '.var_export($aParams, TRUE), mtApi, mlInfo);
    $this -> getTransport();
    $this -> mHtp -> setUri($this -> mUrl.$aJsp);
    if (!empty($aParams)) {
      $this -> mHtp -> setParameterPost($aParams);
    }
    try {
      $lRes = $this -> mHtp -> request(Zend_Http_Client::POST);
      $this -> sendDebug($lRes);
      if ($lRes -> isError()) {
        return FALSE;
      } else {
        $lRet = trim($lRes -> getBody());
        return $lRet;
      }
    } catch (Exception $lExc) {
      $this -> msg($lExc -> getMessage(), mtApi, mlError);
    }
    return FALSE;
  }

  public function login() {
    $lArr = array();
    $lArr['username'] = $this -> mUsr;
    $lArr['password'] = $this -> mPwd;
    return $this -> queryGet('dologin.jsp', $lArr);
  }

  public function logout() {
    // system information reg. WebCenter
    $lCfg = CCor_Cfg::getInstance();
    $lAdmWecUser = $lCfg -> getVal('wec.user');
    $lAdmWecPass = $lCfg -> getVal('wec.pass');

    // user information reg. WebCenter
    $lUsrId = CCor_Usr::getAuthId();
    $lUsrInf = new CCor_Usr_Info($lUsrId);
    $lUsrWecUser = $lUsrInf -> get('wec_usr');
    $lUsrWecPass = $lUsrInf -> get('wec_pwd');

    $lArr = array();
    if (!empty($lUsrWecUser) && !empty($lUsrWecPass)) {
      $lArr['username'] = $lUsrWecUser;
      $lArr['password'] = $lUsrWecPass;
    }

    return $this -> queryGet('logoff.jsp', $lArr);
  }

  public function startApl($aDocVersionId) {
    $lArr = array();
    $lArr['actionVal'] = 'StartApprovalCycle';
    $lArr['docVerID']  = $aDocVersionId;
    $lRes = $this -> queryGet('dodocdetailsactions.jsp', $lArr);
    return (FALSE !== $lRes);
  }

  public function stopApl($aDocVersionId) {
    $lArr = array();
    $lArr['actionVal'] = 'StopApprovalCycle';
    $lArr['docVerID']  = $aDocVersionId;
    return $this -> queryGet('dodocdetailsactions.jsp', $lArr);
  }

  public function startProject($aProjectId) {
    $lArr = array();
    $lArr['projectID'] = $aProjectId;
    $lArr['actionVal'] = 'ChangeStatus';
    $lArr['status']    = '00001_0000000001';
    return $this -> queryPost('doprojectactions.jsp', $lArr);
  }

  public function forcedApproval($aProjectId, $aDocVersionId) {
    $lArr = array();
    $lArr['projectID']  = $aProjectId;
    $lArr['docVerID']   = $aDocVersionId;
    $lArr['actionVal']  = 'NextStep';
    $lArr['curStepTag'] = 'DEF_APPROVE';
    $lArr['menu_file']  = 'myprojects';
    $lArr['approvalStatus'] = '00001_0000000005';
    $lArr['useProjectOption'] = '0';
    $lArr['ApprovalComments'] = '';
    return $this -> queryPost('dosubmitapproval.jsp', $lArr);
  }

  public function forcedRejection($aProjectId, $aDocVersionId) {
    $lArr = array();
    $lArr['projectID']  = $aProjectId;
    $lArr['docVerID']   = $aDocVersionId;
    $lArr['actionVal']  = 'NextStep';
    $lArr['curStepTag'] = 'DEF_APPROVE';
    $lArr['menu_file']  = 'myprojects';
    $lArr['approvalStatus'] = '00001_0000000004';
    $lArr['useProjectOption'] = '0';
    $lArr['ApprovalComments'] = '';
    return $this -> queryPost('dosubmitapproval.jsp', $lArr);
  }

  public function createNewUser($aUame, $aVame, $aLame, $aMail) {
    $lArr = array();
    $lArr['actionVal']  = 'CreateNewUser';
    $lArr['ldapuser']  = 'undefined';
    $lArr['menu_file']  = 'usermgrnewuser';
    $lArr['curStepTag']  = 'STEP_DEF_USER_NAME';
    $lArr['stepLabel']  = 'Benutzerinformationen';
    $lArr['ldapauth']  = '';
    $lArr['username']  = $aUame;
    $lArr['firstName']  = $aVame;
    $lArr['lastName']  = $aLame;
    $lArr['emailLink']  = $aMail;
    $lArr['phoneNumber']  = '';
    $lArr['mobileNumber']  = '';
    $lArr['function']  = '';
    $lArr['newpassword']  = $aUame;
    $lArr['confirmpwrd']  = $aUame;
    $lArr['isAdmin']  = 'false';
    $lArr['userType']  = '0';
    $lArr['projectMgr']  = 'NORMAL_USER';
    return $this -> queryPost('dousermgractions.jsp', $lArr);
  }

  public function getDownloadDocumentVersion($aPrjId, $aDocVerId) {
    $lArr = array();
    $lArr['username'] = $this->mUsr;
    $lArr['password'] = base64_encode($this->mPwd);
    $lArr['projectid'] = $aPrjId;
    $lArr['docversionid'] = $aDocVerId;
    return $this -> queryGet('DownloadDocument.jsp', $lArr, false);
  }


}