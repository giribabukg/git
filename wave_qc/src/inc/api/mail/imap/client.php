<?php
class CInc_Api_Mail_Imap_Client extends CCor_Obj {
  
  public function __construct($aConnectionString, $aUser, $aPass) {
    $this->mConn = $aConnectionString;
    $this->mUser = $aUser;
    $this->mPass = $aPass;
    //var_dump($this);
  }
  
  public function __destruct() {
    $this->disconnect();
  }
  
  public function connect() {
    if (!$this->mHandle) {
      $this->mHandle = imap_open($this->mConn, $this->mUser, $this->mPass);
    }
    return $this->mHandle;
  }
  
  public function disconnect() {
    if ($this->mHandle) {
      imap_close($this->mHandle, CL_EXPUNGE);
    }
    $this->mHandle = null;
  }
  
  public function getMails() {

    $lRet = array();
    $this->connect();
    $lNum = imap_num_msg($this->mHandle);
    //echo $lNum.' eMails found'.BR;
    if (0 == $lNum) {
      return $lRet;
    }
    for ($i=1; $i<=$lNum; $i++) {
      $lMail = new CApi_Mail_Imap_Item($this, $i);
      $lRet[$i] = $lMail;
    }
    return $lRet;
  }
  
  public function getHeader($aMailNum) {
    return imap_header($this->mHandle, $aMailNum);
  }
  
  public function getUid($aMailNum) {
    return imap_uid($this->mHandle, $aMailNum);
  }
  
  public function getStruct($aUid) {
    $lRet = imap_fetchstructure($this->mHandle, $aUid, FT_UID);
    return $lRet;
  }
  
  public function getBody($aMailId) {
    $lPart = $this->getStruct($aMailId);
    //var_dump($lPart);
    if (($lPart->type == 0) && (sizeof($lPart->parts) == 0)) {
      $lRet = imap_fetchbody($this->mHandle, $aMailId, "1");
    } else {
      $lRet = imap_body($this->mHandle, $aMailId, FT_UID);
    }
    return $lRet;
  }
  
  public function getBodyPart($aMailId, $aPrefix = '') {
    $lBody = imap_fetchbody($this->mHandle, $aMailId, $aPrefix, FT_UID);
    return $lBody;
  }
  
  public function delete($aMailNum) {
    imap_delete($this->mHandle, $aMailNum);
  }
  
  public function moveMail($aMailNum, $aFolder) {
    imap_mail_move($this->mHandle, $aMailNum, $aFolder);
  }
  
  public function getFolderList() {
    return imap_list($this->mHandle, $this->mConn, '*');
  }
  
  protected function getLowerEncodings() { 
    $r = mb_list_encodings();
    for ($n = sizeOf($r); $n--; ) { 
      $r[$n] = strtolower($r[$n]); 
    } 
    return $r;
  }
  
  //  Receive a string with a mail header and returns it
  // decoded to a specified charset.
  // If the charset specified into a piece of text from header
  // isn't supported by "mb", the "fallbackCharset" will be
  // used to try to decode it.
  public function decodeMimeString($mimeStr, $inputCharset='utf-8', $targetCharset='utf-8', $fallbackCharset='iso-8859-1') {
    $encodings = $this ->getLowerEncodings();
    $inputCharset    = strtolower($inputCharset);
    $targetCharset   = strtolower($targetCharset);
    $fallbackCharset = strtolower($fallbackCharset);
  
    $decodedStr = '';
    $mimeStrs = imap_mime_header_decode($mimeStr);
    for ($n = sizeOf($mimeStrs), $i = 0; $i < $n; $i++) {
      $mimeStr = $mimeStrs[$i];
      $mimeStr->charset = strtolower($mimeStr->charset);
      if (($mimeStr == 'default' && $inputCharset == $targetCharset)
      || $mimStr->charset == $targetCharset) {
        $decodedStr.= $mimStr->text;
      } else {
        $charSet = (in_array($mimeStr->charset, $encodings)) ?
                $mimeStr->charset : $fallbackCharset;
        $decodedStr.= mb_convert_encoding(
            $mimeStr->text, $targetCharset, $charSet);
      }
    } 
    return $decodedStr;
  }
 
  
}