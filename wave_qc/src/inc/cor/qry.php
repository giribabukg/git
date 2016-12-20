<?php
/**
 * Database query object
 *
 * Encapsulates the result handle.
 * Results can be used in a loop like this:
 * <code>
 * foreach (new CCor_Sql('SELECT * FROM table') as $lRow) {
 *   echo $lRow['name'].BR;
 * }
 * </code>
 *
 * @package     cor
 * @subpackage  db
 * @copyright   5Flow GmbH (http://www.5flow.eu)
 * @version     $Rev: 9378 $
 * @date        $Date: 2015-06-29 22:13:43 +0800 (Mon, 29 Jun 2015) $
 * @author      $Author: ahajali $
 * @see         ICor_Sql
 */
class CInc_Cor_Qry extends CCor_Obj implements IteratorAggregate {

  /**
   * Handle to the DB query result
   * @var resource
   */
  protected $mHandle;

  /**
   * DB connection object
   * @var ICor_Sql
   */
  protected $mDb;

  /**
   * Last SQL statement executed by this query object
   * @var string
   */
  protected $mSql = '';

  /**
   * Constructor - initialize DB connection object reference.
   *
   * Optionally pass an SQL statement to execute straight away.
   * When using a different DB connection than the default, you can pass the
   * DB connection as the second argument
   *
   * @param string $aSql An SQL statement to execute
   * @param ICor_Sql $aDb A DB connection object
   */
  public function __construct($aSql = '', ICor_Sql & $aDb = NULL) {
    if (empty($aDb))
      $this -> mDb  = CCor_Sql::getInstance();
    else
      $this -> mDb  = & $aDb;
    if (!empty($aSql)) $this -> query($aSql);
  }

  /**
   * Destructor, automatically free result handle if necessary
   */
  public function __destruct() {
    $this -> free();
  }

  /**
   * Free the result handle if necessary
   */
  public function free() {
    if ($this -> mHandle) {
      $this -> mDb -> freeResult($this -> mHandle);
      $this -> mHandle = NULL;
      #$this -> dbg('Free '.$this -> mSql);
    }
  }

  /**
   * Execute an SQL statement and remember the result handle
   *
   * @param string $aSql
   * @return resource MySQL result handle
   */
  public function query($aSql, $aWithMessage = TRUE) {
    $this -> free();
    $this -> mSql = $aSql;
    $this -> mHandle = $this -> mDb -> query($aSql, $aWithMessage);
    return $this -> mHandle;
  }

  public function getInsertId() {
    return $this -> mDb -> getInsertId();
  }

  /**
   * Get all result rows as an array of hashes (access using $r[0]['field'] or $r[$id]['field'])
   *
   * @param string $aKey Optional field to use as the array index (usually an ID)
   * @return array
   */
  public function getAssocs($aKey = '' ) {
    $lRet = array();
    if (empty($aKey)) {
      while ($lRow = $this -> mDb -> getAssoc($this -> mHandle)) {
        $lRet[] = $lRow;
      }
    } else {
      while ($lRow = $this -> mDb -> getAssoc($this -> mHandle)) {
        $lKey = $lRow[$aKey];
        $lRet[$lKey] = $lRow;
      }
    }
    return $lRet;
  }

  /**
   * Get a comma-separated list of a single field of all result rows
   *
   * @param string $aKey Name of the field to use for every row
   * @return string Comma-separated list of field values
   */
  public function getImplode($aKey = 'id' ) {
    $lRet = '';
    while ($lRow = $this -> mDb -> getAssoc($this -> mHandle)) {
      $lRet.= $lRow[$aKey].',';
    }
    $lRet = rtrim($lRet, ',');
    return $lRet;
  }

  /**
   * Get all result rows as an array (access using $r[0]->field or $r[$id]->field)
   *
   * @param string $aKey Optional field to use as the array index (usually an ID)
   * @return array
   */
  public function getObjects($aKey = '' ) {
    $lRet = array();
    if (empty($aKey)) {
      while ($lRow = $this -> mDb -> getObject($this -> mHandle)) {
        $lRet[] = $lRow;
      }
    } else {
      while ($lRow = $this -> mDb -> getObject($this -> mHandle)) {
        $lKey = $lRow -> $aKey;
        $lRet[$lKey] = $lRow;
      }
    }
    return $lRet;
  }

  /**
   * Get one result row as a numeric array (access value with $r[0])
   *
   * @return boolean|array False if no valid row available, array otherwise
   */
  public function getArray() {
    return $this -> mDb -> getArray($this -> mHandle);
  }

  /**
   * Get a result row as a hash (access value with $r['field'])
   *
   * @return boolean|array False if no valid row available, array otherwise
   */
  public function getAssoc() {
    return $this -> mDb -> getAssoc($this -> mHandle);
  }

  /**
   * Get a result row as a data object (access value with $r->field)
   *
   * @return boolean|object False if no valid row available, object otherwise
   */
  public function getObject() {
    return $this -> mDb -> getObject($this -> mHandle);
  }


  /**
   * Get the number of rows that were affected by an update/delete/replace...
   *
   * @return integer Number of affected rows
   */
  public function getAffectedRows() {
    return $this -> mDb -> getAffectedRows();
  }

  /**
   * Get a result row as a hash data object
   *
   * @return boolean|CCor_Dat False if no valid row available, hash data object otherwise
   */
  public function getDat() {
    $lRow = $this -> mDb -> getAssoc($this -> mHandle);
    if (!$lRow) {
      return FALSE;
    }
    $lRet = new CCor_Dat();
    $lRet -> assign($lRow);
    return $lRet;
  }

  /**
   * Magic method to enable using the query object in foreach-loops
   *
   * @see IteratorAggregate::getIterator()
   */
  public function getIterator() {
    return new CCor_QryIte($this -> mHandle, $this -> mDb);
  }

  /**
   * Execute a query in the default DB and return the first row as a space-separated list
   *
   * @todo merge with getArrImp
   * @see CCor_Qry::getArrImp
   * @param string $aSql
   * @return string|boolean Space-separated list of values
   */
  public static function getArr($aSql) {
      $lQry = new self($aSql);
    if ($lRet = $lQry -> getArray()) {
      $lRes = implode(' ', $lRet);
      return trim($lRes);
    } else {
      return FALSE;
    }
  }

  /**
   * Execute a query in the default DB and return the first row as a comma-separated list
   *
   * @param string $aSql SQL statement to execute
   * @return string|boolean Comma-separated list of values or false in case of no valid results
   */
  public static function getArrImp($aSql) {
    $lQry = new self($aSql);
    if ($lRet = $lQry -> getArray()) {
      $lRes = implode(',', $lRet);
      return trim($lRes);
    } else {
      return FALSE;
    }
  }

  /**
   * Execute a query in the default DB and return the first value of the first result row
   *
   * @param string $aSql SQL statement to execute
   * @return mixed|boolean The value of the first field of the first result row
   */
  public static function getStr($aSql) {
    $lQry = new self($aSql);
    if ($lRet = $lQry -> getArray()) {
      return $lRet[0];
    } else {
      return FALSE;
    }
  }

  /**
   * Execute a query in the default DB and return the first value of the first result row as an integer
   *
   * @param string $aSql SQL statement to execute
   * @return integer|boolean The value of the first field of the first result row
   */
  public static function getInt($aSql) {
    $lRet = self::getStr($aSql);
    return ($lRet === FALSE) ? FALSE : intval($lRet);
  }

  /**
   * Execute a query and return the result handle
   *
   * @param string $aSql
   * @return ressource The MySQL Handle of the result
   */
  public static function exec($aSql) {
    $lQry = new self();
    return $lQry -> query($aSql);
  }
  
  /**
   * return the Columns names of an DB table.
   * @param unknown $aTableName
   * @return boolean|multitype:unknown
   */
  
  public function getTableColumns($aTableName) {
    if (empty($aTableName)) return false;
    $lSql = 'SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '.esc(CCor_Cfg::get('db.name')).' AND TABLE_NAME = "'.$aTableName.'";';
    $lQry = new self($lSql);
    $lColNames = array();
    foreach ($lQry as $lColName) {
      $lColNames[] = $lColName['COLUMN_NAME'];
    }
    return $lColNames;
  }

}