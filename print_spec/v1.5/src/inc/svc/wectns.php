<?php
class CInc_Svc_Wectns extends CSvc_Base {

  protected $mServiceConfiguration;
  protected $mServiceConfigurationKeys = array(
    'wec-pi.fetch_log',
    'wec-pi.fetch_number',
    'wec-pi.fetch_subfolder',
    'wec-pi.image_name',
    'wec-pi.image_notfound',
    'wec-pi.image_subfolder',
    'wec-pi.jobfield_alink',
    'wec-pi.jobfield_active',
    'wec-pi.jobfield_archive',
    'wec-pi.thumbnail_name',
    'wec-pi.thumbnail_notfound',
    'wec-pi.thumbnail_subfolder'
  );

  protected $mLogConfiguration;
  protected $mClients;

  /**
   * Execute
   *
   * @return boolean
   */
  protected function doExecute() {
    $this -> mServiceConfiguration = $this -> getServiceConfiguration();
    $this -> mLogConfiguration = $this -> getLogConfiguration();

    if (!extension_loaded('gd') && !extension_loaded('gd2')) {
      $this -> log("PHP extension(s) >gd< and/or >gd2< could not be found/loaded! WebCenter Thumbnail Service not started!".LF);
      return FALSE;
    } else {
      ini_set('gd.jpeg_ignore_warning', TRUE);
    }

    $this -> mClients = CCor_Res::extract('id', 'code', 'mand');

    if (array_key_exists(intval(MID), $this -> mClients)) {
      if ($this -> canContinue()) {
        $this -> progressTick('get shadow jobs');
        $this -> getJobsFromShadow(intval(MID));
      }

      if ($this -> canContinue()) {
        $this -> progressTick('get archive jobs');
        $this -> getJobsFromArchive(intval(MID));
      }

      if ($this -> canContinue()) {
        $this -> progressTick('get images');
        $this -> downloadImages(intval(MID));
      }
    }

    return TRUE;
  }

  /**
   * Log
   *
   * @return boolean
   */
  public function log($aMessage, $aTimestamp = TRUE) {
    $lMessage = $aMessage;
    $lTimestamp = $aTimestamp ? '['.date(lan('lib.datetime.long')).'] ' : '';

    $lPath = $this -> mLogConfiguration['path'];
    $lFile = $this -> mLogConfiguration['file'];

    error_log($lTimestamp.$lMessage, 3, $lPath.$lFile);
  }

  /**
   * Array column
   *
   * @return array
   */
  public static function array_col(array $aArray, $aKey) {
    return array_map(function($aArray) use ($aKey) {return $aArray[$aKey];}, $aArray);
  }

  /**
   * Array search
   *
   * @return array|boolean
   */
  public static function array_search(array $aArray, $aKey, $aValue) {
    foreach ($aArray as $lKey => $lValue) {
      if ($lValue[$aKey] === $aValue) {
        return $lValue;
      }
    }

    return FALSE;
  }

  /**
   * Get service configuration
   *
   * @return array
   */
  protected function getServiceConfiguration() {
    $lServiceConfiguration = array();
    $lKeys = implode('","', $this -> mServiceConfigurationKeys);

    $lQry = new CCor_Qry('SELECT code,val FROM al_sys_pref WHERE code IN ("'.$lKeys.'") ORDER BY code;');
    foreach ($lQry as $lKey => $lValue) {
      $lValue['val'] = str_replace("\\", "/", $lValue['val']);
      $lServiceConfiguration[$lValue['code']] = $lValue['val'];
    }

    return $lServiceConfiguration;
  }

  /**
   * Get log configuration
   *
   * @return array
   */
  protected function getLogConfiguration() {
    $lLogPath = str_replace("\\", "/", CCor_Cfg::get('log.path', getcwd()));
    if (strlen($lLogPath) > 0 && substr($lLogPath, -1, 1) != "/") {
      $lLogPath .= "/";
    }

    $lLogFilenamePrefix = CCor_Cfg::get('log.filename.prefix', 'service_');
    $lLogFilename = CCor_Cfg::get('log.filename', CCor_Cfg::get('log.filename.service.wectns', 'wectns_'));
    $lLogFilenamePostfix = CCor_Cfg::get('log.filename.postfix', date("Y.m.d", time()));
    $lLogFilenameExtention = CCor_Cfg::get('log.filename.extention', '.txt');

    $lLogFile = $lLogFilenamePrefix.$lLogFilename.$lLogFilenamePostfix.$lLogFilenameExtention;

    return array('path' => $lLogPath, 'file' => $lLogFile);
  }

  protected static function writeJob($aSrc, $aJobId, $aUpdate) {
    $lFac = new CJob_Fac($aSrc);
    $lMod = $lFac -> getMod($aJobId);
    $lRes = $lMod -> forceUpdate($aUpdate);
    return $lRes;
  }

  /**
   * Create wectns tables if not available
   *
   * @return boolean
   */
  protected function editResponse($aClient, $aResponse, $aRepository = 'shadow') {
    $lClient = $aClient;
    $lResponse = $aResponse;
    $lRepository = $aRepository;

    if (!empty($lResponse)) {
      $lWec = new CApi_Wec_Client();
      $lWec -> loadConfig();

      $lCount = count($lResponse);
      $lNum = 1;

      foreach ($lResponse as $lRow) {
        $lSrc = $lRow['src'];
        $lJobID = $lRow['jobid'];
        $lWecPrjID = $lRow['wec_prj_id'];

        $this -> progressTick('doclist '.$lNum.' of '.$lCount);

        $lWecDocLst = new CApi_Wec_Query_Doclist($lWec);
        $lWecDocRes = $lWecDocLst -> getList($lWecPrjID);

        if (!empty($lWecDocRes)) {
          $lWecVerID = max(self::array_col($lWecDocRes, 'wec_ver_id'));
          $lWecArr = self::array_search($lWecDocRes, 'wec_ver_id', $lWecVerID);
          $lWecDocID = $lWecArr['wec_doc_id'];

          $lResult = CCor_Qry::exec('INSERT INTO al_job_wectns_'.$lClient.' (mid,src,jobid,wec_prj_id,wec_doc_id,wec_ver_id,repository) VALUES ("'.$lClient.'", "'.$lSrc.'", "'.$lJobID.'", "'.$lWecPrjID.'", "'.$lWecDocID.'", "'.$lWecVerID.'", "'.$lRepository.'");');

          switch ($lRepository) {
            case 'shadow':
              $lQry = "UPDATE al_job_shadow_".$lClient." SET";
              $lQry.= " wectns=".esc(time());
              $lQry.= " WHERE jobid=".esc($lJobID).";";

              $lRes = CCor_Qry::exec($lQry);
              break;
            case 'archive':
              $lQry = "UPDATE al_job_arc_".$lClient." SET";
              $lQry.= " wectns=".esc(time());
              $lQry.= " WHERE jobid=".esc($lJobID).";";

              $lRes = CCor_Qry::exec($lQry);
              break;
            default:
              break;
          }
        }

        if (!$this -> canContinue()) {
          break;
        }

        $lNum++;
      }
    }

    return TRUE;
  }

  /**
   * Get jobs from portal
   *
   * @return boolean
   */
  protected function getJobsFromShadow($aClient) {
    $lClient = $aClient;

    $lRequest = new CCor_TblIte('al_job_shadow_'.$lClient);
    $lRequest -> addField('src');
    $lRequest -> addField('jobid');
    $lRequest -> addField('wec_prj_id');
    $lRequest -> addCondition('jobid', 'NOT IN', 'SELECT jobid FROM al_job_wectns_'.$lClient); // WHERE
    $lRequest -> addCondition('src', '<>', ''); // WHERE
    $lRequest -> addCondition('jobid', '<>', ''); // WHERE
    $lRequest -> addCondition('wec_prj_id', '<>', ''); // WHERE
    $lRequest -> setOrder('jobid', 'DESC'); // ORDER BY
    $lRequest -> setLimit($this -> mServiceConfiguration['wec-pi.fetch_number']); // LIMIT
    $lResponse = $lRequest -> getArray();

    if (!empty($lResponse)) {
      $this -> editResponse($lClient, $lResponse, 'shadow');
    }

    return TRUE;
  }

  /**
   * Get jobs from portal archive
   *
   * @return boolean
   */
  protected function getJobsFromArchive($aClient) {
    $lClient = $aClient;

    $lRequest = new CCor_TblIte('al_job_arc_'.$lClient);
    $lRequest -> addField('src');
    $lRequest -> addField('jobid');
    $lRequest -> addField('wec_prj_id');
    $lRequest -> addCondition('jobid', 'NOT IN', 'SELECT jobid FROM al_job_wectns_'.$lClient); // WHERE
    $lRequest -> addCondition('src', '<>', ''); // WHERE
    $lRequest -> addCondition('jobid', '<>', ''); // WHERE
    $lRequest -> addCondition('wec_prj_id', '<>', ''); // WHERE
    $lRequest -> setOrder('jobid', 'DESC'); // ORDER BY
    $lRequest -> setLimit($this -> mServiceConfiguration['wec-pi.fetch_number']); // LIMIT
    $lResponse = $lRequest -> getArray();

    if (!empty($lResponse)) {
      $this -> editResponse($lClient, $lResponse, 'archive');
    }

    return TRUE;
  }

  /**
   * Download images and thumbnails
   *
   * @return boolean
   */
  protected function downloadImages($aClient) {
    $lClient = $aClient;
    $lSvcWecInst = CSvc_Wec::getInstance();

    $lSoftproof = new CApi_Wec_Client();
    $lSoftproof -> loadConfig();

    $lOuterQry = new CCor_Qry('SELECT mid,src,jobid,wec_prj_id,wec_doc_id,wec_ver_id FROM al_job_wectns_'.$lClient.' WHERE downloaded IS NULL ORDER BY jobid DESC LIMIT '.$this -> mServiceConfiguration['wec-pi.fetch_number'].';');
    $lRows = array();
    foreach ($lOuterQry as $lRow) {
      $lRows[] = $lRow;
    }

    $lCount = count($lRows);
    $lNum = 1;

    foreach ($lOuterQry as $lRow) {
      $lMid = $lRow['mid'];
      $lSrc = $lRow['src'];
      $lJobId = $lRow['jobid'];
      $lWecPrjId = $lRow['wec_prj_id'];
      $lWecDocId = $lRow['wec_doc_id'];
      $lWecVerId = $lRow['wec_ver_id'];

      $this->progressTick('image '.$lNum.' of '.$lCount);
      $lInnerQry = new CApi_Wec_Query_Thumbnail($lSoftproof);
      $lImage = $lInnerQry -> getImage($lWecDocId);

      if ($lImage) {
        $lStatics = $lSvcWecInst -> getStatics();
        $lDynamics = $lSvcWecInst -> getDynamics($lJobId);

        // create preview image directories
        $lPreviewImageFound = $lStatics['getcwd'].$lDynamics['image_dir'];
        $lOldUMask = umask(0);
        $lResult = is_dir($lPreviewImageFound) ? FALSE : @mkdir($lPreviewImageFound, 0777, TRUE);
        umask($lOldUMask);

        // create thumbnail directories
        $lThumbnailFound = $lStatics['getcwd'].$lDynamics['thumbnail_dir'];
        $lOldUMask = umask(0);
        $lResult = is_dir($lThumbnailFound) ? FALSE : @mkdir($lThumbnailFound, 0777, TRUE);
        umask($lOldUMask);

        // create preview image
        $lFile = fopen($lPreviewImageFound.$lDynamics['image_file'], "c");
        fwrite($lFile, $lImage);
        fclose($lFile);

        // create thumbnail
        $lImage = @imagecreatefromjpeg($lPreviewImageFound.$lDynamics['image_file']);
        if ($lImage) {
          $lImageWidth = imagesx($lImage);
          $lImageHeight = imagesy($lImage);

          $lThumbnailWidth = 100;
          $lThumbnailHeight = 100;

          $lThumbnail = imagecreatetruecolor($lThumbnailWidth, $lThumbnailHeight);
          imagecopyresampled($lThumbnail, $lImage, 0, 0, 0, 0, $lThumbnailWidth, $lThumbnailHeight, $lImageWidth, $lImageHeight);
          imagejpeg($lThumbnail, $lThumbnailFound.$lDynamics['thumbnail_file'], 75);
        } else {
          $this -> log("MID: ".$lClient.", MAND: ".$this -> mClients[$lClient].", thumbnail could NOT be created: ".$lThumbnailFound.$lDynamics['image_file'].LF);
        }

        $lResult = CCor_Qry::exec('UPDATE al_job_wectns_'.$lClient.' SET downloaded=NOW() WHERE mid="'.$lMid.'" AND src="'.$lSrc.'" AND jobid="'.$lJobId.'" AND wec_prj_id="'.$lWecPrjId.'" AND wec_doc_id="'.$lWecDocId.'";');
      } else {
        $lStatics = $lSvcWecInst -> getStatics();
        $lDynamics = $lSvcWecInst -> getDynamics($lJobId);

        $lPreviewImageFound = $lStatics['getcwd'].$lDynamics['image_dir'];

        $this -> log("MID: ".$lClient.", MAND: ".$this -> mClients[$lClient].", preview could NOT be created: ".$lPreviewImageFound.$lDynamics['image_file'].LF);
      }

      if (!$this -> canContinue()) {
        break;
      }

      $lNum++;
    }

    return TRUE;
  }

  /**
   * Download image and thumbnail
   *
   * @return boolean
   */
  public static function downloadImage($aJobId, $aSrc) {
    $lSoftproof = new CApi_Wec_Client();
    $lSoftproof -> loadConfig();

    $lFie = CCor_Res::extract('alias', 'native', 'fie');

    $lFac = new CJob_Fac($aSrc, $aJobId);
    $lRow = $lFac -> getDat();

    $lWecPrjID = $lRow['wec_prj_id'];

    $lWecDocLst = new CApi_Wec_Query_Doclist($lSoftproof);
    $lWecDocRes = $lWecDocLst -> getList($lWecPrjID);

    if (!$lWecDocRes) return;

    $lWecVerID = max(self::array_col($lWecDocRes, 'wec_ver_id'));
    $lWecArr = self::array_search($lWecDocRes, 'wec_ver_id', $lWecVerID);
    $lWecDocID = $lWecArr['wec_doc_id'];

    $lUpd = array('wectns' => time());
    self::writeJob($lRow['src'], $aJobId, $lUpd);

    $lInnerQry = new CApi_Wec_Query_Thumbnail($lSoftproof);
    $lImage = $lInnerQry -> getImage($lWecDocID);

    if ($lImage) {
      $lResult = CCor_Qry::exec('INSERT INTO al_job_wectns_'.MID.' (mid,src,jobid,wec_prj_id,wec_doc_id,wec_ver_id,repository) VALUES ("'.MID.'", "'.$aSrc.'", "'.$aJobId.'", "'.$lWecPrjID.'", "'.$lWecDocID.'", "'.$lWecVerID.'", "alink");');

      $lSvcWecInst = CSvc_Wec::getInstance();

      $lStatics = $lSvcWecInst -> getStatics();
      $lDynamics = $lSvcWecInst -> getDynamics($aJobId);

      // create preview image directories
      $lPreviewImageFound = $lStatics['getcwd'].$lDynamics['image_dir'];
      $lOldUMask = umask(0);
      $lResult = is_dir($lPreviewImageFound) ? FALSE : @mkdir($lPreviewImageFound, 0777, TRUE);
      umask($lOldUMask);

      // create thumbnail directories
      $lThumbnailFound = $lStatics['getcwd'].$lDynamics['thumbnail_dir'];
      $lOldUMask = umask(0);
      $lResult = is_dir($lThumbnailFound) ? FALSE : @mkdir($lThumbnailFound, 0777, TRUE);
      umask($lOldUMask);

      // create preview image
      $lFile = fopen($lPreviewImageFound.$lDynamics['image_file'], "c");
      fwrite($lFile, $lImage);
      fclose($lFile);

      // create thumbnail
      $lImage = @imagecreatefromjpeg($lPreviewImageFound.$lDynamics['image_file']);
      if ($lImage) {
        $lImageWidth = imagesx($lImage);
        $lImageHeight = imagesy($lImage);

        $lThumbnailWidth = 100;
        $lThumbnailHeight = 100;

        $lThumbnail = imagecreatetruecolor($lThumbnailWidth, $lThumbnailHeight);
        imagecopyresampled($lThumbnail, $lImage, 0, 0, 0, 0, $lThumbnailWidth, $lThumbnailHeight, $lImageWidth, $lImageHeight);
        imagejpeg($lThumbnail, $lThumbnailFound.$lDynamics['thumbnail_file'], 75);
      }

      $lResult = CCor_Qry::exec('UPDATE al_job_wectns_'.MID.' SET wec_ver_id="'.$lWecVerID.'" WHERE mid="'.MID.'" AND src="'.$aSrc.'" AND jobid="'.$aJobId.'" AND wec_prj_id="'.$lWecPrjID.'" AND wec_doc_id="'.$lWecDocID.'";');
      $lResult = CCor_Qry::exec('UPDATE al_job_wectns_'.MID.' SET downloaded=NOW() WHERE mid="'.MID.'" AND src="'.$aSrc.'" AND jobid="'.$aJobId.'" AND wec_prj_id="'.$lWecPrjID.'" AND wec_doc_id="'.$lWecDocID.'";');

      return TRUE;
    } else {
      return FALSE;
    }
  }
}