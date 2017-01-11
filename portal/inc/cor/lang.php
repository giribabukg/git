<?php
class CInc_Cor_Lang extends CCor_Obj {

  private static $mInstance;
  private $mLocale;
  private $mTrans;

  private function __construct($aLocale) {
    $this -> mLocale = $aLocale;
    $lArr = CCor_Res::get('lang', $aLocale);
    require_once 'Zend/Translate.php';
    $this -> mTrans = new Zend_Translate(Zend_Translate::AN_ARRAY, $lArr);
  }

  public function __destruct() {
    self::$mInstance[$this -> mLocale] = NULL;
  }

  /**
   * Singleton getInstance method
   *
   * @param string $aLocale Language code (e.g. en or de)
   * @return CCor_Lang
   */

  public static function getInstance($aLocale = 'en') {
    if (!isset(self::$mInstance[$aLocale]) or (NULL === self::$mInstance[$aLocale])) {
      self::$mInstance[$aLocale] = new self($aLocale);
    }
    return self::$mInstance[$aLocale];
  }

  private function __clone() {}

  public function get($aKey) {
    if (!$this -> mTrans -> isTranslated($aKey)) {
      $this -> dbg('Missing '.$this -> mLocale.' translation: '.$aKey, mlError);
      $lSql = 'INSERT IGNORE INTO al_sys_lang SET code="'.addslashes($aKey).'"';
      CCor_Qry::exec($lSql);
      if ('de' != $this -> mLocale) {
        return self::getStatic($aKey, 'de');
      }
    }
    return $this -> mTrans -> _($aKey);
  }

  public static function getStatic($aKey, $aLocale = NULL, $aDefault = NULL) {
    $lLocale = (empty($aLocale)) ? LAN : $aLocale;
    $lLan = self::getInstance($lLocale);

    if ($lLan -> get($aKey)) {
      $lRes = $lLan -> get($aKey);
    } else {
      $lRes = isset($aDefault) ? $aDefault : $aKey;
    }

    return $lRes;
  }
}