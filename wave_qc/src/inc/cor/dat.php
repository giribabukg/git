<?php
/**
 * Core: Data
 *
 * All purpose array-like dataholder
 * Object provides array or object notation
 * acess to member variables
 *
 * @package    COR
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 9764 $
 * @date $Date: 2015-07-21 17:55:00 +0800 (Tue, 21 Jul 2015) $
 * @author $Author: ahanslik $
 */
class CCor_Dat extends CCor_Obj implements IteratorAggregate,ArrayAccess,Countable {

  protected $mVal = array();
  protected $mCheckMethod = FALSE;
  protected $mLoCase      = FALSE;

  /**
   * No constructor so decendent classes can be Singletons
   */

  public function setLoCase($aFlag = TRUE) {
    $this -> mLoCase = $aFlag;
  }

  public function setCheckMethod($aFlag = TRUE) {
    $this -> mCheckMethod = $aFlag;
  }

  public function assign($aArray) {
    $this -> mVal = array();
    $this -> addValues($aArray);
  }

  public function assignArray($aArray) {
    if ($this -> mLoCase) {
      $this -> mVal = array();
      $this -> addValues($aArray);
    } else {
      $this -> mVal = $aArray;
    }
  }

  public function addValues($aArray) {
    if (0 !== count($aArray))
      foreach ($aArray as $lKey => $lValue) {
        $this -> __set($lKey, $lValue);
      }
  }

  public function toArray() {
    return $this -> mVal;
  }

  /**
   * Returns an ArrayIterator to iterate over value array
   * Necessary to implement IteratorAggregate
   *
   * @access public
   * @retun ArrayIterator The value iterator
   */

  public function getIterator() {
    return new ArrayIterator($this -> mVal);
  }

  /**
   * Returns the value of a property
   *
   * Usually, the value of that property will be retrieved from the mVal value
   * array using doGet($aKey). However, this can be overriden by declaring a
   * function get[$aKey].
   *
   * @access protected
   * @param string $aKey Name of the property to retrieve
   * @return mixed The value of that property or NULL if property does not exist
   */

  public function __get($aKey) {
    if ($this->mCheckMethod) {
      $lMethod = 'get'.$aKey;
      if ($this -> hasMethod($lMethod)) {
        return $this -> $lMethod();
      }
    }
    return $this -> doGet($aKey);
  }

  /**
   * Returns the value of a property
   *
   * This will return the value if there is no h.
   *
   * @access protected
   * @param string $aKey Name of the property to retrieve
   * @return mixed The value of that property or NULL if property does not exist
   */

  protected function doGet($aKey) {
    $lKey = ($this -> mLoCase) ? strtolower($aKey) : $aKey;
    if (isset($this -> mVal[$lKey])) {
      return $this -> mVal[$lKey];
    } else {
      return NULL;
    }
  }

  public function __set($aKey, $aValue) {
    if ($this -> mCheckMethod) {
      $lMethod = 'set'.$aKey;
      if ($this -> hasMethod($lMethod)) {
        $this -> $lMethod($aValue);
        return;
      }
    }
    $this -> doSet($aKey, $aValue);
  }

  protected function doSet($aKey, $aValue) {
    $lKey = ($this -> mLoCase) ? strtolower($aKey) : $aKey;
    $this -> mVal[$lKey] = $aValue;
  }

  public function __call($aMethod, $aArgs) {
    // will only be called if getter/setter method not available
    // therefore, there is no need to call __set/__get
    #$lMethod = strtolower($aMethod);
    $lMethod = $aMethod;
    $lPrefix = substr($lMethod,0,3);
    if ($lPrefix == 'set') {
      $lKey = substr($lMethod, 3);
      $this -> doSet($lKey, $aArgs[0]);
    } else if ($lPrefix == 'get') {
      $lKey = substr($lMethod, 3);
      return $this -> doGet($lKey);
    }
  }

  // ArrayAccess interface

  public function offsetExists($aKey) {
    $lKey = ($this -> mLoCase) ? strtolower($aKey) : $aKey;
    return isset($this -> mVal[$lKey]);
  }

  public function offsetGet($aKey) {
    return $this -> __get($aKey);
  }

  public function offsetSet($aKey, $aValue) {
    $this -> __set($aKey, $aValue);
  }

  public function offsetUnset($aKey) {
    $lKey = ($this -> mLoCase) ? strtolower($aKey) : $aKey;
    unset($this -> mVal[$lKey]);
  }

  // Countable interface

  public function count() {
    return count($this -> mVal);
  }

}