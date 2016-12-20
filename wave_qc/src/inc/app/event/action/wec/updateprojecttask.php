<?php
class CInc_App_Event_Action_Wec_UpdateProjectTask extends CApp_Event_Action {

  public function execute() {
    $lJob = $this -> mContext['job'];
    $lJobId = $lJob['jobid'];
    $lSrc = $lJob['src'];
    $lWecPrjId = $lJob['wec_prj_id'];

    // when there is no WebCenter project ID, create a new one
    if (!$lWecPrjId) {
      $lWecPrj = new CApp_Wec($lSrc, $lJobId);
      $lWecPrjId = $lWecPrj -> createWebcenterProject();
    }

    // load WebCenter access information, load user access information when requested
    $lWec = new CApi_Wec_Client();
    $lWec -> loadConfig(FALSE, TRUE);

    // get WebCenter taskname (mandatory!)
    $lTaskname = CCor_Cfg::get('wec.taskname');

    if ($lTaskname) {
      $lQry = new CApi_Wec_Query($lWec);
      $lQry -> setParam('projectid', $lWecPrjId);
      $lQry -> setParam('taskname', $lTaskname);
      $lQry -> setParam('starttaskoption', 2);
      $lXml = $lQry -> query('UpdateProjectTask.jsp');

      $lRes = new CApi_Wec_Response($lXml);
      if (!$lRes -> isSuccess()) {
        return;
      }
      if (0 === strpos($lXml, '<error>')) {
        return;
      }
      return $lXml;
    } else {
      return FALSE;
    }
  }
}