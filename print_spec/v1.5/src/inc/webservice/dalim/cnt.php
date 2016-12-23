<?php
class CInc_Webservice_Dalim_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    CCor_Qry::exec('SELECT 1'); // force connect to DB so esc() works
    $this->denyIfNonLocalIp();
    $this->mUseDalimDisplayId = CCor_Cfg::get('dalim.displayid', false);
  }
  
  protected function denyIfNonLocalIp() {
    $lIp = $_SERVER['REMOTE_ADDR'];
    $lIsLocal = false;
    if ('127.0.0.1' == $lIp) {
      $lIsLocal = true;
    }
    if (!filter_var($lIp, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE)) {
      $lIsLocal = true;
    }
    if (!$lIsLocal) {
      error_log('Received Dalim request from non-local IP '.$lIp);
      header('HTTP/1.1 500 Internal Server Error');
      exit;
    }
  }

  protected function doLog($aText) {
    error_log($this->mMod.'.'.$this->mAct.': '.$aText.LF, 3, '/home/www-data/dalim-callback.log');
  }

  /*
   <Property Key="GetApprovalStatus" Value="http://192.168.50.6/projektportal/index.php?act=webservice-dalim.getapprovalstatus&amp;user=$(UserName)&amp;doc=$(DocumentName)&amp;page=$(PageNumber)"/>
  <Property Key="SetApprovalStatus" Value="http://192.168.50.6/projektportal/index.php?act=webservice-dalim.setapprovalstatus&amp;user=$(UserName)&amp;doc=$(DocumentName)&amp;page=$(PageNumber)&amp;status=$(Status)"/>
  <Property Key="LoadUserProperties" Value="http://192.168.50.6/projektportal/index.php?act=webservice-dalim.loaduserproperties&amp;user=$(UserName)&amp;doc=$(DocumentName)"/>
  <Property Key="StoreUserProperty" Value="http://192.168.50.6/projektportal/index.php?act=webservice-dalim.storeuserproperty&amp;user=$(UserName)&amp;property=$(PropertyName)&amp;Value=$(PropertyValue)"/>
  <Property Key="RemoveUserProperty" Value="http://192.168.50.6/projektportal/index.php?act=webservice-dalim.removeuserproperty&amp;user=$(UserName)&amp;property=$(PropertyName)"/>
  <Property Key="CreateNoteID" Value="http://192.168.50.6/projektportal/index.php?act=webservice-dalim.createnoteid&amp;doc=$(DocumentName)&amp;page=$(PageNumber)"/>
  <Property Key="LoadNotes" Value="http://192.168.50.6/projektportal/index.php?act=webservice-dalim.loadnotes&amp;doc=$(DocumentName)&amp;page=$(PageNumber)"/>
  <Property Key="StoreNote" Value="http://192.168.50.6/projektportal/index.php?act=webservice-dalim.storenote&amp;doc=$(DocumentName)&amp;page=$(PageNumber)&amp;id=$(NoteID)"/>
  <Property Key="DeleteNote" Value="http://192.168.50.6/projektportal/index.php?act=webservice-dalim.deletenote&amp;doc=$(DocumentName)&amp;page=$(PageNumber)&amp;id=$(NoteID)"/>
  <Property Key="GetDocumentInfos" Value="http://192.168.50.6/projektportal/index.php?act=webservice-dalim.getdocumentinfos&amp;doc=$(DocumentName)&amp;page=$(PageNumber)"/>
  <Property Key="StoreDocumentInfos" Value="http://192.168.50.6/projektportal/index.php?act=webservice-dalim.storedocumentinfos&amp;doc=$(DocumentName)&amp;page=$(PageNumber)"/>
  */

  /**
   *   <Property Key="LoadUserProperties" Value="...loaduserproperties
  *   &amp;user=$(UserName)&amp;doc=$(DocumentName)"/>
  *
  *   Used: Currently not used, we use Dalim Userprops and the locked param when opening files
  */

  protected function actLoaduserproperties() {
    $lUser = $this->getReq('user');
    $lDoc  = $this->getReq('doc');

    $lArr = explode('/', $lDoc);
    $lJid = $lArr[1];
    $lVer = explode('_', $lArr[2]);
    $lVer = intval($lVer[1]);

    $lFiles = new CApi_Dalim_Files('art', $lJid);
    $lJnr = intval($lJid);
    $lMax = $lFiles->getMaxVersion($lJnr.'.pdf');

    $lProp = new CInc_Api_Dalim_Userproperties();
    if ($lMax > $lVer) {
      $lProp->setReaderAccess();
    }
    $this->doLog('loadUserProp on '.$lDoc.' Version '.$lVer.' Max '.$lMax);

    $this->doLog($lProp->getContent());
    echo $lProp->getContent();
  }

  protected function actStoreuserproperty() {

  }

  protected function actRemoveuserproperty() {

  }

  protected function checkVolume($aVol, $aMethod = 'GET') {
  	$lMyVol = CCor_Cfg::get('dalim.volume');
  	$lVol = substr($aVol, 0, 1); // to prevent log forging
  	if ($lVol != $lMyVol) {
  	  error_log('Received Dalim Volume "'.$lVol.'", but only responsible for '.$lMyVol);
  	  header('HTTP/1.1 500 Internal Server Error');
  	  exit;
  	}
  }

  #  <Property Key="CreateNoteID"
  # Value="http://192.168.50.6/projektportal/index.php?act=webservice-dalim.
  # createnoteid&amp;doc=$(DocumentName)&amp;page=$(PageNumber)"/>

  protected function actCreateNoteId() {
    $lDoc  = $this->getReq('doc');

    $lVol = substr($lDoc,0,1);
    $this->checkVolume($lVol);

    $lPag  = $this->getReq('page');
    $lSql = 'INSERT INTO al_dalim_notes SET ';
    $lSql.= 'doc='.esc($lDoc).',page='.intval($lPag);
    $lQry = new CCor_Qry($lSql);
    $lId = $lQry->getInsertId();
    $this->doLog('createNoteId '.$lDoc.':'.$lPage.', created ID '.$lId);
    echo '<Note ID="'.$lId.'"/>';
    exit;
  }

  # <Property Key="StoreNote" Value="http://192.168.50.6/projektportal/index.php?act=webservice-dalim.storenote&amp;doc=$(DocumentName)&amp;page=$(PageNumber)&amp;id=$(NoteID)"/>
  protected function actStoreNote() {
    try {
      $lDoc  = $this->getReq('doc');

      $lVol = substr($lDoc,0,1);
      $this->checkVolume($lVol, 'POST');

      $lPag  = $this->getReq('page');
      $lId  = $this->getReqInt('id');
      $this->doLog('storeNoteId '.$lDoc.':'.$lPag.' ID '.$lId);

      $lArr = explode('/', $lDoc);
      $lVol = $lArr[0];
      $lJid = $lArr[1];
      $lMid = $lArr[2];
      
      if ($this->mUseDalimDisplayId) {
        CApi_Dalim_Utils::checkFields();
      }

      $lContent = '';
      $lNote = file_get_contents("php://input");

      if (!empty($lNote)) {
        $lCont = array();
        $lDom = new DOMDocument();
        $lDom->loadXML($lNote);

        $lKey = $lDom->documentElement->getAttribute('Key');
        $lArr = explode('_', $lKey);
        $lUid = intval($lArr[0]);
        $lMid = intval($lArr[1]);
        
        if ($this->mUseDalimDisplayId) {
          $lParent = $lDom->documentElement->getAttribute('ParentID');
          $lDisplayId = $lDom->documentElement->getAttribute('DisplayID');
        }
        $lSql = 'SELECT src FROM al_job_shadow_'.$lMid.' WHERE jobid='.esc($lJid);
        $lSrc = CCor_Qry::getStr($lSql);
        #$this->doLog($lSql);
        if (empty($lSrc)) $lSrc = 'art';

        $lLoop = new CApp_Apl_Loop($lSrc, $lJid, 'apl', $lMid);
        $lLoopId = $lLoop->getLastOpenLoop();

        $lArr  = $lDom->getElementsByTagName('Content');
        foreach ($lArr as $lNode) {
          $lCont[] = $lNode->textContent;
        }
        $lContent = implode(LF, $lCont);
      }
      
      $lXmlProc = '<?xml version="1.0" encoding="UTF-8"?>';
      $lNoteClean = trim(str_replace($lXmlProc, '', $lNote));
      $lSql = 'UPDATE al_dalim_notes SET ';
      $lSql.= 'xml='.esc($lNoteClean).' ';
      if (!empty($lContent)) {
        $lSql.= ',content='.esc($lContent);
      }
      if ($this->mUseDalimDisplayId) {
        if (!empty($lParent)) {
          $lSql.=',parent_id='.intval($lParent);
        }
        if (!empty($lDisplayId)) {
          $lSql.=',num='.intval($lDisplayId);
        }
      }
      $lSql.= ',jobid='.esc($lJid);
      $lSql.= ' WHERE id='.$lId;
      CCor_Qry::exec($lSql);

      if (!empty($lUid)) {
        // do this separately to avoid overwriting the author of a note
        $lSql = 'UPDATE al_dalim_notes SET ';
        $lSql.= 'user_id='.esc($lUid).' ';
        $lSql.= 'WHERE id='.$lId.' ';
        $lSql.= 'AND user_id=0 LIMIT 1';
        CCor_Qry::exec($lSql);
      }
      if (!empty($lLoopId)) {
        $lSql = 'UPDATE al_dalim_notes SET ';
        $lSql.= 'loop_id='.esc($lLoopId).' ';
        $lSql.= 'WHERE id='.$lId.' ';
        $lSql.= 'AND loop_id IS NULL LIMIT 1';
        CCor_Qry::exec($lSql);
      }
      if (empty($lSrc)) $lSrc = 'art';
      
      $lFiles = new CApi_Dalim_Files($lSrc, $lJid);
      $lFiles->lockLatestVersion();

    } catch (Exception $exc) {
      $this->doLog($exc->getMessage());
    }
    exit;
  }

  #  <Property Key="DeleteNote" Value="http://192.168.50.6/projektportal/index.php?act=webservice-dalim.deletenote&amp;doc=$(DocumentName)&amp;page=$(PageNumber)&amp;id=$(NoteID)"/>
  protected function actDeleteNote() {
    $lDoc  = $this->getReq('doc');

    $lVol = substr($lDoc,0,1);
    $this->checkVolume($lVol);

    $lPag  = $this->getReq('page');
    $lId  = $this->getReqInt('id');
    $this->doLog('deleteNote '.$lDoc.':'.$lPag.' ID '.$lId);

    $lSql = 'DELETE FROM al_dalim_notes ';
    $lSql.= 'WHERE id='.$lId;

    $lQry = new CCor_Qry($lSql);
    exit;
  }

  #   <Property Key="LoadNotes" Value="http://192.168.50.6/projektportal/index.php?act=webservice-dalim.loadnotes&amp;doc=$(DocumentName)&amp;page=$(PageNumber)"/>
  protected function actLoadNotes() {
    $lDoc  = $this->getReq('doc');

    $lVol = substr($lDoc,0,1);
    $this->checkVolume($lVol);

    $lPag  = $this->getReqInt('page');
    $this->doLog('loadNotes '.$lDoc.':'.$lPag);

    $lSql = 'SELECT * FROM al_dalim_notes ';
    $lSql.= 'WHERE doc='.esc($lDoc).' ';
    if (!empty($lPag)) {
      $lSql.= 'AND page='.$lPag;
    }
    $lRet = '';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lRet.= $lRow['xml'];
    }

    echo '<?xml version="1.0" encoding="UTF-8"?>';
    if (empty($lRet)) {
      echo '<Notes />';
    } else {
      echo '<Notes>';
      echo $lRet;
      echo '</Notes>';
    }
    exit;
  }


  # <Property Key="GetApprovalStatus" Value="http://192.168.50.6/projektportal/index.php?act=webservice-dalim.getapprovalstatus&amp;user=$(UserName)&amp;doc=$(DocumentName)&amp;page=$(PageNumber)"/>
  protected function actGetapprovalstatus() {
    $lDoc  = $this->getReq('doc');

    // no need to redirect, answer is static

    $lPag  = $this->getReqInt('page');
    $this->doLog('getApprovalStatus '.$lDoc.':'.$lPag);
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<Approval Status="NotSet" />';
    exit;
  }

  # <Property Key="SetApprovalStatus" Value="http://192.168.50.6/projektportal/index.php?act=webservice-dalim.setapprovalstatus&amp;user=$(UserName)&amp;doc=$(DocumentName)&amp;page=$(PageNumber)&amp;status=$(Status)"/>
  protected function actSetapprovalstatus() {
    $lDoc  = $this->getReq('doc');

    // no need to redirect, no answer required and no data changed
    $lPag  = $this->getReqInt('page');
    $lStat = $this->getReqInt('status');

    $this->doLog('setApprovalStatus '.$lDoc.':'.$lPag.' to '.$lStat);
    exit;
  }

}
