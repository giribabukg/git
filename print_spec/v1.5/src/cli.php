<?php
function dispatch() {
  // execute all actions with only functional namespace
  
  if (PHP_SAPI != 'cli') {
    die('Service only callable using CLI');
  }

  define('DS',  '/');
  define('PS',  PATH_SEPARATOR);
  
  define('CUST_ID', 76);          // Enter Customer ID for this installation
  define('LANGUAGE', 'en');       // Enter Default Language Code
  define('CUST_PATH', 'cust'.DS);
  
  session_start();
  require_once 'inc/cor/include.php';

  CCor_Cli::getInstance() -> dispatch();
}
dispatch();