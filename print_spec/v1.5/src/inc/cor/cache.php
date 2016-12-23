<?php
class CInc_Cor_Cache extends CCor_Obj {

  private static $mInstance = NULL;

  private static $mAppKey;
  private static $mBackend;

/* @var $mCache Zend_Cache */
  private $mCache;
  private $mSub;

  private function __construct($aSub = '') {
    if (!self::$mAppKey) {
      self::$mAppKey  = CCor_Cfg::get('environment').'_'.CCor_Cfg::get('mand.key', CUST_ID);
      self::$mBackend = CCor_Cfg::get('cache.backend', 'file');
    }
    $this -> createCache($aSub);
  }

  private function __clone() {
  }

  /**
   * Singleton Method - Create a Cache Object (or return the existing instance) for a Namespace
   *
   * Each Subdirectory/Namespace will lead to it's own instance
   *
   * @param string $aSub Subdirectory/Namspace for Cache files
   * @return CCor_Cache
   */
  public static function getInstance($aSub = ''){
    if (!isset(self::$mInstance[$aSub])) {
      self::$mInstance[$aSub] = new self($aSub);
    }
    return self::$mInstance[$aSub];
  }

  private function createCache($aSub) {
    require_once 'Zend/Cache.php';
    $lFront = array('lifetime' => 7200, 'automatic_serialization' => true);
    $lBack = array();
    if ('File' == self::$mBackend) {
      if ('' != $aSub) {
        $lBack['cache_dir'] = 'tmp'.DS.$aSub;
      } else {
        $lBack['cache_dir'] = 'tmp';
      }
    }
    $this -> mCache = Zend_Cache::factory('Core', self::$mBackend, $lFront, $lBack);
  }

  public function get($aKey) {
    return $this -> mCache -> load(self::$mAppKey.'_'.$aKey);
  }

  public function set($aKey, $aVal) {
    $this -> mCache -> save($aVal, self::$mAppKey.'_'.$aKey);
  }

  public function clear($aKey) {
    $this -> mCache -> remove(self::$mAppKey.'_'.$aKey);
  }

  public function clean($aMode = Zend_Cache::CLEANING_MODE_OLD) {
    $this -> mCache -> clean($aMode);
  }

  public static function getStatic($aKey) {
    $lSelf = self::getInstance();
    return $lSelf -> get($aKey);
  }

  public static function setStatic($aKey, $aValue) {
    $lSelf = self::getInstance();
    return $lSelf -> set($aKey, $aValue);
  }

  public static function clearStatic($aKey) {
    self::getInstance()->clear($aKey);
  }

}