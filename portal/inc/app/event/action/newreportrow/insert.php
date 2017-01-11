<?php
class CInc_App_Event_Action_Newreportrow_Insert extends CApp_Event_Action {

  public function execute() {
    $this -> dbg('Execute - Insert New Reporting Row');
    $lInsert = new CJob_Utl_Shadow();
    // Old
#    return $lInsert->createNewReportRow($this -> mContext['job']->id, $this -> mContext['job']->src);
    // New
    return $lInsert->createNewReportRow($this -> mContext['job']->jobid, $this -> mContext['job']->src);
  }
}