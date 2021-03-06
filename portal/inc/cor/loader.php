<?php
/**
 * Core: Loader
 *
 *  Description
 *
 * @package    COR
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 8124 $
 * @date $Date: 2015-03-24 15:30:10 +0100 (Tue, 24 Mar 2015) $
 * @author $Author: gemmans $
 */
class CCor_Loader {

  private static $mDirs = array('inc/_mand0/inc','inc/_cust0/inc','inc');

  private static $mCnt = 0;

  public static function addDir($aDir) {
    array_unshift(self::$mDirs, $aDir);
  }

  public static function injectDir($aDir) {
    $lOld = array_shift(self::$mDirs);
    array_unshift(self::$mDirs, $aDir);
    array_unshift(self::$mDirs, $lOld);
  }

  public static function getCount() {
    return self::$mCnt;
  }

  public static function register() {
    spl_autoload_register(array('CCor_Loader', 'loadClass'));
  }

  public static function echoDebug($aText) {
    #echo $aText.BR;
  }

  public static function loadClass($aClass, $aCustClass='' ,$aMandClass='') {
    self::$mCnt++;
    #echo $aClass.BR;
    if (!preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', 
$aClass)) {
      throw new Exception('Loader: Illegal character in filename');
    }
    try {
      if (substr($aClass,0,5) == 'Zend_') {
        $lFil = str_replace('_', DS, $aClass).'.php';
        require_once $lFil;
        return $aClass;
      }
      if (substr($aClass,0,9) == 'PHPExcel_') {
        return false;
      }

      #if ('ccust_job_cnt' == strtolower($aClass)) echo "HI";

      $lDir = '';
      $lNam = strtolower(substr($aClass, 1));
      if (substr($lNam,0,5) == 'cust_') {
        $lDir = 'inc/_cust0/inc';
        $lNam = str_replace('cust_', '', $lNam);
      } elseif (substr($lNam,0,4) == 'inc_') {
        $lDir = 'inc';
        $lNam = str_replace('inc_', '', $lNam);
      }
      $lNam = DS.str_replace('_', DS, $lNam).'.php';
      if (!empty($lDir)) {// Cust und Inc wird sofort abgefr�hst�ckt
        $lFil = $lDir.$lNam;
        if (file_exists($lFil)) {
          require_once $lFil;
          #echo $lFil.BR;
          if (class_exists($aClass)) {
            self::echoDebug($lFil);
            return $aClass;
          }
        }
      }
      if (0 === strpos($lNam, '/mand')) {
        $lNam = str_replace('/mand', '', $lNam);
      }

      if (defined('MAND') AND 0 === strpos($lNam, '/'.MAND)) {
        #$lNam = str_replace('/'.MAND, '', $lNam);
      }

      foreach (self::$mDirs as $lDir) {
        $lFil = $lDir.$lNam;
        #echo 'ite '.$lFil.BR;

        if (file_exists($lFil)) {
          require_once $lFil;
          self::echoDebug($lFil);
          #echo $lFil.BR;
          if     (!empty($aMandClass) AND class_exists($aMandClass)) return $aMandClass;
          elseif (!empty($aCustClass) AND class_exists($aCustClass)) return $aCustClass;
          elseif (class_exists($aClass) OR interface_exists($aClass)) return $aClass;
        }
      }
      CCor_Obj::msg('Unknown Class: '.$aClass, mtUser, mlFatal);
      return false;
    } catch (Exception $lExc) {
      echo $lExc->getMessage();
      return false;
    }
  }

}
