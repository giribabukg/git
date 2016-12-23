<?php
class CInc_Api_Mail_Imap_Item extends CCor_Obj {
  
  /**
   * @var CInc_Api_Mail_Imap_Client
   */
  protected $mClient;
  
  public function __construct($aClient, $aNum) {
    $this->mClient = $aClient;
    $this->mNum = $aNum;
    
    $this->mHeader = $this->mClient->getHeader($this->mNum);
    $this->mUid = $this->mClient->getUid($this->mNum);
    $lBody = $this->getBody();
  }
  
  public function getHeaders() {
    return $this->mHeader;
  }
  
  public function getHeader($aKey, $aDefault = null) {
    $lHeader = (isset($this->mHeader->$aKey)) ? $this->mHeader->$aKey : $aDefault;
    return $this->mClient->decodeMimeString($lHeader);
  }
  
  public function getSubject() {
    return $this->getHeader('subject');
  }
  
  public function getBody() {
    return $this->mClient->getBody($this->mUid);
  }
  
  public function getMatchingParts($aMime = 'text/plain') {
    if (!isset($this->mMimeParts[$aMime])) {
      $this->mTempMime = array();
      $this->mLookingFor = $aMime;
      $lPart = $this->mClient->getStruct($this->mUid);
      $this->scanParts($lPart);
    }
    $this->mMimeParts[$aMime] = $this->mTempMime;
    unset($this->mTempMime);
    return $this->mMimeParts[$aMime];
  }

  protected function scanParts($aPart, $aPrefix = '') {
    if (isset($aPart->parts)) {
      // is multipart, need to iterate over the sub parts
      foreach ($aPart->parts as $lIndex => $lPartOfPart) {
        $lPref = '';
        if ($aPrefix) {
          $lPrefix = $aPrefix.'.';
        }
        $this->scanParts($lPartOfPart, $lPrefix.($lIndex + 1));
      }
    } else {
      // we have a single part with no substructures
      if (isset($aPart->disposition)) {
        //var_dump($part);
        if (strtolower($aPart->disposition) == 'attachment') {
          $lItm = array();
          $lItm['filename'] = $aPart->dparameters[0]->value;
          $lItm['ext'] = pathinfo($lItm['filename'], PATHINFO_EXTENSION);
  
          $lBody = $this->mClient->getBodyPart($this->mUid, $aPrefix);
          switch ($aPart->encoding) {
          	case 3: $lBody = imap_base64($lBody); break;
          	case 4: $lBody = imap_qprint($lBody);
          }
          $lItm['body'] = $lBody;
  
          $this->mAttachments[] = $lItm;
        }
      }
    }
  }
  
  
  public function getAttachments() {
    if (!isset($this->mAttachments)) {
      $this->mAttachments = array();
      $lPart = $this->mClient->getStruct($this->mUid);
      $this->readAttachments($lPart);
    }
    return $this->mAttachments;
  }
  
  protected function readAttachments($aPart, $aPrefix = '') {
    if (isset($aPart->parts)) {
      // is multipart, need to iterate over the sub parts
      foreach ($aPart->parts as $lIndex => $lPartOfPart) {
        $lPref = '';
        if ($aPrefix) {
          $lPrefix = $aPrefix.'.';
        }
        $this->readAttachments($lPartOfPart, $lPrefix.($lIndex + 1));
      }
    } else {
      // we have a single part with no substructures
      if (isset($aPart->disposition)) {
        //var_dump($part);
        if (strtolower($aPart->disposition) == 'attachment') {
          $lItm = array();
          $lItm['filename'] = $aPart->dparameters[0]->value;
          $lItm['ext'] = pathinfo($lItm['filename'], PATHINFO_EXTENSION);
  
          $lBody = $this->mClient->getBodyPart($this->mUid, $aPrefix);
          switch ($aPart->encoding) {
          	case 3: $lBody = imap_base64($lBody); break;
          	case 4: $lBody = imap_qprint($lBody);
          }
          $lItm['body'] = $lBody;
  
          $this->mAttachments[] = $lItm;
        }
      }
    }
  }

}