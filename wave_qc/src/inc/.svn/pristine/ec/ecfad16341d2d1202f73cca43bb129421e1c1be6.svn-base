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

    $lMod = new CJob_Mod($this -> mSrc, $this -> mJid);
    $lMod -> triggerEvent('eve_upload');

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

    $lMod = new CJob_Mod($this -> mSrc, $this -> mJid);
    $lMod -> triggerEvent('eve_upload');

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

      $lMod = new CJob_Mod($this -> mSrc, $this -> mJid);
      $lMod -> triggerEvent('eve_upload');

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

      $lCls = 'CJob_'.$this -> mSrc.'_Mod';
      $lObj = new $lCls($this -> mJid);
      $lObj -> triggerEvent('eve_upload');

      $lQue = new CApp_Queue('dalimthumb');
      $lQue -> setParam('jid', $this -> mJid);
      $lQue -> setParam('doc', $this -> mJid.'/'.$lRes);
      $lQue -> insert();
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

          $lMod = new CJob_Mod($this -> mSrc, $this -> mJid);
          $lMod -> triggerEvent('eve_upload');

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

          $lMod = new CJob_Mod($this -> mSrc, $this -> mJid);
          $lMod -> triggerEvent('eve_upload');

          $lQue = new CApp_Queue('wecthumb');
          $lQue -> setParam('src', $this -> mSrc);
          $lQue -> setParam('jobid', $lJobId);
          $lQue -> insert();
          $lRet = TRUE;
        }
      }
    }

    if (!$lRet) {
      $this -> msg('Upload Error! Please contact an administrator!', mtUser, mlError);
    }

    return $lRet;
  }
  
  public function uploadToPixelboxx($aTempPathAndName, $aDestName, $aParam) {
    if (substr($this -> mJid, 0, 1) == 'A') return FALSE;
  
    $lTempPathAndName = $aTempPathAndName;
    $lDestName = $aDestName;
    $lParam = $aParam;
  
    $lPrefix = isset($lParam['prefix']) ? $lParam['prefix'] : NULL;
    $lOldFilename = isset($lParam['oldfilename']) ? $lParam['oldfilename'] : $aDestName;
  
    // Pixelboxx magic from now on
    $lRet = FALSE;
  
    $lUser = CCor_Usr::getInstance();
  
    $lQry = new CApi_Pixelboxx_Query_Importfile();
    #$lQry -> addAttribute('category', 0); // TODO: category needs to be set
    #$lAuthor = $lUser -> getVal('fullname');
    #$lQry -> addAttribute('byline', $lAuthor);
  
    $lSql = 'SELECT pbox_doi FROM al_job_shadow_'.MID.' WHERE src='.esc($this -> mSrc).' AND jobid='.esc($this -> mJid);
    $lDoi = CCor_Qry::getStr($lSql);
  
    if (empty($lDoi)) {
      $lDoi = $this->createPixelBoxFolder($this->mJid);
    }
    $lRes = $lQry -> upload($lDoi, $lTempPathAndName, $lOldFilename);
  
    if ($lRes) {
      CCor_Usr::insertJobFile($this -> mSrc, $this -> mJid, 'pdf', $lOldFilename, 1105);
      return TRUE;
    }
  
    return $lRet;
  }
  
  protected function createPixelBoxFolder($aId) {
    $lQry = new CApi_Pixelboxx_Query_Createfolder();
    $lFolderId = $lQry ->create('job_'.$aId, 'pboxx-pixelboxx-869');
    $lFac = new CJob_Fac($this->mSrc, $aId);
    $lMod = $lFac->getMod($aId);
    $lUpd = array('pbox_doi' => $lFolderId);
    $lMod -> writeUpdate($aId, $lUpd);
    CJob_Utl_Shadow::reflectUpdate($this -> mSrc, $aId, $lUpd);
    return $lFolderId;
  }
  
}