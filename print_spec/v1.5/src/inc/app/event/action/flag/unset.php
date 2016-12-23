<?php
class CInc_App_Event_Action_Flag_Unset extends CApp_Event_Action {

  public function execute() {
    $this -> dbg('Execute Unsetflag');
    return TRUE;
  }

}