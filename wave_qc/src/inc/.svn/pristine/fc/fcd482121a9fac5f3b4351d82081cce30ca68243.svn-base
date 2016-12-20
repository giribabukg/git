<?php
class CInc_App_Event_Action_Copy_File extends CApp_Event_Action {

  public function execute() {
    $lJob = $this -> mContext['job'];

    // new job information
    $lSrc = $lJob['src'];
    $lJobID = $lJob['jobid'];
    $lWecPrjId = $lJob['wec_prj_id'];

    // old job information
    $lOrigSrc = $lJob['orig_src'];
    $lOrigJobId = $lJob['orig_jobid'];
    $lOrigWecPrjId = $lJob['orig_wecprjid'];

    $lCls = new CApp_Finder($lSrc, $lJobID);
    $lDir = CCor_Cfg::get('flink.destination.pdf.dir', '');
    $lDir = $lCls -> getDynPath($lDir);

    $lOrigCls = new CApp_Finder($lOrigSrc, $lOrigJobId);
    $lOrigDir = CCor_Cfg::get('flink.destination.pdf.dir', '');
    $lOrigDir = $lOrigCls -> getDynPath($lOrigDir);

    // TODO: lines 26 - 34 are needed as long as we can not force job value changes by triggering events
    $lFie = CCor_Res::extract('alias', 'native', 'fie');

    $lFac = new CJob_Fac($lSrc, $lJobID);
    $lJobDet = $lFac -> getDat();

    $lWecPrjId = $lJobDet['wec_prj_id'];
    // foolishness ends here: lines 26 - 34 are needed as long as we can not force job value changes by triggering events

    if (!$lWecPrjId) return FALSE;

    // log-in
    $lWec = new CApi_Wec_Client();
    $lWec -> loadConfig(FALSE, TRUE);

    // TODO: lines 44 - 46 are needed as long as WebCenter can not access folder id in a new project
    $lQry = new CApi_Wec_Query_Doclist($lWec);
    $lRes = $lQry -> getList($lWecPrjId);
    $lName = $lRes[0]['name'];
    // foolishness ends here: lines 44 - 46 are needed as long as WebCenter can not access folder id in a new project

    mkdir($lDir);
    copy($lOrigDir.$lName, $lDir.$lName);

    return TRUE;
  }
}