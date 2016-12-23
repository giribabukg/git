<?php
class CInc_App_Event_Action_Wec_CreateProject extends CApp_Event_Action {

  public function execute() {
    $lJob = $this -> mContext['job'];

    $lJobId = $lJob['jobid'];
    $lSrc = $lJob['src'];

    $lWecPrj = new CApp_Wec($lSrc, $lJobId);
    $lWecPrjId = $lWecPrj -> createWebcenterProject();

    if ($lWecPrjId) {
      return TRUE;
    } else {
      return FALSE;
    }
  }
}