<?php
class CInc_Svc_Event extends CSvc_Base {

  protected function doExecute() {
    $lEventID = !empty($this -> mParam['eventid']) ? intval($this -> mParam['eventid']) : NULL;
    $lNumberOfJobs = is_numeric($this -> mParam['numberofjobs']) ? intval($this -> mParam['numberofjobs']) : 10;

    if ($lEventID) {
      $lResEveAct = CCor_Res::extract('eve_id', 'cond_id', 'eveact', array('eve' => $lEventID));
      $lConditionID = $lResEveAct[$lEventID];

      $lAppConditionRegistry = new CApp_Condition_Registry();
      $lAppConditionRegistryObject = $lAppConditionRegistry -> loadFromDb($lConditionID);
      $lConditionsSQL = $lAppConditionRegistryObject -> paramToSQL();

      $lWriter = CCor_Cfg::get('job.writer.default', 'portal');
      if ('alink' == $lWriter) {
        $lSQL = 'SELECT src, jobid';
        $lSQL.= ' FROM al_job_shadow_'.MID;
        $lSQL.= ' WHERE src<>\'\' AND jobid<>\'\' AND '.$lConditionsSQL;
        $lSQL.= ' ORDER BY jobid DESC';
        $lSQL.= ' LIMIT '.$lNumberOfJobs.';';

        $lQry = new CCor_Qry($lSQL);
      } elseif ('portal' == $lWriter) {
        $lIte = new CCor_TblIte('all');
        $lIte -> addField('src');
        $lIte -> addField('jobid');
        $lIte -> addCondition('src', '<>', '');
        $lIte -> addCondition('jobid', '<>', '');
        $lIte -> addCnd($lConditionsSQL);
        $lIte -> setOrder('jobid', 'DESC');
        $lIte -> setLimit($lNumberOfJobs);

        $lQry = $lIte -> getArray();
      } else {
        return TRUE;
      }

      foreach ($lQry as $lRow) {
        $lSrc = $lRow['src'];
        $lJobID = $lRow['jobid'];

        if ($lSrc && $lJobID) {
          $lJobFac = new CJob_Fac($lSrc, $lJobID);
          $lJobDat = $lJobFac -> getDat();

          $lEvent = new CJob_Event($lEventID, $lJobDat);
          $lEvent -> execute();
        }
      }
    }

    return TRUE;
  }
}