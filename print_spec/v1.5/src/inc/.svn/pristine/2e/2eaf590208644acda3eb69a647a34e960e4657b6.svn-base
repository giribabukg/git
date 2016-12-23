<?php
class CInc_Api_Tcp extends CCor_Obj {
  
  protected $mHost;
  protected $mPort;
  protected $mUser;
  protected $mPass;
  
  protected $mHandle;
  
  protected $mEol    = "\r\n";
  protected $mErrNo  = 0;
  protected $mErrMsg = '';
  protected $mMtype = mtApi;
  protected $mTimeout = 30;
  protected $mByteBuffer = 4096; // 9192; 65536; 
  
  /**
   * Set config options: host, port(, username, password)
   * 
   * Moved from constructor to setConfig because of possible 
   * singleton descendants
   * 
   * @param string $aHost Remote host name or IP
   * @param int $aPort Remote host port
   * @param string $aUser Username for authentication (optional)
   * @param string $aPass Password for authentication (optional)
   */
  
  public function setConfig($aHost, $aPort, $aUser = '', $aPass = '', $aAuthHost = '') {
    $this -> mHost = $aHost;
    if ($aAuthHost == '') $aAuthHost = $aHost;
    $this -> mAuthHost = $aAuthHost;
    $this -> mPort = $aPort;
    $this -> mUser = $aUser;
    $this -> mPass = $aPass;
  }
  
  /**
   * General Debug and error code function
   * 
   * @access protected
   * @param string $aMsg Message text
   * @param int $aLevel Message level (mlInfo, mlWarn etc.)
   */
  
  protected function doMsg($aMsg, $aLevel) {
    $this -> msg($aMsg, $this -> mMtype, $aLevel);
  }
  
  /**
   * Connect to remote host (only if not already connected)
   * 
   * @access public
   */
  public function connect() {
    if ($this -> mHandle) {
      $this -> doMsg('Already connected', mlWarn);
      return TRUE;
    }
    return $this -> doConnect();
  }
  
  protected function doConnect() {
    $this -> mHandle = @fsockopen($this -> mHost, $this -> mPort, $lErrNo, $lErrStr, $this -> mTimeout);
    if (!$this -> mHandle) {
      $this -> setError('Connect failed: '.$lErrStr, $lErrNo, mlFatal);
    } else {
      $this -> setError('Connection to '.$this -> mHost.':'.$this -> mPort.' okay', aeOkay, mlInfo);
    }
    return $this -> mHandle;
  }
  
  public function isConnected() {
    return (bool)$this -> mHandle;
  }

  public function getByteBuffer() {
    return $this -> mByteBuffer;
  }
  
  /**
   * Disconnect if currently connected to a remote host
   * 
   * @access public
   */
  
  public function disconnect() {
    if ($this -> mHandle) {
      $this -> doMsg('Disconnecting from '.$this -> mHost.':'.$this -> mPort, mlInfo);
      $this -> doDisconnect();
      $this -> mHandle = FALSE;
    }
  }
  
  /**
   * Perform the actual disconnection
   * 
   * @access protected
   */
  
  protected function doDisconnect() {
    @fclose($this -> mHandle);
  }
  
  /**
   * Sends some data to remote host
   * 
   * @access public
   * @param string $aStr String data to send
   * @return int Number of bytes sent or FALSE in case of error
   */
  
  public function send($aData) {
    if (!$this -> mHandle) {
      $this -> setError('Not connected', 500, mlError);
      return FALSE;
    } #else {
    #  $this -> setError('Sending okay', mlInfo);
    #}
    $this -> doMsg('SEND : '.$aData, mlInfo);
    try {
      $lRet = @fputs($this -> mHandle, $aData);
      if ($lRet == FALSE) {
        $this -> setError('TCP: Data not sent', 101, mlError);
        return FALSE;
      }
    } catch (Exception $lExc) {
      $this -> setError('TCP: Exception in send data', 101, mlError);
      return FALSE;
    }
    return $lRet;
  }
  
  /**
   * Sends some data to remote host including an EOL
   * 
   * @access public
   * @param string $aStr (optional) String data to send
   * @return int Number of bytes sent or FALSE in case of error
   */
  
  public function sendLine($aStr = '') {
    $this -> send($aStr.$this -> mEol);
  }
  
  public function read($aBytes = 0) {    
    if (!$this -> mHandle) {
      $this -> msg('Not connected', mtApi, mlWarn);
      return '';
    } 
    try {
      if ($aBytes<=0) $aBytes = $this -> mByteBuffer;
      $lRet = fgets($this -> mHandle, $aBytes);
      $this -> dbg('READ : '.$lRet);
      if ($lRet === FALSE) {
        $this -> setError('TCP: Data not read', 102, mlError);
        return '';
      }
    } catch (Exception $lExc) {
      $this -> setError('TCP: Data not read (exception)', 102, mlError);
      return FALSE;
    }
    return $lRet;
  }
  
  /**
   * Reads all data until either EOL or EOF is encountered
   * 
   * @access public
   * @return string Reply data from remote host including EOL's 
   */
  
  public function readLine() {
    if (!$this -> mHandle) {
      $this -> setError('Not connected', '500', mlError);
      return '';
    } 
    $lRet = fgets($this -> mHandle, $this -> mByteBuffer);
    $this -> dbg('READ : '.$lRet);
    if ($lRet === FALSE) {
      $this -> setError('TCP: Data not read', 102, mlError);
      return '';
    }
    return $lRet;
  }
  
  /**
   * Reads all input from remote host until EOF is found
   * 
   * @access public
   * @return string Reply data from remote host 
   */
  public function readAll() {
    if (!$this -> mHandle) {
      $this -> setError('Not connected', 500, mlError);
      return '';
    } 
    $lRet = '';
    while (!feof($this -> mHandle)) {
      $lTmp = fgets($this -> mHandle, $this -> mByteBuffer);
      $this -> dbg('READ : '.$lTmp);
      if ($lTmp === FALSE) {
        $this -> setError('TCP: Data not read', 102, mlError);
        return '';
      }
      $lRet.= $lTmp;
    }
    return $lRet;
  }
  
  /**
   * Reads all input from remote host until fourth char is space or timeout occurs 
   * 
   * @access public
   * @return string Reply data from remote host 
   */
  function readLines() {
    if (!$this -> mHandle) {
      $this -> doMsg('Not connected');
      return '';
    } 
    $lRet = '';
    while ($lTmp = fgets($this -> mHandle, $this -> mByteBuffer)) {
      $lRet.= $lTmp;
      if (substr($lTmp, 3, 1) == ' ') {
        BREAK;
      }
    }
    $this -> dbg('READLINES : '.$lRet);
    if ($lTmp === FALSE) {
      $this -> setError('TCP: Data not read', 102, mlError);
      return '';
    }
    return $lRet;
  }
  
  /**
   * Get the error number of last operation
   * 
   * @access public
   * @return int Error/Status Number of last call 
   */
  public function getErrNo() {
    return $this -> mErrNo;
  }
  
  /**
   * Get the error message of last operation
   * 
   * @access public
   * @return string Error/status message of last call 
   */
  public function getErrMsg() {
    return $this -> mErrMsg;
  }
  
  /**
   * Set error message and number
   * 
   * @access protected
   * @param int Error number
   * @param string Error message
   */
  protected function setError($aMsg, $aNo, $aLvl = mlError) {
    $this -> mErrMsg = $aMsg; 
    $this -> mErrNo  = intval($aNo);
    $this -> msg($aMsg, mtApi, intval($aLvl));
  }
}