<?php
/**
 * Update Local History with Webcenter History
 *
 * Description
 *
 * @package    API
 * @subpackage Webcenter
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 */

class CInc_Api_Wec_Annotations extends CCor_Obj {

  public function __construct($aSrc, $aJobId, $aProjectId, $aDocId) {
    $this -> mSrc = $aSrc;
    $this -> mJid = $aJobId;
    $this -> mPid = $aProjectId;
    $this -> mDocId = $aDocId;

    $this -> mWec = new CApi_Wec_Client();
    $this -> mWec -> loadConfig();
  }

  public function getAnnotations() {
    if (!empty($this -> mDocId)) {
      if (CCor_Cfg::get('wec.version') == 10) {
        $lResDir = substr($this -> mDocId, strpos($this -> mDocId, '_') + 1, 6);

        $lPath = CCor_Cfg::get('wec.filestore').$lResDir.'/'.$this -> mDocId.'/';
      } else {
        $lPath = CCor_Cfg::get('wec.filestore').$this -> mDocId.'/';
      }
      $this->dbg('Webcenter Path: '.$lPath);
      if ( $handle = opendir($lPath) ){
        // einlesen der Verzeichnisses
        while (($lFile = readdir($handle)) !== false){
          if ($lFile != "." AND $lFile != ".." AND strcasecmp(substr( $lFile, -5),".xfdf") == 0) {
            echo $lFile.'<br>';
            $lXmlFile = $lPath.$lFile;

            if (is_readable($lXmlFile)) {
              $lFileDatum = date ('Y-m-d H:i:s', filemtime($lXmlFile));
              $lDat = new CUtl_Wec_Xfdf($lXmlFile, $lFileDatum, ''); // new CUtl_Mem_List();
            # var_dump($lXmlFile);
            # var_dump($lFileDatum);
              $lDat -> parse('file');
            #} else {
            #  exit('Konnte '.$lXmlFile.' nicht öffnen.');
            }
          }
        }
        closedir($handle);
      }
    };
  }

  public function getHistoryDump() {
    $lIds = $this -> getFileIds();
    $lRes = $this -> getHistory($lIds);
    if (empty($lRes)) return '';
    $lRet = '<table border="1">';
    foreach ($lRes as $lRow) {
      if (!$lHead) {
        $lRet.= '<tr>';
        foreach ($lRow as $lKey => $lVal) {
          $lRet.= '<th><b>'.htm($lKey).'</b></td>';
        }
        $lRet.= '</tr>';
        $lHead = true;
      }
      $lRet.= '<tr>';
      foreach ($lRow as $lVal) {
        $lRet.= '<td>'.htm($lVal).'</td>';
      }
      $lRet.= '</tr>';

    }
    $lRet.= '</table>';
    return $lRet;
  }

  protected function getFileIds() {
    $lQry = new CApi_Wec_Query_Doclist($this -> mWec);
    $lRes = $lQry -> getList($this -> mPid);
    if (!$lRes) return FALSE;
    $lRet = array();
    if (empty($lRes)) return $lRet;
    foreach ($lRes as $lRow) {
      if (!empty($this -> mDocId)) {
        if ($lRow['wec_ver_id'] == $this -> mDocId) {
          $lRet[$lRow['wec_ver_id']] = $lRow['name'];
        }
      } else {
        $lRet[$lRow['wec_ver_id']] = $lRow['name'];
     }
    }
    return $lRet;
  }

  protected function getHistory($aFileIds) {
    $lRet = array();
    if (empty($aFileIds)) return $lRet;
    $lQry = new CApi_Wec_Xfdf_Annotations();

    foreach ($aFileIds as $lDocId => $lName) {
      $lRes = $lQry -> getListByDocId($lDocId);
      foreach ($lRes as $lRow) {
        # var_dump($lRow);
        $lRow['filename'] = $lName;
        $lRow['hash'] = CApi_Wec_Xfdf_Annotations::getItemHash($lRow);
        $lRet[] = $lRow;
      }
    }
    return $lRet;
  }

  public function getHistoryArray() {
    $lIds = array();
    if (!empty($this -> mDocId)) {
      $lIds[$this -> mDocId] = $this -> mDocId;
      $lRet = $this -> getHistory($lIds);
    } else {
      $lRet = array();
    }

    #$lRet = $this -> getFileIds();
    #$lIds = $this -> getFileIds();
    #$lRet = $this -> getHistory($lIds);
    return $lRet;
  }

  protected function loadUserMap() {
    if (isset($this -> mUsrMap)) return;
    $this -> mUsrMap = array();
    $lQry = new CCor_Qry('SELECT uid,val FROM al_usr_info WHERE iid="wec_uid"');
    foreach ($lQry as $lRow) {
      $this -> mUsrMap[(string)$lRow['val']] = intval($lRow['uid']);
    }
  }

  public function mapUser($aWecUserId) {
    return $aWecUserId;
  }

}