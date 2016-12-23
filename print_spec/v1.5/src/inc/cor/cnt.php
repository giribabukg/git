<?php
/**
 * Base controller
 *
 * Provides dispatch mechanism, request convenience methods
 * and some common actions like sorting, paging and searching.
 *
 * The entry method dispatch will delegate the request to a
 * method according to the POST/GET variable 'act'. For example,
 * index.php?act=usr.edt will create an instance of CUsr_Cnt
 * and call its actEdt() method.
 *
 * @package     cor
 * @subpackage  controller
 * @copyright   5Flow GmbH (http://www.5flow.eu)
 * @version     $Rev: 10364 $
 * @date        $Date: 2015-09-14 21:34:59 +0800 (Mon, 14 Sep 2015) $
 * @author      $Author: jwetherill $
 */

class CInc_Cor_Cnt extends CCor_Obj {

  protected $mReq;
  protected $mPro; // protections

  public function __construct(ICor_Req $aReq, $aMod, $aAct = 'std') {
    $this -> mReq = & $aReq;
    $this -> mMod = $aMod;
    $this -> mPrf = $aMod;
    $this -> mAct = $aAct;
    $this -> mPro = array();
    $this -> mTitle = 'Title';
    $this -> mMmKey = substr($aMod,0,3);
  }

  public function setMmKey($aKey) {
    $this -> mMmKey = $aKey;
  }

  /**
   * Get a value from the request variables ($_GET and $_POST)
   *
   * @param string $aKey Index of the $_REQUEST-variable
   * @param mixed $aStd Default value to return if the index is not set
   */
  protected function getReq($aKey, $aStd = NULL) {
    return $this -> mReq -> getVal($aKey, $aStd);
  }

  /**
   * Get an integer value from the request variables ($_GET and $_POST)
   *
   * @param string $aKey Index of the $_REQUEST-variable
   * @param mixed $aStd Default value to return if the index is not set
   */
  protected function getReqInt($aKey, $aStd = 0) {
    $lRet = $this -> mReq -> getVal($aKey, NULL);
    return (NULL == $lRet) ? $aStd : intval($lRet);
  }

  /**
   * Get a value from the request variables ($_GET and $_POST)
   *
   * @param string $aKey Index of the $_REQUEST-variable
   * @param mixed $aStd Default value to return if the index is not set
   */
  protected function getVal($aKey, $aStd = NULL) {
    return $this -> mReq -> getVal($aKey, $aStd);
  }

  /**
   * Get an integer value from the request variables ($_GET and $_POST)
   *
   * @param string $aKey Index of the $_REQUEST-variable
   * @param mixed $aStd Default value to return if the index is not set
   */
  protected function getInt($aKey, $aStd = 0) {
    $lRet = $this -> mReq -> getVal($aKey, NULL);
    return (NULL == $lRet) ? $aStd : intval($lRet);
  }

  protected function setProtection($aAct, $aPriv, $aLvl = rdRead) {
    $lPro = array();
    $lPro['priv'] = $aPriv;
    $lPro['lvl'] = intval($aLvl);
    $this -> mPro[$aAct] = $lPro;
  }

  protected function getProtection($aAct) {
    return (isset($this -> mPro[$aAct])) ? $this -> mPro[$aAct] : NULL;
  }

  protected function failed($aAct) {
    $lUsr = CCor_Usr::getInstance();
    $lPro = $this -> getProtection($aAct);
    if (NULL === $lPro) {
      return FALSE;
    }
    if ($lUsr -> canDo($lPro['priv'], $lPro['lvl'])) {
      return FALSE;
    } else {
      return TRUE;
    }
  }

  protected function canDo($aAct) {
    if ($this -> failed('*')) {
      $this -> denyAccess(); // will redirect and exit, no return value needed
    }
    if ($this -> failed($aAct)) {
      $this -> denyAccess();
    }
    return TRUE;
  }

  protected function denyAccess($aMsg = NULL) {
    $lMsg = (NULL === $aMsg) ? lan('lib.perm') : $aMsg;
    CCor_Msg::add($lMsg, mtUser, mlError);
    $this -> redirect('index.php?act=hom-wel');
  }

  public function dispatch() {
    if (!$this -> canDo($this -> mAct)) {
      return;
    }
    $lFnc = 'act'.$this -> mAct;
    if ($this -> hasMethod($lFnc)) {
      $this -> $lFnc();
    } else {
      if (!empty($this -> mAct)) {
        $this -> dbg('Action '.$this -> mAct.' not defined', mlWarn);
      }
      $this -> actStd();
    }
  }

  protected function addToHistory($aUrl) {
    $lSes = CCor_Sys::getInstance();
    $lHis = $lSes -> get('his', array());
    $lCnt = count($lHis);
    if ($lCnt > 0) {
      if ($aUrl != $lHis[$lCnt -1]) {
        $lHis[] = $aUrl;
      }
    } else {
      $lHis[] = $aUrl;
    }
    $lSes['his'] = $lHis;
  }

  protected function getPage() {
    $lPag = CHtm_Page::getInstance();
    return $lPag;
  }

  /**
   * Embed the provided HTML content into the main page template, render the
   * page and exit.
   *
   * @param string|ICor_Ren $aCont HTML content to embed into the main page template
   * @param boolean $aIgnoreInHistory Save the current URI in the history? (used for the portals back button)
   */
  public function render($aCont, $aIgnoreInHistory = FALSE) {
    if (!$aIgnoreInHistory) {
      $this -> addToHistory($_SERVER['REQUEST_URI']);
    }
    $lPag = $this -> getPage();
    $lPag -> setMmKey($this -> mMmKey);
    $lPag -> setPat('pg.cont', toStr($aCont));
    $lPag -> setPat('pg.title', htm($this -> mTitle));

    if(strpos($this->mMod, 'job') !== FALSE || strpos($this->mMod, 'arc') !== FALSE){
      $lSrc = substr($this -> mMod, 4, 3);
      $lAvailSrc = CCor_Cfg::get('all-jobs_ALINK');
      if(!in_array($lSrc, $lAvailSrc)){
        $lSrc = $_GET['src'];
      }
      $lCls = CApp_Crpimage::getColourForSrc($lSrc);
    } else {
      $lCls = '';
    }

    $lPag -> setPat('pg.sysMsg', CInc_Sys_Msg_Cnt::getMessages());

    $lPag -> setPat('pg.act', $lCls);
    $lPag -> setPat('pg.lib.wait', htm(lan('lib.wait')));
    $lPag -> render();
    $lMsg = CCor_Msg::getInstance();
    $lMsg -> clear();
    exit;
  }

  protected function getStdUrl() {
    return 'index.php?act='.$this -> mMod;
  }

  /**
   * Redirect the user to a different page and exit program execution.
   *
   * Always use redirect after changes to data sources to preserve idempotency!
   * form -> save data -> redirect to list.
   *
   * @param string $aUrl URL to redirect to, e.g. index.php?act=usr
   * @param string|array $aParams parameters to append to the URL
   */
  public function redirect($aUrl = NULL, $aParams = array()) {
    if (empty($aUrl)) {
      $aUrl = $this -> getStdUrl();
    }
    if (!empty($aParams)) {
      if (is_array($aParams)) {
        if (!empty($aParams)) {
          foreach ($aParams as $lKey => $lVal) {
            $aUrl.= '&'.$lKey.'='.$lVal;
          }
        }
      } else {
        $aUrl.= '&'.$aParams;
      }
    }
    $this -> dbg('REDIRECT to '.$aUrl);
    header('Location: '.$aUrl);
    exit;
  }

  protected function actStd() {
    $this -> dbg('No Std Action defined', mlError);
    $this -> render('No Std Action defined');
  }

  protected function actPage() {
    $this -> mReq -> expect('page');
    $lPag = $this -> mReq -> getInt('page');
    $lUsr = CCor_Usr::getInstance();
    $lUsr -> setPref($this -> mPrf.'.page', $lPag);
    $this -> redirect();
  }

  protected function actOrd() {
    $this -> mReq -> expect('fie');
    $lFie = $this -> mReq -> getVal('fie');
    $lUsr = CCor_Usr::getInstance();
    $lUsr -> setPref($this -> mPrf.'.ord', $lFie);
    $this -> redirect();
  }

  protected function actLpp() {
    $this -> mReq -> expect('lpp');
    $lLpp = $this -> mReq -> getVal('lpp');
    $lUsr = CCor_Usr::getInstance();
    $lUsr -> setPref($this -> mPrf.'.lpp', $lLpp);
    $lUsr -> setPref($this -> mPrf.'.page', 0);
    $this -> redirect();
  }

  protected function actFil() {
    $this -> mReq -> expect('val');
    $lVal = $this -> mReq -> getVal('val');
    $lUsr = CCor_Usr::getInstance();
    $lUsr -> setPref($this -> mPrf.'.fil', $lVal);
    $lUsr -> setPref($this -> mPrf.'.page', 0);
    $this -> redirect();
  }

  protected function actClfil() {
    $lUsr = CCor_Usr::getInstance();
    $lUsr -> setPref($this -> mMod.'.fil', '');
    $lUsr -> setPref($this -> mMod.'.page', 0);
    $this -> redirect();
  }

  protected function actSer() {
    $this -> mReq -> expect('val');
    $lReq = $this -> getReq('val', array());
    $lArr = array();
    foreach ($lReq as $lKey => $lVal) {
      if ('' === $lVal) continue;
      $lArr[$lKey] = $lVal;
    }
    $lUsr = CCor_Usr::getInstance();
    $lUsr -> setPref($this -> mMod.'.ser', $lArr);
    $lUsr -> setPref($this -> mMod.'.page', 0);
    $this -> redirect();
  }

  protected function actClser() {
    $lUsr = CCor_Usr::getInstance();
    $lUsr -> setPref($this -> mMod.'.ser', '');
    $lUsr -> setPref($this -> mMod.'.page', 0);
    $this -> redirect();
  }

  protected function actPrint() {
    $lPag = CHtm_Page::getInstance();
    $lPag -> openProjectFilename('print.htm');
    $this -> actStd();
  }

  protected function getWecLogout() {
    $lRet = '';
    $lreswec = $this -> getReq('reswec');
    if (!empty($lreswec)) {
      $lWec = new CApi_Wec_Client();
      $lWec -> loadConfig();

      // system information reg. WebCenter
      $lCfg = CCor_Cfg::getInstance();
      $lAdmWecUser = $lCfg -> getVal('wec.user');
      $lAdmWecPass = $lCfg -> getVal('wec.pass');

      // user information reg. WebCenter
      $lUsrId = CCor_Usr::getAuthId();
      $lUsrInf = new CCor_Usr_Info($lUsrId);
      $lUsrWecUser = $lUsrInf -> get('wec_usr');
      $lUsrWecPass = $lUsrInf -> get('wec_pwd');

      if (!empty($lUsrWecUser) && !empty($lUsrWecPass) && ($lAdmWecUser != $lUsrWecUser) && ($lAdmWecPass != $lUsrWecPass)) {
        $lWec -> setUser($lUsrWecUser, $lUsrWecPass);
      } else {
        $lWec -> setUser('', '');
      }

      $lRet.= '<img src="'.$lWec -> getUrl('logoff.jsp').'" width="10" height="10" alt="&nbsp;" ></img>';
    }
    return $lRet;
  }
}