<?php
class CInc_Svc_Event extends CSvc_Base {

  protected $mConditionId;
  protected $mEventId;
  protected $mNumberOfJobs;

  protected function getStatusId($aSrc, $aWebStatus) {
    $lRet = '';
    $lArcStatus = '';

    $lSql = "SELECT b.id";
    $lSql.= " FROM al_crp_master AS a, al_crp_status AS b, al_crp_step AS c";
    $lSql.= " WHERE a.mand=".MID;
    $lSql.= " AND b.mand=".MID;
    $lSql.= " AND c.mand=".MID;
    $lSql.= " AND a.id=b.crp_id";
    $lSql.= " AND b.crp_id=c.crp_id";
    $lSql.= " AND a.code='".$aSrc."'";
    $lSql.= " AND b.status=".$aWebStatus."";
    $lSql.= " AND c.trans='job2arc'";
    $lSql.= " LIMIT 0,1;";

    $lArcStatus = CCor_Qry::getInt($lSql);
    if (is_numeric($lArcStatus)) {
      $lRet = $lArcStatus;
    } else {
      $this -> dbg('CInc_Svc_Event could not find a valid archive status!', mlWarn);
    }

    return $lRet;
  }

  protected function getArchiveStatusId($aSrc) {
    $lRet = '';
    $lArcStatus = '';

    $lSql = "SELECT b.id";
    $lSql.= " FROM al_crp_master AS a, al_crp_status AS b, al_crp_step AS c";
    $lSql.= " WHERE a.mand=".MID;
    $lSql.= " AND b.mand=".MID;
    $lSql.= " AND c.mand=".MID;
    $lSql.= " AND a.id=b.crp_id";
    $lSql.= " AND b.crp_id=c.crp_id";
    $lSql.= " AND a.code='".$aSrc."'";
    $lSql.= " AND b.id=c.to_id";
    $lSql.= " AND c.trans='job2arc'";
    $lSql.= " LIMIT 0,1;";

    $lArcStatus = CCor_Qry::getInt($lSql);
    if (is_numeric($lArcStatus)) {
      $lRet = $lArcStatus;
    } else {
      $this -> dbg('CInc_Svc_Event could not find a valid archive status!', mlWarn);
    }

    return $lRet;
  }

  protected function getStepId($aFromId, $aToId) {
    $lRet = '';
    $lArcStatus = '';

    $lSql = "SELECT c.id";
    $lSql.= " FROM al_crp_master AS a, al_crp_status AS b, al_crp_step AS c";
    $lSql.= " WHERE a.mand=".MID;
    $lSql.= " AND b.mand=".MID;
    $lSql.= " AND c.mand=".MID;
    $lSql.= " AND a.id=b.crp_id";
    $lSql.= " AND b.crp_id=c.crp_id";
    $lSql.= " AND b.id=c.to_id";
    $lSql.= " AND c.from_id=".$aFromId;
    $lSql.= " AND c.to_id=".$aToId;
    $lSql.= " AND c.trans='job2arc'";
    $lSql.= " LIMIT 0,1;";

    $lArcStatus = CCor_Qry::getInt($lSql);
    if (is_numeric($lArcStatus)) {
      $lRet = $lArcStatus;
    } else {
      $this -> dbg('CInc_Svc_Event could not find a valid archive status!', mlWarn);
    }

    return $lRet;
  }

  protected function doExecute() {
    $lConditionId = $this -> getPar('conditionid');
    $lEventId = $this -> getPar('eventid');
    $lNumberOfJobs = $this -> getPar('numberofjobs');

    $lConditions = CCor_Res::extract('id', 'id', 'cond');
    $lEvents = CCor_Res::extract('id', 'id', 'eve');

    $this -> mConditionId = $lConditions[$lConditionId] ? intval($lConditionId) : NULL;
    $this -> mEventId = $lEvents[$lEventId] ? intval($lEventId) : NULL;
    $this -> mNumberOfJobs = is_numeric($lNumberOfJobs) ? intval($lNumberOfJobs) : 10;

    if (!is_null($this -> mConditionId)) {
      $lAppConditionRegistry = new CApp_Condition_Registry();
      $lAppConditionRegistryObject = $lAppConditionRegistry -> loadFromDb($this -> mConditionId);
      $lConditionsSQL = $lAppConditionRegistryObject -> paramToSQL();
      $lConditionsString = $lAppConditionRegistryObject -> paramToString();

      if (!$lConditionsSQL) {
        $lConditionsSQL = 1;
      }

      $lSQL = 'SELECT src, jobid, webstatus FROM al_job_shadow_'.MID.' WHERE src<>\'\' AND jobid<>\'\' AND '.$lConditionsSQL.' ORDER BY jobid DESC LIMIT '.$this -> mNumberOfJobs.';';
      $lQry = new CCor_Qry($lSQL);
      foreach ($lQry as $lRow) {
        $lSrc = $lRow['src'];
        $lJobId = $lRow['jobid'];
        $lWebstatus = $lRow['webstatus'];

        if ($lSrc && $lJobId) {
          $lJobFac = new CJob_Fac($lSrc, $lJobId);
          $lJobDat = $lJobFac -> getDat();

          // as job information may differ between al_job_shadow_XXX and PDB/Networker/MOP, we need a simple check whether the CJob_Fac/CJob_Dat could get any job information at all
          if ($lJobDat['jobid']) {
            $lFromStatusId = $this -> getStatusId($lSrc, $lWebstatus);
            $lToStatusId = $this -> getArchiveStatusId($lSrc);
            $lStepId = $this -> getStepId($lFromStatusId, $lToStatusId);

            $lClass = 'CJob_'.ucfirst($lSrc).'_Step';
            $lStepClass = new $lClass($lJobId);
            $lRet = $lStepClass -> doStep($lStepId);

            $lJobHistory = new CJob_His($lSrc, $lJobId);
            $lJobHistory -> setUser(CCor_Cfg::get('svc.uid'));
            $lJobHistoryResult = $lJobHistory -> add(htStatus, lan('svc.event.done.sub'), lan('svc.event.done.msg'), '', $lStepId, $lFromStatusId, $lToStatusId);

            if (!is_null($this -> mEventId)) {
              $lEvent = new CJob_Event($this -> mEventId, $lJobDat);
              $lEvent -> execute();
            }
          } else {
            $lMsg = lan('svc.event.fail.msg')."/r/n";
            $lMsg.= lan('lib.src').': '.$lSrc."/r/n";
            $lMsg.= lan('lib.jobid').': '.$lJobId."/r/n";
            $lMsg.= lan('lib.webstatus').': '.$lWebstatus."/r/n";
            $lMsg.= lan('lib.condition').': '.$lConditionsString;

            $lSystemLog = new CCor_Log();
            $lSystemLog -> log($lMsg, mlError, mtNone);
          }
        }
      }
    } else {
      $lCriticalPaths = CCor_Res::extract('code', 'id', 'crpmaster');
      foreach ($lCriticalPaths as $lKey => $lValue) {
        $lConditions = CCor_Res::extract('code', 'eve_archive_condition', 'crpmaster');
        $lEvents = CCor_Res::extract('code', 'eve_archive', 'crpmaster');
        $lNumberOfJobs = CCor_Res::extract('code', 'eve_archive_numberofjobs', 'crpmaster');

        $this -> mConditionId = $lConditions[$lKey] ? $lConditions[$lKey] : NULL;
        $this -> mEventId = $lEvents[$lKey] ? $lEvents[$lKey] : NULL;
        $this -> mNumberOfJobs = $lNumberOfJobs[$lKey] ? $lNumberOfJobs[$lKey] : 10;

        if (!is_null($this -> mConditionId)) {
          $lAppConditionRegistry = new CApp_Condition_Registry();
          $lAppConditionRegistryObject = $lAppConditionRegistry -> loadFromDb($this -> mConditionId);
          $lConditionsSQL = $lAppConditionRegistryObject -> paramToSQL();
          $lConditionsString = $lAppConditionRegistryObject -> paramToString();

          if (!$lConditionsSQL) {
            $lConditionsSQL = 1;
          }

          $lSQL = 'SELECT src, jobid, webstatus FROM al_job_shadow_'.MID.' WHERE src<>\'\' AND jobid<>\'\' AND '.$lConditionsSQL.' ORDER BY jobid DESC LIMIT '.$this -> mNumberOfJobs.';';
          $lQry = new CCor_Qry($lSQL);
          foreach ($lQry as $lRow) {
            $lSrc = $lRow['src'];
            $lJobId = $lRow['jobid'];
            $lWebstatus = $lRow['webstatus'];

            if ($lSrc && $lJobId) {
              $lJobFac = new CJob_Fac($lSrc, $lJobId);
              $lJobDat = $lJobFac -> getDat();

              // as job information may differ between al_job_shadow_XXX and PDB/Networker/MOP, we need a simple check whether the CJob_Fac/CJob_Dat could get any job information at all
              if ($lJobDat['jobid']) {
                $lFromStatusId = $this -> getStatusId($lSrc, $lWebstatus);
                $lToStatusId = $this -> getArchiveStatusId($lSrc);
                $lStepId = $this -> getStepId($lFromStatusId, $lToStatusId);

                $lClass = 'CJob_'.ucfirst($lSrc).'_Step';
                $lStepClass = new $lClass($lJobId);
                $lRet = $lStepClass -> doStep($lStepId);

                $lJobHistory = new CJob_His($lSrc, $lJobId);
                $lJobHistory -> setUser(CCor_Cfg::get('svc.uid'));
                $lJobHistoryResult = $lJobHistory -> add(htStatus, lan('svc.event.done.sub'), lan('svc.event.done.msg'), '', $lStepId, $lFromStatusId, $lToStatusId);

                if (!is_null($this -> mEventId)) {
                  $lEvent = new CJob_Event($this -> mEventId, $lJobDat);
                  $lEvent -> execute();
                }
              } else {
                $lMsg = lan('svc.event.fail.msg')."/r/n";
                $lMsg.= lan('lib.src').': '.$lSrc."/r/n";
                $lMsg.= lan('lib.jobid').': '.$lJobId."/r/n";
                $lMsg.= lan('lib.webstatus').': '.$lWebstatus."/r/n";
                $lMsg.= lan('lib.condition').': '.$lConditionsString;

                $lSystemLog = new CCor_Log();
                $lSystemLog -> log($lMsg, mlError, mtNone);
              }
            }
          }
        }
      }
    }

    return TRUE;
  }
}