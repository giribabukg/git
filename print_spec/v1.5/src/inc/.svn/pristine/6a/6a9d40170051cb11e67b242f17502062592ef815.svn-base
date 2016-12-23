<?php
/**
 * Webcenter WebcenterTemplate Object
 *
 * eval the webcenter template of a job
 *
 * @package    API
 * @subpackage Webcenter
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 */

class CInc_Api_Wec_WebcenterTemplate extends CCor_Obj {

  protected $mJobid = '';
  protected $mTemplate = '';
  private static $mInstance = NULL;
  
  public function __construct($aJobid = ''){
    $this -> mJobid = $aJobid;
  }

  public function getInstance($aJobid = ''){
    if (NULL === self::$mInstance) {
      self::$mInstance = new self();
    }
    self::$mInstance -> mJobid = $aJobid;
    return self::$mInstance;
  }

  /**
   * Get the webcenter template of a job
   *
   * @param string $aJobid Unique job identifier
   * @return name of the webcenter template
  **/
  public static function getTemplate($aJobid = '') {
    $lwwt = self::getInstance($aJobid);
    return $lwwt -> getWebcenterTemplate();
  }

  private function getWebcenterTemplate() {
    $lPre = CCor_Cfg::get('wec.prjprefix');
    if (!empty($lPre)) {
      $lTestTpl = CCor_Cfg::get('wec.testtemplate');
      if (!empty($lTestTpl)){
        return $lTestTpl;
      }
    }
    
    $lTpl = CCor_Cfg::get('wec.tpl');
    if (empty($lTpl)) {
      $lQry = new CApi_Alink_Query_GetWebcenterTemplate($this -> mJobid);
      $lTpl = $lQry -> query();
      if (empty($lTpl)) $lTpl = CCor_Cfg::get('wec.defaulttemplate');
    }
    return $lTpl;
  }

}