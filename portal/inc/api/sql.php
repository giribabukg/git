<?php
/**
 * Mysql Connection Class for other servers (non-singleton)
 *
 * This class handles the connection to a MySql Database using the 
 * php_mysql extension, hiding the connection handle and supporting 
 * basic logging of queries and errors.
 */

class CInc_Api_Sql extends CCor_Obj implements ICor_Sql {

  private $mHandle;
  
  protected $mHost;
  protected $mPort;
  protected $mUser;
  protected $mPass;
  protected $mName;
  
  protected $mQueryCount;
  protected $mQueries;
  
  public function setConfig($aHost, $aUser, $aPass, $aDb, $aPort = 3306) {
    $this -> mHost = $aHost;
    $this -> mPort = $aPort;
    $this -> mUser = $aUser;
    $this -> mPass = $aPass;
    $this -> mName = $aDb;
  }

  public function __destruct() {
    $this -> disconnect();
  }

  /**
   * If not already connected, connect to MySql Server. Do this before using
   * query or any other member function
   * 
   * @return boolean TRUE if connection is okay
   */

  public function connect() {
    if ($this -> mHandle) return $this -> mHandle;
    
    $this -> mHandle = mysql_connect(
      $this -> mHost.':'.$this -> mPort, 
      $this -> mUser, 
      $this -> mPass);
      
    if (!$this -> mHandle) {
      $this -> msg('Cannot connect to '.$this -> mHost, mtApi, mlFatal);
      return;
    }
    $this -> selectDb($this -> mName);
    return $this -> mHandle;
  }

  /**
   * If connected, close the connection to the MySql-server
   */
  public function disconnect() {
    if ($this -> mHandle) { 
      @mysql_close($this -> mHandle);
      $this -> mHandle = NULL;
    }  
  }
  
  public function selectDb($aDbName) {
    if (!$this -> mHandle) {
      return FALSE; 
    } 
    $this -> mDbName = $aDbName; 
    @mysql_select_db($this -> mDbName, $this -> mHandle);
    $this -> checkError('Select Db '.$aDbName);
  }
  
  
  private function checkError($aSql = '') {
    $lNum = mysql_errno();
    if (!empty($lNum)) {
      $lTxt = mysql_error();
      if (!empty($aSql)) {
        $lTxt.= ', QUERY: '.$aSql;
      }
      CCor_Msg::add('SQL ('.$lNum.') : '.$lTxt, mtSql, mlError);
    }
  }

  /**
   * Execute an SQL statement and return the result handle
   * @return mysqlresult Result handle
   */
  public function query($aSql) {
    $this -> mQueryCount++;
    $this -> connect();
    $lTim = xdebug_time_index();
    $lRes = @mysql_query($aSql, $this -> mHandle);
    $lTim = xdebug_time_index() - $lTim;
    
    if (isset($this -> mQueries[$aSql])) {
      $this -> mQueries[$aSql]++;
      $lTxt = $this -> mQueries[$aSql].' x Duplicate '.$aSql;      
      $this -> msg($lTxt, mtSql, mlWarn);
    } else {
      $this -> mQueries[$aSql] = 1;
    }
    $this -> msg('['.substr($lTim,0,4).'] '.$aSql, mtApi, mlInfo);
    
    $this -> checkError($aSql);
    return $lRes;
  }

  /**
   * Free a result handle
   * @param mysqlresult $aHandle Result handle to free
   */

  public function freeResult($aHandle) {
    @mysql_free_result($aHandle);
    $this -> checkError();
  }

  public function getAssoc($aHandle) {
    $lArr = @mysql_fetch_assoc($aHandle);
    $this -> checkError();
    return $lArr;
  }

  public function getObject($aHandle) {
    $lRow = @mysql_fetch_object($aHandle);
    $this -> checkError();
    return $lRow;
  }

  public function getArray($aHandle) {
    $lArr = @mysql_fetch_row($aHandle);
    $this -> checkError();
    return $lArr;
  }
  
  public function getInsertId() {
    $lRet = @mysql_insert_id();
    return $lRet;
  }
  
  public function getQueryCount() {
    return $this -> mQueryCount;
  }
  
}