<?php
class CInc_App_Event_Action_Flag_Toggle extends CApp_Event_Action {

  public function execute() {
    $this -> dbg('Execute Toggle Flag');
    return TRUE;
  }

}