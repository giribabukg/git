<?php
class CInc_Utl_Wec_Cnt extends CCor_Cnt {

  protected function actThumb() {
    $lDoc = $this -> getReq('doc');
    $lVer = $this -> getReq('ver');

    $lWec = new CApi_Wec_Client();
    $lWec -> loadConfig();

    $lQry = new CApi_Wec_Query_Thumbnail($lWec);
    $lisViewable = $lQry -> isViewable($lDoc, $lVer);

    if ($lisViewable) {
      $lQry = new CApi_Wec_Query_Thumbnail($lWec);
      $lRet = $lQry -> getImage($lDoc, $lVer);

      if (!$lRet) {
        readfile('img/ico/big/noimg.gif');
        exit;
      }
    } else {
      readfile('img/pag/ajx.gif');
      exit;
    }

    header('Pragma: public');
    header('Expires: 0');
    header('Cache-Control: private');

    echo $lRet;
    exit;
  }

  protected function actThumbExists() {
    $lDoc = $this -> getReq('doc');
    $lVer = $this -> getReq('ver');

    $lWec = new CApi_Wec_Client();
    $lWec -> loadConfig();

    $lQry = new CApi_Wec_Query_Thumbnail($lWec);
    $lRet = $lQry -> isViewable($lDoc, $lVer);

    if (!$lRet) {
      echo 0;
      exit;
    } else {
      echo 1;
      exit;
    }
  }

  protected function actOpen() {

    $lJid = $this -> getReq('jid');
    $lSrc = $this -> getReq('src');
    $lApl = $this -> getReq('apl'); // Back to Apl Invite Page from Webcenter
    $lJobApl = $this -> getReq('jobapl'); // Back to Job-Apl Page from Webcenter
    $lArcApl = $this -> getReq('arcapl'); // Back to Archiv-Apl Page from Webcenter
    $lWecPid = $this -> getReq('pid');
    $lWecDoc = $this -> getReq('doc');
    $lWecDocId = $this -> getReq('docid');

    $lRetUrl = CCor_Cfg::get('base.url').'index.php?act=utl-wec.close';
    $lRetUrl.= '&src='.$lSrc.'&jid='.$lJid.'&docid='.$lWecDocId;
    if ($lApl) {
      $lRetUrl.= '&apl=1';
    }
    if ($lJobApl) {
      $lRetUrl.= '&jobapl=1';
    }
    if ($lArcApl) {
      $lRetUrl.= '&arcapl=1';
    }

    $lUsr = CCor_Usr::getInstance();
    $lWecUid = $lUsr -> getInfo('wec_uid');

    $lWec = new CApi_Wec_Client();
    $lWec -> loadConfig(CCor_Cfg::get('wec.useext', FALSE));

    // create the user?
    if (empty($lWecUid)) {
      $lUid = $lUsr -> getId();
      $lQry = new CApi_Wec_Query_Createuser($lWec);
      $lRes = $lQry -> createFromDb($lUid);
      if (!$lRes) {
        $this -> msg(lan('usr-wecusr.menu').' '.lan('lib.nocreate'));
        $this -> redirect('index.php?act=hom-wel');
      }
    }

    $lWecUsr = $lUsr -> getInfo('wec_usr');
    $lWecPwd = $lUsr -> getInfo('wec_pwd');
    $lWec -> setUser($lWecUsr, $lWecPwd);

    $lPar = array();
    $lPar['projectid']    = $lWecPid;
    $lPar['documentname'] = $lWecDoc;
    $lReturnURL = CCor_Cfg::get('wec.view.close', 'return');
    if ($lReturnURL == 'return') {
      $lPar['returnurl'] = $lRetUrl;
    } else {
      $lRetAltUrl = CCor_Cfg::get('base.url').'index.php?act=utl-wec.closetab';
      $lRetAltUrl.= '&src='.$lSrc.'&jid='.$lJid.'&docid='.$lWecDocId;
      if ($lApl) {
        $lRetAltUrl.= '&apl=1';
      }
      if ($lJobApl) {
        $lRetAltUrl.= '&jobapl=1';
      }
      if ($lArcApl) {
        $lRetAltUrl.= '&arcapl=1';
      }
      $lPar['returnurl'] = $lRetAltUrl;
    }

    $lUid = $lUsr -> getAuthId();
    $lUsrAnnotate = new CInc_Cor_Usr_Annotate($lUid, '', $lJid, $lSrc);
    $lCanUserAnnotate = $lUsrAnnotate -> canAnnotate();
    if ($lCanUserAnnotate != 1) {
      $lPar['annotationmode'] = 3;
    }

    $lUrl = $lWec -> getUrl('OpenViewer.jsp', $lPar);

    if (CCor_Cfg::get('use-passive-apl-roles')) {
      $lApl = new CInc_App_Apl_Loop($lSrc, $lJid);
      $lApl->addPassiveRolesintoApl();
    }

    if (CCor_Cfg::get('use-user-tracking')) {
      $lTrackerLog = new CCor_Log();
      $lTrackerLog -> onClickTracker($lUrl, $lJid);
    }

    $this -> redirect($lUrl);
  }

  protected function actClose() {
    $lJid = $this -> getReq('jid');
    $lSrc = $this -> getReq('src');
    $lApl = $this -> getReq('apl'); // Back to Apl Invite Page from Webcenter
    $lSub = $this -> getReq('sub'); // Back to files page onl if !empty(sub)

    $lJobApl = $this -> getReq('jobapl'); // Back to Job-Apl Page from Webcenter
    $lArcApl = $this -> getReq('arcapl'); // Back to Archiv-Apl Page from Webcenter
    $lNoUserAccess = $this -> getReq('nac'); // no Access = not involved in APL now
    // Add and exucute action wechistory
    $this -> addWecHistory($lJid, $lSrc);

    // set return url
    if ($lApl) {
      $lUrl = 'index.php?act=job-apl-page.setstate&src='.$lSrc.'&jid='.$lJid;
    } elseif ($lNoUserAccess || $lJobApl) {
      $lUrl = 'index.php?act=job-apl&src='.$lSrc.'&jobid='.$lJid;
    } elseif ($lArcApl) {
      $lUrl = 'index.php?act=job-apl&mod=arc&src='.$lSrc.'&jobid='.$lJid;
    } elseif ($lSub) {
      $lUrl = 'index.php?act=job-'.$lSrc.'-fil&jobid='.$lJid.'&sub='.$lSub;
    } else {
      $lUrl = 'index.php?act=job-'.$lSrc.'.edt&jobid='.$lJid;
    }
    $lUrl.= '&reswec=1';

    if (CCor_Cfg::get('use-user-tracking')) {
      $lTrackerLog = new CCor_Log();
      $lTrackerLog -> onClickTracker($lUrl, $lJid);
    }

    // START: LOG OFF
    $lCli = new CApi_Wec_Robot();
    $lCli -> loadConfig();
    $lCli -> logout();
    // END: LOG OFF

    $this -> redirect($lUrl);
  }

  protected function actCloseTab() {
    $lJid = $this -> getReq('jid');
    $lSrc = $this -> getReq('src');

    // Add and exucute action wechistory
    $this -> addWecHistory($lJid, $lSrc);
    $lRet = "<html><head></head><body>";

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

    if (!empty($lUsrWecUser) && !empty($lUsrWecPass)) {
        $lWec -> setUser($lUsrWecUser, $lUsrWecPass);
    } else {
        $lWec -> setUser('', '');
    }

    $lRet.= '<img id="logoff" src="'.$lWec -> getUrl('logoff.jsp').'" width="10" height="10" alt="&nbsp;" />';
    $lRet.= "<script type=\"text/javascript\">function closeTab() {if (document.getElementById('logoff').complete) {window.close();}} setInterval(closeTab, 500);</script></body></html>";
    echo $lRet;
  }

  protected function actRedirect() {
    $lUrl = $this -> getReq('url');

    $lWec = new CApi_Wec_Client();
    $lWec -> loadConfig(CCor_Cfg::get('wec.useext', FALSE));

    // system information reg. WebCenter
    $lCfg = CCor_Cfg::getInstance();
    $lAdmWecUser = $lCfg -> getVal('wec.user');
    $lAdmWecPass = $lCfg -> getVal('wec.pass');

    // user information reg. WebCenter
    $lUsrId = CCor_Usr::getAuthId();
    $lUsrInf = new CCor_Usr_Info($lUsrId);
    $lUsrWecUser = $lUsrInf -> get('wec_usr');
    $lUsrWecPass = $lUsrInf -> get('wec_pwd');

    if (!empty($lUsrWecUser) && !empty($lUsrWecPass)) {
      $lWec -> setUser($lUsrWecUser, $lUsrWecPass);
    } else {
      $lWec -> setUser('', '');
    }

    $lret = '';
    $lret.= '<html>'.LF;
    $lret.= '<head>'.LF;
    $lret.= '  <script language="javascript">'.LF;
    $lret.= '  <!--'.LF;
    $lret.= '    var popupwin;'.LF;
    $lret.= '    function quit() {'.LF;
    $lret.= '    } '.LF;
    $lret.= '    function closewindow() {'.LF;
    $lret.= '      popupwin.close();'.LF;
    $lret.= '      location.href = "'.$lUrl.'";'.LF;
    $lret.= '    } '.LF;
    $lret.= '    '.LF;
    $lret.= '    function copyForm() {'.LF;
    $lret.= '      popupwin = window.open("'.$lWec -> getUrl('logoff.jsp').'","_blank");'.LF;
    $lret.= '      window.setTimeout("closewindow()", 2000);    '.LF;
    $lret.= '      return False;    '.LF;
    $lret.= '    }'.LF;
    $lret.= '    window.setTimeout("copyForm()", 500);'.LF;
    $lret.= '  //-->'.LF;
    $lret.= '  </script>'.LF;
    $lret.= '</head>'.LF;
    $lret.= '<body>'.LF;
    $lret.= '</body>'.LF;
    $lret.= '</html>'.LF;

    if(empty($lUrl)) {
      echo htm($lret);
    } else {
      echo $lret;
    }
  }

  protected function actDownload() {

    function getDocDownloadLink($aPrjId, $aDocName, $aZipName = 'WebCenterDownload.zip') {
      $lRet = $this -> url.'downloadDocuments.jsp?';
      $lRet.= $this -> _getAuthParams();
      $lRet.= '&projectID='.urlencode($aPrjId);
      $lRet.= '&DocumentName='.urlencode($aDocName);
      $lRet.= '&DownloadDocName='.urlencode($aZipName);
      return $lRet;
    }
    $lWec = new CApi_Wec_Client();
    $lWec -> loadConfig(CCor_Cfg::get('wec.useext', FALSE));
    $lQry = new CApi_Wec_Query_Thumbnail($lWec);
    $lRet = $lQry -> getImage($lDoc);

    if (!$lRet) {
      readfile('img/ico/big/noimg.gif');
      exit;
    }

    header('Pragma: public');
    header('Expires: 0');
    header('Cache-Control: private');
    #header('Content-type: image/jpeg');
    echo $lRet;
    exit;
  }

  /*
   * add and execute action 'wechistory' in sys_queue
  * @param string $aJobId
  * @param string $aSrc
  * @return void
  */
  public function addWecHistory($aJobId, $aSrc){
    CApi_Wec_Query_History::updateHistory(MID, $aSrc, $aJobId);
  }
}