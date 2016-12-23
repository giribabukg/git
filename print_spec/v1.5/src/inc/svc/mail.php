<?php
class CInc_Svc_Mail extends CSvc_Base {

  protected function doExecute() {
    $lRes = CApi_Mail_Resend::getInstance();
    // if mand of service is 0, send all
    // otherwise, only send mails for this mandator
    $lMid = intval($this->mRow['mand']);
    $lRes -> send($lMid); 
    return true;
  }
}