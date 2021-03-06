<?php
/**
 * Core: Session
 *
 * SINGLETON
 *
 * @package    COR
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 10335 $
 * @date $Date: 2015-09-11 09:39:48 +0200 (Fri, 11 Sep 2015) $
 * @author $Author: pdohmen $
 */
class CCor_Ses extends CCor_Dat {

  private static $mInstance;

  private $mKeyPrefix = '.ses.';

  private function __construct() {
    $lPfx = CCor_Cfg::get('cust.usr', 'customer');
    $lPfx .= '.';
    $lPfx .= CCor_Cfg::get('cust.pfx', 'pfx');
    $this -> mKeyPrefix = $lPfx.$this -> mKeyPrefix;

    $lSiz = strlen($this -> mKeyPrefix);
    foreach ($_SESSION as $lKey => $lVal) {
      if (substr($lKey, 0, $lSiz) == $this -> mKeyPrefix) {
        $this -> mVal[substr($lKey, $lSiz)] = $lVal;
      }
    }
    //echo '<pre>---ses.php---';var_dump($_SESSION,'#############');echo '</pre>';
  }

  /**
   * @return CCor_Ses
   */

  public static function getInstance() {
    if (NULL === self::$mInstance) {
      self::$mInstance = new self();
    }
    return self::$mInstance;
  }

  private function prefix($aKey) {
    return $this -> mKeyPrefix.strtolower($aKey);
  }

  protected function doSet($aKey, $aValue) {
    parent::doSet($aKey, $aValue);
    $_SESSION[$this -> prefix($aKey)] = $aValue;
  }

  public function offsetUnset($aKey) {
    $lKey = strtolower($aKey);
    unset($this -> mVal[$lKey]);
    unset($_SESSION[$this -> prefix($lKey)]);
  }

  /**
   * @method killSession
   * @author pdohmen
   * @description Session of the given user will be destroyed
   */
  public function killSession($aUId) {
      //Save Own Session
      $lSession = session_id();
      session_commit();

      $lSql = "SELECT session_id FROM al_usr_login WHERE user_id = '".$aUId."';";
      $lQry = new CCor_Qry($lSql);

      //Destroy all found sessions
      foreach($lQry as $lRow){
        session_id($lRow["session_id"]);
        session_start();
        session_destroy();
        session_commit();
      }

      $lSql = "DELETE FROM `al_usr_login` WHERE  `session_id`='".$lRow["session_id"]."';";
      $lQry = new CCor_Qry($lSql);

      //Restore own session
      session_id($lSession);
      session_start();
      session_commit();
  }
}