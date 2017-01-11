<?php
class CInc_Svc_Apl extends CSvc_Base {

  protected $mJobWriterDefault;
  protected $mJobFields;
//   protected $mBackupUserTree; // backup user tree to reproduce who is backing up who

  /**
   * Has job flags, checks whether job is on hold or cancelled
   *
   * @return boolean
   */
  protected function hasFlags($aJobID, $aSrc, $aClientID) {
    if ($this -> mJobWriterDefault == 'portal') {
      $lSql = 'SELECT flags FROM al_job_'.$aSrc.'_'.$aClientID.' WHERE jobid='.esc($aJobID).';';
    } else {
      $lSql = 'SELECT flags FROM al_job_shadow_'.$aClientID.' WHERE jobid='.esc($aJobID).';';
    }
    $lJobFlag = CCor_Qry::getInt($lSql);

    if (bitset($lJobFlag, jfOnhold) || bitset($lJobFlag, jfCancelled)) {
      return TRUE;
    } else {
      return FALSE;
    }
  }

  /**
   * Get attendant user
   *
   * @return array
   */
  protected function getAttendantUser($aUserID, $aBackUpUserID, $aActiveUserID, $aClientID, $aLoopID) {
    if (($aUserID == $aBackUpUserID) && ($aActiveUserID == 0)) {
      $lUserID = $aUserID;
    } elseif (($aUserID != $aBackUpUserID) && ($aBackUpUserID == $aActiveUserID)) {
      $lUserID = $aBackUpUserID;
    } else {
      $lSysLog = new CCor_Log();
      $lSysLog -> log('Combination of user ID #'.$aUserID.', backup user ID #'.$aBackUpUserID.' and active user ID #'.$aActiveUserID.' in loop ID #'.$aLoopID.' seems to break the al_job_apl_states rule!', mlError, mtNone);

      return NULL;
    }

    $lUser = new CCor_Anyusr($lUserID);
    $lResult = array(
      'id' => $lUser -> getVal('id'),
      'anrede' => $lUser -> getVal('anrede'),
      'firstname' => $lUser -> getVal('firstname'),
      'lastname' => $lUser -> getVal('lastname'),
      'backup' => $lUser -> getVal('backup'),
      'email' => $lUser -> getVal('email')
    );

    if (empty($lResult['id'])) {
      $lSysLog = new CCor_Log();
      $lSysLog -> log('User ID #'.$aUserID.' does not exist (anymore)!', mlError, mtNone);

      return NULL;
    }

    if (empty($lResult['email']) || !filter_var($lResult['email'], FILTER_VALIDATE_EMAIL)) {
      $lSysLog = new CCor_Log();
      $lSysLog -> log('User ID #'.$aUserID.' does not have a valid >email address<!', mlError, mtNone);

      return NULL;
    }

    return $lResult;
  }

  /**
   * Notify project owner
   *
   * @return boolean
   */
  protected function notifyProjectOwner($aJob, $aClientID, $aFromMail, $aFromName, $aToUserRole, $aSubject, $aBody) {
    $lJobID = $aJob -> __get('jobid');
    $lProjectOwnerID = $aJob -> __get($aToUserRole);
    if (empty($lProjectOwnerID)) {
      $lSysLog = new CCor_Log();
      $lSysLog -> log('Project owner user ID #'.$aUserID.' does not exist (anymore)!', mlError, mtNone);

      return NULL;
    }

    $lAttendantUser = $this -> getAttendantUser($lProjectOwnerID, $lProjectOwnerID, 0, $aClientID);
    if (empty($lAttendantUser)) {
      $lSysLog = new CCor_Log();
      $lSysLog -> log('Project owner with user ID #'.$lProjectOwnerID.' in loop ID #'.$lJobID.' seems to break the al_job_apl_states rule!', mlError, mtNone);

      return NULL;
    }

    $aSubject = 'FYI ------ '.$aSubject;
    $aBody = 'FYI'.CR.LF.'------'.CR.LF.$aBody;

    $lMail = new CApi_Mail_Item($aFromMail, $aFromName, $lAttendantUser['email'], $lAttendantUser['firstname'].' '.$lAttendantUser['lastname'], $aSubject, $aBody);
    $lMail -> setReciverId($lAttendantUser['id']);
    $lMail -> setJobId($lJobID);
    $lMail -> setMailType(mailAplReminder);
    $lMail -> insert('', $aClientID);
  }

  /**
   * Do email
   *
   * @return boolean
   */
  protected function createEmail($aTemplate, $aClientID, $aSql) {
    $lToday = strtotime(date('Y-m-d'));

    $lOuterQry = new CCor_Qry($aSql);
    foreach ($lOuterQry as $lRow) {
      $lID = $lRow['id'];
      $lSrc = $lRow['src'];
      $lJobID = $lRow['jobid'];
      $lUserID = $lRow['userid'];
      $lBackUpUserID = $lRow['backupuserid'];
      $lActiveUserID = $lRow['activeuserid'];
      $lUsrDdl = strtotime($lRow['usrddl']);

      if (empty($lSrc)) {
        $lSysLog = new CCor_Log();
        $lSysLog -> log('Loop ID #'.$lID.' does not have a valid >src<!', mlError, mtNone);

        continue;
      }

      if (empty($lJobID)) {
        $lSysLog = new CCor_Log();
        $lSysLog -> log('Loop ID #'.$lID.' does not have a valid >job ID<!', mlError, mtNone);

        continue;
      }

      if (empty($lUserID)) {
        $lSysLog = new CCor_Log();
        $lSysLog -> log('Loop ID #'.$lID.' does not have a valid >user ID<!', mlError, mtNone);

        continue;
      }

      if ($this -> hasFlags($lJobID, $lSrc, $aClientID)) {
        $lSysLog = new CCor_Log();
        $lSysLog -> log('Job ID #'.$lJobID.' of loop ID #'.$lID.' is either cancelled or set on hold!', mlError, mtNone);

        continue;
      }

      if ($lToday < $lUsrDdl) {
        continue;
      }

      // from
      $lFromMail = CCor_Cfg::get('svc.apl.frommail', '');
      $lFromName = CCor_Cfg::get('svc.apl.fromname', '');

      // to
      $lAttendantUser = $this -> getAttendantUser($lUserID, $lBackUpUserID, $lActiveUserID, $aClientID, $lID);

      if (empty($lAttendantUser)) {
        $lSysLog = new CCor_Log();
        $lSysLog -> log('Combination of user ID #'.$lUserID.', backup user ID #'.$lBackUpUserID.' and active user ID #'.$lActiveUserID.' in loop ID #'.$lID.' seems to break the al_job_apl_states rule!', mlError, mtNone);

        continue;
      }

      $aTemplate -> setPat('to.anrede', $lAttendantUser['anrede']);
      $aTemplate -> setPat('to.firstname', $lAttendantUser['firstname']);
      $aTemplate -> setPat('to.lastname', $lAttendantUser['lastname']);

      $lFac = new CJob_Fac($lSrc, $lJobID);
      $lJob = $lFac -> getDat();

      // put job data
      foreach ($this -> mJobFields as $lKey => $lValue) {
        if (isset($lJob[$lKey]) && !empty($lJob[$lKey])) {
          $aTemplate -> setPat('bez.'.$lKey, $lValue);
          $aTemplate -> setPat('val.'.$lKey, $lJob[$lKey]);
        } else {
          $aTemplate -> setPat('bez.'.$lKey, '-');
          $aTemplate -> setPat('val.'.$lKey, '-');
        }
      }

      // jobid is usually not in the job fields and therefore needs to be handled separately
      $aTemplate -> setPat('val.jobid', $lJobID);

      // START: for Seal only
      if (method_exists('CApp_Sender', 'getSealUrl')) {
        $lSender = new CApp_Sender('usr', array(), $lJob);
        $lSealHome = $lSender -> getSealUrl('act=hom-wel');
        $lSealLink = $lSender -> getSealUrl('act=job-'.$lSrc.'.edt&jobid='.$lJobID.'&_mid='.$aClientID);
        $aTemplate -> setPat('seal.home', $lSealHome);
        $aTemplate -> setPat('seal.link', $lSealLink);
      }
      // STOP: for Seal only

      $aTemplate -> setPat('link', CCor_Cfg::get('base.url').'index.php?act=job-'.$lSrc.'.edt&jobid='.$lJobID.'&_mid='.$aClientID);

      // subject/body
      $lSubject = $aTemplate -> getSubject();
      $lBody = $aTemplate -> getBody();

      $lMail = new CApi_Mail_Item($lFromMail, $lFromName, $lAttendantUser['email'], $lAttendantUser['firstname'].' '.$lAttendantUser['lastname'], $lSubject, $lBody);
      $lMail -> setReciverId($lAttendantUser['id']);
      $lMail -> setJobId($lJobID);
      $lMail -> setJobSrc($lSrc);
      $lMail -> setMailType(mailAplReminder);
      $lMail -> insert('', $aClientID);

      if (CCor_Cfg::get('svc.apl.inform.project_owner')) {
        $this -> notifyProjectOwner($lJob, $aClientID, $lFromMail, $lFromName, CCor_Cfg::get('svc.apl.project_owner'), $lSubject, $lBody);
      }
    }

    return TRUE;
  }

  /**
   * Do execute
   *
   * @return boolean
   */
  protected function doExecute() {
    $lTemplateID = intval(CCor_Cfg::get('svc.apl.tpl', -1));
    if ($lTemplateID > -1) {
      $this -> mJobWriterDefault = strtolower(CCor_Cfg::get('job.writer.default', 'alink')); // job.writer.default can be alink, mop (uses al_job_shadow_XXX) or portal (uses al_job_XXX)
      $this -> mJobFields = CCor_Res::extract('alias', 'name_'.LAN, 'fie');

      $lTemplate = new CApp_Tpl();
      $lTemplate -> loadTemplate($lTemplateID);

      $lTemplate -> setPat('from.firstname', CCor_Cfg::get('svc.apl.firstname'), '');
      $lTemplate -> setPat('from.lastname', CCor_Cfg::get('svc.apl.lastname'), '');
      $lTemplate -> setPat('from.email', CCor_Cfg::get('svc.apl.email'), '');
      $lTemplate -> setPat('from.phone', CCor_Cfg::get('svc.apl.phone'), '');

      $lStartDate = Date("Y-m-d", strtotime("-".CCor_Cfg::get('svc.apl.from', 0)." days"));
      $lEndDate = Date("Y-m-d", strtotime("+".CCor_Cfg::get('svc.apl.to', 0)." days"));

      if (defined('MID') && MID > 0) {
        $lSql = 'SELECT q.loop_id AS id, p.jobid AS jobid, p.src AS src, q.user_id AS userid, q.uid AS backupuserid, q.backupuser_id AS activeuserid, q.pos AS pos, q.confirm AS confirm, q.ddl AS usrddl';
        $lSql.= ' FROM al_job_apl_loop AS p, al_job_apl_states AS q';
        $lSql.= ' WHERE p.mand='.MID;
        $lSql.= ' AND p.id=q.loop_id';
        $lSql.= ' AND p.typ LIKE "apl%"';

        if (FALSE == CCor_Cfg::get('svc.apl.late', FALSE)) {
          $lSql.= ' AND p.start_date>="'.$lStartDate.'" AND p.start_date<="'.$lEndDate.'"';
        } else {
          $lSql.= ' AND p.start_date<="'.$lStartDate.'"';
        }

        $lSql.= ' AND p.status="open"';
        $lSql.= ' AND q.`pos`=(SELECT MIN(st2.`pos`) FROM `al_job_apl_states` AS st2 WHERE st2.`loop_id`=p.id AND st2.`del`="N" GROUP BY st2.`loop_id`)';
        $lSql.= ' AND q.status=0';
        $lSql.= ' AND q.done="N"';
        $lSql.= ' AND q.confirm="all"';
        $lSql.= ' AND q.inv="Y"';
        $lSql.= ' AND q.`del`="N"';
        $lSql.= ' AND p.jobid NOT IN (SELECT jobid FROM al_job_arc_'.MID.')';
        $lSql.= ' ORDER BY id ASC;';

        $this -> createEmail($lTemplate, MID, $lSql);

        $lSql = 'SELECT q.loop_id AS id, p.jobid AS jobid, p.src AS src, q.user_id AS userid, q.uid AS backupuserid, q.backupuser_id AS activeuserid, q.pos AS pos, q.confirm AS confirm, q.ddl AS usrddl';
        $lSql.= ' FROM al_job_apl_loop AS p, al_job_apl_states AS q';
        $lSql.= ' WHERE p.mand='.MID;
        $lSql.= ' AND p.id=q.loop_id';
        $lSql.= ' AND p.typ LIKE "apl%"';

        if (FALSE == CCor_Cfg::get('svc.apl.late', FALSE)) {
          $lSql.= 'AND p.start_date>="'.$lStartDate.'" AND p.start_date<="'.$lEndDate.'" ';
        } else {
          $lSql.= 'AND p.start_date<="'.$lStartDate.'" ';
        }

        $lSql.= ' AND p.status="open"';
        $lSql.= ' AND NOT q.loop_id IN (SELECT DISTINCT s.loop_id FROM al_job_apl_states AS s WHERE s.confirm="one" AND s.done<>"N" AND s.pos=q.pos)';
        $lSql.= ' AND q.`pos`=(SELECT MIN(st2.`pos`) FROM `al_job_apl_states` AS st2 WHERE st2.`loop_id`=p.`id` AND st2.`del`="N" GROUP BY st2.`loop_id`)';
        $lSql.= ' AND q.status=0';
        $lSql.= ' AND q.done="N"';
        $lSql.= ' AND q.confirm="one"';
        $lSql.= ' AND q.inv="Y"';
        $lSql.= ' AND q.`del`="N"';
        $lSql.= ' AND p.jobid NOT IN (SELECT jobid FROM al_job_arc_'.MID.')';
        $lSql.= ' ORDER BY id ASC;';

        $this -> createEmail($lTemplate, MID, $lSql);
      }
    }

    return TRUE;
  }
}