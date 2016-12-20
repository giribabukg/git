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

class CInc_Api_Wec_Updatehistory extends CCor_Obj {

  public function __construct($aSrc, $aJobId, $aProjectId, $aDebug = FALSE, $aDownloadAnnotations = FALSE) {
    $this -> mSrc = $aSrc;
    $this -> mJid = $aJobId;
    $this -> mPid = $aProjectId;
    $this -> mDebug = $aDebug;
    $this -> mDownloadAnnotations = $aDownloadAnnotations;

    $this -> mWec = new CApi_Wec_Client();
    $this -> mWec -> loadConfig();
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
  
  /**
   * Load Combination of WebCenterDocId and FileName
   * @return array
   */
  protected function getFileIds() {
    $lQry = new CApi_Wec_Query_Doclist($this -> mWec);
    $lRes = $lQry -> getList($this -> mPid);
    if (!$lRes) return FALSE;

    $lRet = array();
    if (empty($lRes)) return $lRet;

    foreach ($lRes as $lRow) {
      $lRet[$lRow['wec_doc_id']] = $lRow['name'];
    }
    return $lRet;
  }
  
  /**
   * Get Webcenter History
   * @param array $aFileIds Combination of WebcenterDocId and WebcenterFileName
   * @return array $lRet Array of History + filename+ Hash.
   */
  protected function getHistory($aFileIds) {
    $lRet = array();
    if (empty($aFileIds)) return $lRet;

    $lQry = new CApi_Wec_Query_History($this -> mWec);
    foreach ($aFileIds as $lDocId => $lName) {
      if ($this -> mDebug) {
        echo '<pre>#######---updatehistory.php--- File Name #########</pre>';
        echo '<pre>'.var_export($lName, TRUE).'</pre>';
      }

      if ($this -> mDownloadAnnotations == FALSE) {
        $lRes = $lQry -> getListByDocId($lDocId, TRUE, TRUE, $this -> mDebug);
      } else {
        $lRes = $lQry -> getListByDocName($lName, $this -> mPid, $this -> mDebug);
      }

      foreach ($lRes as $lRow) {
        $lRow['filename'] = $lName;
        $lRow['hash'] = CApi_Wec_Query_History::getItemHash($lRow);
        $lRet[] = $lRow;
      }
    }
    return $lRet;
  }
  
  /**
   * Get Webcenter History
   * @return array $lRet Array of History + filename+ Hash.
   */
  public function getHistoryArray() {
    $lIds = $this -> getFileIds();

    if ($this -> mDebug) {
      echo '<pre>#######---updatehistory.php--- Get DocList: Array[DocId][FileName] #########</pre>';
      echo '<pre>'.var_export($lIds, true).'</pre>';
    }

    $lRet = $this -> getHistory($lIds);

    if ($this -> mDebug) {
      echo '<pre>#######---updatehistory.php--- Get History #########</pre>';
      echo '<pre>'.var_export($lRet, true).'</pre>';
    }

    return $lRet;
  }
  
  /**
   * Load Combination of WebcenterUserId and Portal UserId
   * @return array $mUsrMap Combination of WebcenterUserId and Portal UserId
   */
  protected function loadUserMap() {
    if (isset($this -> mUsrMap)) return;
    $this -> mUsrMap = array();
    $lQry = new CCor_Qry('SELECT uid,val FROM al_usr_info WHERE iid="wec_uid"');
    foreach ($lQry as $lRow) {
      $this -> mUsrMap[(string)$lRow['val']] = intval($lRow['uid']);
    }
  }
  
  /**
   * Get Portal UserId
   * @param string $aWecUserId Webcenter UserId
   * @return int Portal UserId
   */
  public function mapUser($aWecUserId) {
    $lWec = (string)$aWecUserId;
    $this -> loadUserMap();
    if (!empty($this -> mUsrMap[$lWec])) {
      return $this -> mUsrMap[$lWec];
    } else {
      return 0; // unknown user
    }
  }

}