<?php
/**
 * Download a Webcenter file as ZIP
 *
 * @package    API
 * @subpackage Webcenter
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 */


class CInc_Api_Wec_Query_Download extends CApi_Wec_Query {

  public function getLink() {
  }

  /**
   * Downloading a document
   *
   * @param string $aWecPrjID
   * @param string $aDocVerId
   * @param string $aDocName
   * @param string $aDownloadDocName
   * @param string $aDownloadType
   * @return boolean Success?
   */
  public function download($aWecPrjID, $aDocVerId = NULL, $aDocName = NULL, $aDownloadDocName = NULL, $aDownloadType = NULL) {
    $this -> setParam('projectid', $aWecPrjID);
    if (!is_null($aDocVerId)) {
      $this -> setParam('docversionid', $aDocVerId);
    }
    if (!is_null($aDocName)) {
      $this -> setParam('documentname', $aDocName);
    }
      if (!is_null($aDownloadDocName)) {
      $this -> setParam('downloaddocname', $aDownloadDocName);
    }
    if (!is_null($aDownloadType)) {
      $this -> setParam('downloadtype', $aDownloadType);
    }
    $lXml = $this -> query('DownloadDocument.jsp');

    $this -> dbg($lXml);

    $lRes = new CApi_Wec_Response($lXml);
    return $lRes -> isSuccess();
  }
}