<?php
class CInc_Api_Xchange_Email extends CApi_Xchange_Base {

  protected function doExec() {
    if (!$this->hasParam('mand')) {
      $this->msg('Please specify a mandator ID for the Xchange Email Service', mtApi, mlError);
      return false;
    }
    $lMid = $this->getParam('mand');
    if ($lMid != MID) return true;
    
    if (!$this->hasParam('host')) {
      $this->msg('Please specify a host connection string', mtApi, mlError);
      return false;
    }
    
    $lHost = $this->getParam('host');
    $lUser = $this->getParam('user');
    $lPass = $this->getParam('pass');
    
    $lAction = $this->getParam('ok.action');
    if ('move' == $lAction) {
      $lFolder = $this->getParam('ok.folder');
    }
    
    $this->mImap = new CApi_Mail_Imap_Client($lHost, $lUser, $lPass);
    $lMails = $this->mImap->getMails();

    foreach ($lMails as $lId => $lMail) {
      $this->logDebug('Subject '.$lMail->getSubject());
      if ($this->isMatch($lMail)) {
        $lRet = $this->handleMail($lMail);
        if ($lRet) {
          if ('move' == $lAction) {
            $this->mImap->moveMail($lId, $lFolder);
          } else {
            $this->mImap->delete($lId);
          }
        }
      }
    }
    return true;
  }
  
  protected function isMatch($aMail) {
    $lRet = true;
    $lPattern = $this->getParam('pattern');
    if (!empty($lPattern)) {
      $lSubject = $aMail->getHeader('subject');
      $lRet = preg_match($lPattern, $lSubject);
    }
    if ($lRet) {
      $this->logDebug('Match');
    } else {
      $this->logDebug('No match');
    }
    return $lRet;
  }
  
  protected function handleMail($aMail) {
    $this->logDebug('Processing Mail '.$this->mSubject);
    $lHandler = $this->getNextHandler();
    if (!$lHandler) {
      $this->logError('Could not create next handler');
      return false;
    }
    $lRet = $lHandler->handleMail($aMail);
    if ($lRet) {
      $this->mCanDelete = true;
    }
    return $lRet;
  }
  
  protected function getNextHandler() {
    $lHandler = $this->getParam('next.handler');
    if (!class_exists($lHandler)) {
      return false;
    }
    $lParam = $this->getPrefixParams('next.');
    $lRet = new $lHandler($lParam);
    return $lRet;
  }

}