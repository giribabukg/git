<?php
/**
 * Webcenter Project Annotations by xfdf file
 *
 * Description
 *
 * @package    API
 * @subpackage Webcenter
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 */

class CInc_Api_Wec_Xfdf_Annotations extends CCor_Dat {

  protected $mSorter;
  protected $mDebug;
  protected $mIsMultipage = FALSE;

  // dieses Objekt kann immer nur einmal pro XML-Quelle aufgerufen werden!
  public function __construct($aSorter='') {
    $this -> mSorter = $aSorter;
    $this -> mDebug = False;
  }

  /**
   * Get the history list, using Project ID and Filename
   *
   * @param string $aProjectId Webcenter Project ID containing the file
   * @param string $aFilename Filename of the document
   * @param bool $aAnnotations Retrieve regular comments?
   * @param bool $aApprovals Retrieve approval comments?
   */
  public function getListByFilename($aProjectId, $aFilename, $aAnnotations = TRUE, $aApprovals = TRUE) {
    $this -> setParam('projectID', $aProjectId);
    $this -> setParam('DocumentName', $aFilename);
    if ($aAnnotations) {
      $this -> setParam('annotationinfo', 1);
    }
    if ($aApprovals) {
      $this -> setParam('approvalinfo', 1);
    }
    return $this -> _getHistory();
  }


  /**
   * Get the history list, using a valid Document ID
   *
   * @param string $aDocId Webcenter Document ID
   */
  public function getListByDocId($aWecDocId, $aDebug = False) {
    $lPath = '';
    $this -> mDebug = $aDebug;

    $lCounter = 1;
    $lMultiPage = array();

    $lProcessedFiles = array();

    if (!empty($aWecDocId)) {
      $lPath = $this -> getWecFileStore($aWecDocId);
      #$this->msg($lPath.$lCounter, mtApi, mlFatal);

      if (file_exists($lPath.$lCounter) AND $handle = opendir($lPath.$lCounter)) {
        $this -> mIsMultipage = TRUE;
        #$this->msg('is multipage', mtApi, mlFatal);
        do {
          while (false !== ($lFile = @readdir($handle))) {
            if ($lFile != "." AND $lFile != ".." AND strcasecmp(substr($lFile, -5), ".xfdf") == 0) {
              $lXmlFile = $lPath.$lCounter.'/'.$lFile;
              if (in_array($lXmlFile, $lProcessedFiles)) {
                continue;
              }
              $lProcessedFiles[] = $lXmlFile;
              if (is_readable($lXmlFile)) {
                $lFileDatum = date('Y-m-d H:i:s', filemtime($lXmlFile));
                $lDat = new CUtl_Wec_Xfdf($lXmlFile, $lFileDatum, $this -> mSorter); // new CUtl_Mem_List();
                closedir($handle);
                $lMultiPage[] = $lDat -> parse('file');
              } else {
                // XFDF File is not readable
                $this -> dbg('XFDF file '.$lXmlFile.' is not readable', mlWarn);
              }
            } else {
              // XFDF File can not be found
              $this -> dbg ('No XFDF file found at: '.$lPath, mlInfo);
            }
          }//end_while (false !== ($lFile = @readdir($handle)))
          @closedir($handle);

          $lCounter++;
        } while (file_exists($lPath.$lCounter) AND $handle = opendir($lPath.$lCounter));

        return $lMultiPage;
      } else
      if (file_exists($lPath) AND $handle = opendir($lPath)) {
        while (($lFile = readdir($handle)) !== false) {
          if ($lFile != "." AND $lFile != ".." AND strcasecmp(substr($lFile, -5), ".xfdf") == 0) {
            $lXmlFile = $lPath.$lFile;
            if (is_readable($lXmlFile)) {
              $lFileDatum = date('Y-m-d H:i:s', filemtime($lXmlFile));
              $lDat = new CUtl_Wec_Xfdf($lXmlFile, $lFileDatum, $this -> mSorter); // new CUtl_Mem_List();
              closedir($handle);
              $lPage = $lDat -> parse('file');
              return $lPage;

            } else {
              $this -> dbg('XFDF file '.$lXmlFile.' is not readable', mlWarn);
            }
          } else {
            $this -> dbg ('No XFDF file found at: '.$lPath, mlInfo);
          }
        }
        closedir($handle);
      } else {
        $this -> dbg('Webcenter File Store:"'. $lPath.'" is not reachable or not existing', mlWarn);
      }
    }
  }

  /**
   * Get the history list, using a valid Document ID
   *
   * @param string $aDocId Webcenter Document ID
   */

  public function getXmlByDocId($aWecDocId) {
  	#return $this->getXmlByDocIdFromApi($aWecDocId);
  	return $this->getXmlByDocIdFromFile($aWecDocId);
  }

  public function getXmlByDocIdFromApi($aProjectId, $aWecDocVersionId) {
  	$lClient = new CApi_Wec_Client();
  	$lClient->loadConfig();
  	$lQry = new CApi_Wec_Query($lClient);
  	$lQry -> setParam('projectid', $aProjectId);
  	$lQry -> setParam('docversionid', $aWecDocVersionId);

  	$lQry -> setParam('returnasfile', 0);
  	$lQry -> setParam('pageindex', 0);
  	$lXml = $lQry -> query('DownloadAnnotations.jsp');
  	$lRes = new CApi_Wec_Response($lXml);
  	if (!$lRes -> isSuccess()) {
  		return;
  	}
    if (0 === strpos($lXml, '<error>')) { //We should handle this in the $lRes -> isSuccess()
    	return;
    }
  	return $lXml;
  }

  public function getXmlByDocIdFromFile($aWecDocId) {
    $lPath = '';
    $lWecDocId = $aWecDocId;
    $lCounter = 1;
    $lMultiPage = array();

    if (empty($lWecDocId)) {
      // No Webcenter Document Id
      return '';
    }

    $lPath = $this -> getWecFileStore($lWecDocId);

    if (file_exists($lPath.$lCounter) AND $handle = opendir($lPath.$lCounter)) {
      do {
        while (false !== ($lFile = @readdir($handle))) {
          if ($lFile != "." AND $lFile != ".." AND strcasecmp(substr($lFile, -5), ".xfdf") == 0) {
            $lXmlFile = $lPath.$lCounter.'/'.$lFile;
            if (is_readable($lXmlFile)) {
              closedir($handle);
              $lMultiPage[] = file_get_contents($lXmlFile);
            } else {
              // XFDF File is not readable
              $this -> dbg('XFDF file '.$lXmlFile.' is not readable', mlWarn);
            }
          } else {
            // XFDF File can not be found
            $this -> dbg ('No XFDF file found at: '.$lPath, mlInfo);
          }
        }
        @closedir($handle);

        $lCounter++;
      } while (file_exists($lPath.$lCounter) AND $handle = opendir($lPath.$lCounter));

      return $lMultiPage;
    } else
    if (file_exists($lPath) AND $handle = opendir($lPath)) {
      // einlesen der Verzeichnisses
      while (($lFile = readdir($handle)) !== false){
        if ($lFile != "." AND $lFile != ".." AND strcasecmp(substr( $lFile, -5), ".xfdf") == 0) {
          $lXmlFile = $lPath.$lFile;
          if (is_readable($lXmlFile)) {
            closedir($handle);
            return file_get_contents($lXmlFile);
          } else {
            // XFDF File is not readable
            $this -> dbg('XFDF file '.$lXmlFile.' is not readable', mlWarn);
          }
        } else {
          // XFDF File can not be found
          $this -> dbg('No XFDF file found at: '.$lPath, mlInfo);
        }
      }
      closedir($handle);
    } else {
      // Webcenter File Store is not reachable or NOT existing
      $this -> dbg('Webcenter File Store:"'. $lPath.'" is not reachable or not existing', mlWarn);
    }
    return '';
  }

  protected function _getHistory() {
    $lXml = $this -> query('GetDocumentHistory.jsp');

    #$lHdl = fopen('inc/req/req'.date('Y-m-d-H-i-s').'.xml', 'w+');
    #fwrite($lHdl, $lXml);
    #fclose($lHdl);

    #echo htm($lXml).BR.BR;
    $lRes = new CApi_Wec_Response($lXml);
    if (!$lRes -> isSuccess()) {
      return false;
    }
    $lRet = array();

    $lDoc = $lRes -> getDoc();
    foreach ($lDoc -> document_version as $lVersionNode) {
      $lVerStr = (string)$lVersionNode -> version_number;
      #$lVersionNode = $lDoc -> document_version; // TODO: loop over versions
      if (isset($lVersionNode -> approvals -> approval -> approval_comment_list)) {
        $lRoot = $lVersionNode -> approvals -> approval -> approval_comment_list;
        foreach ($lRoot -> children() as $lRow) {
          if (empty($lRow)) continue;
          $lLine = array();
          $lLine['date']    = substr($lRow -> date, 0, 19);
          $lLine['version'] = $lVerStr;
          $lStat = utf8_decode($lRow -> approval_status);
          switch ($lStat) {
            case wecAplOk :
              $lTyp = htAplOk;
              BREAK;
            case wecAplNok :
              $lTyp = htAplNok;
              BREAK;
            case wecAplCond:
              $lTyp = htAplCond;
              BREAK;
            default:
              $lTyp = htWecComment;
          }
          $lLine['typ'] = $lTyp;

          $lUsr = $lRow -> approval_user -> user;
          $lLine['user'] = cat(utf8_decode($lUsr -> lastname), utf8_decode($lUsr -> firstname),', ');
          $lLine['uid'] = (string)$lUsr -> userID;
          #$lLine['comment'] = utf8_decode($lRow -> comment).' '.$lLine['uid'];
          $lLine['comment'] = utf8_decode($lRow -> comment);
          $lRet[] = $lLine;
        }
      }

      $lInc = 1;
      if (isset($lVersionNode -> annotations)) {
        $lRoot = $lVersionNode -> annotations;
        foreach ($lRoot -> children() as $lRow) {
          if (empty($lRow)) continue;
          $lLine = array();
          $lLine['date']    = substr($lRow -> annotation_date, 0, 19);
          $lLine['version'] = $lVerStr;
          $lLine['typ']     = htWecComment;
          $lUsr = $lRow -> annotation_author -> user;
          $lLine['user'] = cat(utf8_decode($lUsr -> lastname), utf8_decode($lUsr -> firstname),', ');
          $lLine['uid'] = (string)$lUsr -> userID;
          $lCom = $lRow -> annotation_text;
          $lLeft  = strpos($lCom, '"');
          $lRight = strrpos($lCom, '"');
          if ($lLeft and $lRight) {
            $lCom = substr($lCom, $lLeft+1, $lRight - $lLeft -1);
          }
          #$lLine['comment'] = $lCom.' '.$lLine['uid'];
          #$lLine['comment'] = $lInc.'. '.$lCom;
          $lLine['comment'] = $lCom;
          $lInc++;
          $lRet[] = $lLine;
        }
      }
    }
    return $lRet;
  }

  public static function getItemHash($aArr) {
    # var_dump($aArr);
    $lRet = $aArr['name'].$aArr['date'].$aArr['uid'].$aArr['typ'].$aArr['comment'];
    $lRet = sha1($lRet);
    return $lRet;
  }

  /*
   * Get Webcenter File Store Path
   * @param  string $aWecDocId string Webcenter Document Id
   * @return string $lRet Webcenter File Store Path
   */
  public function getWecFileStore($aWecDocId = ''){
    $lRet = '';
    $lWecDocId = $aWecDocId;
    $lWecFileStorePath = CCor_Cfg::get('wec.filestore','');
    if ($lWecFileStorePath == ''){
      $this ->dbg ('Webcenter File Store is not set',mlWarn);
    }
    if (CCor_Cfg::get('wec.version') == 10) {
        // Path Kombination wec.filestore/first six char from wecDocId/wecDocId
      $lResDir = substr($lWecDocId, strpos($lWecDocId, '_') + 1, 6);
      $lRet = $lWecFileStorePath.$lResDir.'/'.$lWecDocId.'/';
    } else {
      $lRet = $lWecFileStorePath.$lWecDocId.'/';
    }
    $this -> dbg('Webcenter File Store (XFDF File located): '.$lRet);
    return $lRet;

  }

  /**
   * Get the info, if Multipage or single side webcenter document
   * @return boolean
   */
  public function getIsMultipage() {
    return $this -> mIsMultipage;
  }
}