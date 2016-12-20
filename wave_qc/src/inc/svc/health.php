<?php
class CInc_Svc_Health extends CSvc_Base {

  protected function doExecute() {
    $lRunner = new CHealth_Runner();
    $lRunner->runAll();
    return true;
  }
  
  
}