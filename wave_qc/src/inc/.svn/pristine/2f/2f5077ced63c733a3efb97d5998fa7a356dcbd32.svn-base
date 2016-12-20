<?php
/**
 * Upload a document
 *
 * Description
 *
 * @package    API
 * @subpackage Webcenter
 * @copyright  5Flow GmbH
 */

class CInc_Api_Wec_Query_Upload extends CApi_Wec_Query {

  public function __construct($aClient, $aDebug = false) {
    parent::__construct($aClient, $aDebug);
    $this -> setParam('createnewdocversion', 'true');
    $this -> setParam('sourceid', '5Flow.Wave');
  }

  /**
   * Upload a file to a Webcenter project if the project's ID is known
   *
   * @param string $aWebcenterProjectId  The ID of the existing Webcenter project
   * @param string $aFilename The absolute filename as seen from Webcenter Application server
   * @param string $aAsName Alternative name (e.g. upload xyz.pdf as artwork.pdf)
   * @param string $aFolder The subfolder of the Webcenter project to place the file in
   * @param string $aComment An optional comment for this document version
   * @return boolean Success?
   */
  public function upload($aWebcenterProjectId, $aFilename, $aAsName = NULL,
      $aFolder = NULL, $aComment = NULL) {
    $this -> setParam('projectid', $aWebcenterProjectId);
    $this -> setParam('datafileUNC', $aFilename);
    if (!is_null($aAsName)) {
      $this -> setParam('documentname', $aAsName);
    }
    if (!is_null($aFolder)) {
      $this -> setParam('folder', $aFolder);
    }
    if (!is_null($aComment)) {
      $this -> setParam('comment', $aComment);
    }
    $lXml = $this -> query('UploadDocument.jsp');

    $this->dbg($lXml);

    $lRes = new CApi_Wec_Response($lXml);
    return $lRes -> isSuccess();
  }

  /**
   * Upload a file to Webcenter using the Projectname
   *
   * @param string $aWebcenterProjectName The name of the existing Webcenter project
   * @param string $aFilename The absolute filename as seen from Webcenter Application server
   * @param string $aAsName Alternative name (e.g. upload xyz.pdf as artwork.pdf)
   * @param string $aFolder The subfolder of the Webcenter project to place the file in
   * @param string $aComment  An optional comment for this document version
   * @return false|string False on failure, Webcenter Document Version ID otherwise
   */
  public function uploadToProject($aWebcenterProjectName, $aFilename,
      $aAsName = NULL, $aFolder = NULL, $aComment = NULL) {
    $this -> setParam('projectname', $aWebcenterProjectName);
    $this -> setParam('datafileUNC', $aFilename);
    if (!is_null($aAsName)) {
      $this -> setParam('documentname', $aAsName);
    }
    if (!is_null($aFolder)) {
      $this -> setParam('folder', $aFolder);
    }
    if (!is_null($aComment)) {
      $this -> setParam('comment', $aComment);
    }
    $lXml = $this -> query('UploadDocument.jsp');
    $this->dbg($lXml);

    $lRes = new CApi_Wec_Response($lXml);
    if ($lRes->isSuccess()) {
      return (string)$lRes->getDoc();
    }
    return false;
  }

  /**
   * Create a Webcenter project and upload a file to it
   * @param string $aProjectName The project name to use for the new project
   * @param string $aTemplate The template to use for the project
   * @param string $aFilename The absolute filename as seen from Webcenter Application server
   * @param string $aAsName Alternative name (e.g. upload xyz.pdf as artwork.pdf)
   * @param string $aFolder The subfolder of the Webcenter project to place the file in
   * @param string $aComment  An optional comment for this document version
   * @return CApi_Wec_Response
   */
  public function createProjectAndUpload(
      $aProjectName, $aTemplate,
      $aFilename, $aAsName = NULL, $aFolder = NULL, $aComment = NULL)
  {
    $this -> setParam('createproject', 'true');
    $this -> setParam('projectname', $aProjectName);
    $this -> setParam('projecttemplate', $aTemplate);

    $this -> setParam('datafileUNC', $aFilename);
    if (!is_null($aAsName)) {
      $this -> setParam('documentname', $aAsName);
    }
    if (!is_null($aFolder)) {
      $this -> setParam('folder', $aFolder);
    }
    if (!is_null($aComment)) {
      $this -> setParam('comment', $aComment);
    }
    $lXml = $this -> query('UploadDocument.jsp');

    $lRes = new CApi_Wec_Response($lXml);
    return $lRes;
  }

}