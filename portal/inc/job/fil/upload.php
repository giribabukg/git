<?php
class CInc_Job_Fil_Upload extends CCor_Obj {

  protected $mFlink = FALSE;

  public function __construct($aSrc, $aJobId, $aJobData = null) {
    $this -> mSrc = $aSrc;
    $this -> mJid = $aJobId;
    $this -> mDat = $aJobData;
    $this -> mUid = CCor_Usr::getAuthId();

    $this -> mFlink = CCor_Cfg::get('flink', FALSE);
  }

  public function setUid($aUid) {
    $this -> mUid = intval($aUid);
  }

  protected function getJobVal($aKey, $aDefault = NULL) {
    if (is_null($this -> mDat)) {
      $this -> loadJob();
    }
    return (isset($this -> mDat[$aKey])) ? $this -> mDat[$aKey] : $aDefault;
  }

  protected function loadJob() {
    $lFac = new CJob_Fac($this -> mSrc, $this -> mJid);
    $this -> mDat = $lFac -> getDat();
  }

  public function uploadRec($aRec, $aParam) {
    return $this -> uploadFile($aRec['tmp_name'], $aRec['name'], $aParam);
  }

  public function uploadFile($aTempPathAndName, $aDestName, $aParam) {
    $lDest = 'doc';
    if (isset($aParam['dest'])) {
      $lDest = $aParam['dest'];
    }
    $lFunc = 'uploadTo'.$lDest;
    if ($this -> hasMethod($lFunc)) {
      return $this -> $lFunc($aTempPathAndName, $aDestName, $aParam);
    }
    return $this -> uploadToDoc($aTempPathAndName, $aDestName, $aParam);
  }

  // provider specific upload functions

  public function uploadToDoc($aTempPathAndName, $aDestName, $aParam) {
    $lSubFolder = (isset($aParam['folder'])) ? $aParam['folder'] : null;
    $lCls = new CApp_Finder($this -> mSrc, $this -> mJid);
    $lDir = $lCls -> getPath('doc');
    if (!empty($lSubFolder)) $lDir.= DS.$lSubFolder;
    $lCat = (isset($aParam['category'])) ? $aParam['category'] : '';
    $lUpl = new CCor_Upload();
    $lRes = $lUpl -> doUpload($aTempPathAndName, $lDir, $aDestName, umAddIndex);
    if (!$lRes) {
      return FALSE;
    }

    CCor_Usr::insertJobFile($this -> mSrc, $this -> mJid, 'doc', $lRes, $lCat);

    $lMsg = sprintf(lan('filupload.success'), $lRes);
    $lHis = new CApp_His($this -> mSrc, $this -> mJid);
    $lHis -> setVal('user_id', $this -> mUid);
    $lHis -> add(htFileupload, lan('filupload.his.msg'), $lMsg);

    $lClass = 'CJob_'.$this -> mSrc.'_Mod';
    $lObject = new $lClass($this -> mJid);
    $lObject -> triggerEvent('eve_upload');

    return TRUE;
  }

  public function uploadToPdf($aTempPathAndName, $aDestName, $aParam) {
    $lFilCon = file_get_contents($aTempPathAndName);

    $lQry = new CApi_Alink_Query('putFile');
    $lQry -> addParam('sid', MAND);
    $lQry -> addParam('jobid', $this -> mJid);
    $lQry -> addParam('filename', intval($this -> mJid).'.pdf');
    $lQry -> addParam('data', base64_encode($lFilCon));
    $lQry -> addParam('mode', 2);
    $lRes = $lQry -> query();

    CCor_Usr::insertJobFile($this -> mSrc, $this -> mJid, 'pdf', $lRes, $lCat);

    $lMsg = sprintf(lan('filupload.success'), $lRes);
    $lHis = new CApp_His($this -> mSrc, $this -> mJid);
    $lHis -> setVal('user_id', $this -> mUid);
    $lHis -> add(htFileupload, lan('filupload.his.msg'), $lMsg);

    $lClass = 'CJob_'.$this -> mSrc.'_Mod';
    $lObject = new $lClass($this -> mJid);
    $lObject -> triggerEvent('eve_upload');

    unlink($aTempPathAndName);
    return TRUE;
  }

  public function uploadToDms($aTempPathAndName, $aDestName, $aParam) {
    $lTemp = basename($aTempPathAndName);
    $lOldName = $aDestName;

    $lShare = CCor_Cfg::get('dms.upload.folder');

    if (false === CCor_Cfg::get('flink', false)) {
      $lRes = move_uploaded_file($aTempPathAndName, $lShare.DS.$lTemp);
    } else {
      $lRes = TRUE;
    }

    if ($lRes OR !CCor_Cfg::get('flink', false)) {
      $lUser = CCor_Usr::getInstance();
      $lAuthor = $lUser -> getVal('fullname');
      $lQry = new CApi_Dms_Query();
      #$lStub = new CApi_Dms_Stub(); $lQry -> setClient($lStub);
      $lQry -> uploadFile($lTemp, $lOldName, $lAuthor, MANDATOR_ENVIRONMENT, $this -> mSrc, $this -> mJid);

      CCor_Usr::insertJobFile($this -> mSrc, $this -> mJid, 'dms', $lRes, 1105);

      $lMsg = sprintf(lan('filupload.success'), $lRes);
      $lHis = new CApp_His($this -> mSrc, $this -> mJid);
      $lHis -> setVal('user_id', $this -> mUid);
      $lHis -> add(htFileupload, lan('filupload.his.msg'), $lMsg);

      $lClass = 'CJob_'.$this -> mSrc.'_Mod';
      $lObject = new $lClass($this -> mJid);
      $lObject -> triggerEvent('eve_upload');

      return TRUE;
    }
    return FALSE;
  }

  public function uploadToDalim($aTempPathAndName, $aDestName, $aParam) {
    $lJobId = $this -> mJid;
    $lJnr   = intval($lJobId);

    $lTemp    = $aTempPathAndName;
    $lOldName = $aDestName;
    $lParam   = $aParam;
    $lFileNamePrefix = (isset($lParam['prefix'])) ? $lParam['prefix'] : NULL;

    $lParts = pathinfo($lOldName);
    $lExt   = strtolower($lParts['extension']);

    $lNewName = $lJnr.'.'.$lExt;
    $lNewName = (empty($lFileNamePrefix)) ? $lNewName : $lFileNamePrefix.'_'.$lNewName;

    $lCls = new CApp_Finder($this -> mSrc, $this -> mJid);
    $lDir = $lCls -> getPath('dalim').DS;

    $lUpl = new CCor_Upload();
    $lRes = $lUpl -> doUploadVersion($lTemp, $lDir, $lNewName, '_');

    if (!$lRes) {
    } else {
      $lUtil = new CApi_Dalim_Utils();
      $lUtil -> registerDocument($lJobId.'/'.$lRes);

      CCor_Usr::insertJobFile($this -> mSrc, $this -> mJid, 'dalim', $lRes, 1105);

      $lMsg = sprintf(lan('filupload.success'), $aDestName);
      $lHis = new CApp_His($this -> mSrc, $this -> mJid);
      $lHis -> setVal('user_id', $this -> mUid);
      $lHis -> add(htFileupload, lan('filupload.his.msg'), $lMsg);

      $lClass = 'CJob_'.$this -> mSrc.'_Mod';
      $lObject = new $lClass($this -> mJid);
      $lObject -> triggerEvent('eve_upload');

      $lQue = new CApp_Queue('dalimthumb');
      $lQue -> setParam('jid', $this -> mJid);
      $lQue -> setParam('doc', $this -> mJid.'/'.$lRes);
      $lQue -> insert();
    }

    if (CCor_Cfg::get('globalvision.available', FALSE)) {
      $this -> uploadToGlobalVision($aTempPathAndName, $aDestName, $lParam);
    }

    return $lRes;
  }

  public function uploadToWec($aTempPathAndName, $aDestName, $aParam) {
    if (substr($this -> mJid, 0, 1) == 'A') return FALSE;

    $lTempPathAndName = $aTempPathAndName;
    $lParam = $aParam;

    $lFolder = isset($lParam['folder']) ? $lParam['folder'] : NULL;
    $lPrefix = isset($lParam['prefix']) ? $lParam['prefix'] : NULL;

    $lFlink = isset($lParam['flink']) ? $lParam['flink'] : 0;
    $lOldFilename = isset($lParam['oldfilename']) ? $lParam['oldfilename'] : $aDestName;

    $lJobId = $this -> mJid;
    $lJobNr = intval($lJobId);

    $lParts = pathinfo($lOldFilename);
    $lExt = strtolower($lParts['extension']);

    $lNewFilename = $lJobNr.'.'.$lExt;
    $lNewFilename = !empty($lPrefix) ? $lPrefix.'_'.$lNewFilename : $lNewFilename;

    $lSql = 'SELECT wec_prj_id FROM al_job_shadow_'.MID.' ';
    $lSql.= 'WHERE jobid='.esc($lJobId);
    $lWecPrjId = CCor_Qry::getStr($lSql);

    $lRet = FALSE;
    $lUploadViaAlink = CCor_Cfg::get('wec.upload.alink', TRUE);

    if ($lUploadViaAlink && $lFlink == 0) {
      $lContent = file_get_contents($lTempPathAndName);

      $lQry = new CApi_Alink_Query('putFile');
      $lQry -> addParam('sid', MAND);
      $lQry -> addParam('jobid', $lJobId);
      $lQry -> addParam('filename', $lNewFilename);
      $lQry -> addParam('data', base64_encode($lContent));
      $lQry -> addParam('mode', 2);
      $lRes = $lQry -> query();

      if ($lRes) {
        if (empty($lWecPrjId)) {
          $lWec = new CApp_Wec($this -> mSrc, $this -> mJid);
          $lWecPrjId = $lWec -> createWebcenterProject();
        }

        $lQry = new CApi_Alink_Query('uploadToWebCenter');
        $lQry -> addParam('prjprefix', CCor_Cfg::get('wec.prjprefix'));
        $lQry -> addParam('sid', MAND);
        $lQry -> addParam('jobid', $lJobId);
        $lQry -> addParam('filename', $lNewFilename);
        $lRes = $lQry -> query();
        $lErr = $lRes ->  getVal('errno');
        if (0 == $lErr) {
          CCor_Usr::insertJobFile($this -> mSrc, $lJobId, 'pdf', $lNewFilename, 1105);

          $lMsg = sprintf(lan('filupload.success'), $aDestName);
          $lHis = new CApp_His($this -> mSrc, $this -> mJid);
          $lHis -> setVal('user_id', $this -> mUid);
          $lHis -> add(htFileupload, lan('filupload.his.msg'), $lMsg);

          $lClass = 'CJob_'.$this -> mSrc.'_Mod';
          $lObject = new $lClass($this -> mJid);
          $lObject -> triggerEvent('eve_upload');

          $lRet = TRUE;
        }
      }
    } else {
      $lCls = new CApp_Finder($this -> mSrc, $this -> mJid);

      $lFolder = CCor_Cfg::get('wec.upload.folder');
      $lFolder = $lCls -> getDynPath($lFolder);

      $lOnserver = CCor_Cfg::get('wec.upload.onserver');
      $lOnserver = $lCls -> getDynPath($lOnserver);

      $lRemote = $lFolder.$lNewFilename;
      $lSuccess = @copy($lTempPathAndName, $lRemote);

      $this -> msg("UPLOAD to WEC SDK copy($lTempPathAndName, $lRemote); RES:". $lSuccess);

      if (empty($lWecPrjId)) {
        $lWec = new CApp_Wec($this -> mSrc, $this -> mJid);
        $lWecPrjId = $lWec -> createWebcenterProject();
      }

      if (!empty($lWecPrjId)) {
        $lWec = new CApi_Wec_Client();
        $lWec -> loadConfig();

        $lQry = new CApi_Wec_Query_Upload($lWec);
        $lRes = $lQry -> upload($lWecPrjId, $lOnserver.$lNewFilename, $lNewFilename);

        if ($lRes) {
          CCor_Usr::insertJobFile($this -> mSrc, $lJobId, 'pdf', $lNewFilename, 1105);

          $lMsg = sprintf(lan('filupload.success'), $aDestName);
          $lHis = new CApp_His($this -> mSrc, $this -> mJid);
          $lHis -> setVal('user_id', $this -> mUid);
          $lHis -> add(htFileupload, lan('filupload.his.msg'), $lMsg);

          $lClass = 'CJob_'.$this -> mSrc.'_Mod';
          $lObject = new $lClass($this -> mJid);
          $lObject -> triggerEvent('eve_upload');

          $lQue = new CApp_Queue('wecthumb');
          $lQue -> setParam('src', $this -> mSrc);
          $lQue -> setParam('jobid', $lJobId);
          $lQue -> insert();
          $lRet = TRUE;
        }
      }
    }

    if (CCor_Cfg::get('globalvision.available', FALSE)) {
      $this -> uploadToGlobalVision($aTempPathAndName, $aDestName, $lParam);
    }

    if (!$lRet) {
      $this -> msg('Upload Error! Please contact an administrator!', mtUser, mlError);
    }

    return $lRet;
  }
  
  public function uploadToPixelboxx($aTempPathAndName, $aDestName, $aParam) {
    if (substr($this -> mJid, 0, 1) == 'A') return FALSE;
  
    // pixelboxx does not care about the extension provided, but some servers will not serve a .tmp file
	// just appending .jpg so the server will give the file to Pixelboxx when it tries to grab it
    $lTemp = 'tmp/'.basename($aTempPathAndName).'.jpg';
    if (!move_uploaded_file($aTempPathAndName, $lTemp)) {
      $this->msg('Elements: failed to upload '.$aDestName, mtApi, mlError);
      return false;
    }
    $lDestName = $aDestName;
    $lParam = $aParam;
    $lCat = isset($lParam['category']) ? $lParam['category'] : ''; 
    $lPrefix = isset($lParam['prefix']) ? $lParam['prefix'] : '';
    $lOldFilename = isset($lParam['oldfilename']) ? $lParam['oldfilename'] : $aDestName;
    $lOldFilename = $lPrefix.$lOldFilename;
  
    // Pixelboxx magic from now on
    $lRet = FALSE;
  
    $lUser = CCor_Usr::getInstance();
  
    $lQry = new CApi_Pixelboxx_Query_Importfile();
    $lMeta = CPixelboxx_Utils::getMetaMap();
    if (!empty($lMeta)) {
      foreach ($lMeta as $lAlias => $lNative) {
        $lVal = $this->getJobVal($lAlias);
        if (is_null($lVal)) {
          continue;
        }
        $lQry->addAttribute($lNative, $lVal);
      }
    }
    $lAuthor = $lUser -> getVal('fullname');
    $lQry -> addAttribute('pbcustom__uploadedby', $lAuthor);
    $lQry -> addAttribute('pbcustom__jobid', $this->mJid);
    
    $lSql = 'SELECT pbox_doi FROM al_job_shadow_'.MID.' WHERE src='.esc($this -> mSrc).' AND jobid='.esc($this -> mJid);
    $lDoi = CCor_Qry::getStr($lSql);
    
    if (empty($lDoi)) {
      $lDoi = $this->createPixelBoxFolder($this->mJid);
    }
    $lRes = $lQry -> upload($lDoi, $lTemp, $lOldFilename);
    @unlink($lTemp);
    if ($lRes) {
      CCor_Usr::insertJobFile($this -> mSrc, $this -> mJid, 'pbox', $lOldFilename, $lCat);
      return true;
    }
    return $lRet;
  }
  
  protected function createPixelBoxFolder($aId) {
    $this->dbg('Creating folder for '.$aId);
    $lQry = new CApi_Pixelboxx_Query_Createfolder();
    $lRootDoi = CCor_Cfg::get('pbox.folder.jobs');
    $lFolderId = $lQry ->create('job_'.$aId, $lRootDoi);
    if ($lFolderId) {
    $lFac = new CJob_Fac($this->mSrc, $aId);
    $lMod = $lFac->getMod($aId);
    $lUpd = array('pbox_doi' => $lFolderId);
    $lMod -> writeUpdate($aId, $lUpd);
    CJob_Utl_Shadow::reflectUpdate($this -> mSrc, $aId, $lUpd);
    }
    return $lFolderId;
  }
  
  protected function uploadToGlobalVision($aTempPathAndName, $aDestName, $aParam) {
    $lGlobalVisionDir = CCor_Cfg::get('globalvision.hotfolder');
    $lOldFilename = isset($aParam['oldfilename']) ? $aParam['oldfilename'] : $aDestName;
    
    $lJobId = $this -> mJid;
    $lJobNr = intval($lJobId);
    
    $lParts = pathinfo($lOldFilename);
    $lFileName = $lParts['filename'];
    $lExt = strtolower($lParts['extension']);
    
    $lNewName = $lJobNr.$lFileName.'.'.$lExt;
    
    $lUpl = new CCor_Upload();
    $lRes = $lUpl -> doUploadVersion($aTempPathAndName, $lGlobalVisionDir, $lNewName);
    if (!$lRes) {
      $this -> msg('Upload Error! Please contact an administrator!', mtUser, mlError);
      return FALSE;
    }
    
    $lArr = array('jid' => $this -> mJid, 'src' => $this -> mSrc, 'name' => $lNewName);
    CApp_Queue::add('gvxml', $lArr);

    CCor_Usr::insertJobFile($this -> mSrc, $this -> mJid, 'doc', $lRes, 'globalvision');
    $lHis = new CApp_His($this -> mSrc, $this -> mJid);
    $lHis -> setVal('user_id', $this -> mUid);
    $lMsg = sprintf(lan('filupload.success'), $lRes);
    $lHis -> add(htFileupload, lan('filupload.his.msg'), $lMsg);

    return $lRes;
  }
}