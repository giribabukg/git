<?php
/**
 * Core: Mysql Connection
 *
 * SINGLETON
 *
 * @package    COR
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 3948 $
 * @date $Date: 2014-03-18 17:06:53 +0800 (Tue, 18 Mar 2014) $
 * @author $Author: gemmans $
 */

/**
 * Core: Connection Class
 *
 * This class handles the connection to a MySql Database using the
 * php_mysql extension, hiding the connection handle and supporting
 * basic logging of queries and errors.
 *
 * @package    COR
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 3948 $
 * @date $Date: 2014-03-18 17:06:53 +0800 (Tue, 18 Mar 2014) $
 * @author $Author: gemmans $
 */
class CCor_Sqlmig extends CCor_Obj implements ICor_Sql {

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

    $this -> mHost = $lCfg -> get('db.networker.ip');
    $this -> mUser = $lCfg -> get('db.networker.user');
    $this -> mPass = $lCfg -> get('db.networker.pass');
    $this -> mName = $lCfg -> get('db.networker.name');

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
      $this -> mPass);

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
    $lNum = mysql_errno();
    #if ($lNum !== 0) {
    if (!empty($lNum)) {
      $lTxt = mysql_error();
      if (!empty($aSql)) {
        $lTxt.= ', QUERY: '.$aSql;
      }
   #   CCor_Msg::add('SQL ('.$lNum.') : '.$lTxt, mtSql, mlError);
   #   $this -> dbg($aSql);
      #addMsg($lMsg, mtSql, mlError);
    }
  }

  /**
   * Execute an SQL statement and return the result handle
   *
	 *
   * @return resource Result handle
   */
  public function query($aSql) {
    $this -> mQueryCount++;
    $this -> connect();

    // Da anschließend getInsertId() aufgerufen werden kann, muß das SQL
    // mit der CCor_Msg gespeichert vorher gespeichert werden,
    // da sonst die ID aus al_sys_log geliefert wird!
    if (extension_loaded('xdebug')) {
      $lTim = xdebug_time_index();

  #   $lMsg = CCor_Msg::getInstance();
  #    $lMsg -> addMsg('['.substr($lTim, 0, 4).'] '.$aSql, mtSql, mlInfo);
    }

    $lRes = @mysql_query($aSql, $this -> mHandle);
    if (extension_loaded('xdebug')) {
      $lTim = xdebug_time_index() - $lTim;
    }

    if (isset($this -> mQueries[$aSql])) {
      $this -> mQueries[$aSql]++;
      $lTxt = $this -> mQueries[$aSql].' x Duplicate '.$aSql;
      # $lMsg -> addMsg($lTxt, mtSql, mlWarn);
    } else {
      $this -> mQueries[$aSql] = 1;
    }

  #  $this -> checkError($aSql);
    return $lRes;
  }

  /**
   * Execute an SQL without debug stuff
   * @return mysqlresult Result handle
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
    $lRet = @mysql_insert_id();
    return $lRet;
  }

  public function getQueryCount() {
    return $this -> mQueryCount;
  }

}