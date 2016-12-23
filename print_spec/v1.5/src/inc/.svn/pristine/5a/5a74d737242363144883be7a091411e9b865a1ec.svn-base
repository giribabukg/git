<?php
/**
 * SQL Query Iterator
 *
 * Supplies iteration capabilities to a given database result handle. Since the
 * row object returned by current() and next() is a CData-Object, the data
 * fields can be accessed as array-members, object variables or via get/set-
 * methods.
 */

class CInc_Cor_QryIte extends CCor_Obj implements Iterator {

  protected $mHandle;
  protected $mDb;
  protected $mKey;
  protected $mRow;

  /**
   * Constructor, initialize result handle and database object
   * 
   * @param ressource $aHandle Database result handle to use for the iteration
   * @param object $aDb Database object to use
   */  
   
  public function __construct($aHandle, $aDb = NULL) {
    $this -> mHandle = $aHandle;
    $this -> mDb = $aDb;
  }
  
  /**
   * Rewind - reset iterator to the first dataset record
   */

  public function rewind() {
    $this -> mKey = 0;
    @mysql_data_seek($this -> mHandle, 0); 
    $this -> mRow = $this -> next();
  }

  /**
   * Return the current dataset record or false if no further records are
   * available
   * 
   * @return mixed Either false or a CData object containing the values of the
   * current record
   */
   
  public function current() {
    return $this -> mRow;
  }

  /**
   * Return the key of the record currently pointed at by the iterator
   * 
   * @return integer The current key
   * 
   */
  public function key() {
    return $this -> mKey;
  }

  public function next() {
    $this -> mKey++;
    $lRow = $this -> mDb -> getAssoc($this -> mHandle);
    if (!$lRow) {
      $this -> mRow = FALSE;
      return FALSE;
    }  
    
    $this -> mRow = new CCor_Dat();
    $this -> mRow -> assignArray($lRow);
    return $this -> mRow;
  }

  public function valid() {
    return $this -> mRow !== false;
  }  
}