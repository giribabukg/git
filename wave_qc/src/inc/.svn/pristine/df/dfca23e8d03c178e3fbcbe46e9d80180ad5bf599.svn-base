<?php
/**
 * System Session Access
 *
 * SINGLETON
 *
 * @package    Core
 * @subpackage Request
 * @copyright  2004-2009 The Quick Brown Fox GmbH <info@qbf.de>
 * @link       http://www.qbf.de
 */

/**
 * System Session Access
 *
 * Retrieve and store variables in its own namespace.
 *
 * @package    Core
 * @subpackage Request
 */
class CCor_Sys extends CCor_Dat {

  /**
   * Singleton Instance Variable
   *
   * @var CCor_Sys Singleton Instance
   */
  private static $mInstance;

  /**
   * Namespace Prefix
   *
   * @var string Namespace Prefix
   */
  private $mKeyPrefix = '.sys.';

  /**
   * Private constructor, please use getInstance() instead of new object()
   *
   * Retrieve matching vars from $_SESSION into internal data array
   */
  private function __construct() {
    $lPfx = CCor_Cfg::get('cust.usr', 'customer');
    $lPfx .= '.';
    $lPfx .= CCor_Cfg::get('cust.pfx', 'pfx');
    $this -> mKeyPrefix = $lPfx.$this -> mKeyPrefix;

    $lSiz = strlen($this -> mKeyPrefix);
    if (isset($_SESSION)) {
      foreach ($_SESSION as $lKey => $lVal) {
        if (substr($lKey, 0, $lSiz) == $this -> mKeyPrefix) {
          $this -> mVal[substr($lKey, $lSiz)] = $lVal;
        }
      }
    }
#    echo '<pre>---sys.php---';var_dump($_SESSION,'#############');echo '</pre>';
  }

  /**
   * Singleton getInstance method
   *
   * @return CCor_Sys
   */
  public static function getInstance() {
    if (NULL === self::$mInstance) {
      self::$mInstance = new self();
    }
    return self::$mInstance;
  }

  /**
   * Add the namespace prefix to a given key
   *
   * @param string $aKey Session key to add the prefix to
   * @return string Key with namespace prefix
   */
  private function prefix($aKey) {
    return $this -> mKeyPrefix.strtolower($aKey);
  }

  /**
   * Set a session value
   *
   * Set the value in the internal array and $_SESSION at the same time. Will be
   * called whenever $obj[$aKey] = 'a value'; is done because of ArrayAccess
   * interface in CCor_Dat.
   *
   * @param string $aKey Name of the session variable to set
   * @param mixed $aValue Value to set
   */
  protected function doSet($aKey, $aValue) {
    parent::doSet($aKey, $aValue);
    $_SESSION[$this -> prefix($aKey)] = $aValue;
  }

  /**
   * Unset a session value
   *
   * Unset the value in the internal array and $_SESSION at the same time. Will
   * be called whenever unset($obj[$aKey]) is done because of ArrayAccess
   * interface in CCor_Dat.
   *
   * @param string $aKey Name of the session variable to unset
   */
  public function offsetUnset($aKey) {
    $lKey = strtolower($aKey);
    unset($this -> mVal[$lKey]);
    unset($_SESSION[$this -> prefix($lKey)]);
  }

  /**
   * Get a session value or a default value if the variable is not set
   *
   * Get a session value. If the variable is not set yet, do not throw a notice
   * but return a default value instead.
   *
   * @param string $aKey Name of the session variable to get
   * @param string $aStd Default value to return if session variable is not set
   * @return mixed Value of session variable or default if var is not set
   */
  public function get($aKey, $aStd = NULL) {
    if (isset($this -> mVal[$aKey])) {
      return $this -> mVal[$aKey];
    } else {
      return $aStd;
    }
  }

}