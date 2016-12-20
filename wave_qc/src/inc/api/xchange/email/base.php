<?php
class CInc_Api_Xchange_Email_Base extends CApi_Xchange_Base {
  
  public function handleMail($aMail) {
    $this->mMail = $aMail;
    $this->logDebug('Handling Mail '.$aMail->getHeader('subject').'...');
    $lRet = false;
    try {
      $lRet = $this->doHandle($aMail);
    } catch (Exception $ex) {
      $this->logError($ex->getMessage());
      $lRet = false;
    }
    return $lRet;
  }
  
  protected function doHandle($aMail) {
    $this->logError('Abstract doHandle used');
    return false;
  }
  

}