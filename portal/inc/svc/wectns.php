<?php
class CInc_Svc_Wectns extends CSvc_Base {

  const LIMIT = 10;

  protected $mAPIWecClient;
  protected $mJobTypesArray;
  protected $mJobTypesImploded;
  protected $mSvcWec;

  /**
   * Execute service
   *
   * @return boolean
   */
  protected function doExecute() {
    if (!extension_loaded('gd') && !extension_loaded('gd2')) {
      $this -> log("PHP extension(s) >gd< and/or >gd2< could not be found/loaded! >WebCenter thumbnail service< not started!".LF);
      return FALSE;
    } else {
      ini_set('gd.jpeg_ignore_warning', TRUE);
    }

    $this -> mAPIWecClient = new CApi_Wec_Client();
    $this -> mAPIWecClient -> loadConfig();

    $this -> mJobTypesArray = CCor_Cfg::get('all-jobs');
    $this -> mJobTypesImploded = '\''.implode('\',\'', $this -> mJobTypesArray).'\'';

    $this -> mSvcWec = CSvc_Wec::getInstance();

    if ($this -> canContinue()) {
      $this -> progressTick('get archived jobs');
      $this -> getJobsFromArchive();
    }

    $lWriter = CCor_Cfg::get('job.writer.default', 'portal');
    if ($lWriter == 'portal') {
      if ($this -> canContinue()) {
        $this -> progressTick('get active jobs from pdb');
        $this -> getJobsFromPDB();
      }
    } else {
      if ($this -> canContinue()) {
        $this -> progressTick('get active jobs from shadow');
        $this -> getJobsFromShadow();
      }
    }

    if ($this -> canContinue()) {
      $this -> progressTick('get images');
      $this -> downloadImages();
    }

    return TRUE;
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

  protected static function writeJob($aSrc, $aJobID, $aUpdate) {
    $lFac = new CJob_Fac($aSrc);
    $lMod = $lFac -> getMod($aJobID);
    $lRes = $lMod -> forceUpdate($aUpdate);
    return $lRes;
  }

  /**
   * Create preview tables if not available
   *
   * @return boolean
   */
  protected function editResponse($aResponse) {
    $lResponse = $aResponse;

    if (!empty($lResponse)) {
      foreach ($lResponse as $lKey => $lValue) {
        $lSrc = $lValue['src'];
        $lJobID = $lValue['jobid'];
        $lWecPrjID = $lValue['wec_prj_id']; // TODO: generalize

        $lWecDocLst = new CApi_Wec_Query_Doclist($this -> mAPIWecClient); // TODO: generalize
        $lWecDocRes = $lWecDocLst -> getList($lWecPrjID); // TODO: generalize

        if ($lWecDocRes) {
          $lWecVerID = max(self::array_col($lWecDocRes, 'wec_ver_id')); // TODO: generalize
          if (!$lWecVerID) continue;

          $lWecArr = self::array_search($lWecDocRes, 'wec_ver_id', $lWecVerID); // TODO: generalize
          if (!$lWecArr) continue;

          $lWecDocID = $lWecArr['wec_doc_id']; // TODO: generalize
          if (!$lWecDocID) continue;

          $lSQL = 'INSERT INTO al_job_preview_'.MID.' (';
          $lSQL.= ' `jobid`,';
          $lSQL.= ' `src`,';
          $lSQL.= ' `project`,';
          $lSQL.= ' `document`,';
          $lSQL.= ' `version`';
          $lSQL.= ') VALUES (';
          $lSQL.= esc($lJobID).',';
          $lSQL.= esc($lSrc).',';
          $lSQL.= esc($lWecPrjID).',';
          $lSQL.= esc($lWecDocID).',';
          $lSQL.= esc($lWecVerID).'';
          $lSQL.= ');';
          $lResult = CCor_Qry::exec($lSQL);

          if (!$this -> canContinue()) {
            break;
          }
        }
      }
    }

    return TRUE;
  }

  /**
   * Get jobs from archive
   *
   * @return boolean
   */
  protected function getJobsFromArchive() {
    $lRequest = new CCor_TblIte('al_job_arc_'.MID);
    $lRequest -> addField('src');
    $lRequest -> addField('jobid');
    $lRequest -> addField('wec_prj_id'); // TODO: generalize
    $lRequest -> addCondition('jobid', 'NOT IN', 'SELECT jobid FROM al_job_preview_'.MID); // WHERE
    $lRequest -> addCondition('src', 'IN', $this -> mJobTypesImploded); // WHERE
    $lRequest -> addCondition('jobid', '<>', ''); // WHERE
    $lRequest -> addCondition('wec_prj_id', '<>', ''); // TODO: generalize
    $lRequest -> setOrder('jobid', 'DESC'); // ORDER BY
    $lRequest -> setLimit(self::LIMIT); // LIMIT
    $lResponse = $lRequest -> getArray();

    if (!empty($lResponse)) {
      $this -> editResponse($lResponse);
    }

    return TRUE;
  }

  /**
   * Get jobs from shadow
   *
   * @return boolean
   */
  protected function getJobsFromShadow() {
    $lRequest = new CCor_TblIte('al_job_shadow_'.MID);
    $lRequest -> addField('src');
    $lRequest -> addField('jobid');
    $lRequest -> addField('wec_prj_id'); // TODO: generalize
    $lRequest -> addCondition('jobid', 'NOT IN', 'SELECT jobid FROM al_job_preview_'.MID); // WHERE
    $lRequest -> addCondition('src', 'IN', $this -> mJobTypesImploded); // WHERE
    $lRequest -> addCondition('jobid', '<>', ''); // WHERE
    $lRequest -> addCondition('wec_prj_id', '<>', ''); // TODO: generalize
    $lRequest -> setOrder('jobid', 'DESC'); // ORDER BY
    $lRequest -> setLimit(self::LIMIT); // LIMIT
    $lResponse = $lRequest -> getArray();

    if (!empty($lResponse)) {
      $this -> editResponse($lResponse);
    }

    return TRUE;
  }

  /**
   * Get jobs from pdb
   *
   * @return boolean
   */
  protected function getJobsFromPDB() {
    foreach ($this -> mJobTypesArray as $lKey => $lValue) {
      $lRequest = new CCor_TblIte('al_job_'.$lValue.'_'.MID);
      $lRequest -> addField('src');
      $lRequest -> addField('jobid');
      $lRequest -> addField('wec_prj_id'); // TODO: generalize
      $lRequest -> addCondition('jobid', 'NOT IN', 'SELECT jobid FROM al_job_preview_'.MID); // WHERE
      $lRequest -> addCondition('jobid', '<>', ''); // WHERE
      $lRequest -> addCondition('wec_prj_id', '<>', ''); // TODO: generalize
      $lRequest -> setOrder('jobid', 'DESC'); // ORDER BY
      $lRequest -> setLimit(self::LIMIT); // LIMIT
      $lResponse = $lRequest -> getArray();

      if (!empty($lResponse)) {
        $this -> editResponse($lResponse);
      }
    }

    return TRUE;
  }

  /**
   * Download images and thumbnails
   *
   * @return boolean
   */
  public function downloadImages($aArguments = NULL) {
    if (is_null($aArguments)) {
      $lBatch = TRUE;

      $lSQL = 'SELECT';
      $lSQL.= ' jobid,';
      $lSQL.= ' src,';
      $lSQL.= ' project,';
      $lSQL.= ' document,';
      $lSQL.= ' version';
      $lSQL.= ' FROM al_job_preview_'.MID;
      $lSQL.= ' WHERE created = CONVERT(0, DATETIME)';
      $lSQL.= ' ORDER BY jobid DESC';
      $lSQL.= ' LIMIT '.self::LIMIT;
      $lQry = new CCor_Qry($lSQL);
    } else {
      // TODO: START: EXTRACT TO SEPARATE OBJECT
      $lBatch = FALSE;

      // jobid is mandatory
      if (array_key_exists('jobid', $aArguments)) {
        $lJobID = $aArguments['jobid'];
      } else {
        return TRUE;
      }

      // TODO: currently src is mandatory. This can be improved by finding out src only by jobid.
      if (array_key_exists('src', $aArguments)) {
        $lSrc = $aArguments['src'];
      } else {
        return TRUE;
      }

      if (array_key_exists('document', $aArguments)) {
        $lProject = NULL;
        $lDocument = $aArguments['document'];
        $lVersion = NULL;
      } else {
        $lProject = NULL;
        $lDocument = NULL;
        $lVersion = NULL;

        $this -> mAPIWecClient = new CApi_Wec_Client();
        $this -> mAPIWecClient -> loadConfig();

        $this -> mSvcWec = CSvc_Wec::getInstance();

        if (array_key_exists('project', $aArguments)) {
          $lProject = $aArguments['project'];

          $lWecDocLst = new CApi_Wec_Query_Doclist($this -> mAPIWecClient); // TODO: generalize
          $lWecDocRes = $lWecDocLst -> getList($lProject); // TODO: generalize

          if ($lWecDocRes) {
            $lVersion = max(self::array_col($lWecDocRes, 'wec_ver_id')); // TODO: generalize
            if (!$lVersion) return TRUE;

            $lWecArr = self::array_search($lWecDocRes, 'wec_ver_id', $lVersion); // TODO: generalize
            if (!$lWecArr) return TRUE;

            $lDocument = $lWecArr['wec_doc_id']; // TODO: generalize
            if (!$lDocument) return TRUE;
          }
        } else {
          if (CCor_Cfg::get('job.writer.default') == 'portal') {
            $lIte = new CCor_TblIte('all', FALSE);
            $lIte -> addField('jobid');
            $lIte -> addField('wec_prj_id');
            $lIte -> addCondition('jobid', '=', $lJobID);
            $lJobs = $lIte -> getIterator();
            $lProject = $lJobs[0]['wec_prj_id'];
          } else {
            $lFie = CCor_Res::getByKey('alias', 'fie');

            $lIte = new CApi_Alink_Query_Getjoblist();
            $lIte -> addField('jobid', 'jobid');
            $lIte -> addField('wec_prj_id', $lFie['wec_prj_id']['native']);
            $lIte -> addCondition('jobid', '=', $lJobID);
            $lJobs = $lIte -> getIterator();
            $lProject = $lJobs[0]['wec_prj_id'];
          }

          $lWecDocLst = new CApi_Wec_Query_Doclist($this -> mAPIWecClient); // TODO: generalize
          $lWecDocRes = $lWecDocLst -> getList($lProject); // TODO: generalize

          if ($lWecDocRes) {
            $lVersion = max(self::array_col($lWecDocRes, 'wec_ver_id')); // TODO: generalize
            if (!$lVersion) return TRUE;

            $lWecArr = self::array_search($lWecDocRes, 'wec_ver_id', $lVersion); // TODO: generalize
            if (!$lWecArr) return TRUE;

            $lDocument = $lWecArr['wec_doc_id']; // TODO: generalize
            if (!$lDocument) return TRUE;
          }
        }
      }
      // TODO: END: EXTRACT TO SEPARATE OBJECT

      $lJob = array(
        'jobid' => $lJobID,
        'src' => $lSrc,
        'project' => $lProject,
        'document' => $lDocument,
        'version' => $lVersion
      );

      $lQry = array(
        $lJobID => $lJob  
      );
    }

    foreach ($lQry as $lKey => $lValue) {
      $lSrc = $lValue['src'];
      $lJobID = $lValue['jobid'];
      $lWecPrjID = $lValue['project']; // TODO: generalize
      $lWecDocID = $lValue['document']; // TODO: generalize
      $lWecVerID = $lValue['version']; // TODO: generalize

      $lAPIWecQueryThumbnail = new CApi_Wec_Query_Thumbnail($this -> mAPIWecClient); // TODO: generalize
      $lString = $lAPIWecQueryThumbnail -> getImage($lWecDocID); // TODO: generalize

      $lImageFromString = imagecreatefromstring($lString);
      if ($lImageFromString !== FALSE) {
        $lAttributes = $this -> mSvcWec -> getAttributes($lJobID);

        // create directory
        $lOldUMask = umask(0);
        $lResult = is_dir($lAttributes['preview.path']) ? FALSE : @mkdir($lAttributes['preview.path'], 0777, TRUE);
        umask($lOldUMask);

        // create file
        $lFile = fopen($lAttributes['preview.path'].$lAttributes['preview.filename'], "c");
        fwrite($lFile, $lString);
        fclose($lFile);

        imagedestroy($lImageFromString);

        if (!$lBatch) {
          $lOSI = $lAttributes['preview.path'].$lAttributes['preview.filename']; // original sized image
          $lMessage = lan('lib.file.created');
        }

        // create thumbnail
        $lOriginalSizedImage = @imagecreatefromjpeg($lAttributes['preview.path'].$lAttributes['preview.filename']);
        if ($lOriginalSizedImage) {
          $lFilename = pathinfo($lAttributes['preview.filename'], PATHINFO_FILENAME);
          $lExtention = pathinfo($lAttributes['preview.filename'], PATHINFO_EXTENSION);

          $lImageWidth = imagesx($lOriginalSizedImage);
          $lImageHeight = imagesy($lOriginalSizedImage);

          $lNewSize = 128;

          $lSmallerSizedImage = imagecreatetruecolor($lNewSize, $lNewSize);
          imagecopyresampled($lSmallerSizedImage, $lOriginalSizedImage, 0, 0, 0, 0, $lNewSize, $lNewSize, $lImageWidth, $lImageHeight);
          imagejpeg($lSmallerSizedImage, $lAttributes['preview.path'].$lFilename.'_'.$lNewSize.'.'.$lExtention, 75);

          if (!$lBatch) {
            $lSSI = $lAttributes['preview.path'].$lFilename.'_'.$lNewSize.'.'.$lExtention; // small sized image
          }
        } else {
          $lMsg = "MID: ".MID.", Jobtype: ".$lSrc.", JobID: ".$lJobID.", WebCenter project ID: ".$lWecPrjID.", WebCenter document ID: ".$lWecDocID.", WebCenter version ID: ".$lWecVerID." small sized image could not be created!"; // TODO: generalize
          $lLog = new CCor_Log();
          $lLog -> log($lMsg, mlError, mtNone);
        }

        $lSQL = 'UPDATE al_job_preview_'.MID.' SET';
        $lSQL.= ' created='.esc(date('Y.m.d H:i:s'));
        $lSQL.= ' WHERE jobid='.esc($lJobID);
        $lResult = CCor_Qry::exec($lSQL);
      } else {
        $lMsg = "MID: ".MID.", Jobtype: ".$lSrc.", JobID: ".$lJobID.", WebCenter project ID: ".$lWecPrjID.", WebCenter document ID: ".$lWecDocID.", WebCenter version ID: ".$lWecVerID." original sized image could not be created!"; // TODO: generalize
        $lLog = new CCor_Log();
        $lLog -> log($lMsg, mlError, mtNone);

        $lMessage = lan('lib.file.not.created');
      }

      if (!$this -> canContinue()) {
        break;
      }
    }

    if (!$lBatch) {
      return array('img' => $lOSI, 'thb' => $lSSI, 'msg' => $lMessage);  
    } else {
      return TRUE;
    }
  }
}