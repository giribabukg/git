<?php
#MOP2010
class CInc_Mig_Cnt extends CCor_Cnt {

  protected $mAllJobsFromAlink;
  
  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);

    $this -> mTitle = lan('mig.tls');
    $lPn = 'mig';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lPn)) {
      $this -> setProtection('*', $lPn, rdNone);
    }

    $this -> mAllJobsFromAlink = CCor_Cfg::get('all-jobs_ALINK', array());
    $this -> mBlocksize = 10;
  }

  protected function actStd() {
    $this -> actArcjob();
  }
  ###############################################################################################################################
  protected function actCopyJobsToWave() {
    $lFrm = new CHtm_Form('mig.scopyjobstowave', 'Migration Tool', false);
    $AllTypes = array();
    foreach ($this -> mAllJobsFromAlink as $lSrc) {
      if('rep' == $lSrc) {
        $AllTypes = array_merge(array($lSrc => lan('job-'.$lSrc.'.menu')), $AllTypes);
      } else {
        $AllTypes[$lSrc] = lan('job-'.$lSrc.'.menu');
      }
    }
    $lField = CCor_Res::getByKey('alias', 'fie');
    $lFrm -> addDef(fie('typ', 'Job Type', 'select', $AllTypes));
    $lFrm -> addDef(fie('status', 'Invoiced', 'boolean'));
    $lFrm -> addDef(fie('canceled', 'Canceled', 'boolean'));
    $lFrm -> addDef(fie('blocksize', 'Blocksize (max. '.$this -> mBlocksize.')'));
    $lFrm -> setVal('blocksize', 1);
#    $lFrm -> setVal('status', 'on');
  
    $lMen = new CMig_Menu('copyjobstowave');
        
    $this -> render(CHtm_Wrap::wrap($lMen, $lFrm));
  }
  
  protected function actSCopyJobsToWave() {
    $lRet = '';
    $lVal = $this -> getReq('val');
    $lInvoiced = (isset($lVal['status']) AND 'on' == $lVal['status']) ? $lVal['status'] : NULL;
    $lCanceled = (isset($lVal['canceled']) AND 'on' == $lVal['canceled']) ? $lVal['canceled'] : NULL;
    $lBlocksize = (int) $lVal['blocksize'] ;
    if (empty($lBlocksize)) $lBlocksize = 1;
    elseif ($this -> mBlocksize < $lBlocksize) $lBlocksize = $this -> mBlocksize;
    $lSrc = $lVal['typ'];
    if (!in_array($lSrc, $this -> mAllJobsFromAlink)) {
      $this -> msg('Unknown Jobtype '.$lSrc, mtUser, mlError);
      $this -> redirect('index.php?act=mig.copyjobstowave');
    }
  
    $lJobIds = array();
    $lArchievedJobIds = array();
    $lNotUpdatedJobIds = array();
    $lNotArchievedJobIds = array();
    $lJoblist = $this -> getJobsFromAlink($lSrc, $lInvoiced, $lCanceled);
  
    ####
    $lFrm = new CHtm_Form('mig.scopyjobstowave', 'Migration Tool', false);
    $AllTypes = array();
    foreach ($this -> mAllJobsFromAlink as $lSrc) {
      if('rep' == $lSrc) {
        $AllTypes = array_merge(array($lSrc => lan('job-'.$lSrc.'.menu')), $AllTypes);
      } else {
        $AllTypes[$lSrc] = lan('job-'.$lSrc.'.menu');
      }
    }
    $lField = CCor_Res::getByKey('alias', 'fie');
    $lFrm -> addDef(fie('typ', 'Job Type', 'select', $AllTypes));
    $lFrm -> addDef(fie('status', 'Invoiced', 'boolean'));
    $lFrm -> addDef(fie('canceled', 'Canceled', 'boolean'));
    $lFrm -> addDef(fie('blocksize', 'Blocksize (max. '.$this -> mBlocksize.')'));
    $lFrm -> addDef(fie('domove', 'Move to Wave DB?', 'boolean'));
    $lFrm -> setVal('typ', $lVal['typ']);
    $lFrm -> setVal('blocksize', $lBlocksize);
#    $lFrm -> setVal('status', $lInvoiced);
    $lFrm -> setVal('canceled', $lCanceled);
    $lLimitedJobs = array_slice($this -> mJobIds, 0, $lBlocksize);
    if (isset($lVal['domove']) AND 'on' == $lVal['domove']) $this -> moveToWave($lLimitedJobs, $lVal['typ']);
    $lMen = new CMig_Menu('copyjobstowave');
    $this -> render(CHtm_Wrap::wrap($lMen, $lFrm, $lJoblist));
  }
  
  protected function getJobsFromAlink($aSrc, $aInvoiced, $aCanceled) {
    $lSrc = $aSrc;
    $lInvoiced = $aInvoiced;
    $lCanceled = $aCanceled;
    if ($lCanceled) {
      $lCanceledJobs = $this -> getCanceledJobs($lSrc);
    }
    $lJoblist = new CApi_Alink_Query_Getjoblist();
    $lJoblist -> addCondition('src', '=', $lSrc);
    $lJoblist -> addCondition('webstatus', '>=', '10');
    $lJoblist -> addCondition('webstatus', '<=', '190');
    $lJoblist -> addCondition('mig_flag', 'IS NULL OR', 0);
    $lJoblist -> addField('jobid', 'jobid');
  
    if (!empty($lInvoiced))   $lJoblist -> addCondition('status', 'IN', '"RS","RE"');
    if (!empty($lCanceledJobs))   $lJoblist -> addCondition('jobid', 'IN', $lCanceledJobs);
    $this -> mJobIds = array_keys($lJoblist -> getArray('jobid'));
  
    $lTbl = new CHtm_List('job-'.$aSrc, 'JobIds');
    $lTbl -> addCtr();
    $lTbl -> addColumn('jobid', 'JobId');
    $lTbl -> mIte = $lJoblist -> getArray('jobid');
  
    $lTbl -> setAtt('class', 'tbl w200');
    $lTbl -> mTitle = 'JobIds need to updated';
    return $lTbl;
  }
  
  protected function  moveToWave($aJobIds, $aSrc) {
    foreach ($aJobIds as $lKey => $lJid) {
  
      $lJob2Wave = $this -> copyJob($lJid, $aSrc);
  
      if ('OK' == $lJob2Wave) {
        $lArchievedJobIds[] = $lJid;
        $this -> msg('Okay! JobId '.$lJid.' copied to Wave DB!', mtUser, mlInfo);
      } elseif ('UpdateError' == $lJob2Wave) {
        $lNotUpdatedJobIds[] = $lJid;
        $this -> msg('Error! Update "update flag ="1 failed for JobId='.$lJid, mtUser, mlError);
      } else {
        $lNotArchievedJobIds[] = $lJid;
        $this -> msg('An Error occurred! JobId '.$lJid.' is NOT copied to Wave DB!', mtUser, mlError);
      }
    }
  }
  
  protected function copyJob($aJobId, $aSrc) {
    $lRet = '';
    $lJobId = $aJobId;
    $lSrc = $aSrc;
    
    if ($this -> copyJobToWave($lJobId, $lSrc)) {
      $lMsg = 'Okay! JobId '.$lJobId.' copied to Wave used '.$lSrc.'!';
      $this -> msg($lMsg, mtUser, mlInfo);
  
      $lWriter = CCor_Cfg::get('job.writer.default', 'alink');
      if($lWriter == 'alink'){
        $lAlink = new CInc_Api_Alink_Query_Updatejob($aJobId);
        $lAlink -> addField('Zus.987654321', "1");
        $lUpdate = $lAlink ->query();
        $lMsg = $lUpdate -> mDoc -> errmsg;
      } else {
        $lFie = array('key' => 'mig_flag', 'typ' => 'int', 'par' => '', 'nat' => 'Job#Zus#mig_flag', 'lis' => '');
        $lFields = array('mig_flag' => & $lFie);
        $lMop = new CInc_Job_Writer_Mop($lFields);
        $lUpdateOutcome = $lMop -> update($aJobId, array('mig_flag' => '1'));
        
        $lMsg = ($lUpdateOutcome !== FALSE) ? 'OK' : 'ERROR';
      }
      
      if ('OK' == $lMsg) {
        $lMsg = 'Okay! Update "Mig-Flag="1 for JobId='.$lJobId;
        $this -> msg($lMsg, mtUser, mlInfo);
        $lRet.= 'OK';
      } else {
        $lMsg = 'Error! Update "Mig-Flag="1 failed for JobId='.$lJobId;
        $this -> msg($lMsg, mtUser, mlError);
        $lRet.= 'UpdateError';
      }
    } else {
      $lMsg = 'CopyJobToWave failed for '.$lJobId.'!';
      $this -> msg($lMsg, mtUser, mlError);
      $lRet.= 'Failed';
    }
    return $lRet;
  }
  
  public function copyJobToWave($aJobId, $aSrc) {
    $lJob = $this -> getJobFromAlink($aJobId, $aSrc);
    $lJobId = $aJobId;
  
    $lQry = new CCor_Qry();
    
    // exiting archive tabel columns
    $lExistingColumns = array();
    if (!(empty($lJobId))) {
      $lArr = $lJob -> toArray();
      
      // Get existing columns from archive table
      $lTabelColumns = new CCor_Qry('SHOW COLUMNS FROM al_job_'.$aSrc.'_'.MID);
      $lConvertDatetime = array();
      foreach ($lTabelColumns as $lRow) {
        if ('datetime' == $lRow['Type']) {
          $lConvertDatetime[$lRow -> Field] = TRUE;
        }
        $lExistingColumns[] = $lRow -> Field;
      }
  
      $lSql = 'INSERT INTO al_job_'.$aSrc.'_'.MID.' SET ';
      foreach ($lArr as $lKey => $lVal) {
        if (!empty($lVal)) {
          // Ask if the column exists.
          if (in_array($lKey, $lExistingColumns)) {
            if (isset($lConvertDatetime[$lKey])) {
              $lTime = strtotime($lVal);
              $lVal = date('Y-m-d H:i:s', $lTime);
            }
            $lSql.= $lKey.'='.esc($lVal).',';
          }
        }
      }
      $lRet = FALSE;
      $lSql = strip($lSql);
      $lRet = $lQry -> query($lSql);
      return $lRet;
    } else {
      return FALSE;
    }
  }
  
  protected function getJobFromAlink($aId, $aSrc) {
    $lQry = new CApi_Alink_Query_Getjobdetails($aId, $aSrc);
    $lFie = CCor_Res::get('fie');
    foreach ($lFie as $lDef) {
      if (!empty($lDef['native'])) {
        $lQry -> addDef($lDef);
      }
    }
    $lRes = $lQry -> query();
    if (!$lRes) return FALSE;
    $lJob = $lQry -> getDat();
    return $lJob;
  }
  ###############################################################################################################################

  protected function networkerdbname() {
    return CCor_Cfg::get('db.networker.name');
  }

  protected function networkerdbip() {
    return CCor_Cfg::get('db.networker.ip');
  }

  protected function networkerdbpass() {
    return CCor_Cfg::get('db.networker.pass');
  }

  protected function networkerdbuser() {
    return CCor_Cfg::get('db.networker.user');
  }

  //---START Migriere einen einzelnen Job ins Archiv
  protected function actArcjob() {
    $lMsg = $this -> getReq('msg','');
    $lFrm = new CHtm_Form('mig.sarcjob', lan('mig.arc.job.missing'), '');
    $lFrm ->addDef(fie('jobid', 'JobId'));
    #$lFrm -> setDescription(lan('mig.arcjob.onlyCopy'));
    $lFrm -> setDescription(lan('mig.arcjob.JobId.restrict'));
    if (!empty($lMsg)) {
      $lFrm -> setDescription('');
      $lFrm -> setDescription($lMsg);
      $lFrm -> setDescription('');
    }
    $lFrm -> addDef(fie('typ', lan('job.typ'), 'select', array('rep' => lan('job-rep.menu'), 'art' => lan('job-art.menu'))));

    $lMen = new CMig_Menu('arcjob');
    $this -> render(CHtm_Wrap::wrap($lMen, $lFrm));
  }

  protected function actSarcjob() {
    $lUrl = 'index.php?act=mig.arcjob';
    $lMsg = '';

    $lVal = $this -> getReq('val');
    $lJobId = $lVal['jobid'];
    $lSrc = $lVal['typ'];
    $lOk = TRUE;
    if (9 != strlen($lJobId)) {
      $lMsg = 'Invalid jobid!';
      $this -> msg($lMsg, mtUser, mlError);
      $lUrl.= '&msg='.$lMsg;
      $this -> redirect($lUrl);
    }
    
    $this -> archive1job($lJobId, $lSrc);
    
    $this -> redirect($lUrl);
  }

  protected function archive1job($aJobId, $aSrc) {
    $lRet = '';
    $lJobId = $aJobId;

    $lSrc = ucfirst($aSrc);
    $lStepClass = 'CJob_'.$lSrc.'_Step';
    $lModClass  = 'CJob_'.$lSrc.'_Mod';
    $lStp = new $lStepClass($lJobId);
    $lMod = new $lModClass($lJobId);

    $lUpd = array(
        'webstatus' => STATUS_ARCHIV,
        'src' => $aSrc,
        'last_status_change' => date('Y-m-d H:i:s')
    );
    if ($lStp -> copyJobToArc($lUpd)) {
      $lMsg = 'Okay! JobId '.$lJobId.' copied to archive used '.$lSrc.'!';
      $this -> msg($lMsg, mtUser, mlInfo);
      $lStp -> addMigToHistory();

      $lUpdate = $lMod -> forceUpdate($lUpd);
      if ('OK' == $lUpdate -> mDoc -> errmsg) {
        $lMsg = 'Okay! Update "auftrag-webstatus="'.STATUS_ARCHIV.' for JobId='.$lJobId;
        $this -> msg($lMsg, mtUser, mlInfo);
        $lRet.= 'OK';
      } else {
        $lMsg = 'Error! Update "auftrag-webstatus="'.STATUS_ARCHIV.' failed for JobId='.$lJobId;
        $this -> msg($lMsg, mtUser, mlError);
        $lRet.= 'UpdateError';
      }
    } else { // copyJobToArc failed
      $lMsg = 'CopyJobToArchiv failed for '.$lJobId.'!';
      $this -> msg($lMsg, mtUser, mlError);
      $lRet.= 'Failed';
    }
    return $lRet;
  }
  //---ENDE Migriere einen einzelnen Job ins Archiv

  //--START: Copy Jobs To Archive
/*
  protected function getSelectJobsFromAuftrag($aSrc) {

    $lSql = 'SELECT `JobId` FROM `auftrag` WHERE 1';

    if ('art' == $aSrc){
      $lKnr = CCor_Cfg::get(MAND.'.art.knr');
    } else {
      $lKnr = CCor_Cfg::get(MAND.'.def.knr');
    }
    $lKnrArr = explode(',',$lKnr);
    foreach ($lKnrArr as $lKey => $lKnr) {
      $lKnrArr[$lKey] = esc($lKnr);
    }
    $lKnrStr = implode(',',$lKnrArr);
    $lSql.= ' AND `BNr` IN ('.$lKnrStr.')';

    $lSql.= ' AND `JobId`<"A"';
    $lSql.= ' AND `webstatus`=0';
    $lSql.= ' AND `UnterNr`=0'; // keine Autorenkorrekturen o.�.
    $lSql.= ' AND `Status` IN ("RS","RE")';

    return $lSql;
  }
*/

  protected function getJobsFromAuftrag($aSrc) {
    $lJoblist = new CApi_Alink_Query_Getjoblist($aSrc);
    $lJoblist -> addField('jobid', 'jobid');

    $lJoblist -> addCondition('status', 'IN', '"RS","RE"');
    $lJoblist -> addCondition('webstatus', '=', 0);
    $lJoblist -> addCondition('JobId', '<', "A");
    // Weil meistens aus Networker engelegte Jobs kein Zusatzsfeld 'src' haben,
    // soll es bei getjoblist() nicht berücksichtigt werden.
    
    # $lJoblist -> addCondition('src', '=', $aSrc);

    return $lJoblist;
  }

  protected function actCopyJobsToArchive() {
    $lFrm = new CHtm_Form('mig.scopyjobstoarchive', lan('mig.cpy.jobs.archiv'), false);
    $lFrm -> setDescription(lan('mig.cpy.jobs.archiv.descr'));

    $AllTypes = array();
    foreach ($this -> mAllJobsFromAlink as $lSrc) {
      if('rep' == $lSrc) { //Repro soll per Default ausgewählt sein
        $AllTypes = array_merge(array($lSrc => lan('job-'.$lSrc.'.menu')), $AllTypes);
      } else {
        $AllTypes[$lSrc] = lan('job-'.$lSrc.'.menu');
      }
    }
    $lFrm -> addDef(fie('typ', lan('job.typ'), 'select', $AllTypes));
    $lFrm -> addDef(fie('from_date', lan('lib.from').' '.lan('lib.date'), 'date'));
    $lFrm -> addDef(fie('to_date', lan('lib.to').' '.lan('lib.date'), 'date'));
    $lFrm -> addDef(fie('from_jobnr', lan('lib.from').' '.'JobId'));
    $lFrm -> addDef(fie('to_jobnr', lan('lib.to').' '.'JobId'));
    $lFrm -> addDef(fie('blocksize', 'Blocksize (max. '.BLOCKSIZE_MIG.')'));
    $lFrm -> setVal('blocksize', 1);

    $lMen = new CMig_Menu('copyjobstoarchive');
        
    $this -> render(CHtm_Wrap::wrap($lMen, $lFrm));
  }

  protected function actSCopyJobsToArchive() {
    $lRet = '';
    $lVal = $this -> getReq('val');

    $lDat = new CCor_Date();
    $lDat -> setInp( $lVal['from_date'] );
    if (!$lDat -> isEmpty()) {
      $lFromDate = $lDat -> mDat;  // im Format: ('Y-m-d')
    } else {
      $lFromDate = '';
    }
    $lDat -> setInp( $lVal['to_date'] );
    if (!$lDat -> isEmpty()) {
      $lToDate = $lDat -> mDat;  // im Format: ('Y-m-d')
    } else {
      $lToDate = '';
    }
    
    $lFromJobnr = (int) $lVal['from_jobnr'];
    $lToJobnr   = (int) $lVal['to_jobnr'];
   #echo '<pre>---cnt.php---';var_dump($lVal,$lFromDate,$lToDate,$lFromJobnr,$lToJobnr, '#############');echo '</pre>';

    $lBlocksize = (int) $lVal['blocksize'] ;
    if (empty($lBlocksize)) $lBlocksize = 1;
    elseif (BLOCKSIZE_MIG < $lBlocksize) $lBlocksize = BLOCKSIZE_MIG;

    $lSrc = $lVal['typ'];
    if (!in_array($lSrc, $this -> mAllJobsFromAlink)) {
      $this -> msg('Unknown Jobtype '.$lSrc, mtUser, mlError);
      $this -> redirect('index.php?act=mig.copyjobstoarchive');
    }

    $lJobIds = array();
    $lArchievedJobIds = array();
    $lNotUpdatedJobIds = array();
    $lNotArchievedJobIds = array();

    // Teil 1:
    // wird in getjobdetails & getjoblist ausgewertet -> setzt den Parameter webstatuslist!
    define('MIGRATION', TRUE);

    $lJoblist = $this -> getJobsFromAuftrag($lSrc);
    if (!empty($lFromDate)) $lJoblist -> addCondition('ausgang', '>=', $lFromDate);
    if (!empty($lToDate))   $lJoblist -> addCondition('ausgang', '<',  $lToDate);
    if (0 < $lFromJobnr)    $lJoblist -> addCondition('jobnr', '>=', $lFromJobnr);
    if (0 < $lToJobnr)      $lJoblist -> addCondition('jobnr', '<',  $lToJobnr);
    $lJoblist -> setLimit(0, $lBlocksize);
    $lJobIds  = $lJoblist -> getArray('jobid');
    $lAmount  = count($lJobIds);//$lJoblist -> getCount(); funktioniert nicht korrekt :(
    #echo '<pre>---cnt.php---';var_dump($lJobIds,$lAmount,'#############');echo '</pre>';

/*
    $lSql = $this -> getSelectJobsFromAuftrag($lSrc);
    $lSql.= ' LIMIT 0 , '.$lBlocksize;
    $this -> msg('Networker SQL: '.$lSql, mtUser, mlInfo);
    # $lRet.= $lSql.BR.BR;
    $lQryAlink = new CCor_Qrymig($lSql);
    $lQryAlink -> query($lSql);
    foreach ($lQryAlink as $lRow) {
      $lJobIds[] = $lRow['JobId'];
    }
    echo '<pre>---cnt.php---';var_dump($lSql,$lJobIds,'#############');echo '</pre>';
*/
    // Teil 2:
    if (!empty($lJobIds)) {
      foreach ($lJobIds as $lJid => $lVal) {

        $lJob2Arc = $this -> archive1job($lJid, $lSrc);

        if ('OK' == $lJob2Arc) {
          $lArchievedJobIds[] = $lJid;
          $this -> msg('Okay! JobId '.$lJid.' copied to archive!', mtUser, mlInfo);
#          $lRet.= 'Okay! JobId '.$lJid.' copied to archive!'.BR;
        } elseif ('UpdateError' == $lJob2Arc) {
          $lNotUpdatedJobIds[] = $lJid;
          $this -> msg('Error! Update "auftrag-webstatus="'.STATUS_ARCHIV.' failed for JobId='.$lJid, mtUser, mlError);
#          $lRet.= '--- Error! Update "auftrag-webstatus="'.STATUS_ARCHIV.' failed for JobId='.$lJid.'!'.BR;
        } else {
          $lNotArchievedJobIds[] = $lJid;
          $this -> msg('An Error occurred! JobId '.$lJid.' is NOT copied to archive!', mtUser, mlError);
#          $lRet.= 'An Error occurred! '.$lJid.BR;
        }
      }
      if (!empty($lArchievedJobIds)) {
        $lRet .= BR.BR.lan('mig.cpy.jobs.archiv.success').BR.BR;
        foreach ($lArchievedJobIds as $lJid) {
          $lRet.= $lJid.BR;
        }
        $lRet.= BR.'##########################################################'.BR.BR;
      }
      if (!empty($lNotUpdatedJobIds)) {
        $lRet .= BR.BR.lan('mig.cpy.jobs.archiv.nowebstatus').BR.BR;
        foreach ($lNotArchievedJobIds as $lJid) {
          $lRet.= $lJid.BR;
        }
        $lRet.= BR.'##########################################################'.BR.BR;
      }
      if (!empty($lNotArchievedJobIds)) {
        $lRet .= BR.BR.lan('mig.cpy.jobs.archiv.failed').BR.BR;
        foreach ($lNotArchievedJobIds as $lJid) {
          $lRet.= $lJid.BR;
        }
        $lRet.= BR.'##########################################################'.BR.BR;
      }
      if ($lAmount > $lBlocksize) {
        $lRet.= BR.BR.'<a href="index.php?act=mig.scopyjobstoarchive&amp;val[blocksize]='.$lBlocksize.'&amp;val[typ]='.$lSrc.'"><b>&nbsp; <u>Copy next max. '.$lBlocksize.' Jobs</u> &nbsp;</b></a>';
      } else {
        $lRet.= BR.BR.'<b>&nbsp; '.lan('mig.cpy.jobs.archiv.finish').'</b>';
      }
    } else {
      $lRet.= BR.BR.'<b>&nbsp; '.lan('mig.cpy.jobs.archiv.finish').'</b>';

    }//if (!empty($lJobIds))
    $lRet.= BR.BR.BR.BR.'<a href="index.php?act=mig.copyjobstoarchive"><b>&nbsp; << <u>'.lan('lib.back').'</u> &nbsp;</b></a>';
    $this -> render($lRet);
  }
  //--ENDE: Copy Jobs To Archive

  protected function actManualCopyJobsToArchive() {
    $lVal = $this -> getReq('val');
    $lFrm = new CHtm_Form('mig.smanualcopyjobstoarchive', lan('lib.manual').': '.lan('mig.cpy.jobs.archiv'), false);
    $lFrm -> setDescription(lan('lib.manual').': '.lan('mig.cpy.jobs.archiv.descr'));
    $lFrm -> addDef(fie('typ', lan('job.typ'), 'select', array('rep' => lan('job-rep.menu'), 'art' => lan('job-art.menu'))));
    $lFrm -> addDef(fie('blocksize', 'Blocksize (max. '.BLOCKSIZE_MIG.')'));
    $lMen = new CMig_Menu('manualcopyjobstoarchive');
    $this -> render(CHtm_Wrap::wrap($lMen, $lFrm));
  }
  public function setLimit($aFrom, $aLpp = NULL) {
    $lLimit = intval($aFrom);
    if (NULL !== $aLpp) {
      $lLimit.= ','.intval($aLpp);
    }
    return ' LIMIT '.$lLimit;
  }

  protected function actSManualCopyJobsToArchive() {
    $lVal = $this -> getReq('val');
    $lBlocksize = $lVal['blocksize'];
    if (empty($lBlocksize)) $lBlocksize = 1;
    elseif (BLOCKSIZE_MIG < $lBlocksize) $lBlocksize = BLOCKSIZE_MIG;

    $lSrc = $lVal['typ'];
    if (!empty($lSrc)) {
      $lNext = $this -> getInt('page');
      $lRet = 'Hole die Aufträge mit folg. SQL:'.BR.BR;
      $lJobIds = array();

      // Teil 1:
      $lSql = 'SELECT `JobId` FROM `auftrag` WHERE 1';
      if ('art' == $lSrc){
        $lSql.= ' AND BNr='.esc(CCor_Cfg::get(MAND.'.art.knr'));
      } else {
        $lSql.= ' AND BNr='.esc(CCor_Cfg::get(MAND.'.def.knr'));
      }
      $lSql.= ' AND `JobId`<"A"';
      $lSql.= ' AND `webstatus`=0';
      $lSql.= ' AND `UnterNr`=0'; // keine Autorenkorrekturen o.ä.
      $lSql.= ' AND `Status` IN ("RS","RE")';
      $lSql.= $this -> setLimit($lNext, $lBlocksize);
#      echo '<pre>---cnt.php---';var_dump($lSql,'#############');echo '</pre>';
      $lRet.= $lSql.BR;
      $lRet.= BR.'##########################################################'.BR.BR;
      $lRet.= 'Folgende Aktion wurde max. '.$lBlocksize.'-mal ausgeführt:'.BR.BR;

      $lQryAlink = new CCor_Qrymig($lSql);
      $lQryAlink -> query($lSql);
      foreach ($lQryAlink as $lRow) {
        $lJobIds[] = $lRow['JobId'];
      }

      // Teil 2:
      if (!empty($lJobIds)) {
        define('MIGRATION', TRUE);

        foreach ($lJobIds as $lJid) {
          $lJid = (string) $lJid;
          #        echo '$lJid = '.$lJid.BR;
          $lStp = new CJob_Step($lSrc,$lJid);
          #      echo '<pre>---cnt.php---';var_dump( $lStp,'#############');echo '</pre>';
          if ($lStp -> copyJobToArc()) {
            $this -> msg('Okay! JobId '.$lJid.' copied to archive!', mtUser, mlInfo);
            $lRet.= 'Okay! JobId '.$lJid.' copied to archive!'.BR;
          } else {
            $this -> msg('An Error occurred!', mtUser, mlError);
            $lRet.= 'An Error occurred! '.$lJid.BR;
          }
        }
        $lRet.= BR.'##########################################################'.BR.BR;

        // Teil 3:
        // Setze den richtigen Status = 200 in auftrag
        $lRet .= '<b>&nbsp; Folgende SQLs müssen in Networker ausgeführt werden: </b>'.BR.BR;

        foreach ($lJobIds as $lJid) {//REPLACE brauche ich eigentlich nicht, da der Job ja ins Portalarchiv migriert wurde.
          $lJid = (string) $lJid;
          $lSql = 'REPLACE INTO `jobinfos` SET ';
          $lSql.= '`Val`="rep",`JobId`='.esc($lJid).',`InfoId`="4376";'.BR; // Zusatzfeld für Src
          $lRet.= $lSql;
          $lSql = 'UPDATE `auftrag` SET `webstatus`='.STATUS_ARCHIV.' WHERE `JobId`='.esc($lJid).';'.BR;
          $lRet.= $lSql;
        }
        $lRet.= BR.'##########################################################'.BR.BR;

        $lRet.= BR.BR.'<a href="index.php?act=mig.smanualcopyjobstoarchive&amp;page='.($lNext + $lBlocksize).'&amp;val[blocksize]='.$lBlocksize.'&amp;val[typ]='.$lSrc.'"><b>&nbsp; <u>Copy next max. '.BLOCKSIZE_MIG.' Jobs</u> &nbsp;</b></a>';
      } else {
        $lRet.= BR.'##########################################################'.BR.BR;
        $lRet.= BR.'<b>&nbsp; There are no more Jobs to Copy to Archive!</b>'.BR.BR;
      }//end_if/else(!empty($lJobIds))
    }

    $this -> render($lRet);
  }

  protected function actArt2arc() {
    $this -> CopyJobs2Archiv('art');
  }

  protected function CopyJobs2Archiv_old($aSrc = '') {
    $lSrc = $aSrc;
    if (!empty($lSrc)) {
      $lBlocksize = 300;
      $lNex = $this -> getInt('page');
      $lFie = CCor_Res::extract('alias', 'native', 'fie');
      $lQry = new CApi_Alink_Query_Getjoblist($lSrc);
      $lQry -> addCondition('webstatus','=','-50');
      $lQry -> setLimit($lNex, $lBlocksize);
      $lQry -> setOrder('jobnr');
      foreach ($lFie as $lAli => $lNat) {
        if (empty($lNat)) continue;
        if ('zusatzabsprachen' == $lAli) continue;
        $lQry -> addField($lAli, $lNat);
      }
      $lRes = $lQry -> query();
      echo '<pre>---cnt.php---';var_dump($lRes,'#############');echo '</pre>';
      $lRows = $lQry -> getArray();
      foreach ($lRows as $lJob) {
        $lJob['src'] = $lSrc;
        $lJob['webstatus'] = STATUS_ARCHIV;
        $lSql = 'INSERT INTO al_job_arc_'.MID.' SET ';
        foreach ($lJob as $lKey => $lVal) {
          if (!empty($lVal)) {
            $lSql.= $lKey.'='.esc($lVal).',';
          }
        }
        $lSql = strip($lSql);
        echo '<pre>---cnt.php---';var_dump($lSql,'#############');echo '</pre>';
     #   CCor_Qry::exec($lSql);
      }
      $this -> render('<a href="index.php?act=mig.arc'.$lSrc.'&amp;page='.($lNex + $lBlocksize).'">Next</a>'.$lQry -> getCount());
    }
  }


  protected function actJobrep() {
    $lFrm = new CHtm_Form('mig.sjobrep', lan('mig.rep.to.status'), false); // Repro-Jobs in den Produktivstatus setzen
    $lFrm -> setDescription(lan('mig.rep.to.status.descr'));
    $lFrm -> addDef(fie('webstatus', lan('lib.status').' '.lan('lib.nr')));
    $lFrm -> addDef(fie('typ', lan('job.typ'), 'select', array('rep' => lan('job-rep.menu'), 'art' => lan('job-art.menu'))));

    $lMen = new CMig_Menu('jobrep');
    $this -> render(CHtm_Wrap::wrap($lMen, $lFrm));
  }

  protected function actSjobrep() {
    $lVal = $this -> getReq('val');
    $lWebstatus = (isset($lVal['webstatus'])) ? $lVal['webstatus'] : '';

    if (!empty($lWebstatus)) {
      $lSql = 'SELECT `JobId` FROM `auftrag` WHERE 1';
      if ('art' == $lVal['typ']){
        $lSql.= ' AND BNr='.esc(CCor_Cfg::get(MAND.'.art.knr'));
      } else {
        $lSql.= ' AND BNr='.esc(CCor_Cfg::get(MAND.'.def.knr'));
      }
      $lSql.= ' AND `JobId`<"A"';
      $lSql.= ' AND `webstatus`=0';
      $lSql.= ' AND `UnterNr`=0'; // keine Autorenkorrekturen o.ä.
      $lSql.= ' AND `Status` IN ("N","F")';
      $this -> msg('Networker SQL: '.$lSql, mtUser, mlInfo);
    #  echo '<pre>---cnt.php---';var_dump($lSql,'#############');echo '</pre>';
      #$lCon = new CApi_Sql();
      #$lCon -> setConfig($this -> networkerdbip(), $this -> networkerdbuser(), $this -> networkerdbpass(), $this -> networkerdbname());

      $lRet = '';
      #$lQry = new CCor_Qry($lSql, $lCon);
      $lQry = new CCor_Qrymig($lSql);
      $lQry -> query($lSql);
      foreach ($lQry as $lRow) {
        $lJobId = $lRow['JobId'];
        $lSql = 'REPLACE INTO `jobinfos` SET ';
        $lSql.= '`Val`="rep",`JobId`='.esc($lJobId).',`InfoId`="4376";'.BR; // Zusatzfeld für Src
        $lRet.= $lSql;
        $lSql = 'UPDATE `auftrag` SET `webstatus`='.$lWebstatus.' WHERE `JobId`='.esc($lJobId).';'.BR;
        $lRet.= $lSql;
      }
      $this -> render($lRet);
    } else {
      $this -> redirect('index.php?act=mig.jobrep');
    }
  }

  protected function actArchivstatusrep() {
    $lKnr = CCor_Cfg::get(MAND.'.def.knr');
    $lKnrArr = explode(',',$lKnr);
    foreach ($lKnrArr as $lKey => $lKnr) {
      $lKnrArr[$lKey] = esc($lKnr);
    }
    $lKnrStr = implode(',',$lKnrArr);
    
    $lSql = 'SELECT `JobId` FROM `auftrag` WHERE 1';
    $lSql.= ' AND `BNr` IN ('.$lKnrStr.')';
    $lSql.= ' AND `JobId`<"A"';
    $lSql.= ' AND `webstatus`=0';
    $lSql.= ' AND `UnterNr`=0'; // keine Autorenkorrekturen o.ä.
    $lSql.= ' AND `Status` IN ("RS","RE")';
    $this -> msg('Networker SQL: '.$lSql, mtUser, mlInfo);
    echo '<pre>--mig/cnt.php---Archivstatusrep';var_dump($lSql,'#############');echo '</pre>';

    $lRet = 'Folgende SQLs müssen in Networker ausgeführt werden:'.BR.BR;

    $lQry = new CCor_Qrymig($lSql);
    $lQry -> query($lSql);
    foreach ($lQry as $lRow) {
      $lJobId = $lRow['JobId'];
      $lSql = 'REPLACE INTO `jobinfos` SET ';
      $lSql.= '`Val`="rep",`JobId`='.esc($lJobId).',`InfoId`="4376";'.BR; // Zusatzfeld für Src
      $lRet.= $lSql;
      $lSql = 'UPDATE `auftrag` SET `webstatus`='.STATUS_ARCHIV.' WHERE `JobId`='.esc($lJobId).';'.BR;
      $lRet.= $lSql;
    }
    $this -> render($lRet);
  }

  // liefert die Auswahllisten für alte & neue Zusatzfelder
  protected function actJobSelFie() {
    $lIdOld = $this -> getInt('id_old', 0);
    $lIdNew = $this -> getInt('id_new', 0);

    $lCon = new CApi_Sql();
    $lCon -> setConfig($this -> networkerdbip(), $this -> networkerdbuser(), $this -> networkerdbpass(), $this -> networkerdbname());

    $lWhere = 'WHERE 1 ';
    if ($lIdOld > 0) {
      $lWhere.= 'AND nr='.$lIdOld;
    } else {
      $lWhere.= 'AND name LIKE "%farbe%" ';
    }
    $lSql = 'SELECT nr,name FROM jidefs '.$lWhere;
    $lSql.= ' ORDER BY name ASC';
    $this -> msg('Networker SQL: '.$lSql, mtUser, mlInfo);

    $lQry_Old = new CCor_Qry($lSql, $lCon);

    $lWhere = 'WHERE 1 ';
    if ($lIdNew > 0) {
      $lWhere.= 'AND nr='.$lIdNew;
    } else {
      $lWhere.= 'AND name LIKE "%farbe%" ';
    }
    $lSql = 'SELECT nr,name FROM jidefs '.$lWhere;
    $lSql.= ' ORDER BY name ASC';
    $this -> msg('Networker SQL: '.$lSql, mtUser, mlInfo);

    $lQry_New = new CCor_Qry($lSql, $lCon);

    $lFrm = new CMig_Select($lIdOld, $lIdNew, $lQry_Old, $lQry_New);

    $this -> render($lFrm);
  }

  // liefert die Inserts f�r das neue Zusatzfeld
  protected function actJobAddFie() {
    $lIdOld = $this -> getInt('to_chg', 0);
    $lIdNew = $this -> getInt('id_new', 0);
    $lFrm = '';

    if ($lIdOld > 0 AND $lIdNew > 0) {
      $lSql = 'SELECT a.jobid, j.val ';
      $lSql.= 'FROM auftrag a, jobinfos j ';
      $lSql.= 'WHERE 1 ';
      $lSql.= 'AND a.knr IN (1131,1527) ';
      $lSql.= 'AND a.jobid<"A" ';
      $lSql.= 'AND a.unternr=0 ';
      $lSql.= 'AND j.jobid=a.jobid ';
      $lSql.= 'AND j.infoid='.$lIdOld;
      $lSql.= ';';
      $this -> msg('Networker SQL: '.$lSql, mtUser, mlInfo);

      $lCon = new CApi_Sql();
      $lCon -> setConfig($this -> networkerdbip(), $this -> networkerdbuser(), $this -> networkerdbpass(), $this -> networkerdbname());
      $lQry = new CCor_Qry($lSql, $lCon);

      $lFrm = new CMig_AddField($lIdNew, $lSql, $lQry);
    }
    $this -> render($lFrm);
  }

  protected function actWs() {
    $lArr = '284284
284824
284883
292999
286876';
    $lRet = '';
    $lArr = explode("\n", $lArr);
    foreach ($lArr as $lJnr) {
      $lRet.= '"000'.trim($lJnr).'",';
    }

    $lSql = 'SELECT a.knr,ji.jobid,ji.val FROM jobinfos ji, auftrag a WHERE a.jobid = ji.jobid AND ji.infoid=4376 ';
    $lSql.= 'AND a.jobid IN ('.strip($lRet).')';
    $this -> msg('Networker SQL: '.$lSql, mtUser, mlInfo);

    $lCon = new CApi_Sql();
    $lCon -> setConfig($this -> networkerdbip(), $this -> networkerdbuser(), $this -> networkerdbpass(), $this -> networkerdbname());

    $lRet = '';
    $lQry = new CCor_Qry($lSql, $lCon);
    foreach($lQry as $lRow) {
      #$lRet.= $lRow['knr'].' : '.$lRow['jobid'].' : '.$lRow['val'].BR;
      $lRet.= 'UPDATE auftrag SET webstatus=100 WHERE jobid="'.$lRow['jobid'].'";'.BR;
      $this -> msg('Networker SQL: '.$lSql, mtUser, mlInfo);
    }
    $this -> render($lRet);
  }

  protected function actIncjob() {
    $lFrm = new CHtm_Form('mig.sincjob', lan('job.to.portal'), false); // Job ins Portal holen
    $lFrm -> addDef(fie('jobnr', lan('job.nr')));
    $lFrm -> addDef(fie('typ', lan('job.typ'), 'select', array('art' => lan('job-art.menu'), 'rep' => lan('job-rep.menu'))));

    $lArr = CCor_Res::extract('status', 'name_'.LAN, 'crp', 1);
    unset($lArr[10]);
    unset($lArr[20]);
    unset($lArr[200]);
    $lFrm -> addDef(fie('stat_art', lan('mig.stat_art'), 'select', $lArr));

    $lArr = CCor_Res::extract('status', 'name_'.LAN, 'crp', 2);
    unset($lArr[10]);
    unset($lArr[20]);
    unset($lArr[200]);
    $lFrm -> addDef(fie('stat_rep', lan('mig.stat_rep'), 'select', $lArr));

    $this -> render($lFrm);
  }

  protected function actSincjob() {
    $lVal = $this -> getReq('val');

    $lJnr = (isset($lVal['jobnr'])) ? $lVal['jobnr'] : '';

    $lCon = new CApi_Sql();
    $lCon -> setConfig($this -> networkerdbip(), $this -> networkerdbuser(), $this -> networkerdbpass(), $this -> networkerdbname());

    $lSql = 'SELECT jobid,knr FROM auftrag WHERE jobnr='.esc($lJnr);
    $lSql.= ' AND unternr=""';
    if ('art' == $lVal['typ']){
      $lSql.= ' AND BNr='.CCor_Cfg::get(MAND.'.art.knr');
    } else {
      $lSql.= ' AND BNr='.CCor_Cfg::get(MAND.'.def.knr');
    }
    $this -> msg('Networker SQL: '.$lSql, mtUser, mlInfo);

    $lRet = '';
    $lQry = new CCor_Qry($lSql, $lCon);
    if ($lRow = $lQry -> getDat()) {
      $lKnr = $lRow['knr'];
      $lJobId = $lRow['jobid'];
      if (!in_array($lKnr, array('1131', '1527'))) {
        $this -> msg('Kundennummer '.$lKnr.' ist keine GdB-Kundennummer', mtUser, mlError);
        $this -> redirect('index.php?act=mig.incjob');
      }
      $lTyp = $lVal['typ'];
      if (!in_array($lTyp, array('art', 'rep'))) {
        $this -> msg('Unbekannter Auftragstyp '.$lTyp, mtUser, mlError);
        $this -> redirect('index.php?act=mig.incjob');
      }
      $lSql = 'REPLACE INTO jobinfos SET jobid='.esc($lJobId).',';
      $lSql.= 'val='.esc($lTyp).',';
      $lSql.= 'infoid=4376;'; // src
      $this -> msg('Networker SQL: '.$lSql, mtUser, mlInfo);
      $lQry -> query($lSql);

      $lSta = $lVal['stat_'.$lTyp];
      $this -> dbg('stat_'.$lTyp.'='.$lSta);
      if (!empty($lSta)) {
        $lSql = 'UPDATE auftrag SET webstatus='.esc($lSta).' WHERE jobid='.esc($lJobId);
        $lQry -> query($lSql);
      }
      $this -> redirect('index.php?act=mig.incjob');
    } else {
      $this -> msg('Kundennummer '.$lKnr.' ist keine GdB-Kundennummer', mtUser, mlError);
      $this -> redirect('index.php?act=mig.incjob');
    }
  }

  protected function actDiff() {
    $lFrm = new CHtm_Form('mig.sdiff', lan('job.diff'), FALSE); // Diff Jobs
    $lFrm -> addDef(fie('src', 'Source Job'));
    $lFrm -> addDef(fie('dst', 'Destination Job'));
    $this -> render($lFrm);
  }

  protected function actSdiff() {
    $lVal = $this -> getReq('val');
    $lSrc = (isset($lVal['src'])) ? $lVal['src'] : '';
    $lDst = (isset($lVal['dst'])) ? $lVal['dst'] : '';

    $lFrm = new CHtm_Form('mig.sdiff', lan('job.diff'), FALSE); // Diff Jobs
    $lFrm -> addDef(fie('src', 'Source Job'));
    $lFrm -> addDef(fie('dst', 'Destination Job'));
    $lFrm -> setVal('src', $lSrc);
    $lFrm -> setVal('dst', $lDst);

    if (empty($lSrc) or empty($lDst)) {
      $this -> msg('Please enter both Job IDs!', mtUser, mlWarn);
      $this -> render($lFrm);
    } else {
      $lDif = new CMig_Diff($lSrc, $lDst);
      $this -> render(CHtm_Wrap::wrap($lFrm, $lDif));
    }
  }

  protected function actCnfdiff() {
    $lSrc = $this -> getReq('src');
    $lJobId = $this -> getReq('jobid');
    $lOldJid = $this -> getReq('srcjid');

    $lVal = $this -> getReq('val');
    if (empty($lVal)) {
      $this -> redirect('index.php?act=mig.sdiff&val[src]='.$lOldJid.'&val[dst]='.$lJobId);
    }
    $lOld = $this -> getReq('old');

    $lNewOld = array();
    $lNewVal = array();
    foreach ($lOld as $lKey => $lValue) {
      if (isset($lVal[$lKey])) {
        $lNewOld[$lKey] = $lValue;
        $lNewVal[$lKey] = $lVal[$lKey];
      }
    }
    $lAll = $this -> mReq -> getAll();
    $lAll['old'] = $lNewOld;
    $lAll['val'] = $lNewVal;
    $lReq = new CCor_Req();
    $lReq -> assign($lAll);

    $lFac = new CJob_Fac($lSrc);
    $lMod = $lFac -> getMod($lJobId);
    #$lMod -> setTestMode();
    $lMod -> getPost($lReq);
    $lMod -> update();

    $this -> redirect('index.php?act=mig.sdiff&val[src]='.$lOldJid.'&val[dst]='.$lJobId);
  }

}