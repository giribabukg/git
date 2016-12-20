<?php
class CInc_App_Event_Action_Dalim_Unlockversion extends CApp_Event_Action {

  const VERSION_DELIMITER = '_';

  public function execute() {
    $lJob = $this -> mContext['job'];
    $lJid = $lJob -> getId();
    $lSrc = $lJob -> getSrc();

    $lFiles = new CApi_Dalim_Files($lSrc, $lJid);
    $lFiles -> unlockAllFiles();

    return TRUE;
  }
}