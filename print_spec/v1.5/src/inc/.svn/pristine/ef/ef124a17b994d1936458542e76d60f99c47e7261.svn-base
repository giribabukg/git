<?php
/**
 * Webcenter thumbnail
 *
 * Get thumbnail (jpg) for either specific document id or document version id
 *
 * @package    API
 * @subpackage Webcenter
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 */
class CInc_Api_Wec_Query_Thumbnail extends CApi_Wec_Query {

  /**
   * Get image
   *
   * @param string $aWecDocId WebCenter document id
   * @param string $aWecVerId WebCenter document version id
   */
  function getImage($aWecDocId, $aWecVerId = NULL) {
    if (!$aWecVerId) {
      $this -> setParam('docid', $aWecDocId);
    } else {
      $this -> setParam('docverid', $aWecVerId);
    }
    $lRet = $this -> query('DocumentGetThumbnailData.jsp');
    if ('<error>' == substr($lRet, 0, 7)) return FALSE;
    return $lRet;
  }

  /**
   * Is viewable
   *
   * @param string $aWecDocId WebCenter document id
   * @param string $aWecVerId WebCenter document version id
   */
  function isViewable($aWecDocId, $aWecVerId = NULL) {
    if (!$aWecVerId) {
      $this -> setParam('documentid', $aWecDocId);
    } else {
      $this -> setParam('docversionid', $aWecVerId);
    }
    $lXml = $this -> query('GetDocViewableStatus.jsp');

    $lRes = new CApi_Wec_Response($lXml);
    if (!$lRes -> isSuccess()) {
      return FALSE;
    }

    $lDoc = $lRes -> getDoc();
    $lDocSupported = (string)$lDoc -> doc_supported;
    $lDocReadyToView = (string)$lDoc -> doc_ready_to_view;
    $lDataGenerationInProgress = (string)$lDoc -> data_generation_in_progress;

//     $lRet = array(
//       'doc_supported' => $lDocSupported,
//       'doc_ready_to_view' => $lDocReadyToView,
//       'data_generation_in_progress' => $lDataGenerationInProgress
//     );

    if ($lDocReadyToView == 1 && $lDataGenerationInProgress == 0) {
      return TRUE;
    } else {
      return FALSE;
    }
  }
}