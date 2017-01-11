<?php
/**
 * Mysql Connection object
 *
 * Singleton. Hides the database connection handle and provides basic
 * query and result fetching methods.
 *
 * @package     cor
 * @subpackage  db
 * @copyright   5Flow GmbH (http://www.5flow.eu)
 * @version     $Rev: 13312 $
 * @date        $Date: 2016-04-08 12:36:31 +0200 (Fri, 08 Apr 2016) $
 * @author      $Author: ahajali $
 */

/**
 * Database Connection Interface
 *
 * Defines the minimum interface a database connection object should expose to
 * be able to work well with CCor_Qry
 */
interface ICor_Sql {

  /**
   * If not already connected, connect to the DB Server.
   *
   * Will be called implicitly when a query is executed without prior connect()
   *
   * @return boolean TRUE if connection is okay
   */
  public function connect();

  /**
   * Surprise ;-) disconnect from the DB server, releasing the connection handle
   */
  public function disconnect();

  /**
   * Execute an SQL statement and return the result handle
   *
   * Will implicitly connect to the DB server if the connection handle is not yet available.
   * All SQL-statements should eventually turn up here for debugging/benchmarking/whatever purposes
   *
   * @param string $aSql SQL statement to execute
   * @return resource Result handle
   */
  public function query($aSql);
  public function getAssoc($aHandle);
  public function getObject($aHandle);
  public function getArray($aHandle);

  /**
   * Return the last inserted ID in case of auto-increment primary keys
   *
   * @return integer The ID that was created during the last insert/replace statement
   */
  public function getInsertId();

  /**
   * Free a query result specified by the handle
   *
   * @param ressource $aHandle
   */
  public function freeResult($aHandle);

} // interface


/**
 * Core: Connection Class
 *
 * This class handles the connection to a MySql Database using the
 * php_mysql extension, hiding the connection handle and supporting
 * basic logging of queries and errors.
 *
 * @package    COR
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 13312 $
 * @date $Date: 2016-04-08 12:36:31 +0200 (Fri, 08 Apr 2016) $
 * @author $Author: ahajali $
 */
class CCor_Sql extends CCor_Obj implements ICor_Sql {

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
  protected $mName;

  protected $mQueryCount;
  protected $mQueries;

  private function __construct() {
    $lCfg = CCor_Cfg::getInstance();

    $this -> mHost = $lCfg -> get('db.host');
    $this -> mPort = $lCfg -> get('db.port');
    $this -> mUser = $lCfg -> get('db.user');
    $this -> mPass = $lCfg -> get('db.pass');
    $this -> mName = $lCfg -> get('db.name');
    $this -> mClientFlags = $lCfg -> get('db.client_flags', NULL);

    $this -> mQueryCount = 0;
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
        $this -> mPass, NULL, $this -> mClientFlags);
  
    #$this -> bench();
  
    if (!$this -> mHandle) {
      die('Cannot connect to '.$this -> mHost);
    }
    $this -> selectDb($this -> mName);
    $lResr = @mysql_query('set names utf8', $this -> mHandle);
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
      #addSqlErr('Not connected, cannot select Db '.$aDbName);
      return FALSE;
    }
    $this -> mDbName = $aDbName;
    @mysql_select_db($this -> mDbName, $this -> mHandle);
    $this -> checkError('Select Db '.$aDbName);
  }


  private function checkError($aSql = '') {
    if ($this -> mHandle) {
      $lNum = mysql_errno($this -> mHandle);
      if (!empty($lNum)) {
        $lTxt = mysql_error($this -> mHandle);
        if (!empty($aSql)) {
          $lTxt.= ', QUERY: '.$aSql;
        }
        CCor_Msg::add('SQL ('.$lNum.') : '.$lTxt, mtSql, mlError);
      }
    }
  }

  /**
   * Execute an SQL statement and return the result handle
   *
	 *
   * @return resource Result handle
   */
  public function query($aSql, $aWithMessage = TRUE) {
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
      $lRes = @mysql_query($aSql, $this -> mHandle);
      $this->mInsertId = mysql_insert_id($this->mHandle);
      if ($aWithMessage) {
        CCor_Msg::add($aSql, mtSql, mlInfo);
      }
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
   * Get next row in result-set as an associative array('fieldname' => 'value');
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