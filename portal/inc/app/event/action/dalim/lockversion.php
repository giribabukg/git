<?php
/**
 * When a job is approved, lookup the current Dalim version and store that in
 * the job field "version" so users can see the approved version number.
 * @author gemmans
 *
 */
class CInc_App_Event_Action_Dalim_Lockversion extends CApp_Event_Action {

  const VERSION_DELIMITER = '_';

  public function execute() {
    $lJob = $this -> mContext['job'];
    $lJid = $lJob -> getId();
    $lSrc = $lJob -> getSrc();

    $lFiles = new CApi_Dalim_Files($lSrc, $lJid);
    $lFiles -> lockAllFiles();

    return TRUE;
  }
}