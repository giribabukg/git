<?php
/**
 * Start or stop an approval loop on a document
 *
 * This query can be used either to start or stop an approval loop on a single
 * file in Webcenter
 *
 * Deprecated!!! See CApi_Wec_Robot s'tut's nich
 *
 * @package    API
 * @subpackage Webcenter
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 */

class CInc_Api_Wec_Query_Loop extends CApi_Wec_Query {

  public function start($aDocumentVersionId) {
    $this -> setParam('documentversionID', $aDocumentVersionId);
    $this -> setParam('startapprovalcycle', 'true');
    $lXml = $this -> query('UploadDocument.jsp');

    $lRes = new CApi_Wec_Response($lXml);

    $lHdl = fopen('inc/req/UploadDoc'.date('Y-m-d-H-i-s').'.xml', 'w+');
    fwrite($lHdl, $lXml);
    fclose($lHdl);

    return $lRes -> isSuccess();
  }

  public function stop($aDocumentVersionId) {
    $this -> setParam('documentversionID', $aDocumentVersionId);
    $this -> setParam('startapprovalcycle', 'false');
    $lXml = $this -> query('UploadDocument.jsp');
    $lRes = new CApi_Wec_Response($lXml);
    $this -> dbg($lXml);
    return $lRes -> isSuccess();
  }

}