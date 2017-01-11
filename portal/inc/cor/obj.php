<?php
/**
 * Core: Object Base class of most other classes with reflection functions
 *
 *  Description
 *
 * @package    COR
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 14145 $
 * @date $Date: 2016-05-25 16:33:15 +0200 (Wed, 25 May 2016) $
 * @author $Author: ahanslik $
 */
class CCor_Obj {

  protected $mReflection;

  /**
   * Create the reflection object if not yet initialized
   *
   * @return object   Reflection object
   */

  protected function & getReflection() {
    if (empty($this -> mReflection))
      $this -> mReflection = new ReflectionClass(get_class($this));
    return $this -> mReflection;
  }

  /**
   * Returns true if object has a method of given name
   *
   * @param string $aMethodName    Name of the method to check
   * @return bool    True if method exists
   */

  public function hasMethod($aMethodName) {
    $this -> getReflection();
    return $this -> mReflection -> hasMethod($aMethodName);
  }

  public function dbg($aText, $aLvl = mlInfo) {
    CCor_Msg::add('['.get_class($this).'] '.$aText, mtDebug, $aLvl);
  }

  public function msg($aText, $aType = mtUser, $aLvl = mlInfo) {
    CCor_Msg::add($aText, $aType, $aLvl);
  }

  public function dump($aVar, $aPrefix = '') {
    $lPrf = ('' == $aPrefix) ? '' : $aPrefix.' ';
    CCor_Msg::add('['.get_class($this).'] '.$lPrf.var_export($aVar, TRUE), mtDebug, mlInfo);
  }

  public function bench() {
    $this -> dbg($lMsg = microtime().'/ M'.xdebug_memory_usage());
  }

  public function backTrace() {
    $lArr = debug_backtrace();
    $lArr = $lArr[1];
    $lMsg = 'Line '.$lArr['line'].':';
    $lMsg = cat($lMsg, $lArr['function'].'(');
    if (!empty($lArr['args'])) {
      foreach ($lArr['args'] as $lArg) {
        $lTyp = gettype($lArg);
        $lMsg = cat($lMsg, $lTyp);
        switch ($lTyp) {
          case 'array' :
            $lMsg = cat($lMsg, '['.count($lArg).']');
            break;
          case 'object' :
            $lMsg = cat($lMsg, get_class($lArg));
            break;
          case 'ressource' :
            break;
          case 'boolean' :
            if ($lArg) {
              $lMsg = cat($lMsg, 'TRUE');
            } else {
              $lMsg = cat($lMsg, 'FALSE');
            }
            break;
          default :
            $lMsg = cat($lMsg, $lArg);
        }
        $lMsg.= ', ';

      }
      $lMsg = strip($lMsg, 2).' )';
    }
    $this -> dbg($lMsg);
  }

  public function assign($aArray) {
    if (empty($aArray)) {
      return;
    }
    foreach ($aArray as $lKey => $lVal) {
      $this -> $lKey = $lVal;
    }
  }
} // class

function bitSet($aVar, $aBit) {
  return (($aVar & $aBit) == $aBit);
}

function setBit($aVar, $aBit) {
  return ($aVar | $aBit);
}

function unsetBit($aVar, $aBit) {
  return ($aVar & (~$aBit));
}

function lan($aKey) {
  return CCor_Lang::getStatic($aKey);
}

function lang($aKey, $aLan, $aDef = NULL) {
  return CCor_Lang::getStatic($aKey, $aLan, $aDef);
}

function htmlan($aKey) {
  return htm(CCor_Lang::getStatic($aKey));
}

// TODO: Attention! The wildcards needs care as it does not work for all characters. E.g. % is currently not allowed.
function wildcard($aNeedle, $aHaystack, $aWildcard = '*') {
  $aNeedle = '/^'.preg_quote($aNeedle).'$/';
  $aNeedle = str_replace('\\'.$aWildcard, '.'.$aWildcard, $aNeedle);
  if (!preg_match($aNeedle, $aHaystack, $lResult)) {
    return FALSE;
  } else {
    return $lResult;
  }
}