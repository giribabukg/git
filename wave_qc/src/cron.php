<?php
function dispatch() {
  // execute all actions with only functional namespace
  
  if (PHP_SAPI != 'cli') {
    die('Service only callable using CLI');
  }

  if (PHP_OS == 'WINNT') {
    $lCli = 'php-win.exe %s > nul';
    $lExecFunc = function($aCmd) {
      if (class_exists('COM')) {
        $lShell = new COM('WScript.Shell');
        $lShell->Run($aCmd, 0, false);
      } else {
        exec('start /B '.$aCmd);
      }
    };
  } else {
    $lCli = 'php %s > /dev/null';
    $lExecFunc = function($aCmd) {
      exec($aCmd);
    };
  }

  define('DS',  '/');
  define('PS',  PATH_SEPARATOR);
  
  define('CUST_ID', 76);          // Enter Customer ID for this installation
  define('LANGUAGE', 'en');       // Enter Default Language Code
  define('CUST_PATH', 'cust'.DS);
  
  session_start();
  require_once 'inc/cor/include.php';
  
  $lNow = date('H:i:s');
  
  $lDat = new CCor_Date();
  $lDow = $lDat -> getDow();
  $lDowFlag = 1 << $lDow;
  
  $lSql = 'SELECT DISTINCT(s.mand) FROM al_sys_svc s, al_sys_mand m ';
  $lSql.= 'WHERE (s.mand=m.id OR s.mand=0) ';
  $lSql.= 'AND (flags &1) ';
  $lSql.= 'AND (dow & '.$lDowFlag.') ';
  $lSql.= 'AND (running="N") ';
  $lSql.= 'AND (from_time<="'.$lNow.'") ';
  $lSql.= 'AND (to_time>="'.$lNow.'") ';
  $lSql.= 'ORDER BY s.mand';
  
  $lQry = new CCor_Qry($lSql);
  foreach ($lQry as $lRow) {
    $lMand = $lRow['mand'];
    $lCmd = sprintf($lCli, ' -f cli.php act=svc.run mid='.$lMand);
    $lExecFunc($lCmd);
  }
  exit;
}
dispatch();