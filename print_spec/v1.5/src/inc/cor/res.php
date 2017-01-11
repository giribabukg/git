<?php
/**
 * Core: Resource Repository
 *
 * SINGLETON
 * Retrieve lists from a database, external sources etc. using various plugins.
 * Provide a uniform way how to get lists for html-selects, helptable entries
 * etc. to allow for better parameterization of form fields, columns and so on.
 * The client object does not have to know how this data is retrieved.
 * Each list will have its own key (called domain or "dom") that can be used to
 * create the corresponding plugin and retrieve the data array.
 * Several helper functions can be used to retrieve only specific fields from
 * the resource data, e.g. retrieving an associative array(user-Id => name) from
 * the DB with extract()
 *
 * @package    COR
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 4961 $
 * @date $Date: 2014-07-02 05:59:42 +0800 (Wed, 02 Jul 2014) $
 * @author $Author: gemmans $
 */
final class CCor_Res extends CCor_Obj {

  /**
   * Singleton Instance Variable
   *
   * @var CCor_Res Singleton Instance
   */
  private static $mInstance = NULL;

  /**
   * Array of instantiated plugins
   *
   * @var array Array of already instantiated resource plugins
   */
  private $mPlugins = array();

  /**
   * Private constructor, please use getInstance() instead of new object()
   */
  private function __construct() {
  }

  /**
   * Do not allow cloning
   */
  private final function __clone() {}

  /**
   * Singleton getInstance method
   *
   * @return CCor_Res
   */
  public static function getInstance() {
    if (null === self::$mInstance) {
      self::$mInstance = new self();
    }
    return self::$mInstance;
  }

  /**
   * Retrieve the result from specified resource
   *
   * @param string $aDom Key (or "domain") of resource to retrieve
   * @param mixed $aParam (Optional) parameters (e.g. filter) to pass to plugin
   * @return array
   */
  public function getResult($aDom, $aParam = NULL) {
    error_log('.....CCor_Res.....getResult.....$aDom.......'.var_export($aDom,true)."\n",3,'logggg.txt');
    error_log('.....CCor_Res.....getResult.....$this -> mPlugins[$aDom]......'.var_export($this -> mPlugins[$aDom],true)."\n",3,'logggg.txt');
    error_log('.....CCor_Res.....getResult.....$aParam.......'.var_export($aParam,true)."\n",3,'logggg.txt');
    if (!isset($this -> mPlugins[$aDom])) {
      $lCls = 'CCor_Res_'.ucfirst($aDom);
      if (!class_exists($lCls, true)) {
        $this->msg('Unknown Resource '.$aDom, mtDebug, mlError);
        return array();
      }
      $lPlug = new $lCls();
      $this -> mPlugins[$aDom] = & $lPlug;
    }
    error_log('.....CCor_Res.....getResult.....Return value.......'.var_export($this -> mPlugins[$aDom] -> get($aParam),true)."\n",3,'logggg.txt');
    return $this -> mPlugins[$aDom] -> get($aParam);
  }

  /**
   * Static version of getResult provided as convenience method
   *
   * @param string $aDom Key (or "domain") of resource to retrieve
   * @param mixed $aParam (Optional) parameters (e.g. filter) to pass to plugin
   * @return array
   */
  public static function get($aDom, $aParam = NULL) {
    $lRes = self::getInstance();
    return $lRes -> getResult($aDom, $aParam);
  }

  /**
   * Get a key/value array of specified keyfield and valuefield
   *
   * @access public
   * @param string $aKeyField The field from the result set to become the array key
   * @param string $aValField The field from the result set to become the array value
   * @param string $aDom The Ressource key (e.g. usr for users)
   * @param mixed $aParam Optional filter criteria
   */
  public static function extract($aKeyField, $aValField, $aDom, $aParam = NULL) {
    $lRes = self::getInstance();
    $lArr = $lRes -> getResult($aDom, $aParam);
	
    $lRet = array();
    if (!empty($lArr)) {
      foreach ($lArr as $lRow) {
        $lKey = $lRow[$aKeyField];
        $lVal = $lRow[$aValField];
        $lRet[$lKey] = $lVal;
      }
    }
    return $lRet;
  }

  /**
   * Get full rows with specified field as key
   *
   * @access public
   * @param string $aKeyField The field from the result set to become the array key
   * @param string $aDom The Ressource key (e.g. usr for users)
   * @param mixed $aParam Optional filter criteria
   */
  public static function getByKey($aKeyField, $aDom, $aParam = NULL) {
    $lRes = self::getInstance();
    $lArr = $lRes -> getResult($aDom, $aParam);
    $lRet = array();
    if (!empty($lArr)) {
      foreach ($lArr as $lRow) {
        $lKey = $lRow[$aKeyField];
        $lRet[$lKey] = $lRow;
      }
    }
    return $lRet;
  }

}