<?php
require_once 'api.php';

class CInc_Api_Cloudflow_Client extends CCor_Obj {

  public function __construct() {
    $this->mUrl = CCor_Cfg::get('cloudflow.url', 'http://cloudflow.5flow.net:9090/');
    cloudflow\api_helper::set_address($this->mUrl);
    $this->mUsr = CCor_Usr::getInstance();
    $this->mUsrName = $this->mUsr->getVal('fullname');
  }

  /**
   * Create a session (if it doesn't exist yet)
   * @param string|null $aUser Username in Cloudflow
   * @param string|null $aPass Password Cloudflow
   * @return string|false The Cloudflow Session ID or false if unsuccessfull
   */
  public function createSession($aUser = null, $aPass = null) {
    if (!$this->mSession) {
      $this->mSession = $this->createNewSession($aUser, $aPass);
    }
    return $this->mSession;
  }

  /**
   * Force the creation of a new session, even if one exists already
   * @param string|null $aUser Username in Cloudflow
   * @param string|null $aPass Password Cloudflow
   * @return string|false The Cloudflow Session ID or false if unsuccessfull
   */
  public function createNewSession($aUser = null, $aPass = null) {
    $lUser = is_null($aUser) ? CCor_Cfg::get('cloudflow.user', 'admin') : $aUser;
    $lPass = is_null($aPass) ? CCor_Cfg::get('cloudflow.pass', 'corifabi16') : $aPass;

    $lRes = \cloudflow\auth\create_session($lUser, $lPass);
    $lRet = ($lRes['session']) ? $lRes['session'] : false;

    $this->mSession = $lRet;
    cloudflow\api_helper::set_session($this->mSession);
    return $this->mSession;
  }

  /**
   * Alternative authentication for web based queries (e.g. GET/POST)
   * @param string|null $aUser
   * @param string|null $aPass
   */
  public function login($aUser = null, $aPass = null) {
    $lUser = is_null($aUser) ? CCor_Cfg::get('cloudflow.user', 'admin') : $aUser;
    $lPass = is_null($aPass) ? CCor_Cfg::get('cloudflow.pass', 'corifabi16') : $aPass;

    $lRet = \cloudflow\auth\login($lUser, $lPass);
    if (!$lRet['user_id']) {
      return false;
    }
    return $lRet;
  }

  public function getSubFolderFor($aJob) {
    if (is_scalar($aJob)) {
      $lSvcId = intval($aJob);
    } else {
      $lSvcId = intval($aJob['service_order_id']);
    }
    if (empty($lSvcId)) {
      return false;
    }
    $lParentFolder = intval($lSvcId / 1000);
    $lSubFolder = $lSvcId;
    $lRet = $lParentFolder.'/'.$lSubFolder.'/';
    return $lRet;
  }

  public function getFileList($aJob, $aOrderBy = 'file_name') {
    $this->createSession();
    $lSubFolder = $this->getSubFolderFor($aJob);
    if (empty($lSubFolder)) {
      return false;
    }
    $lQuery = array('cloudflow.enclosing_folder', 'ends with', $lSubFolder);
    $lOrder = array($aOrderBy, 'ascending');
    $lFields = array('_id', 'url', 'file_name', 'file_size', 'modtime', 'pages', 'thumb');
    //$lOptions  = array('use_index' => 'Folder_EnclosingFolderURL');
    $lOptions  = new stdClass();
    $lRet = $this->getGenericFileList($lQuery, $lFields, $lOrder, $lOptions);//, $lOptions);
    return $lRet;
  }

  public function getGenericFileList($aQuery, $aFields, $aOrder, $aOptions) {
    $lRet = false;
    $lRes = cloudflow\asset\list_all($aQuery, $aFields);//, $aOrder);//, $aFields, $aOptions);
    //$lRes = cloudflow\asset\list_with_options($aQuery, $aOrder, $aFields, $aOptions);
    if ($lRes['results']) {
      $lRet = $this->convertFileList($lRes['results']);
    }
    return $lRet;
  }

  protected function convertFileList($aResults) {
    $lRet = array();
    foreach ($aResults as $lRow) {
      $lItm = array();
      $lItm = $lRow;
      $lItm['_id']  = $lRow['_id'];
      $lItm['url']  = $lRow['url'];
      $lItm['name'] = $lRow['file_name'];
      $lItm['size'] = $lRow['file_size'];
      $lItm['date'] = $lRow['modtime'];
      $lItm['category'] = '';
      $lRet[$lRow['file_name']] = $lItm;
    }
    return $lRet;
  }

  protected function getTransport() {
    if (!isset($this -> mHtp)) {
      $this -> mHtp = new Zend_Http_Client();
      $this -> mHtp -> setConfig(array('timeout' => 120));
      $this -> mHtp -> setCookieJar(true);
    }
    return $this->mHtp;
  }

  public function download2($aUrl) {
    $this->createSession();
    $lSub = 'cloudflow://'.$aUrl;
    $lUrl = $this->mUrl.'portal.cgi?asset=download_file&url='.urlencode($lSub);
    header('Cache-Control: public');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($aUrl).'"');
    $lData = file_get_contents($lUrl);
    echo $lData;
  }

  public function download($aUrl) {
    $lAuth = $this->login();
    $lSub = $aUrl;
    $lUrl = $this->mUrl.'portal.cgi?asset=download_file&url='.urlencode($lSub);

    $lTrans = $this->getTransport();
    $lTrans->setUri($lUrl);

    //$lJar = $lTrans->getCookieJar();
    $lTrans->setCookie('user_hash', $lAuth['user_hash']);
    $lTrans->setCookie('user_id', $lAuth['user_id']);
    $lTrans->setCookie('expiration_date', $lAuth['expiration_date']);

    $lRes = $lTrans -> request(Zend_Http_Client::GET);
    if ($lRes -> isError()) {
      $lResp = $lTrans->getLastResponse();
      echo 'error ' . $lResp->getMessage();
      return FALSE;
    } else {
      $lRet = $lRes->getBody();
    }
    header('Cache-Control: public');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($aUrl).'"');

    echo $lRet;
  }

  public function getEmbedPreview($aUrl, $aPage = 0, $aSize = 200) {
    $this->createSession();
    //$lRes = cloudflow\metadata\get_preview(urldecode($aUrl), $aPage, $aSize);
    $lRes = cloudflow\metadata\get_thumbnail($aUrl, $aPage, $aSize);
    if ($lRes['data']) {
      return $lRes['data'];
    }
    if (isset($lRes['error'])) {
      $this->msg($lRes['error'], mtApi, mlWarn);
    }
    //var_dump($lRes);
    return false;
  }

  public function getSingleViewerUrl($aFileUrl) {
    $this->createSession();

    $lOpt = new stdClass();
    $lOpt->email = $this->mUsrName;
    $lOpt->require_login = false;

    $lUrl = $this->mUrl;
    //$lRes = \cloudflow\proofscope\create_view_file_url_with_options($this->mUrl, $aFileUrl, $lOptions);
    $lRes = \cloudflow\proofscope\create_view_file_url_with_options($lUrl, $aFileUrl, $lOpt);
    if (isset($lRes['url'])) {
      return $lRes['url'];
    }
    return false;
  }

  public function getDiffViewerUrl($aFile, $aDiffFile) {
    $this->createSession();

    $lOpt = new stdClass();
    $lOpt->email = $this->mUsrName;
    $lOpt->require_login = false;
    $lOpt->viewer = 'Difference';

    $lRes = \cloudflow\proofscope\create_view_file_difference_url_with_options($this->mUrl, $aFile, $aDiffFile, $lOpt);
    if (isset($lRes['url'])) {
      return $lRes['url'];
    }
    return false;
  }


}
