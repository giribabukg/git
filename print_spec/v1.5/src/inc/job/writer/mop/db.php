<?php
class CInc_Job_Writer_Mop_Db extends CCor_Obj implements ICor_Sql {

  /**
   * Singleton instance
   *
   * @var CCor_Sql $mInstance
   */
  private static $mInstance;

  /**
   * Database connection handle
   *
   * @var ressource $mHandle
   */
  private $mHandle;

  protected $mHost;
  protected $mPort;
  protected $mUser;
  protected $mPass;
  public $mName;

  protected $mQueryCount;
  protected $mQueries;

  private function __construct() {
    $lCfg = CCor_Cfg::getInstance();

    $this -> mHost = $lCfg -> get('mop.db.host');
    $this -> mUser = $lCfg -> get('mop.db.user');
    $this -> mPass = $lCfg -> get('mop.db.pass');
    $this -> mName = $lCfg -> get('mop.db.name');

    $this -> mQueryCount = 0;
  }

  /**
   * If not already connected, connect to MySql Server. Do this before using
   * query or any other member function
   *
   * @return boolean TRUE if connection is okay
   */

  public function connect() {
    if ($this -> mHandle) return $this -> mHandle;
    #$this -> bench();

    $this -> mHandle = mysql_connect(
        $this -> mHost.':'.$this -> mPort,
        $this -> mUser,
        $this -> mPass, true);

    #$this -> bench();

    if (!$this -> mHandle) {
      die('Cannot connect to '.$this -> mHost);
    }
    $this -> selectDb($this -> mName);
    $lResr = @mysql_query('set names utf8', $this -> mHandle);
    
    $this->mMaxRetries = CCor_Cfg::get('mop.db.maxretries', 10);
    
    return $this -> mHandle;
  }

  public function __destruct() {
    $this -> disconnect();
    self::$mInstance = NULL;
  }

  public static function getInstance() {
    if (NULL === self::$mInstance) {
      self::$mInstance = new self();
    }
    return self::$mInstance;
  }

  private function __clone() {}

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
      #addSqlErr('Not connected, cannot select Db '.$aDbName);
      return FALSE;
    }
    $this -> mDbName = $aDbName;
    @mysql_select_db($this -> mDbName, $this -> mHandle);
    $this -> checkError('Select Db '.$aDbName);
  }


  private function checkError($aSql = '') {
    $lNum = mysql_errno($this -> mHandle);
    if (!empty($lNum)) {
      $lTxt = mysql_error($this -> mHandle);
      if (!empty($aSql)) {
        $lTxt.= ', QUERY: '.$aSql;
      }
      CCor_Msg::add('MOP SQL ('.$lNum.') : '.$lTxt, mtSql, mlError);
    }
  }

  
  public function query($aSql, $aWithMessage = TRUE) {
    $lRes = $this->doQuery($aSql, $aWithMessage);
    $lNum = mysql_errno($this -> mHandle);
    if ($lNum == 1213 || $lNum == 1205) {
      for ($i=0; $i< $this->mMaxRetries; $i++) {
        sleep(1);
        $lRes = $this->doQuery($aSql, $aWithMessage);
        $lNum = mysql_errno($this -> mHandle);
        if ($lNum != 1213 || $lNum != 1205) {
          CCor_Msg::add('MOP SQL ('.$lNum.') : Deadlock solved in '.($i+1).' retries', mtSql, mlError);
          break;
        }
      }
    }
    return $lRes;
  }
  
  
  
  /**
   * Execute an SQL statement and return the result handle
   *
	 *
   * @return resource Result handle
   */
  protected function doQuery($aSql, $aWithMessage = TRUE) {
    $this -> mQueryCount++;
    $this -> connect();

    // Remember any insert ID to avoid receiving the id of the log entry
    if (extension_loaded('xdebug')) {
      $lStart = xdebug_time_index();
      $lRes = @mysql_query($aSql, $this -> mHandle);
      $this -> mInsertId = mysql_insert_id($this->mHandle);
      $lTim = xdebug_time_index() - $lStart;
      if ($aWithMessage) {
        CCor_Msg::add('[start '.substr($lStart, 0, 4).'][duration '.substr($lTim, 0, 4).'] '.$aSql, mtSql, mlInfo);
      }
    } else {
       if ($aWithMessage) {
        CCor_Msg::add('MOP '.$aSql, mtSql, mlInfo);
      }
      $lRes = @mysql_query($aSql, $this -> mHandle);
      $this->mInsertId = mysql_insert_id($this->mHandle);
    }
    if (isset($this -> mQueries[$aSql])) {
      $this -> mQueries[$aSql]++;
      $lTxt = $this -> mQueries[$aSql].' x Duplicate '.$aSql;
      if ($aWithMessage) {
        CCor_Msg::add($lTxt, mtSql, mlWarn);
      }
    } else {
      $this -> mQueries[$aSql] = 1;
    }

    $this -> checkError($aSql);
    return $lRes;
  }

  /**
   * Execute an SQL statement without debugging stuff
   *
   * Used to avoid infinite recursion when logging SQL statements to the log table
   *
   * @return resource Result handle
   */
  public function dbgQuery($aSql) {
    $lRes = @mysql_query($aSql, $this -> mHandle);
    return $lRes;
  }

  /**
   * Free a query result specified by the handle
   *
   * @param ressource $aHandle
   */
  public function freeResult($aHandle) {
    @mysql_free_result($aHandle);
    $this -> checkError();
  }

  /**
   * Get next row in result-set as an associative array array('fielname' => 'value');
   *
   * @param ressource $aHandle
   */
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
    return $this -> mInsertId;
  }

  public function getQueryCount() {
    return $this -> mQueryCount;
  }

  public function getAffectedRows() {
    $lRet = @mysql_affected_rows($this -> mHandle);
    return $lRet;
  }

}