<?php
/**
 * Core: Mysql Connection to any database (not a singleton)
 *
 * @package    COR
 * @copyright  5Flow GmbH
 * @version $Rev: 6 $
 * @date $Date: 2012-02-21 09:50:56 +0100 (Di, 21 Feb 2012) $
 * @author $Author: gemmans $
 */
class CCor_Anysql extends CCor_Sql {

  public function __construct($aConfig = null) {
    $this -> mQueryCount = 0;
    if (!empty($aConfig)) {
      $this->setConfig($aConfig);
    }
  }

  public function setConfig($aConfig) {
    $this -> mHost = (isset($aConfig['host'])) ? $aConfig['host'] : 'localhost';
    $this -> mPort = (isset($aConfig['port'])) ? $aConfig['port'] : 3306;
    $this -> mUser = (isset($aConfig['user'])) ? $aConfig['user'] : 'root';
    $this -> mPass = (isset($aConfig['pass'])) ? $aConfig['pass'] : 'pass';
    if (isset($aConfig['name'])) {
      $this->mName = $aConfig['name'];
    }
    $this -> mHandle = null; // reset in case we change host or db
    $this -> mQueryCount = 0;
  }

}