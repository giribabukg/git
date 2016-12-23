<?php
class CInc_Svc_Wecchk extends CSvc_Base {

  protected $mServiceConfiguration;
  protected $mServiceConfigurationKeys = array(
    'wec-pi.fetch_log',
    'wec-pi.fetch_check',
    'wec-pi.fetch_number_of_check',
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

  /**
   * Execute
   *
   * @return boolean
   */
  protected function doExecute() {
    $this -> mServiceConfiguration = $this -> getServiceConfiguration();
    $this -> mLogConfiguration = $this -> getLogConfiguration();

    $this -> mClients = CCor_Res::extract('id', 'code', 'mand');

    if (array_key_exists(intval(MID), $this -> mClients)) {
      $lGetCheckForNewerDocumentVersions = $this -> getCheckForNewerDocumentVersions(intval(MID));
    }

    return true;
  }

  /**
   * Log
   *
   * @return boolean
   */
  public function log($aMessage, $aTimestamp = true) {
    if (array_key_exists('wec-pi.fetch_log', $this -> mServiceConfiguration)) {
      $lResult = $this -> mServiceConfiguration['wec-pi.fetch_log'];

      if ('on' == $lResult) {
        $lMessage = $aMessage;
        $lTimestamp = $aTimestamp ? '['.date(lan('lib.datetime.long')).'] ' : '';

        $lPath = $this -> mLogConfiguration['path'];
        $lFile = $this -> mLogConfiguration['file'];

        error_log($lTimestamp.$lMessage, 3, $lPath.$lFile);
      }
    }
  }

  /**
   * Array column
   *
   * @return array
   */
  public function array_col(array $aArray, $aKey) {
    return array_map(function($aArray) use ($aKey) {return $aArray[$aKey];}, $aArray);
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

  /**
   * Create wectns tables if not available
   *
   * @return boolean
   */
  protected function getCheckForNewerDocumentVersions($aClient) {
    $lClient = $aClient;

    $lRequest = new CCor_TblIte('al_job_wectns_'.$lClient);
    $lRequest -> addField('jobid');
    $lRequest -> addField('wec_prj_id');
    $lRequest -> addField('wec_doc_id');
    $lRequest -> addField('wec_ver_id');
    $lRequest -> addCondition('repository', '<>', 'archive'); // WHERE
    $lRequest -> addCondition('downloaded', '<>', ''); // WHERE
    $lRequest -> addCondition('checked', 'IS NULL OR', '0'); // WHERE
    $lRequest -> setOrder('jobid', 'desc'); // ORDER BY
    $lRequest -> setLimit($this -> mServiceConfiguration['wec-pi.fetch_number_of_check']); // LIMIT
    $lResponse = $lRequest -> getArray();

    if (!empty($lResponse)) {
      $lWec = new CApi_Wec_Client();
      $lWec -> loadConfig();

      foreach ($lResponse as $lRow) {
        $lJobID = $lRow['jobid'];
        $lWecPrjID = $lRow['wec_prj_id'];
        $lWecDocID = $lRow['wec_doc_id'];
        $lOldWecVerID = $lRow['wec_ver_id'];

        $lWecDocLst = new CApi_Wec_Query_Doclist($lWec);
        $lWecDocRes = $lWecDocLst -> getList($lWecPrjID);

        $lNewWecVerID = max($this -> array_col($lWecDocRes, 'wec_ver_id'));
        if ($lNewWecVerID > $lOldWecVerID) {
          $this -> log("Id: ".$lClient.", Code: ".$this -> mClients[$lClient].", Nachfolgendem Auftrag liegt eine neue Dokumentenversion vor: ".$lJobID.LF);
          CCor_Qry::exec('UPDATE al_job_wectns_'.$lClient.' SET checked=NULL WHERE jobid="'.$lJobID.'";');
          CCor_Qry::exec('UPDATE al_job_wectns_'.$lClient.' SET downloaded=NULL WHERE jobid="'.$lJobID.'";');
        } else {
          $lDir = CCor_Cfg::get('svc.dir', '');
          error_log("NO!", 3, $lDir.DS.'NO_'.$lJobID.".txt");
          CCor_Qry::exec('UPDATE al_job_wectns_'.$lClient.' SET checked="'.time().'" WHERE jobid="'.$lJobID.'";');
        }
      }
    } else {
      CCor_Qry::exec('UPDATE al_job_wectns_'.$lClient.' SET checked=NULL;');
    }

    return true;
  }
}