<?php
/**
 * Get the list of documents for a given Webcenter project
 *
 * Description
 *
 * @package    API
 * @subpackage Webcenter
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 */

class CInc_Api_Wec_Query_Doclist extends CApi_Wec_Query {

  private function ignoreFolder($aFolder) {
    $lRet = False;
    if (empty($aFolder)) return False;
    $lIgn = CCor_Cfg::get('wec.ignore.folders', '');
    if (empty($lIgn)) return False;
    if ($lIgn == '*') {
      if ($this -> mDebug) {
        print_r('ignore Folder: '.$aFolder.LF);
      }
      return True;
    }
    $lIgn = explode(",", strtolower($lIgn));
    $aFolder = strtolower($aFolder);
    if (in_array($aFolder, $lIgn)) $lRet = True;
    // Recht pruefen
    if ($lRet) {
      $lUsr = CCor_Usr::getInstance();
      if ($lUsr -> canRead('wec-dir.'.$aFolder)) {
        $lRet = False;
      }
    }
    if ($this -> mDebug) {
      if ($lRet) print_r('ignore Folder: '.$aFolder.LF);
    }
    return $lRet;
  }

  public function getList($aPid) {
    $lUsrID = CCor_Usr::getAuthId();
    $this -> setParam('projectID', $aPid);
    $lXml = $this -> query('GetDocumentList.jsp');
    if ($this -> mDebug && $lUsrID == 1) print_r('--------- X M L GetDocumentList --------------------------------------'.LF);
    if ($this -> mDebug && $lUsrID == 1) print_r($lXml);

    $lRes = new CApi_Wec_Response($lXml);
    if (!$lRes -> isSuccess()) {
      return false;
    }

    $lDoc = $lRes -> getDoc();
    $lDatObj = new CCor_DateTime();
    $lRet = array();
    foreach ($lDoc as $lKey => $lRow) {
      $lTmp = array();

      foreach ($lRow as $lSub => $lVal) {
        $lTmp[(string)$lSub] = utf8_decode($lVal);
      }

      if (!empty($lTmp)) {
        if ($this -> mDebug && $lUsrID == 1) print_r('--------- X M L getDoc --------------------------------------'.LF);
        if ($this -> mDebug && $lUsrID == 1) print_r($lTmp);

        if ($this -> ignoreFolder($lTmp['folder_name'])) continue;
        $lItm['wec_ver_id'] = $lTmp['document_version_id'];
        $lItm['wec_doc_id'] = $lTmp['document_id'];
        $lItm['name']       = $lTmp['document_name'];
        $lItm['version']    = $lTmp['document_version'];
        $lItm['size']       = $lTmp['file_size'];
        $lItm['folder']     = $lTmp['folder_name'];
        $lItm['folder_id']  = $lTmp['folder_id'];
        $lItm['comment']    = $lTmp['version_comments'];
        $lItm['projectid']  = $aPid;

        $lDat = substr($lTmp['creation_date'], 0, 19);  // cut off millisec
        $lDatObj -> setSql($lDat);
        $lItm['date'] = $lDatObj -> getTime();

        $lItm['author'] = cat(
          $lRow -> author -> last_name,
          $lRow -> author -> first_name, ', '
        );

        if ('true' == $lTmp['viewable']) {
          $lItm['viewer'] = true;

          $lUrl = 'index.php?act=utl-wec.open';
          $lUrl.= '&pid='.$aPid;
          $lUrl.= '&doc='.urlencode($lItm['name']);
          $lUrl.= '&docid='.urlencode($lItm['wec_ver_id']);
          $lItm['link'] = $lUrl;
        } else {
          $lItm['viewer'] = false;
        }

        $lRet[] = $lItm;
      }
    }

    return $lRet;
  }

  public function getListByName($aPname) {
    $this -> setParam('projectname', $aPname);
    $lXml = $this -> query('GetDocumentList.jsp');
    $lPid = '';
    # $lHdl = fopen('inc/req/GetDocumentList'.date('Y-m-d-H-i-s').'.xml', 'w+');
    # fwrite($lHdl, $lXml);
    # fclose($lHdl);

    $lRes = new CApi_Wec_Response($lXml);
    if (!$lRes -> isSuccess()) {
      return false;
    }
    $lDoc = $lRes -> getDoc();
    $lDatObj = new CCor_DateTime();
    $lRet = array();
    $lo = $lDoc['projectid'];

    foreach ($lDoc -> attributes() as $a => $b) {
      if ($a == 'projectid') {
        $lPid = $b;
      }
    }

    foreach ($lDoc as $lKey => $lRow) {
      $lTmp = array();
      foreach ($lRow as $lSub => $lVal) {
        $lTmp[(string)$lSub] = utf8_decode($lVal);
      }
      if ($this->ignoreFolder($lTmp['folder_name'])) continue;
      $lItm['wec_ver_id'] = $lTmp['document_version_id'];
      $lItm['wec_doc_id'] = $lTmp['document_id'];
      $lItm['name']    = $lTmp['document_name'];
      $lItm['version'] = $lTmp['document_version'];
      $lItm['size']    = $lTmp['file_size'];
      $lItm['folder']  = $lTmp['folder_name'];
      $lItm['projectid'] = $lPid;

      $lDat = substr($lTmp['creation_date'], 0, 19);  // cut off millisec
      $lDatObj -> setSql($lDat);
      $lItm['date'] = $lDatObj -> getTime();

      $lItm['author'] = cat(
        $lRow -> author -> last_name,
        $lRow -> author -> first_name, ', ');

      if ('true' == $lTmp['viewable']) {
        $lItm['viewer'] = true;
        $lUrl = 'index.php?act=utl-wec.open';
        $lUrl.= '&pid='.$lPid;
        $lUrl.= '&doc='.urlencode($lItm['name']);
        $lItm['link'] = $lUrl;
      } else {
        $lItm['viewer'] = false;
      }
      $lRet[] = $lItm;
    }
    return $lRet;
  }
}