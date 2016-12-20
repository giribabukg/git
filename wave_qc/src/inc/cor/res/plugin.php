<?php
/**
 * Core: Ressource - Plugin
 *
 * Description
 *
 * @package    COR
 * @subpackage    Ressource
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 6 $
 * @date $Date: 2012-02-21 16:50:56 +0800 (Tue, 21 Feb 2012) $
 * @author $Author: gemmans $
 */
abstract class CCor_Res_Plugin extends CCor_Obj {
  
  protected $mRet = NULL;
  
  public function get($aParam = NULL) {
    if (NULL === $this -> mRet) {
      $this -> mRet = $this -> refresh($aParam);
    } else {
      $this -> resHit();
    }
    return $this -> mRet;
  }
  
  protected abstract function refresh($aParam = NULL);
  
  protected function getCache($aKey) {
    $lRet = CCor_Cache::getStatic($aKey);
    if ($lRet) {
      $this -> dbg('ZCache '.$aKey.' hit');
      incCtr('zch');
    }
    return $lRet;
  }
  
  protected function setCache($aKey, $aVal) {
    CCor_Cache::setStatic($aKey, $aVal);
  }
  
  protected function resHit() {
    #$this -> dbg('RCache hit');
    incCtr('rch');
  }
  
}