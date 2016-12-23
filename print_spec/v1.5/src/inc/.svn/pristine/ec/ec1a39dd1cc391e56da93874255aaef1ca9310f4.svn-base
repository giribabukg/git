<?php
/**
 * Class for parsing response XML to arrays
 *
 * An XML string is passed and an array is returned
 *
 * @package    API
 * @subpackage DMS
 * @copyright  Copyright (c) 5Flow GmbH (http://www.5flow.eu)
 * @version $Rev: 687 $
 * @date $Date: 2013-01-18 03:56:42 +0100 (Fr, 18 Jan 2013) $
 * @author $Author: gemmans $
 */
class CInc_Api_Dms_Response extends CCor_Obj {

  /**
   * Constructor - optionally, pass in the XML retrieved from the service
   * @param string|null $aXml The XML retrieved from the DMS API
   */
  public function __construct($aXml = null) {
    $this->setXml($aXml);
  }

  /**
   * Set and parse the XML from the DMS API
   * @param unknown_type $aXml
   */
  public function setXml($aXml) {
    $this->mXml = $aXml;
    $this->log($aXml, 'Param');
    if (false !== $aXml) {
      try {
        $this->mDoc = simplexml_load_string($aXml);
      } catch (Exception $ex) {
        $this->msg('DMS: '.$ex->getMessage(), mtApi, mlError);
        $this->mDoc = false;
      }
    } else {
      $this->log('already false in param');
      $this->mDoc = false;
    }
    $this->log($this->mDoc, 'DOC');
    return $this->mDoc;
  }

  protected function log($aText, $aDescripton = '') {
    $lRet = 'DMS '.$aDescription.': ';
    if (is_scalar($aText)) {
      $lRet.= $aText;
    } else {
      $lRet.= var_export($aText, true);
    }
    $this->msg($lRet, mtApi, mlInfo);
  }

  /**
   * Return the current parsed XML as an array or false if XML was not valid
   * @return array|false
   */
  public function toArray() {
    if (!$this->mDoc) return false;
    return $this->nodeToArray($this->mDoc);
  }

  protected function nodeToArray($aNode) {
    $lRet = json_decode(json_encode($aNode), TRUE);

    foreach (array_slice($lRet, 0) as $lKey => $lValue) {
      if (empty($lValue)) $lRet[$lKey] = NULL;
      elseif (is_array($lValue)) $lRet[$lKey] = $this->nodeToArray($lValue);
    }
    return $lRet;
  }



}