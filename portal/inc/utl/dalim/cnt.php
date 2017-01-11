<?php
class CInc_Utl_Dalim_Cnt extends CCor_Cnt {

  // Based on preference, viewer related actions need to use
  // html5 methods instead of default (java) ones
  public function dispatch() {
    if (!$this -> canDo($this -> mAct)) {
      return;
    }
    $lUsr = CCor_Usr::getInstance();
    $lPref = $lUsr->getPref('dalim.viewer', 'html5');
    if ('java' == $lPref) {
      parent::dispatch();
    }
    $lFnc = 'actHtml5'.$this -> mAct;
    if ($this -> hasMethod($lFnc)) {
      $this -> $lFnc();
    } else {
      parent::dispatch();
    }
  }


  // Get thumbnail of a file, same for java & html5
  protected function actThumb() {
    $lDoc = $this->getReq('doc');
    $lUtil = new CApi_Dalim_Utils();
    $lImg = $lUtil->getThumbnail($lDoc);
    if ($lImg) {
      header('Content-type: image/jpeg');
      echo $lImg;
    }
    exit;
  }

  // Download a pdf with a preview with the notes report, same for java & html5
  protected function actDownloadNotes() {
    $lDoc = $this->getReq('doc');
    $lName = $this->getReq('fn');
    $lUtil = new CApi_Dalim_Utils();
    $lPdf = $lUtil->getPdfReport($lDoc);
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="'.$lName.'"');
    echo $lPdf;
    exit;
  }

  // Download a pdf with a preview with the notes report, same for java & html5
  protected function actDownloadHiresNotes() {
    $lDoc = $this->getReq('doc');
    $lUtil = new CApi_Dalim_Utils ();
    $lPdf = $lUtil->getHires($lDoc);
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . basename ($lDoc) . '"');
    echo $lPdf;
    exit ();
  }
  
  // Called when returning from viewer, same for java & html5
  protected function actClose() {
    $lJid = $this -> getReq('jid');
    $lSrc = $this -> getReq('src');
    $lReturnTo = $this -> getReq('returnto');

    if ($lReturnTo == 'files') {
      $lUrl = 'index.php?act=job-'.$lSrc.'-fil&jobid='.$lJid;
    } elseif ($lReturnTo == 'apl') {
      $lUrl = 'index.php?act=job-apl&src='.$lSrc.'&jobid='.$lJid;
    } else {
      $lUrl = 'index.php?act=job-'.$lSrc.'.edt&jobid='.$lJid;
    }
    $this -> trackClick($lUrl, $lJid);
    $this -> redirect($lUrl);
  }

  protected function trackClick($aUrl, $aJid) {
    if (CCor_Cfg::get('use-user-tracking')) {
      $lUrl = (empty($aUrl)) ? $_SERVER['REQUEST_URI'] : $aUrl;
      $lTrackerLog = new CCor_Log();
      $lTrackerLog -> onClickTracker($lUrl, $aJid);
    }
  }

  protected function canAnnotate($aSrc, $aJid) {
    $lKey = $aSrc.'-'.$aJid;
    // cache this as it requires loading the job
    if (!isset($this->mCanAnnotate[$lKey])) {
      $lUid =  CCor_Usr::getAuthId();
      $lCan = new CCor_Usr_Annotate($lUid, '', $aJid, $aSrc);
      $this->mCanAnnotate[$lKey] = $lCan->canAnnotate();
    }
    return $this->mCanAnnotate[$lKey];
  }

  // ---------------------------------------------------------------------------
  // JAVA
  // ---------------------------------------------------------------------------


  // return to->     | FILES    | APL
  //---------------------------------------------
  // SINGLE FILE     | actOpen  | actOpenApl
  // MULTIPLE FILES  | actMulti | actMultipleApl


  // open single file in Java Viewer and return to files tab
  protected function actOpen() {
    $this->openSingleJava('files');
  }

  // open a file in java viewer and return to APL tab
  protected function actOpenapl() {
    $this->openSingleJava('apl');
  }

  // Used by actOpen and actOpenapl
  protected function openSingleJava($aReturn) {
    $lSrc = $this->getReq('src');
    $lJid = $this->getReq('jid');
    $lDoc = $this->getReq('doc');

    $lUsr = CCor_Usr::getInstance();
    $lCanUserAnnotate = $this->canAnnotate($lSrc, $lJid);

    if (false !== strpos($lDoc, '/')) {
      $lDoc = substr(strrchr($lDoc, '/'), 1);
    }

    $lVolDoc = CCor_Cfg::get('dalim.volume', 'A').':'.$lJid.'/'.$lDoc;
    $lKey = $lUsr->getVal('id').'_'.MID.'_'.uniqid();

    $lUsrName = $lUsr->getVal('fullname');
    $lSid = CApi_Dalim_Auth::getSessionId($lUsrName, $lVolDoc, $lKey);

    $lRet = '<html><head>';
    $lRet.= '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
    $lRet.= '</head><body style="border:0; margin:0">';

    $lApp = new CApi_Dalim_Applet($lSid);
    $lApp->setUser($lUsrName);
    $lApp->addParam('doc1', $lVolDoc);
    if (!$lCanUserAnnotate) {
      $lApp->addParam('lock1', true);
    }
    $lRetUrl = 'utl-dalim.close&returnto='.$aReturn.'&jid='.$lJid.'&src='.$lSrc;
    $lApp->addClose($lRetUrl);

    $lRet.= $lApp->getContent();
    $lRet.= '</body></html>';

    $this -> trackClick('', $lJid);
    echo $lRet;
    exit;
  }

  // Open multiple files in java viewer, return to files
  protected function actMulti() {
    $lDoc = $this->getReq('docs');
    if (empty($lDoc)) {
      return;
    }
    $lBase = CCor_Cfg::get('dalim.volume', 'A').':';
    $lDocParam = array();
    $lNum = 1;
    foreach ($lDoc as $lRow) {
      $lArr = explode(',', $lRow);
      $lCurJid = $lArr[1]; // 0 is src, currently not used
      $lCurDoc = $lArr[2];
      $lVolDoc = $lBase.$lCurJid.'/'.$lCurDoc;

      $lDocParam[$lNum] = $lVolDoc;
      $lNum++;
    }
    $this->openMultiJava($lDocParam, 'files');
  }

  // Open multiple files in java viewer, return to APL tab
  protected function actMultipleapl() {
    $lDoc = $this->getReq('doc');
    $lJid = $this->getReq('jid');
    if (empty($lDoc)) {
      return;
    }
    $lBase = CCor_Cfg::get('dalim.volume', 'A').':';
    $lBase.= $lJid.'/';
    $lDocParam = array();
    $lNum = 1;
    foreach ($lDoc as $lName) {
      $lVolDoc = $lBase.$lName;
      $lDocParam[$lNum] = $lVolDoc;
      $lNum++;
    }
    $this->openMultiJava($lDocParam, 'apl');
  }



  protected function openMultiJava($aDocs, $aReturn) {
    $lSrc = $this->getReq('src');
    $lJid = $this->getReq('jid');

    $lUsr = CCor_Usr::getInstance();
    $lUsrName = $lUsr->getVal('fullname');
    $lKey = $lUsr->getVal('id').'_'.MID.'_'.uniqid();

    $lSid = CApi_Dalim_Auth::getSessionId($lUsrName, null, $lKey);

    $lUid = $lUsr -> getAuthId();
    $lCanUserAnnotate = $this-> canAnnotate($lSrc, $lJid);

    $lRet = '<html><head>';
    $lRet.= '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
    $lRet.= '</head><body style="border:0; margin:0">';

    $lApp = new CApi_Dalim_Applet($lSid);
    $lApp->setUser($lUsrName);

    $lBase = CCor_Cfg::get('dalim.volume', 'A').':';

    $lNum = 1;
    foreach ($aDocs as $lVolDoc) {
      $lApp->addParam('doc'.$lNum, $lVolDoc);
      if (($lNum > 1) || (!$lCanUserAnnotate)) {
        $lApp->addParam('lock'.$lNum, 'true');
      }
      $lNum+= 1;
    }
    $lRetUrl = 'utl-dalim.close&returnto='.$aReturn.'&jid='.$lJid.'&src='.$lSrc;
    $lApp->addClose($lRetUrl);
    $lRet.= $lApp->getContent();

    $lRet.= '</body></html>';

    $this -> trackClick('', $lJid);
    echo $lRet;
    exit;
  }


  // ---------------------------------------------------------------------------
  // HTML 5
  // ---------------------------------------------------------------------------

  // return to->     | FILES         | APL
  //---------------------------------------------
  // SINGLE FILE     | actHtml5Open  | actHtml5OpenApl
  // MULTIPLE FILES  | actHtml5Multi | actHtml5MultipleApl


  // open single file in HTML5 Viewer and return to files tab
  protected function actHtml5Open() {
    $lJid = $this->getReq('jid');
    $lDoc = $lJid.'/'.$this->getReq('doc');
    $this->openSingleHtml5('files', $lDoc);
  }

  // open a file in HTML5 viewer and return to APL tab
  protected function actHtml5Openapl() {
    $lDoc = $this->getReq('doc');
    $this->openSingleHtml5('apl', $lDoc);
  }

  // helper for single HTML5 files
  protected function openSingleHtml5($aReturn, $aDoc) {
    // for callback
    $lSrc = $this->getReq('src');
    $lJid = $this->getReq('jid');
    $lDoc = $aDoc;

    $lRetUrl = 'utl-dalim.close&returnto='.$aReturn.'&jid='.$lJid.'&src='.$lSrc;

    $lVolDoc = CCor_Cfg::get('dalim.volume', 'A').':'.$lDoc;
    $lUsr = CCor_Usr::getInstance();
    $lUid = $lUsr -> getAuthId();

    $lCanUserAnnotate = $this->canAnnotate($lSrc, $lJid);
    $lLock = ($lCanUserAnnotate) ? '' : ', locked:true';

    $lDisplayName = substr($lDoc, strripos($lDoc, '/') +1);
    $lDocs = '[{id:"'.$lVolDoc.'",displayName:"'.$lDisplayName.'"'.$lLock.'}]';

    $lPath = pathinfo(__FILE__, PATHINFO_DIRNAME);

    $this -> trackClick('', $lJid);

    $lKey = $lUsr->getVal('id').'_'.MID.'_'.uniqid();
    $lUsrName = $lUsr->getVal('fullname');
    $lSid = CApi_Dalim_Auth::getSessionId($lUsrName, $lVolDoc, $lKey);
    $this->setJsessionId($lSid);

    $lTpl = new CCor_Tpl();
    $lTpl->open($lPath . '/viewer.htm');
    $lTpl->setPat('my.docs', $lDocs);
    $lTpl->setPat('my.username', $lUsrName);
    $lTpl->setPat('dalim.baseurl', CCor_Cfg::get('dalim.baseurl'));
    $lTpl->setPat('my.return', $lRetUrl);
    $lCon = $lTpl->getContent();

    echo $lCon;
    exit;
  }

  protected function setJsessionId($aSessionId) {
    $lBase   = CCor_Cfg::get('dalim.baseurl');
    $lArr    = parse_url($lBase);
    $lSecure = $lArr['scheme'] == 'https';
    $lDomain = $lArr['host'];
    $lPath   = $lArr['path']; // dirname(CCor_Cfg::get('dalim.baseurl')).'/';
    setcookie('JSESSIONID', $aSessionId, 0, $lPath, $lDomain, $lSecure, true);
  }

  // OPEN MULTIPLE FILES ---------------------------------------

  // Open multiple files in HTML5 viewer, return to files tab
  protected function actHtml5Multi() {
    $lDoc = $this->getReq('docs');
    $lDocs = array();
    foreach ($lDoc as $lRow) {
      $lArr = explode(',', $lRow);
      $lItm = array();
      $lItm['src'] = $lArr[0];
      $lItm['jid'] = $lArr[1];
      $lItm['doc'] = $lArr[2];
      $lKey = implode('-',$lItm); // prevent opening the same file twice
      $lDocs[$lKey] = $lItm;
    }
    $this->openMultiHtml5($lDocs, 'files');
  }

  // Open multiple files in html5 viewer, return to APL tab
  protected function actHtml5Multipleapl() {
    $lSrc = $this->getReq('src');
    $lJid = $this->getReq('jid');
    $lDoc = $this->getReq('doc');

    $lDocs = array();
    foreach ($lDoc as $lDocName) {
      $lItm = array();
      $lItm['src'] = $lSrc;
      $lItm['jid'] = $lJid;
      $lItm['doc'] = $lDocName;
      $lKey = implode('-',$lItm); // prevent opening the same file twice
      $lDocs[$lKey] = $lItm;
    }
    $this->openMultiHtml5($lDocs, 'apl');
  }

  protected function openMultiHtml5($aDocs, $aReturn) {
    $lSrc = $this->getReq('src');
    $lJid = $this->getReq('jid');
    if (empty($aDocs)) {
      echo 'No files selected';
      return;
    }
    $lBaseUrl = CCor_Cfg::get('base.url');
    $lRetUrl = 'utl-dalim.close&returnto='.$aReturn.'&jid='.$lJid.'&src='.$lSrc;

    $lUsr = CCor_Usr::getInstance();

    $lTpl = new CCor_Tpl();
    $lPath = pathinfo(__FILE__, PATHINFO_DIRNAME);
    $lTpl->open($lPath . '/viewer.htm');
    $lTpl->setPat('my.username', $lUsr->getVal('fullname'));
    $lTpl->setPat('dalim.baseurl', CCor_Cfg::get('dalim.baseurl'));

    $lDocs = '';
    $lBase = CCor_Cfg::get('dalim.volume', 'A');//.':'.$lJid.'/';
    $lNum = 1;
    $lIsFirst = true;
    foreach ($aDocs as $lRow) {
      $lSrc = $lRow['src'];
      $lJid = $lRow['jid'];
      $lDoc = $lRow['doc'];
      $lVolDoc = $lBase.':'.$lJid.'/'.$lDoc;
      if ($lIsFirst) {
        $lIsFirst = false;
        $lLocked = ($this->canAnnotate($lSrc, $lJid)) ? 'false' : 'true';
        $lDocs.= '[{id:"'.$lVolDoc.'",displayName:"'.$lRow['doc'].'", locked:'.$lLocked.', revisions:['.LF;
        $lDocs.= '{id:"'.$lVolDoc.'",displayName:"'.$lRow['doc'].'", locked:'.$lLocked.'},';
      } else {
        $lDocs.= '{id:"'.$lVolDoc.'",displayName:"'.$lRow['doc'].'", locked:true },';
      }
    }
    $lDocs = strip($lDocs).'] }]';

    $lKey = $lUsr->getVal('id').'_'.MID.'_'.uniqid();
    $lUsrName = $lUsr->getVal('fullname');
    $lSid = CApi_Dalim_Auth::getSessionId($lUsrName, null, $lKey);
    $this->setJsessionId($lSid);

    $lTpl->setPat('my.docs', $lDocs);
    $lTpl->setPat('my.return', $lRetUrl);
    $lRet = $lTpl->getContent();

    if (CCor_Cfg::get('use-user-tracking')) {
      $lTrackerLog = new CCor_Log();
      $lTrackerLog -> onClickTracker('', $lJid);
    }
    echo $lRet;
    exit;
  }

}
