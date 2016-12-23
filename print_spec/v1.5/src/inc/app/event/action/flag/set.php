<?php
class CInc_App_Event_Action_Flag_Set extends CApp_Event_Action {

  public function execute() {
    $this -> dbg('Execute Setflag');
    return TRUE;
  }

}