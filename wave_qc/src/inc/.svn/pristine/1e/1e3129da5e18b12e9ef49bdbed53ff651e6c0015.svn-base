<?php
/**
 * DMS API Wrapper
 *
 * Provides all commands available in the DMS API as a facade.
 * In most cases, other classes will only use this class to send DMS queries.
 *
 * @package    API
 * @subpackage DMS
 * @copyright  Copyright (c) 5Flow GmbH (http://www.5flow.eu)
 * @version $Rev: 687 $
 * @date $Date: 2013-01-18 03:56:42 +0100 (Fr, 18 Jan 2013) $
 * @author $Author: gemmans $
 */
class CInc_Api_Dms_Query extends CCor_Obj {

  /**
   * The DMS API Client (a thin wrapper around Zend_Http_Client)
   *
   * Could be replaced by a stub for local testing.
   * @var CApi_Dms_Client
   */
  protected $mClient;

  /**
   * Constructor - initialize the DMS API client object
   *
   * Optionally, you can provide an alternative Client (@see setClient)
   * @param string $aClient
   */
  public function __construct($aClient = null) {
    $this->setClient($aClient);
  }

  /**
   * Set the client explicitly.
   *
   * Only need to use this directly if you want to replace the real client later
   * with a stub for local testing.
   *
   * @param string $aClient
   */
  public function setClient($aClient = null) {
    if (is_null($aClient)) {
      $aClient = new CApi_Dms_Client();
    }
    $this->mClient = $aClient;
  }

  /**
   * Tell the DMS to pick up an uploaded file from the shared folder
   *
   * @param string $aTempName The temporary name of the uploaded file in the shared folder
   * @param string $aFileName The original filename as uploaded by the author
   * @param string $aAuthor The name of the author of the document (user uploading the file).
   * @param string $aMandator The mandator/MAND, add Live or Stage to avoid collisions
   * @param string $aSrc The job's src, e.g. 'art' or 'rep'
   * @param string $aJobId The job's JobID, e.g. '000004711'
   * @param array $aMeta Optional hash array with metadata fields
   * @param integer $aJobFileId An ID unique within this job. Determines if we have a new file or a new version
   */
  public function uploadFile($aTempName, $aFileName, $aAuthor, $aMandator, $aSrc, $aJobId, $aMeta = null, $aJobFileId = null) {
    $lXml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" standalone="yes"?><request />');
    $lDoc = $lXml->addChild('file'); 
    $lDoc->addChild('filenameshare', $aTempName);
    $lDoc->addChild('filename', $aFileName);
    $lDoc->addChild('author', $aAuthor);
    $lDoc->addChild('mandator', $aMandator);
    $lDoc->addChild('jobtype', $aSrc);
    $lDoc->addChild('jobid', $aJobId);
    $lJobFileId = (is_null($aJobFileId)) ? 1 : intval($aJobFileId);
    $lDoc->addChild('jobfileid', $lJobFileId);
    if (empty($aMeta)) { $aMeta = array('dummy' => 1); }
    if (!empty($aMeta)) {
      $lMeta = $lDoc->addChild('metadata');
      foreach($aMeta as $lKey => $lVal) {
        $lMeta->addChild('field', $lVal)->addAttribute('name', $lKey);
      }
    }
    $lPost = $lXml->asXML();
    $this->dbg('XML '.$lPost);
    $lResXml = $this->mClient->query('dmsapifileupload.aspx', null, $lPost);

    if (!$lResXml) return false;
    $lResXml = strtr($lResXml, array('</jobid>' => '</jobfileid>'));
    # Workaround: XML answer from DMS is malformed. Replace first </jobid> with </jobfileid>
    $lResXml = implode('</jobid>', explode('</jobfileid>', $lResXml, 2));

    $lResponse = new CApi_Dms_Response($lResXml);
    if (!$lResponse) return false;

    return $lResponse->toArray();
  }

  /**
   * Open a file version
   *
   * Depending on the mode parameter, this can be
   * 1 - Open document in internal viewer (needs browser plugin)
   * 2 - Open document in internal viewer, like 1 but with edit lock
   * 3 - Open document via webdav for editing
   * 4 - Download document
   * 5 - Download document, like 4 with edit lock
   *
   * @param int $aFileVersionId The unique FileVersionId of the file to open (@see getFileList)
   * @param string $aUserName The name of the user opening the file (used for handling locks)
   * @param int $aMode The open mode (see above)
   * @return string Echo this to the browser
   */
  public function openFile($aFileVersionId, $aUsername, $aMode = 4) {
    $lParam = array();
    $lParam['fileversionid'] = $aFileVersionId;
    $lParam['username']      = $aUsername;
    $lParam['openmode']      = $aMode;

    $lRet = $this->mClient->query('dmsapiopenfile.aspx', $lParam);
    return $lRet;
  }

  /**
   * Get a list of files for a specific job
   *
   * The resulting array will have the following format:
   *
   * array(
   *   fileid1 => array (
   *     fileid => fileid1,
   *     filename => 'file1.ext',
   *     version => max version of fileid1,
   *     fileversionid => fileversionid of max version for fileid1,
   *     date => '2013-10-10 15:50:46',
   *     author => 'Geoffrey Emmans',
   *     versions => array(
   *       fileversionid1 => array(
   *         'fileid' => '42',
   *         'fileversionid' => '191',
   *         'filename' => 'Document.docx',
   *         'author' => 'Geoffrey Emmans',
   *         'date' => '2013-10-10 15:50:46',
   *         'version' => 3
   *       )
   *     )
   *   )
   * )
   * Note: If live and stage server are using the same DMS, add 'Live' or
   * 'Stage' to the MAND to avoid jobid conflicts.
   *
   * @param string $aMand The mandator, usually from MAND.
   * @param string $aSrc The src of the job, e.g. 'art' or 'rep'
   * @param string $aJobId The JobID of the job, e.g. '000004711'
   * @param int $aVersions 1=show all versions, 0=only current max version
   * @return array|false
   */

  public function getFileList($aMand, $aSrc, $aJobId, $aVersions = 0) {
    $lParam = array();
    $lParam['mandator'] = $aMand;
    $lParam['jobtype']  = $aSrc;
    $lParam['jobid']    = $aJobId;
    if ($aVersions) {
      $lParam['versions'] = 1;
    }

    $lXml = $this->mClient->query('dmsapigetfilelist.aspx', $lParam);
    if (!$lXml) return false;
    $lXml = strtr($lXml, array('&' => '&amp;'));

    $lResponse = new CApi_Dms_Response($lXml);
    if (!$lResponse) {
      return false;
    }

    $lArr = $lResponse->toArray();

    $lRet = array();
    if (empty($lArr)) return $lRet;

    $lFiles = $lArr['file'];

    if (isset($lFiles['version'])) {
      $lFiles = array(0 => $lFiles);
    }
    foreach ($lFiles as $lFile) {
      if (is_string($lFile)) return array();
      if (!isset($lFile['version'])) return $lRet;
      if (empty($lFile)) return array();

      $lFile = $lFile['version'];
      if (isset($lFile['fileid'])) {
        // only one version for this fileid but need to iterate over versions
        $lFile = array(0 => $lFile);
      }
      foreach ($lFile as $lRow) {
        $lItm = $lRow;
        $lFileId = $lItm['fileid'];
        $lJobFileId = $lItm['jobfileid'];
        $lFileVersionId =  $lItm['fileversionid'];

        // reformat some fields
        $lItm['version'] = intval($lItm['version']);
        $lDate = DateTime::createFromFormat('d.m.Y H:i:s', $lItm['date']);
        $lItm['date'] = $lDate->format('Y-m-d H:i:s');

        // make fileid and filename available at file level (instead of version level)
        $lRet[$lFileId]['jobfileid'] = $lJobFileId;
        $lRet[$lFileId]['fileid'] = $lFileId;
        $lRet[$lFileId]['filename'] = $lItm['filename'];

        // calc maxversion for each fileid and copy that version's info to file level
        $lFileMax = (isset($lRet[$lFileId]['maxversion'])) ? $lRet[$lFileId]['maxversion'] : 0;
        if ($lItm['version'] > $lFileMax) {
          $lRet[$lFileId]['version']       = $lItm['version'];
          $lRet[$lFileId]['fileversionid'] = $lItm['fileversionid'];
          $lRet[$lFileId]['jobfileid']     = $lItm['jobfileid'];
          $lRet[$lFileId]['date']          = $lItm['date'];
          $lRet[$lFileId]['author']        = $lItm['author'];
          $lRet[$lFileId]['maxversion']    = $lItm['version'];
        }
        $lRet[$lFileId]['versions'][$lFileVersionId] = $lItm;
      }
    }
    $this->dump($lRet, 'FILE RESPONSE');
    return $lRet;
  }

}
