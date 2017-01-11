<?php
/*
 * Central bootstrap index.php file
 *
 * @copyright Copyright (c) 5Flow GmbH (http://www.5flow.eu)
 * @version $Rev: 845 $
 * @date $Date: 2013-02-11 14:36:39 +0100 (Mon, 11 Feb 2013) $
 * @author $Author: gemmans $
*/

function dispatch() {
  // execute all actions with only functional namespace

  define('DS',  '/');
  define('PS',  PATH_SEPARATOR);
  
  define('CUST_ID', 76);          // Enter Customer ID for this installation
  define('LANGUAGE', 'en');       // Enter Default Language Code
  define('CUST_PATH', 'cust'.DS);
  
  $revision = '$Rev: 845 $';
  $date     = '$Date: 2013-02-11 14:36:39 +0100 (Mon, 11 Feb 2013) $';
  $version  = substr($revision, 6, -2);
  $versiondate = substr($date, 7, 10);
  define('VERSION', $version);
  define('VERSIONDATE', $versiondate);
  
  session_start();
  require_once 'inc/cor/include.php';

  CCor_Fct::getInstance() -> dispatch();
}
dispatch();