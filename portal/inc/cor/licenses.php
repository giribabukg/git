<?php
/**
 * Core: Main Licenses File/Object
 *
 * SINGLETON
 * Provides access to license parameters
 * like how many mandators, Customer ID
 * Read-only access for security reasons.
 * Singleton providing either static or non-static
 *
 * @package COR
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 2017 $
 * @date $Date: 2013-09-12 02:16:57 +0200 (Thu, 12 Sep 2013) $
 * @author $Author: gemmans $
 */


/**
 * Main Licenses Object
 *
 * Singleton providing either static or non-static
 *
 * --> Funktionalität abschalten
 * auslesen in inc/cor/usr.php
 *   public function canDo($aKey, $aLvl) {
 *    if (CCor_Licenses::get($aKey)) { ...
 * --> Link zum Tool/Funktion abschalten
 * z.B. im inc/htm/mainmenu.php
 *
 * @package core
 */
final class CCor_Licenses extends CCor_Obj {

  private static $mInstance = NULL;
  private $mVal = array();

  private function __construct() {
    if (!defined('CUST_ID')) exit;

    $lSyspref = CCor_Res::get('sysrights');
    foreach($lSyspref as $lPref => $lLic) {// $Pref => TRUE
      $this -> mVal[$lPref] = $lLic;
    }
    //-- explizites Einschalten, da die Rechteverwaltung nicht "ganz sauber funktioniert"
    // bzw. kommt hier später noch Code aus der Cfg rein
    $lAllSrc = CCor_Cfg::get('all-jobs');
    array_unshift($lAllSrc, 'pro');
    array_unshift($lAllSrc, 'sku');
    foreach ($lAllSrc as $ls) {
      $this -> mVal['arc-'.$ls] = TRUE; //--
      $this -> mVal['arc-'.$ls.'-his'] = TRUE; //--
      $this -> mVal['job-'.$ls] = TRUE; //--
      $this -> mVal['job-'.$ls.'-his'] = TRUE; //--
      $this -> mVal['arc-'.$ls] = TRUE; //--
      $this -> mVal['arc-'.$ls.'-sku'] = TRUE; //--
      $this -> mVal['job-'.$ls] = TRUE; //--
      $this -> mVal['job-'.$ls.'-sku'] = TRUE; //--
    }
    //-- Archiv
    $this -> mVal['job-pro-sub'] = TRUE; //--
    //-- Jobs
    $this -> mVal['job-apl'] = TRUE; //--
    $this -> mVal['job.crp.chg'] = TRUE; //--
    $this -> mVal['job-fil'] = TRUE; //--
    $this -> mVal['job-his'] = TRUE; //--
    $this -> mVal['job-pdf'] = TRUE; //--
    $this -> mVal['job-pro-sub'] = TRUE; //--
    $this -> mVal['job-sku-sub'] = TRUE; //--
    $this -> mVal['job-wec'] = TRUE; //--
    $this -> mVal['job-wec-id'] = TRUE; //--
    $this -> mVal['job-wec-pdf'] = TRUE; //--
    $this -> mVal['wec.view'] = TRUE; //--

    $this -> mVal['ast'] = TRUE; //--
    $this -> mVal['crp-sta'] = TRUE; //--
    $this -> mVal['fie-learn'] = TRUE; //--
    $this -> mVal['jfl'] = TRUE; //--
    $this -> mVal['gpm'] = TRUE; //--
    $this -> mVal['rep'] = TRUE; //--
    $this -> mVal['svc'] = TRUE; //--
    $this -> mVal['sys-log'] = TRUE; //--
    $this -> mVal['sys-mail'] = TRUE; //--

    ////////////////////////////////////////////////////////
    // Aufzählung aller Module, die lizensiert werden können => FALSE

    //-- Migrationstools
    $this -> mVal['mig'] = FALSE;
    //-- Reporting
#    $this -> mVal['rep'] = FALSE;
    //-- Jobs: Kosten
    $this -> mVal['job-cos'] = FALSE;


    ////////////////////////////////////////////////////////
    //
    // AB hier kommt die LIZENSIERUNG!
    //
    // Aufzählung aller Module, die lizensiert wurden => TRUE
    if(file_exists(CUST_PATH.'inc/cor/licenses.php')) {

      require_once(CUST_PATH.'inc/cor/licenses.php');


     #  $this -> mVal['lang'] = '';


#echo '<pre>---src/inc/cor/licenses.php---'.CUST_ID;var_dump($this -> mVal,'#############');echo '</pre>';
    }
  }

  /**
   * Singleton getInstance method
   *
   * @return CCor_Licenses
  **/
  public static function getInstance(){
    if (NULL === self::$mInstance) {
      self::$mInstance = new self();
    }
    return self::$mInstance;
  }

  /**
   * Get a configuration variable
   *
   * @param string $aKey Unique name (e.g. 'db.host') of config var
   * @param mixed $aStd Default value to return if config var does not exist
   * @return mixed Value of the config variable or null if key is not set
  **/
  public function getVal($aKey) {
    $lRet = '';
    if (isset($this -> mVal[$aKey])) {
      $lRet = $this -> mVal[$aKey];
    } else {
      $lRet = FALSE;
      $this -> msg(lan('lib.noLicense').' '.$aKey, mtUser, mlFatal);//'No License'
    }
    return $lRet;
  }

  /**
   * Get the configuration value based on a key
   *
   * @param string $aKey Unique name (e.g. 'db.host') of config var
   * @return mixed Value of the config variable or null if key is not set
  **/
  public static function get($aKey) {
    $lLic = self::getInstance();
    return $lLic -> getVal($aKey);
  }

  private final function __clone() {}

  // set "Abgeschaltet", da nur Auslesen möglich sein soll
  /**
   * Set a configuration variable
   *
   * @param string $aKey Unique name (e.g. 'db.host') of config var
   * @param mixed $aVal Default value to return if config var does not exist
  **/
  private final function setVal($aKey, $aVal) {
   # $this -> mVal[$aKey] = $aVal;
  }

  /**
   * Set the configuration value based on a key
   *
   * @param string $aKey Unique name (e.g. 'db.host') of config var
  **/
  private final function set($aKey, $aVal) {
   # $lLic = self::getInstance();
   # $lLic -> setVal($aKey, $aVal);
  }

  }