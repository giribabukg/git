<?php
class CInc_Svc_Poll extends CSvc_Base {

  /**
   * The current message being handled
   * @var Zend_Mail_Message
   */
  protected $mMessage;
  
  protected $lSubject = null;
  protected $lBody = null;

  /**
   * The number/ID of the current mail (used for deletion of handled mails)
   * @var integer
   */
  protected $mNum;

  public function debugMsg($aText, $aType = mtUser, $aLvl = mlInfo) {
    echo $aText.BR;
    parent::msg($aText, $aType, $aLvl);
  }

  protected function doExecute() {
    if (!isset($this->mParam['pattern'])) {
      $this->msg('Please specify mail pattern for Poll Email Service!', mtApi, mlError);
      return false;
    }
    if (!isset($this->mParam['matchfield'])) {
      $this->msg('Please specify match field for Poll Email Service!', mtApi, mlError);
      return false;
    }
    if (!isset($this->mParam['mand'])) {
      $this->msg('Please specify mandator ID for Poll Email Service!', mtApi, mlError);
      return false;
    }
    $lMailbox = $this->getMailStorage();
    $lCount = $lMailbox->countMessages();
    if (0 == $lCount) {
      $this->debugMsg('no mails');
      return true;
    }
    $this->debugMsg($lCount.' messages');
    foreach ($lMailbox as $num => $message) {
      $this->debugMsg('mail no. '.$num);
      $this->mNum = $num;
      $this->mMessage = $message;
      
      $this->handleMail();
    }
    $lMailbox->close();
    return true;
  }

  protected function getMailStorage() {
    if (!isset($this->mStorage)) {
      $lPar = array();
      if (isset($this->mParam['host'])) {
        $lPar['host'] = $this->mParam['host'];
      }
      if (isset($this->mParam['user'])) {
        $lPar['user'] = $this->mParam['user'];
      }
      if (isset($this->mParam['pass'])) {
        $lPar['password'] = $this->mParam['pass'];
      }
      $lTyp = 'pop3';
      if (isset($this->mParam['type'])) {
        $lTyp = $this->mParam['type'];
      }
      if ('imap' == $lTyp) {
        $this -> mStorage = new Zend_Mail_Storage_Imap($lPar);
      } else {
        $this -> mStorage = new Zend_Mail_Storage_Pop3($lPar);
      }
    }
    return $this->mStorage;
  }

  protected function decodeMessage(){
    $this->lSubject = $this->convHeader($this->mMessage->subject);
    $this->lBody = $this->getPlainBody();
  }
  
  protected function handleMail() {
    $this->mCanDelete = false;
    $this->decodeMessage();
    
    if ($this->match()) {
      $this->processMail();
    }
    if ($this->mCanDelete) {
      $this->deleteMail($this->mNum);
    }
  }

  protected function convHeader($aHeader) {
    $lArr = imap_mime_header_decode($aHeader);
    $lRet = '';
    foreach ($lArr as $lRow) {
      $lCharSet = $lRow->charset;
      if ($lCharSet == 'default') {
        $lCharSet = ini_get('iconv.internal_encoding');
      }
      $lRet.= iconv($lCharSet, 'UTF-8', $lRow->text);
    }
    return $lRet;
  }

  protected function match() {
    $lSubject = $this->lSubject;

    $lMatches = array();
    $lRegPattern = $this->mParam['pattern'];
    try {
      if (!preg_match($lRegPattern, $lSubject, $lMatches)) {
        return false;
      }
    } catch (Exception $ex) {
      $this->msg('Invalid RegExp Pattern '.$this->mParam['pattern'].' for Poll Email Service!', mtApi, mlWarn);
      return false;
    }
    $lMatchField = $this->mParam['matchfield'];
    $lMatchValue = $lMatches[1];
    $lMand = intval($this->mParam['mand']);
    $this->debugMsg('Match '.$lMatchValue);

    $lSql = 'SELECT COUNT(*) FROM al_job_shadow_'.$lMand.' ';
    $lSql.= 'WHERE '.$lMatchField.'='.esc($lMatchValue);
    $this->debugMsg($lSql);
    $lCount = CCor_Qry::getInt($lSql);
    $this->debugMsg($lCount.' matching jobs found');

    return ($lCount > 0);
  }

  protected function processMail() {
    
    $lMatches = array();
    $lRegPattern = $this->mParam['pattern'];
    try {
      if (!preg_match($lRegPattern, $this->lSubject, $lMatches)) {
        return;
      }
    } catch (Exception $ex) {
      $this->msg('Invalid RegExp Pattern '.$this->mParam['pattern'].' for Poll Email Service!', mtApi, mlWarn);
      return;
    }
    $lMatchField = $this->mParam['matchfield'];
    $lMatchValue = $lMatches[1];
    $lMand = MID;

    $lHistoryUid = 0;
    if (isset($this->mParam['default_uid'])) {
      $lHistoryUid = intval($this->mParam['default_uid']);
    }
    $lFrom = $this->mMessage->from;
    $lMatches = array();
    if (preg_match('/.* <(.*@.*)>/',$lFrom, $lMatches)) {
      $lMailAddress = $lMatches[1];
      $lSql = 'SELECT id FROM al_usr WHERE email LIKE "'.trim($lMailAddress).'"';
      $lUid = CCor_Qry::getInt($lSql);
      if ($lUid) {
        $lHistoryUid = $lUid;
      }
    }
    $lSql = 'SELECT jobid,src FROM al_job_shadow_'.$lMand.' ';
    $lSql.= 'WHERE '.$lMatchField.'='.esc($lMatchValue);
    $lQry = new CCor_Qry($lSql);

    foreach ($lQry as $lRow) {
      $lHis = new CApp_His($lRow['src'], $lRow['jobid']);
      $lHis->setVal('mand', $lMand);
      $lHis->setVal('user_id', $lHistoryUid);
      $lHis->add(htMail,  $this->lSubject,  $this->lBody);
      $this->mCanDelete = true;
    }
  }


  protected function deleteMail() {
    //TODO: activate again!
    $this->mStorage->removeMessage($this->mNum);
  }

  protected function getCharSetFromContentType($aContentType) {
    preg_match("/charset *=([^;]+)/", $aContentType, $lMatch);
    return $lMatch[1];
  }

  protected function getPlainBody() {
    $lMsg = $this->getFirstPlainPart();
    if (!$lMsg) return false;
    $lContentType = $lMsg->getHeader('content-type');
    $this->debugMsg('CT '.$lContentType);
    $lCharSet = $this->getCharSetFromContentType($lContentType);
    $this->debugMsg('CS '.$lCharSet);
    $lRet=$lMsg->getContent();
    $this->debugMsg('RAW content '.$lRet);
    $encoding = $lMsg->getHeader('Content-Transfer-Encoding');
    $this->debugMsg('Encoding: '.$encoding);
    switch(strtolower($encoding)){
      case 'base64':{
        $lRet = base64_decode($lRet);
        break;    
      }
      case 'quoted-printable':{
        $lRet = quoted_printable_decode($lRet);
        break;
      }
      default:{
        break;
      }
    }
    
    $lRet = iconv($lCharSet, 'UTF-8', $lRet);
    return $lRet;
  }

  protected function getFirstPlainPart() {
    if (strtok($this->mMessage->contentType, ';') == 'text/plain') {
      return $this->mMessage;
    }
    $foundPart = null;
    foreach (new RecursiveIteratorIterator($this->mMessage) as $lPart) {
      try {
        if (strtok($lPart->contentType, ';') == 'text/plain') {
          return $lPart;
          break;
        }
      } catch (Zend_Mail_Exception $e) {
        // ignore
      }
    }
    return false;
  }

}
