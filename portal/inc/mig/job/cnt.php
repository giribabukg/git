<?php
class CInc_Mig_Job_Cnt extends CJob_Cnt {

  protected $mSrc = 'com';

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    
    // Ask If user has right for this page
    $lpn = 'mig';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdRead);
    }
  }

  protected function actStd() {
    $lVie = new CMig_Job_List();
    $this -> render($lVie);
  }

  protected function actMig() {
    ini_set(max_execution_time, 3600);

    $lJobIDs = $this -> getVal('ids');
    $lJobIDsToArray = explode(",", $lJobIDs);
    $lCancelMigration = false;

    $lDateTime = date("YmdHis", time());
    $lUsr = CCor_Usr::getInstance();
    $lFileHandle = fopen ("migration_".$lDateTime."_".$lUsr -> getAuthId().".txt", "w+");

    # ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
    # the following part is NONcritical!
    # aborts don't do any harm!
    # ##### ##### ##### ##### ##### ##### ##### ##### ##### #####

    # ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
    # check whether in job history job type (src) is set
    # actually this is not essential: job history data sets without job type would be ignored otherwise
    # ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
      foreach ($lJobIDsToArray as $lKey => $lValue) {
        $lErrorMessage = '';

        $lDeleteResult = CCor_Qry::exec("DELETE FROM al_mig WHERE oldid=".$lValue);
        if ($lDeleteResult == 1) {
          $lDeleteResult = 'OK: ';
        } else {
          $lDeleteResult = 'ERROR: ';
        }
        fwrite($lFileHandle, $lDeleteResult."DELETE FROM al_mig WHERE oldid=".$lValue."\r\n");

        $lSelectResult = CCor_Qry::getStr("SELECT src FROM al_job_his WHERE src_id=".$lValue." AND mand=".intval(MID));
        fwrite($lFileHandle, $lSelectResult.": SELECT src FROM al_job_his WHERE src_id=".$lValue." AND mand=".intval(MID)."\r\n");

        if ($lSelectResult === '') {
          $lCancelMigration = true;
          $lErrorMessage.= 'No job type (src) in job history (al_job_his) set!';
        }

        $lInsertResult = CCor_Qry::exec("INSERT INTO al_mig (oldid, newid, errors) VALUES (".$lValue.", NULL, '".$lErrorMessage."')");
        if ($lInsertResult == 1) {
          $lInsertResult = 'OK: ';
        } else {
          $lInsertResult = 'ERROR: ';
        }
        fwrite($lFileHandle, $lInsertResult."INSERT INTO al_mig (oldid, newid, errors) VALUES (".$lValue.", NULL, '".$lErrorMessage."')\r\n\r\n");
      }

      if ($lCancelMigration == true) {
        $this -> redirect();
      }

    # ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
    # check whether all essential job fields are set
    # essential job fields are: jobid, src, webstatus, net_knr
    # ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
      foreach ($lJobIDsToArray as $lKey => $lValue) {
        $lErrorMessage = '';

        $lDeleteResult = CCor_Qry::exec("DELETE FROM al_mig WHERE oldid=".$lValue);
        if ($lDeleteResult == 1) {
          $lDeleteResult = 'OK: ';
        } else {
          $lDeleteResult = 'ERROR: ';
        }
        fwrite($lFileHandle, $lDeleteResult."DELETE FROM al_mig WHERE oldid=".$lValue."\r\n");

        $lSelectResult = CCor_Qry::getArrImp("SELECT src, webstatus, net_knr FROM al_job_pdb_".intval(MID)." WHERE jobid=".$lValue);
        fwrite($lFileHandle, $lSelectResult.": SELECT src, webstatus, net_knr FROM al_job_pdb_".intval(MID)." WHERE jobid=".$lValue."\r\n");

        $lResultToArray = explode(",", $lSelectResult);
        $lResultToArray[0] = (string)$lResultToArray[0];
        $lResultToArray[1] = (int)$lResultToArray[1];
        $lResultToArray[2] = (string)$lResultToArray[2];

        if (!$lResultToArray[0]) {
          $lCancelMigration = true;
          $lErrorMessage.= 'No job type (src) set! ';
        }
        if (!$lResultToArray[1]) {
          $lCancelMigration = true;
          $lErrorMessage.= 'No webstatus (webstatus) set! ';
        }
        if (!$lResultToArray[2]) {
          $lCancelMigration = true;
          $lErrorMessage.= 'No client number (net_knr) set! ';
        }

        $lInsertResult = CCor_Qry::exec("INSERT INTO al_mig (oldid, newid, errors) VALUES (".$lValue.", NULL, '".$lErrorMessage."')");
        if ($lInsertResult == 1) {
          $lInsertResult = 'OK: ';
        } else {
          $lInsertResult = 'ERROR: ';
        }
        fwrite($lFileHandle, $lInsertResult."INSERT INTO al_mig (oldid, newid, errors) VALUES (".$lValue.", NULL, '".$lErrorMessage."')\r\n\r\n");
      }

      if ($lCancelMigration == true) {
        $this -> redirect();
      }

    # ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
    # check whether all mandatory job fields are set
    # have a look at the job fields for mandatory flags
    # ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
      $lMandatoryFields = new CCor_TblIte('al_fie');
      $lMandatoryFields -> addCondition('mand', '=', MID);
      $lMandatoryFields -> addCondition('flags', '>=', 512);
      $lMandatoryFieldsToArray = $lMandatoryFields -> getArray();
      foreach ($lJobIDsToArray as $lKeyOuter => $lValueOuter) {
        $lErrorMessage = '';

        $lDeleteResult = CCor_Qry::exec("DELETE FROM al_mig WHERE oldid=".$lValueOuter);
        if ($lDeleteResult == 1) {
          $lDeleteResult = 'OK: ';
        } else {
          $lDeleteResult = 'ERROR: ';
        }
        fwrite($lFileHandle, $lDeleteResult."DELETE FROM al_mig WHERE oldid=".$lValueOuter."\r\n");

        foreach ($lMandatoryFieldsToArray as $lKeyInner => $lValueInner) {
          $lSelectResult = CCor_Qry::getArrImp("SELECT ".$lValueInner['alias']." FROM al_job_pdb_".intval(MID)." WHERE jobid=".$lValueOuter);
          fwrite($lFileHandle, $lSelectResult.": SELECT ".$lValueInner['alias']." FROM al_job_pdb_".intval(MID)." WHERE jobid=".$lValueOuter."\r\n");

          if (!$lSelectResult) {
            $lErrorMessage.= 'Error: Mandatory field >>'.$lValueInner['alias'].'<< not set! ';
            $lCancelMigration = true;
          }
        }

        $lInsertResult = CCor_Qry::exec("INSERT INTO al_mig (oldid, newid, errors) VALUES (".$lValueOuter.", NULL, '".$lErrorMessage."')");
        if ($lInsertResult == 1) {
          $lInsertResult = 'OK: ';
        } else {
          $lInsertResult = 'ERROR: ';
        }
        fwrite($lFileHandle, $lInsertResult."INSERT INTO al_mig (oldid, newid, errors) VALUES (".$lValueOuter.", NULL, '".$lErrorMessage."')\r\n\r\n");
      }

      if ($lCancelMigration == true) {
        $this -> redirect();
      }

    # ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
    # the following part is CRITICAL!
    # do not abort by hand during migration!
    # ##### ##### ##### ##### ##### ##### ##### ##### ##### #####

    # ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
    # copy jobs from project portal database to Networker database
    # by inserting with Alink
    # ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
    foreach ($lJobIDsToArray as $lKeyOuter => $lValueOuter) {
      $lObj = 'CJob_'.ucfirst($this -> mSrc).'_Dat';
      $lJob = new $lObj();
      $lJob -> load($lValueOuter);
      $lJobArr = $lJob -> toArray();

      $lSelectTransitionToProduction = CCor_Qry::getInt("SELECT b.status FROM al_crp_master a, al_crp_status b, al_crp_step c WHERE a.code='".$this -> mSrc."' AND a.mand=".MID." AND a.id=b.crp_id AND a.mand=b.mand AND a.mand=c.mand AND c.trans='anf2job' AND c.to_id=b.id");
      if ($lJobArr['webstatus'] < $lSelectTransitionToProduction) {
        $lQry = new CApi_Alink_Query_Insertjob(true);
      } else {
        $lQry = new CApi_Alink_Query_Insertjob(false);
      }
      foreach ($lJobArr as $lKeyInner => $lValueInner) {
        if (strtolower($lKeyInner) != 'jobid') {
          $lQry -> addVal($lKeyInner, $lValueInner);
        }
      }
      $lRet = $lQry -> query();

      if ($lRet) {
        $lNewJobID = (string)$lRet -> getVal('jobid');

        $lMod = new CJob_His($this -> mSrc, $lNewJobID);
        $lMod -> add(htStatus, lan('job-'.$this -> mSrc.'.menu').' job migrated', '', '', '', '', 10);

        $lArr = array();
        $lArr['fti_1'] = $lNow;
        $lArr['lti_1'] = $lNow;

        CJob_Utl_Shadow::reflectInsert($this -> mSrc, $lNewJobID, $this -> mVal, $lArr);

        $lUpdateResult = CCor_Qry::exec("UPDATE al_mig SET newid='".$lNewJobID."' WHERE oldid='".$lValueOuter."'");
        if ($lUpdateResult == 1) {
          $lUpdateResult = 'OK: ';
        } else {
          $lUpdateResult = 'ERROR: ';
        }
        fwrite($lFileHandle, $lUpdateResult."UPDATE al_mig SET newid='".$lNewJobID."' WHERE oldid='".$lValueOuter."'\r\n\r\n");

        # com jobs that have been successfully migrated are renamed to oldcom
        # so these can be re-migrated afterwards easily
        $lUpdateResult = CCor_Qry::exec("UPDATE al_job_pdb_".intval(MID)." SET src='oldcom' WHERE src='".$this -> mSrc."' AND jobid=".$lValueOuter);
        if ($lUpdateResult == 1) {
          $lUpdateResult = 'OK: ';
        } else {
          $lUpdateResult = 'ERROR: ';
        }
        fwrite($lFileHandle, $lUpdateResult."UPDATE al_job_pdb_".intval(MID)." SET src='oldcom' WHERE src='".$this -> mSrc."' AND jobid=".$lValueOuter."\r\n\r\n");
      } else {
        $lNewJobID = NULL;

        $lUpdateResult = CCor_Qry::exec("UPDATE al_mig SET errors='Job could not be migrated!' WHERE oldid=".$lValueOuter);
        if ($lUpdateResult == 1) {
          $lUpdateResult = 'OK: ';
        } else {
          $lUpdateResult = 'ERROR: ';
        }
        fwrite($lFileHandle, $lUpdateResult."UPDATE al_mig SET errors='Job could not be migrated!' WHERE oldid=".$lValueOuter."\r\n\r\n");
      }
    }

    # ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
    # switch ID from old to new in database
    # ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
    foreach ($lJobIDsToArray as $lKey => $lValue) {
      $lSelectResult = CCor_Qry::getStr("SELECT newid FROM al_mig WHERE oldid=".$lValue);

      # al_job_apl_loop
      if (substr($lSelectResult, 0) == 'A') {
        $lUpdateResult = CCor_Qry::exec("UPDATE al_job_apl_loop SET jobid='".$lSelectResult."' WHERE jobid=".$lValue." AND mand=".intval(MID)." AND src='".$this -> mSrc."'");
      } else {
        $lUpdateResult = CCor_Qry::exec("UPDATE al_job_apl_loop SET jobid='".$lSelectResult."' WHERE jobid=".$lValue." AND mand=".intval(MID)." AND src='".$this -> mSrc."'");
      }
      if ($lUpdateResult == 1) {
        $lUpdateResult = 'OK: ';
      } else {
        $lUpdateResult = 'ERROR: ';
      }
      if (substr($lSelectResult, 0) == 'A') {
        fwrite($lFileHandle, $lUpdateResult."UPDATE al_job_apl_loop SET jobid='".$lSelectResult."' WHERE jobid=".$lValue." AND mand=".intval(MID)." AND src='".$this -> mSrc."'\r\n\r\n");
      } else {
        fwrite($lFileHandle, $lUpdateResult."UPDATE al_job_apl_loop SET jobid='".$lSelectResult."' WHERE jobid=".$lValue." AND mand=".intval(MID)." AND src='".$this -> mSrc."'\r\n\r\n");
      }

      # al_job_files
      if (substr($lSelectResult, 0) == 'A') {
        $lUpdateResult = CCor_Qry::exec("UPDATE al_job_files SET jobid='".$lSelectResult."' WHERE jobid=".$lValue." AND src='".$this -> mSrc."'");
      } else {
        $lUpdateResult = CCor_Qry::exec("UPDATE al_job_files SET jobid='".$lSelectResult."' WHERE jobid=".$lValue." AND src='".$this -> mSrc."'");
      }
      if ($lUpdateResult == 1) {
        $lUpdateResult = 'OK: ';
      } else {
        $lUpdateResult = 'ERROR: ';
      }
      if (substr($lSelectResult, 0) == 'A') {
        fwrite($lFileHandle, $lUpdateResult."UPDATE al_job_files SET jobid='".$lSelectResult."' WHERE jobid=".$lValue." AND src='".$this -> mSrc."'\r\n\r\n");
      } else {
        fwrite($lFileHandle, $lUpdateResult."UPDATE al_job_files SET jobid='".$lSelectResult."' WHERE jobid=".$lValue." AND src='".$this -> mSrc."'\r\n\r\n");
      }

      # al_job_his
      if (substr($lSelectResult, 0) == 'A') {
        $lUpdateResult = CCor_Qry::exec("UPDATE al_job_his SET src_id='".$lSelectResult."' WHERE src_id=".$lValue." AND src='".$this -> mSrc."' AND mand=".intval(MID));
      } else {
        $lUpdateResult = CCor_Qry::exec("UPDATE al_job_his SET src_id='".$lSelectResult."' WHERE src_id=".$lValue." AND src='".$this -> mSrc."' AND mand=".intval(MID));
      }
      if ($lUpdateResult == 1) {
        $lUpdateResult = 'OK: ';
      } else {
        $lUpdateResult = 'ERROR: ';
      }
      if (substr($lSelectResult, 0) == 'A') {
        fwrite($lFileHandle, $lUpdateResult."UPDATE al_job_his SET src_id='".$lSelectResult."' WHERE src_id=".$lValue." AND src='".$this -> mSrc."' AND  mand=".intval(MID)." \r\n\r\n");
      } else {
        fwrite($lFileHandle, $lUpdateResult."UPDATE al_job_his SET src_id='".$lSelectResult."' WHERE src_id=".$lValue." AND src='".$this -> mSrc."' AND mand=".intval(MID)." \r\n\r\n");
      }

      # al_job_shadow_[...]
      if (substr($lSelectResult, 0) == 'A') {
        $lUpdateResult = CCor_Qry::exec("UPDATE al_job_shadow_".intval(MID)." SET jobid='".$lSelectResult."' WHERE jobid=".$lValue." AND src='".$this -> mSrc."'");
      } else {
        $lUpdateResult = CCor_Qry::exec("UPDATE al_job_shadow_".intval(MID)." SET jobid='".$lSelectResult."' WHERE jobid=".$lValue." AND src='".$this -> mSrc."'");
      }
      if ($lUpdateResult == 1) {
        $lUpdateResult = 'OK: ';
      } else {
        $lUpdateResult = 'ERROR: ';
      }
      if (substr($lSelectResult, 0) == 'A') {
        fwrite($lFileHandle, $lUpdateResult."UPDATE al_job_shadow_".intval(MID)." SET jobid='".$lSelectResult."' WHERE jobid=".$lValue." AND src='".$this -> mSrc."'\r\n\r\n");
      } else {
        fwrite($lFileHandle, $lUpdateResult."UPDATE al_job_shadow_".intval(MID)." SET jobid='".$lSelectResult."' WHERE jobid=".$lValue." AND src='".$this -> mSrc."'\r\n\r\n");
      }

      # al_job_sub_[...]
      if (substr($lSelectResult, 0) == 'A') {
        $lUpdateResult = CCor_Qry::exec("UPDATE al_job_sub_".intval(MID)." SET jobid_".$this -> mSrc."='".$lSelectResult."' WHERE jobid_".$this -> mSrc."=".$lValue);
      } else {
        $lUpdateResult = CCor_Qry::exec("UPDATE al_job_sub_".intval(MID)." SET jobid_".$this -> mSrc."='".$lSelectResult."' WHERE jobid_".$this -> mSrc."=".$lValue);
      }
      if ($lUpdateResult == 1) {
        $lUpdateResult = 'OK: ';
      } else {
        $lUpdateResult = 'ERROR: ';
      }
      if (substr($lSelectResult, 0) == 'A') {
        fwrite($lFileHandle, $lUpdateResult."UPDATE al_job_sub_".intval(MID)." SET jobid_".$this -> mSrc."='".$lSelectResult."' WHERE jobid_".$this -> mSrc."=".$lValue."\r\n\r\n");
      } else {
        fwrite($lFileHandle, $lUpdateResult."UPDATE al_job_sub_".intval(MID)." SET jobid_".$this -> mSrc."='".$lSelectResult."' WHERE jobid_".$this -> mSrc."=".$lValue."\r\n\r\n");
      }

      # al_usr_bookmark
      if (substr($lSelectResult, 0) == 'A') {
        $lUpdateResult = CCor_Qry::exec("UPDATE al_usr_bookmark SET jobid='".$lSelectResult."' WHERE jobid=".$lValue." AND src='".$this -> mSrc."'");
      } else {
        $lUpdateResult = CCor_Qry::exec("UPDATE al_usr_bookmark SET jobid='".$lSelectResult."' WHERE jobid=".$lValue." AND src='".$this -> mSrc."'");
      }
      if ($lUpdateResult == 1) {
        $lUpdateResult = 'OK: ';
      } else {
        $lUpdateResult = 'ERROR: ';
      }
      if (substr($lSelectResult, 0) == 'A') {
        fwrite($lFileHandle, $lUpdateResult."UPDATE al_usr_bookmark SET jobid='".$lSelectResult."' WHERE jobid=".$lValue." AND src='".$this -> mSrc."'\r\n\r\n");
      } else {
        fwrite($lFileHandle, $lUpdateResult."UPDATE al_usr_bookmark SET jobid='".$lSelectResult."' WHERE jobid=".$lValue." AND src='".$this -> mSrc."'\r\n\r\n");
      }

      # al_usr_recent
      if (substr($lSelectResult, 0) == 'A') {
        $lUpdateResult = CCor_Qry::exec("UPDATE al_usr_recent SET jobid='".$lSelectResult."' WHERE jobid=".$lValue." AND src='".$this -> mSrc."'");
      } else {
        $lUpdateResult = CCor_Qry::exec("UPDATE al_usr_recent SET jobid='".$lSelectResult."' WHERE jobid=".$lValue." AND src='".$this -> mSrc."'");
      }
      if ($lUpdateResult == 1) {
        $lUpdateResult = 'OK: ';
      } else {
        $lUpdateResult = 'ERROR: ';
      }
      if (substr($lSelectResult, 0) == 'A') {
        fwrite($lFileHandle, $lUpdateResult."UPDATE al_usr_recent SET jobid='".$lSelectResult."' WHERE jobid=".$lValue." AND src='".$this -> mSrc."'\r\n\r\n");
      } else {
        fwrite($lFileHandle, $lUpdateResult."UPDATE al_usr_recent SET jobid='".$lSelectResult."' WHERE jobid=".$lValue." AND src='".$this -> mSrc."'\r\n\r\n");
      }
    }

    fclose($lFileHandle);

    # ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
    # rename directories from old ID to new ID
    # ##### ##### ##### ##### ##### ##### ##### ##### ##### #####
    $lFilesDirectory = getcwd()."\mand\mand_".MID."\\files\job\\".$this -> mSrc."\\";
    foreach ($lJobIDsToArray as $lKey => $lValue) {
      $lSelectResult = CCor_Qry::getInt("SELECT newid FROM al_mig WHERE oldid=".$lValue);

      if (is_dir($lFilesDirectory.$lValue)) {
        rename($lFilesDirectory.$lValue, $lFilesDirectory.$lSelectResult);
      } elseif (is_dir($lFilesDirectory.'000'.$lValue)) {
        rename($lFilesDirectory.'000'.$lValue, $lFilesDirectory.$lSelectResult);
      }
    }

    $this -> redirect();
  }

  protected function actDel() {
    $lJobId = $this -> getInt('jobid');
    $lId  = $this -> getInt('id');

    $lSql = 'DELETE FROM al_job_'.$this -> mSrc.'_'.MID.' WHERE id="'.$lId.'" ';
    if (CCor_Qry::exec($lSql)) {
      $lMod = new CJob_His($this -> mSrc, $this -> mInsertId);
      $lMod -> add(htStatus, lan($this -> mSrc.'.deleted'), '', '', '','',0);
    }
    $this -> redirect('index.php?act=job-pro-sub&jobid='.$lJobId);
  }

}