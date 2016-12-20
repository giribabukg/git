<?php
class CInc_Svc_Skip extends CSvc_Base {
  
  private $mMand;
  private $mSkipGroups;
  
  /**
   * Send an email out to APL user with job content
   * 
   * @param integer $aUsrId          
   * @param char $aSrc          
   * @param string $aJobId          
   * @param integer $aTemplateId          
   */
  protected function sendMailToAplUser($aUsrId, $aSrc, $aJobId, $aTemplateId) {
    // Get Job
    $lFac = new CJob_Fac($aSrc, $aJobId);
    $lJob = $lFac -> getDat();
    
    // Send Email
    $lParams = array(
        'sid' => $aUsrId,
        'tpl' => $aTemplateId
    );
    $lSender = new CApp_Sender('usr', $lParams, $lJob);
    $lSender -> execute();
  }
  
  /**
   * Returns an array of Marketing sub group ids to only search on 'Marketing Groups'
   */
  protected function getGroupsToSkip() {
    $lIds = array();
    
    $lTim = "SELECT * FROM al_gru WHERE parent_id IN (".$this -> mSkipGroups.") AND mand=".$this -> mMand;
    $lQry = new CCor_Qry($lTim);
    foreach ($lQry as $lRow) {
      array_push($lIds, $lRow['id']);
    }
    
    return $lIds;
  }
  
  /**
   * Perform a search on the APL Loop/State Tables to get all APLs with a deadline date
   * 
   * @param date $lDate          
   * @param integer $lTplId          
   * @param boolean $setStatus          
   */
  protected function performLoopSearch($aDate, $aTplId, $aSetStatus = FALSE) {
    $lSql = 'SELECT q.loop_id AS id, p.jobid AS jobid, p.src AS src, q.user_id AS user_id, q.pos AS pos, q.confirm AS confirm ';
    $lSql.= 'FROM al_job_apl_loop AS p, al_job_apl_states AS q ';
    $lSql.= 'WHERE p.src="com" AND p.mand=1 AND p.typ="apl" AND p.ddl="'.$aDate.'" AND p.status="open" ';
    $lSql.= 'AND q.loop_id=p.id AND q.done="N" AND q.del="N" ';
    $lSql.= 'AND q.gru_id IN (' . implode(",", $this -> getGroupsToSkip()).') ';
    $lSql.= 'AND q.`pos`=(SELECT MIN(st2.`pos`) FROM `al_job_apl_states` AS st2 WHERE st2.loop_id=p.id AND st2.del="N" GROUP BY st2.loop_id);';
    
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      if ($aSetStatus === TRUE) { // only triggered when need to skip
        $lApl = new CApp_Apl_Loop($lRow['src'], $lRow['jobid'], 'apl');
        $lApl -> setState($lRow['user_id'], CApp_Apl_Loop::APL_STATE_FORWARD, '', $lRow['id']);
      }
      $this -> sendMailToAplUser($lRow['user_id'], $lRow['src'], $lRow['jobid'], $aTplId);
    }
  }
  protected function doExecute() {
    // Retrieve all parameters from service
    $this -> mMand = MID;
    $this -> mSkipGroups = explode(",", $this -> getPar('skip_groups'));
    
    // Find all APL which are one day before due [Warning sent to each user that the task is due tomorrow]
    $lWarningTpl  = $this -> getPar('warning_tpl');
    $lWarningDate = date("Y-m-d", strtotime("+1 days"));
    $this -> performLoopSearch($lWarningDate, $lWarningTpl);
    
    // Find all APL which late today [Warning that the task is late today and it will be skipped tomorrow]
    $lLateTpl = $this -> getPar('late_tpl');
    $lToday = date("Y-m-d");
    $this -> performLoopSearch($lToday, $lLateTpl);
    
    // Find all APL tasks which are one day late [Task has been skipped]
    $lSkippedTpl  = $this -> getPar('skip_tpl');
    $lSkippedDate = date("Y-m-d", strtotime("-1 days"));
    $this -> performLoopSearch($lSkippedDate, $lSkippedTpl, TRUE);
    
    return TRUE;
  }
}