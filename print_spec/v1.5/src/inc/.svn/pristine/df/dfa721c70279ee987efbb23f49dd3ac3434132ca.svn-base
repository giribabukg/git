<?php
include_once 'inc/htm/lib.php';

class CInc_Cor_Cli extends CCor_Obj {

  private static $mInstance = NULL;

  private function __construct() {}

  private function __clone() {}

  public static function getInstance() {
    if (null === self::$mInstance) {
      self::$mInstance = new self();
    }
    return self::$mInstance;
  }

  function dispatch() {
    if (PHP_SAPI != 'cli') {
      die('Service only callable using CLI');
    }
    global $argv;

    $lReq = new CCor_Req();
    $lReq -> loadArgv();

    $lModAct = $lReq -> act;
    $lModMid = $lReq -> mid;

    $lConsole = true;
    if (empty($lModAct)) {
      $lReq -> loadRequest();

      $lModAct = $lReq -> act;
      $lModMid = $lReq -> mid;

      if (empty($lModAct)) {
        echo "empty Act";
        return;
      } else {
        $lConsole = false;
      }
    }
    if ($lConsole) {
      echo 'Action: '.$lModAct.LF.$lModMid.LF;
    }

    $lArr = explode('.', $lModAct, 2);
    if (count($lArr) == 1) {
      $lMod = $lModAct;

      $lArr = explode('-', $lMod);
      $lCls = '';
      foreach($lArr as $lVal) {
        $lCls.= ucfirst($lVal).'_';
      }
      $lAct = 'std';
    } else {
      $lAct = array_pop($lArr);
      $lMod = array_pop($lArr);
      $lArr = explode('-', $lMod);
      $lCls = '';
      foreach($lArr as $lVal) {
        $lCls.= ucfirst($lVal).'_';
      }
    }
    $lSys = CCor_Sys::getInstance();
    $lSys['usr.id'] = CCor_Cfg::get('svc.uid');
    $lUsr = CCor_Usr::getInstance();
    if (CCor_Cfg::get('svc.uid') == $lUsr -> getId()) {
      $lArr = array();
      $lSys = CCor_Sys::getInstance();
      foreach($lUsr -> getKeyVals() as $lKey => $lVal) {
        $lSys['usr.'.$lKey] = $lVal;
        $lArr[$lKey] = $lVal;
      }
      $lSys['usr.val'] = $lArr;

      $lLan = $lUsr -> getPref('sys.lang', LANGUAGE);
      if ($lLan == '') {
        $lLan = LANGUAGE;
      }
      define('LAN', $lLan);

      if (empty($lModMid)) {
        $lModMid = 0;
      }
      $lMandArr = array();
      $lQry = new CCor_Qry("SELECT id, code FROM al_sys_mand ORDER BY id");
      $lRow = $lQry -> getDat();
      foreach ($lQry as $lRow) {
        $lMandArr[$lRow -> id] = $lRow -> code;
      }
      #echo '<pre>---cli.php---'.get_class().'---';var_dump($lMandArr,$lModMid,'#############');echo '</pre>';
      if (isset($lMandArr[$lModMid])) {
        $lMand = $lMandArr[$lModMid];
      } elseif (0 == $lModMid) {
        $lMand = 'cust'; 
      } else {
        exit;
      }
      define('MID', $lModMid);
      define('MAND', $lMand);
      $this -> dbg('cor/cli $lModMid = '.MID);

      // Pfade zu kunden- & mandanten-spezifischen Dateien
      define('MAND_PATH', 	 'mand'.DS.'mand_'.MID.DS);
      define('MAND_PATH_HTM', MAND_PATH.'htm'.DS);
      define('MAND_PATH_INC', MAND_PATH.'inc');
      define('MAND_PATH_IMG', MAND_PATH.'img'.DS);
      CCor_Loader::addDir(MAND_PATH_INC);

      if (file_exists(MAND_PATH_INC.'/cor/cfg.php')) {
        ob_start();
        include_once MAND_PATH_INC.'/cor/cfg.php';
        ob_end_clean();
      }
      $lEnviromentMode = CCor_Cfg::get('environment');
      $lMandator = ($lEnviromentMode) ? $lEnviromentMode.'_'.MANDATOR : MANDATOR;
      define('MANDATOR_ENVIRONMENT',$lMandator);

      // Pfade zu kunden- & mandanten-spezifischen Dateien
      #define('CUST_PATH', 		'cust'.DS.'cust_'.CUST_ID.DS.'cust'.DS);// definiert in const.php
      define('CUST_PATH_HTM', CUST_PATH.'htm'.DS);
      define('CUST_PATH_INC', CUST_PATH.'inc');
      define('CUST_PATH_IMG', CUST_PATH.'img'.DS);
      CCor_Loader::addDir(CUST_PATH_INC);

      $lMandCls = 'C'.$lCls.'Cnt';

      $lCustCls = 'CCust_'.$lCls.'Cnt';

      $lClass  = 'C'.$lCls.'Cnt';

      $lGetClass = CCor_Loader::loadClass($lClass, $lCustCls, $lMandCls);
      #echo '<pre>---cli.php---'.MAND.' '.MID;var_dump($lCls,$lClass,$lCustCls,$lMandCls,$lGetClass,$lReq, $lMod, $lAct,'#############');echo '</pre>';

      if (FALSE !== $lGetClass) {
        $lCon = new $lGetClass($lReq, $lMod, $lAct);
        #$lCon = new $lCls($lReq, $lMod, $lAct);
        $lCon -> dispatch();
      } else {
        $this ->dbg('cor/cli: Unknown Module '.$lMod, mtUser, mlFatal);
      }
    }
    exit;
  }

}
