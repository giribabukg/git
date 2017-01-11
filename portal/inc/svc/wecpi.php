<?php
class CInc_Svc_Wecpi extends CSvc_Base {

  protected $mPreviewKeys = array('wec-pi.sync_subfolder', 'wec-pi.sync_number', 'wec-pi.sync_log', 'wec-pi.image_subfolder', 'wec-pi.image_name', 'wec-pi.image_notfound', 'wec-pi.thumbnail_subfolder', 'wec-pi.thumbnail_name', 'wec-pi.thumbnail_notfound');
  protected $mPreviewArray;

  protected $mLog; // service log keeps path, file and prefix
  protected $mLogPath; // service log path
  protected $mLogFile; // service log file
  protected $mLogPrefix; // datetime stamp for service log

  protected $mClients; // stores client numbers in an array
  protected $mWec; // stores client numbers in an array
  protected $mWecPi; // stores client numbers in an array
  protected $mWecTns; // stores client numbers in an array

  protected function doExecute() {
    $this -> mService = $this -> setUpService();
    $this -> mClients = $this -> getClients();

    $this -> mWecTns = CCor_Qry::getInt("SELECT flags FROM al_sys_svc WHERE act='wectns';");
    $this -> mWecPi = CCor_Qry::getInt("SELECT flags FROM al_sys_svc WHERE act='wecpi';");

    $this -> mWec = new CApi_Wec_Client();
    $this -> mWec -> loadConfig();
    if (array_key_exists(MID, $this -> mClients)) {
      $lTablesUpdated = $this -> updateWectnsTables(MID);
      $lDatasetsUpdated = $this -> updateWectnsDatasets(MID);
      if (1 == $this -> mWecTns) {
        $lCopyToNetworker = $this -> copy_to_networker(MID);
      }
      if (1 == $this -> mWecPi) {
        $lCopyToPortal = $this -> copy_to_portal(MID);
      }
    }
    return TRUE;
  }

  protected function setUpLog() {
    // service log path
    $this -> mLogPath = str_replace("\\", "/", CCor_Cfg::get('svc.dir', getcwd()));
    if (substr($this -> mLogPath, -1, 1) != "/" && strlen($this -> mLogPath) > 0) {
      $this -> mLogPath .= "/";
    }

    // service log file
    $this -> mLogFile = $this -> mLogPath."services_wectns-".date("Y.m.d", time()).".txt";

    // datetime stamp for service log
    $this -> mLogPrefix = date("Y.m.d G:i:s", time() + 60 * 60);

    return array('path' => $this -> mLogPath, 'file' => $this -> mLogFile, 'prefix' => $this -> mLogPrefix);
  }

  protected function setUpService() {
    $this -> mPreviewArray = array();

    $lLog = CCor_Qry::getStr("SELECT val FROM al_sys_pref WHERE code='wec-pi.sync_log';");
    if (!empty($lLog)) {
      $this -> mLog = $this -> setUpLog();
      $this -> mPreviewArray['log'] = TRUE;
    } else {
      $this -> mPreviewArray['log'] = FALSE;
    }

    $lKeys = implode('","', $this -> mPreviewKeys);

    $lQry = new CCor_Qry('SELECT code, val FROM al_sys_pref WHERE code IN ("'.$lKeys.'");');
    foreach ($lQry as $lKey => $lValue) {
      $lValue['val'] = str_replace("\\", "/", $lValue['val']);
      $this -> mPreviewArray[$lValue['code']] = $lValue['val'];
    }

    $this -> mPreviewArray['getcwd'] = str_replace("\\", "/", getcwd());
    if (substr($this -> mPreviewArray['getcwd'], -1, 1) != "/" && strlen($this -> mPreviewArray['getcwd']) >= 1) {
      $this -> mPreviewArray['getcwd'].= "/";
    }

    return $this -> mPreviewArray;
  }

  protected function getClients() {
    $lResult = array();
    $lQuery = new CCor_Qry('SELECT id, code FROM al_sys_mand WHERE id NOT IN (0, 998, 999, 1000) ORDER BY id;');
    foreach ($lQuery as $lRow) {
      $lResult[$lRow['id']] = $lRow['code'];
    }

    return $lResult;
  }

  protected function updateWectnsTables($aClient) {
    $lClient = $aClient;
    $lResult = TRUE;

    if (0 == $this -> mWecTns && 1 == $this -> mWecPi) {
//       $lResult = CCor_Qry::exec('CREATE TABLE IF NOT EXISTS al_job_wecpi (
//           jobid CHAR(13),
//           src CHAR(3),
//           wec_prj_id CHAR(16),
//           wec_doc_id CHAR(16),
//           downloaded DATETIME,
//           copied_to_portal DATETIME,
//           copied_to_networker DATETIME,
//           jobnr INT(11),
//           mid CHAR(4),
//           INDEX `jobid` (`jobid`),
//           INDEX `wec_prj_id` (`wec_prj_id`),
//           INDEX `wec_doc_id` (`wec_doc_id`)
//       );'
//       );
    } else {
      $lResult = CCor_Qry::exec('CREATE TABLE IF NOT EXISTS al_job_wectns_'.$lClient.' (
          jobid CHAR(13),
          src CHAR(3),
          wec_prj_id CHAR(16),
          wec_doc_id CHAR(16),
          downloaded DATETIME,
          copied_to_portal DATETIME,
          copied_to_networker DATETIME,
          jobnr INT(11),
          mid CHAR(4),
          INDEX `jobid` (`jobid`),
  	      INDEX `wec_prj_id` (`wec_prj_id`),
  	      INDEX `wec_doc_id` (`wec_doc_id`)
      );'
      );
    }

    if (TRUE == $this -> mPreviewArray['log']) {
      if (1 == $lResult) {
        error_log('Service table created!\n\r', 3, $this -> mLogFile);
      } else {
        error_log('Service table available!\n\r', 3, $this -> mLogFile);
      }
    }

    return $lResult;
  }

  protected function editResponse($aClient, $aResponse, $aVia = 'alink') {
    $lClient = $aClient;
    $lResponse = $aResponse;
    $lVia = $aVia;

    if ($lVia == 'alink') {
      $lViaCfg = CCor_Cfg::get('all-jobs_ALINK');
    } elseif ($lVia == 'pdb') {
      $lViaCfg = CCor_Cfg::get('all-jobs_PDB');
    } elseif ($lVia == 'arc') {
      $lViaCfg = CCor_Cfg::get('all-jobs');
    }

    $lValuesAvailable = FALSE;
    if (!empty($lViaCfg)) {

      $lValues = '';
      foreach ($lResponse as $lRow) {
        $lJobID = $lRow['jobid'];
        $lSrc = $lRow['src'];
        $lWecPrjID = $lRow['wec_prj_id'];
        $lJobNr = $lRow['jobnr'];

        $lWecDocLst = new CApi_Wec_Query_Doclist($this -> mWec);
        $lWecDocRes = $lWecDocLst -> getList($lWecPrjID);

        if (!empty($lJobID) && !empty($lSrc) && !empty($lWecPrjID) && in_array($lSrc, $lViaCfg)) {
          if (!empty($lWecDocRes)) {
            foreach ($lWecDocRes as $lWecDocRow) {
              if ($lWecDocRow['wec_doc_id']) {
                $lValuesAvailable = TRUE;
                $lValues = $lValues.'("'.$lJobID.'", "'.$lSrc.'", "'.$lWecPrjID.'", "'.$lWecDocRow['wec_doc_id'].'", null, null, null, "'.$lJobNr.'", "'.$lClient.'"),';
              }
            }
          }
        }
      }
    }

    if ($lValuesAvailable === TRUE) {
      $lResult = CCor_Qry::exec('REPLACE INTO al_job_wectns_'.$lClient.' (jobid, src, wec_prj_id, wec_doc_id, downloaded, copied_to_portal, copied_to_networker, jobnr, mid) VALUES '.rtrim($lValues, ','));
    } else {
    }
  }

  protected function updateWectnsDatasets($aClient) {
    if (0 == $this -> mWecTns && 1 == $this -> mWecPi) {
//       $lClient = $aClient;

//       for ($lOuterValue = 0; $lOuterValue < 1000000; $lOuterValue = $lOuterValue + 5000) {
//         $lValues = '';
//         for ($lInnerValue = 1; $lInnerValue < 5000; $lInnerValue++) {
//           $lSum = $lInnerValue + $lOuterValue;
//           $lValues.= '("'.str_pad($lSum, 9 , "0", STR_PAD_LEFT).'","'.$lSum.'"),';
//         }

//         $lResult = CCor_Qry::exec('INSERT INTO al_job_wectns_'.$lClient.' (jobid,jobnr) VALUES '.rtrim($lValues, ','));
//       }
    } else {
      $lClient = $aClient;

      $lSvcWecInst = CSvc_Wec::getInstance();

      require_once 'cust/inc/cor/cfg.php';
      require_once 'mand/mand_'.$lClient.'/cor/cfg.php';

      $lKNr = CCor_Cfg::get('wec.tns.knr');
      $lShortName = CCor_Cfg::get('wec.tns.short');

      // get job fields
      $lAliasToNative = array();
      $lQuery = new CCor_Qry('SELECT alias, native FROM al_fie WHERE mand='.$lClient);
      foreach ($lQuery as $lRow) {
        $lAliasToNative[$lRow -> alias] = $lRow -> native;
      }

      $lLimitFrom = CCor_Qry::getInt("SELECT COUNT(DISTINCT jobid) FROM al_job_wectns_".$lClient);

      $lWriter = CCor_Cfg::get('job.writer.default', 'alink');
      if ('portal' == $lWriter) {
        $lIte = new CCor_TblIte('all', FALSE);
        $lIte -> addField('jobnr');
        $lIte -> addField('src');
        $lIte -> addField('wec_prj_id');
        $lIte -> addCondition('wec_prj_id', '<>', '');
        $lIte -> setOrder('jobnr', 'asc');
        $lIte -> setLimit($lLimitFrom, $lAttributes['fetch_number']);
        $lResponse = $lIte -> getArray();

        if (!empty($lResponse)) {
          $this -> editResponse($lClient, $lResponse, 'pdb');
        }
      } else {
        // check whether jobs are stored in Networker database
        $lRequest = new CInc_Api_Alink_Query_Getjoblist('', FALSE, $lShortName, $lKNr);
        $lRequest -> addField('jobnr', $lAliasToNative['jobnr']);
        $lRequest -> addField('src', $lAliasToNative['src']);
        $lRequest -> addField('wec_prj_id', $lAliasToNative['wec_prj_id']);
        $lRequest -> addCondition('wec_prj_id', '<>', '');
        $lRequest -> setOrder('jobnr', 'asc');
        $lRequest -> setLimit($lLimitFrom, $lAttributes['fetch_number']);
        $lResponse = $lRequest -> getArray();

        if (!empty($lResponse)) {
          $this -> editResponse($lClient, $lResponse, 'alink');
        }
      }

      $lPDB = CCor_Cfg::get('all-jobs_PDB');
      if (is_array($lPDB) AND count($lPDB) > 0) {
        // check whether jobs are stored in Portal database
        $lRequest = new CCor_TblIte('al_job_pdb_'.$lClient);
        $lRequest -> addField('jobid');
        $lRequest -> addField('jobnr');
        $lRequest -> addField('src');
        $lRequest -> addField('wec_prj_id');
        $lRequest -> addCondition('wec_prj_id', '<>', '');
        $lRequest -> setOrder('jobnr', 'asc');
        $lRequest -> setLimit($lLimitFrom, $lAttributes['fetch_number']);
        $lResponse = $lRequest -> getArray();

        if (!empty($lResponse)) {
          $this -> editResponse($lClient, $lResponse, 'pdb');
        }
      }

      // check whether there are archived jobs
      $lRequest = new CCor_TblIte('al_job_arc_'.$lClient);
      $lRequest -> addField('jobid');
      $lRequest -> addField('jobnr');
      $lRequest -> addField('src');
      $lRequest -> addField('wec_prj_id');
      $lRequest -> addCondition('wec_prj_id', '<>', '');
      $lRequest -> setOrder('jobnr', 'asc');
      $lRequest -> setLimit($lLimitFrom, $lAttributes['fetch_number']);
      $lResponse = $lRequest -> getArray();

      if (!empty($lResponse)) {
        $this -> editResponse($lClient, $lResponse, 'arc');
      }
    }

    return TRUE;
  }

  protected function copy_to_networker($aClient) {
    $lClient = $aClient;

    $lSvcWecInst = CSvc_Wec::getInstance();

    $lQuery = new CCor_Qry('SELECT * FROM al_job_wectns_'.$lClient.' WHERE downloaded IS NOT NULL AND copied_to_networker IS NULL AND mid='.$lClient.' ORDER BY jobid LIMIT 0, '.$this -> mPreviewArray['wec-pi.sync_number']);
    foreach ($lQuery as $lRow) {
      $lJobID = $lRow['jobid'];
      $lSrc = $lRow['src'];
      $lWecPrjID = $lRow['wec_prj_id'];
      $lWecDocID = $lRow['wec_doc_id'];
      $lJobNr = $lRow['jobnr'];

      $lAttributes = $lSvcWecInst -> getAttributes($lJobID);

      $lImageFound = $lAttributes['getcwd'].$lAttributes['image_directoryname'].$lAttributes['image_filename'];

      $lQry = new CApi_Alink_Query('putFile');
      $lQry -> addParam('sid', $this -> mClients[$lClient]);
      $lQry -> addParam('jobid', $lJobID);
      $lQry -> addParam('filename', $lAttributes['image_filename']);
      $lQry -> addParam('data', base64_encode(file_get_contents($lImageFound)));
      $lQry -> addParam('mode', 2);
      $lRes = $lQry -> query();

      if ($lRes) {
        $lResult = CCor_Qry::exec('UPDATE al_job_wectns_'.$lClient.' SET copied_to_networker=NOW() WHERE jobid="'.$lJobID.'" AND src="'.$lSrc.'" AND  wec_prj_id="'.$lWecPrjID.'" AND wec_doc_id="'.$lWecDocID.'" AND mid="'.$lClient.'"');
      }
    }

    return TRUE;
  }

  protected function copy_to_portal($aClient) {
    if (0 == $this -> mWecTns && 1 == $this -> mWecPi) {
      require_once 'cust/inc/cor/cfg.php';

      $lClient = $aClient;

      $lSvcWecInst = CSvc_Wec::getInstance();

      $lFrom = CCor_Cfg::get('svc.wecpi.from', 0);
      $lTo = CCor_Cfg::get('svc.wecpi.to', 1000000);

      $lCount = CCor_Qry::getInt("SELECT COUNT(*) FROM al_job_wecpi WHERE downloaded IS NULL AND copied_to_portal IS NULL AND jobnr >= ".$lFrom." AND jobnr <= ".$lTo);
      if (0 == $lCount) {
        $lResult = CCor_Qry::exec('UPDATE al_job_wecpi SET downloaded=NULL WHERE copied_to_portal IS NULL AND jobnr >= '.$lFrom.' AND jobnr <= '.$lTo);
      }

      $lQuery = new CCor_Qry('SELECT * FROM al_job_wecpi WHERE downloaded IS NULL AND copied_to_portal IS NULL AND jobnr >= '.$lFrom.' AND jobnr <= '.$lTo.' ORDER BY jobid DESC LIMIT 0, '.$this -> mPreviewArray['wec-pi.sync_number']);
      foreach ($lQuery as $lRow) {
        $lJobID = $lRow['jobid'];

        $lAttributes = $lSvcWecInst -> getAttributes($lJobID);

        # create preview image directories
        $lPreviewImageFound = $lAttributes['getcwd'].$lAttributes['image_directoryname'];
        $lOldUMask = umask(0);
        $lResult = is_dir($lPreviewImageFound) ? FALSE : @mkdir($lPreviewImageFound, 0755, TRUE);
        umask($lOldUMask);

        header('Cache-Control: public');
        header('Content-Type: application/jpg');
        header('Content-Disposition: attachment; filename="'.$lAttributes['image_filename'].'"');

        $lQry = new CApi_Alink_Query('getFile');
        $lQry -> addParam('sid',  $this -> mClients[$lClient]);
        $lQry -> addParam('jobid', $lJobID);
        $lQry -> addParam('filename', $lAttributes['image_filename']);
        $lRes = $lQry -> query();

        $lErr = $lRes -> getVal('errno');
        if (0 == $lErr) {
          $lRet = base64_decode($lRes -> getVal('data'));
          if (!empty($lRet)) {
            $lFile = fopen($lPreviewImageFound.$lAttributes['image_filename'], "w");
            fwrite($lFile, $lRet);
            fclose($lFile);

            # create thumbnail
            $lImage = @imagecreatefromjpeg($lPreviewImageFound.$lAttributes['image_filename']);
            if ($lImage) {
            $lImageWidth = imagesx($lImage);
              $lImageHeight = imagesy($lImage);
              $lThumbnailWidth = 100;
              $lThumbnailHeight = 100;
              $lThumbnail = imagecreatetruecolor($lThumbnailWidth, $lThumbnailHeight);
              imagecopyresampled($lThumbnail, $lImage, 0, 0, 0, 0, $lThumbnailWidth, $lThumbnailHeight, $lImageWidth, $lImageHeight);
              header("Content-Type: image/jpeg");
              imagejpeg($lThumbnail, $lPreviewImageFound.$lAttributes['thumbnail_filename'], 75);
            }

            $lResult = CCor_Qry::exec('UPDATE al_job_wecpi SET copied_to_portal=NOW() WHERE jobid="'.$lJobID.'"');
          } else {
            $lResult = CCor_Qry::exec('UPDATE al_job_wecpi SET downloaded=NOW() WHERE jobid="'.$lJobID.'"');
          }
        }
      }
    } else {
      $lClient = $aClient;

      $lSvcWecInst = CSvc_Wec::getInstance();

      $lQuery = new CCor_Qry('SELECT * FROM al_job_wectns_'.$lClient.' WHERE downloaded IS NULL AND copied_to_portal IS NULL AND mid='.$lClient.' ORDER BY jobid LIMIT 0, '.$this -> mPreviewArray['wec-pi.sync_number']);
      foreach ($lQuery as $lRow) {
        $lJobID = $lRow['jobid'];
        $lSrc = $lRow['src'];
        $lWecPrjID = $lRow['wec_prj_id'];
        $lWecDocID = $lRow['wec_doc_id'];
        $lJobNr = $lRow['jobnr'];

        $lAttributes = $lSvcWecInst -> getAttributes($lJobID);

        # create preview image directories
        $lPreviewImageFound = $lAttributes['getcwd'].$lAttributes['image_directoryname'];
        $lOldUMask = umask(0);
        $lResult = is_dir($lPreviewImageFound) ? FALSE : @mkdir($lPreviewImageFound, 0755, TRUE);
        umask($lOldUMask);

        header('Cache-Control: public');
        header('Content-Type: application/jpg');
        header('Content-Disposition: attachment; filename="'.$lAttributes['image_filename'].'"');

        $lQry = new CApi_Alink_Query('getFile');
        $lQry -> addParam('sid',  $this -> mClients[$lClient]);
        $lQry -> addParam('jobid', $lJobID);
        $lQry -> addParam('filename', $lAttributes['image_filename']);
        $lRes = $lQry -> query();

        $lRet = base64_decode($lRes -> getVal('data'));
        $lFile = fopen($lPreviewImageFound.$lAttributes['image_filename'], "w");
        fwrite($lFile, $lRet);
        fclose($lFile);

        # create thumbnail
        $lImage = @imagecreatefromjpeg($lPreviewImageFound.$lAttributes['image_filename']);
        if ($lImage) {
          $lImageWidth = imagesx($lImage);
          $lImageHeight = imagesy($lImage);
          $lThumbnailWidth = 100;
          $lThumbnailHeight = 100;
          $lThumbnail = imagecreatetruecolor($lThumbnailWidth, $lThumbnailHeight);
          imagecopyresampled($lThumbnail, $lImage, 0, 0, 0, 0, $lThumbnailWidth, $lThumbnailHeight, $lImageWidth, $lImageHeight);
          header("Content-Type: image/jpeg");
          imagejpeg($lThumbnail, $lPreviewImageFound.$lAttributes['thumbnail_filename'], 75);
        }

        if ($lRes) {
          $lResult = CCor_Qry::exec('UPDATE al_job_wectns_'.$lClient.' SET copied_to_portal=NOW() WHERE jobid="'.$lJobID.'" AND src="'.$lSrc.'" AND  wec_prj_id="'.$lWecPrjID.'" AND wec_doc_id="'.$lWecDocID.'" AND mid="'.$lClient.'"');
        }
      }
    }

    return TRUE;
  }
}