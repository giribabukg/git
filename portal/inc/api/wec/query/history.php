<?php
/**
 * Webcenter Project History Query
 *
 * Description
 *
 * @package    API
 * @subpackage Webcenter
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 */

class CInc_Api_Wec_Query_History extends CApi_Wec_Query {

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
   * @param string $aDocId WebCenter Document ID
   * @param bool $aAnnotations Retrieve regular comments?
   * @param bool $aApprovals Retrieve approval comments?
   */
  public function getListByDocId($aDocId, $aAnnotations = TRUE, $aApprovals = TRUE, $aDebug = FALSE) {
    $this -> setParam('docID', $aDocId);
    $this -> setParam('debugmode', $aDebug);
    if ($aAnnotations) {
      $this -> setParam('annotationinfo', 1);
    }
    if ($aApprovals) {
      $this -> setParam('approvalinfo', 1);
    }
    return $this -> _getHistory(FALSE);
  }

  /**
   * Get the history list, using a valid document name
   *
   * @param string $aDocName WebCenter document name
   */
  public function getListByDocName($aDocName, $aWecProID, $aDebug = FALSE) {
    $this -> setParam('projectid', $aWecProID);
    $this -> setParam('documentname', $aDocName);
    $this -> setParam('debugmode', $aDebug);

    return $this -> _getHistory(TRUE);
  }

  /**
   * Get a list of DocumentVersion IDs for a given Document ID
   * @param string $aDocId
   * @return boolean|array False on error, array of document version, date and version_id's otherwise
   */
  public function getDocVersionIds($aDocId) {
    $this -> setParam('docID', $aDocId);
    $lXml = $this -> query('GetDocumentHistory.jsp');
    $lRes = new CApi_Wec_Response($lXml);
    if (!$lRes -> isSuccess()) {
      return false;
    }
    $lRet = array();

    $lDoc = $lRes -> getDoc();

	$lDescription = (string)$lDoc -> description;
    foreach ($lDoc -> document_version as $lVersionNode) {
      $lDocVerId = (string)$lVersionNode -> docVersionID;
      $lDocVerNr = (string)$lVersionNode -> version_number;
	  $lComment = (string)$lVersionNode -> version_comments;
      $lDocDate  = substr((string)$lVersionNode -> creation_date, 0, -4);
      $lRow['version'] = $lDocVerNr;
      $lRow['date']    = $lDocDate;
      $lRow['version_id'] = $lDocVerId;
	  $lRow['comment'] = (intval($lDocVerNr) > 1 ? $lComment : $lDescription);
      $lRet[] = $lRow;
    }
    return $lRet;
  }

  protected function _getHistory($lDownloadAnnotations = FALSE) {
    if ($lDownloadAnnotations == TRUE) {
      $this -> setParam('returnasfile', 0);
      $lRet = array();

      $lPageIndex = 0;
      $lValidPage = 1;
      do {
        if ($lPageIndex == 1) $lPageIndex = 2; //Webcenter returns index=0 and index=1 for page1 in multiple pages pdf!
        $this -> setParam('pageindex', $lPageIndex);
        $lXml = $this -> query('DownloadAnnotations.jsp');
        $lRes = new CApi_Wec_Response($lXml);
        if (!$lRes -> isSuccess()) {
          break;
        }

        if (0 === strpos($lXml, '<error>')) { //We should handle this in the $lRes -> isSuccess()
          #$lValidPage = 0;
          break;
        }
        $lDoc = $lRes -> getDoc();

        if ($this -> getParam('debugmode')) {
          echo 'lDoc:'.var_export($lDoc, TRUE).BR;
        }

        foreach ($lDoc -> annots as $lAnnotations) {
          $lNumberOfPages = $lPageIndex;
          foreach ($lAnnotations -> children() as $lRow) {
            $lRow = (array)$lRow; // @attributes and contents-richtext are hard to grab by SimpleXMLElement
            if (empty($lRow)) continue;
            if ($this -> getParam('debugmode')) {
              echo 'lRow: '.var_export($lRow, true).BR;
            }

            $lLine = array();
            // User
            $lUser = trim(substr($lRow['@attributes']['title'], strpos($lRow['@attributes']['title'], ' '), strlen($lRow['@attributes']['title'])));
            $lUserInBrackets = trim(substr($lUser, strrpos($lUser, '('), strrpos($lUser, ')')));
            $lUserWithOutBrackets = ltrim($lUserInBrackets, '(');
            $lUserWithOutBrackets = rtrim($lUserWithOutBrackets, ')');
            $lUserBeforeComma = trim(substr($lUser, 0, strpos($lUser, ',')));
            $lUserAfterComma = ltrim($lUser, substr($lUser, 0, strpos($lUser, ',') + 1));
            $lUserAfterComma = rtrim($lUserAfterComma, $lUserInBrackets);
            $lUserAfterComma = trim($lUserAfterComma);
            $lUserID = CCor_Qry::getInt('SELECT uid FROM al_usr_info WHERE iid="wec_usr" AND val="'.$lUserWithOutBrackets.'";'); //
            if (is_int($lUserID)) {
              $lLine['uid'] = $lUserID;
              $lLine['userid'] = $lLine['uid'];
              $lUserName = CCor_Qry::getArrImp('SELECT firstname, lastname FROM al_usr WHERE id="'.$lUserID.'";'); //
              $lUserFirstName = trim(substr($lUserName, 0, strpos($lUserName, ',')));
              $lUserLastName = trim(substr($lUserName, strpos($lUserName, ',') + 1, strlen($lUserName)));
              $lLine['user'] = cat(utf8_decode($lUserLastName), utf8_decode($lUserFirstName),', ');
            }

            // Date
            $lDate = trim(substr($lRow['@attributes']['date'], strpos($lRow['@attributes']['date'], ':') + 1, strpos($lRow['@attributes']['date'], '+') - 2));
            $lDateYear = substr($lDate, 0, 4);
            $lDateMonth = substr($lDate, 4, 2);
            $lDateDay = substr($lDate, 6, 2);
            $lDateHour = substr($lDate, 8, 2);
            $lDateMinute = substr($lDate, 10, 2);
            $lDateSecond = substr($lDate, 12, 2);
            $lLine['date'] = $lDateYear.'-'.$lDateMonth.'-'.$lDateDay.' '.$lDateHour.':'.$lDateMinute.':'.$lDateSecond;

            // Comment
            $lRowContentsRichtext = (array)$lRow['contents-richtext']; // @attributes and contents-richtext are hard to grab by SimpleXMLElement
            $lRowBody = (array)$lRowContentsRichtext['body']; // @attributes and contents-richtext are hard to grab by SimpleXMLElement
            if (is_array($lRowBody['p'])) {
              $lRowBody['p'] = implode(' ', $lRowBody['p']);
            }
            if (is_object($lRowBody['p'])) {
              $lTmp = get_object_vars($lRowBody['p']);
              $lRowBody['p'] = implode(' ', $lTmp);
            }
            $lPageNumber = $lPageIndex;
            if ($lPageIndex == 0) {
              $lLine['comment'] = $lRowBody['p'];
            } else {
              $lPageText = CCor_Cfg::get('wec.pagetext', '(Page %s)');
              $lLine['comment'] = sprintf($lPageText, $lPageNumber).' '.$lRowBody['p'];
            }
            // Type
            $lLine['typ'] = htWecComment;

            $lLine['internal_version'] = '';
            $lLine['version'] = '';

            $lRet[] = $lLine;
          }
        }

        $lPageIndex++;
      } while ($lValidPage == 1);

      return $lRet;
    } else {
      $lXml = $this -> query('GetDocumentHistory.jsp');

      #$lHdl = fopen('inc/req/xml'.date('Y-m-d-H-i-s').'.xml', 'w+');
      #fwrite($lHdl, var_export($lXml, true));
      #fclose($lHdl);

      $lRes = new CApi_Wec_Response($lXml);
      if (!$lRes -> isSuccess()) {
        return false;
      }

      $lRet = array();

      $lDoc = $lRes -> getDoc();

      if ($this -> getParam('debugmode')) {
        echo 'lDoc:'.var_export($lDoc, true).BR;
      }

      foreach ($lDoc -> document_version as $lVersionNode) {
        $lVerStr = (string)$lVersionNode -> version_number;
        // ---------------------------------------------------------------------------------------------------------
        // entweder ueber die API oder ueber xfdf
        // ---------------------------------------------------------------------------------------------------------
        if (CCor_Cfg::get('wec.api.annotation', False)) {

          // ---- alte Version mit Webcenter API -----------------------------------------------------------------------------------------------------
          #$lVersionNode = $lDoc -> document_version; // TODO: loop over versions
          if (isset($lVersionNode -> approvals -> approval -> approval_comment_list)) {
          $lRoot = $lVersionNode -> approvals -> approval -> approval_comment_list;
          foreach ($lRoot -> children() as $lRow) {
            if (empty($lRow)) continue;
            $lLine = array();
            $lLine['internal_version'] = '';
            $lLine['date']             = substr($lRow -> date, 0, 19);
            $lLine['version']          = $lVerStr;
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

        if (isset($lVersionNode -> annotations)) {
          $lRoot = $lVersionNode -> annotations;
          foreach ($lRoot -> children() as $lRow) {
            if (empty($lRow)) continue;
            $lLine = array();
            $lLine['internal_version'] = '';
            $lLine['date']             = substr($lRow -> annotation_date, 0, 19);
            $lLine['version']          = $lVerStr;
            $lLine['typ']              = htWecComment;
            $lUsr = $lRow -> annotation_author -> user;
            $lLine['user'] = cat(utf8_decode($lUsr -> lastname), utf8_decode($lUsr -> firstname),', ');
            $lLine['uid']  = (string)$lUsr -> userID;
            $lCom = $lRow -> annotation_text;
            $lLeft  = strpos($lCom, '"');
            $lRight = strrpos($lCom, '"');
            if ($lLeft and $lRight) {
              $lCom = substr($lCom, $lLeft+1, $lRight - $lLeft -1);
            }
            $lLine['comment'] = $lCom;
            $lRet[] = $lLine;
          }
        }
        } else {
          // ---- neue Version mit xfdf Dateien -----------------------------------------------------------------------------------------------------
          if (isset($lVersionNode -> annotations)) {
            $lXfdf = new CApi_Wec_Xfdf_Annotations();
            if ($this -> getParam('debugmode')) {
              echo 'lXfdf: '.var_export($lXfdf, true).BR;
            }
            $lXRes = $lXfdf -> getListByDocId($lVersionNode -> docVersionID, $this -> getParam('debugmode'));
            if ($this -> getParam('debugmode')) {
              echo 'lXRes: '.var_export($lXRes, true).BR;
            }
            #$lHdl = fopen('inc/req/xfdf'.date('Y-m-d-H-i-s').'.xml', 'w+');
            #$dump = var_export($lXRes, true);
            #fwrite($lHdl, $dump);
            #fclose($lHdl);
            if (!empty($lXRes)) {
              if ($lXRes !== False) {
                foreach ($lXRes as $lKey => $lVal) {
                  if (is_array($lVal)) {
                    $lLine = array();
                    $lLine['internal_version'] = '1';
                    $lLine['date']             = $lVal['date'];
                    $lLine['version']          = $lVerStr;
                    $lLine['typ']              = htWecComment;
                    $lLine['user']             = $lVal['user'];
                    if (isset($lVal['uid'])) {
                      $lLine['userid'] = $lVal['uid'];  // Hier stimmt die Userid schon
                      $lLine['uid'] = $lVal['wecuserid'];
                    } else {
                      $lLine['uid']  = '0';
                    }
                    $lLine['comment']          = $lVal['comment'];
                    $lRet[] = $lLine;
                    if ($this -> getParam('debugmode')) {
                      echo 'M:lLine: '.var_export($lLine, true).BR;
                    }
                  }
                }
              }
            }
            $lRoot = $lVersionNode -> annotations;
            foreach ($lRoot -> children() as $lRow) {
              if (empty($lRow)) continue;
              if ($this -> getParam('debugmode')) {
                echo 'lRow: '.var_export($lRow, true).BR;
              }
              $lLine = array();
              $lLine['internal_version'] = '';
              $lLine['date']             = substr($lRow -> annotation_date, 0, 19);
              $lLine['version']          = $lVerStr;
              $lLine['typ']              = htWecComment;
              $lUsr = $lRow -> annotation_author -> user;
              $lLine['user'] = cat(utf8_decode($lUsr -> lastname), utf8_decode($lUsr -> firstname),', ');
              $lLine['uid']  = (string)($lUsr -> userID);

              if ($this -> getParam('debugmode')) {
                echo 'USER: '.$lUsr -> userID.BR;
              }

              $lCom = $lRow -> annotation_text;
              $lLeft  = strpos($lCom, '"');
              $lRight = strrpos($lCom, '"');
              $lx = '';
              if ($lLeft and $lRight) {
                $lx = substr($lCom, $lRight+1);
                $lCom = substr($lCom, $lLeft+1, $lRight - $lLeft -1);
              }
              $lLine['comment'] = $lCom;

              // nach "geloescht" suchen
              $lpos = strpos($lx, CCor_Cfg::get('wec.pat.deleted.search', 'scht von'));
              if ($lpos == 6) {
                $lLine['internal_version'] = '1';
                $lLine['comment'] = utf8_encode(CCor_Cfg::get('wec.pat.deleted.set', 'Gel�scht: ')).$lCom;
                $lRet[] = $lLine;
                if ($this -> getParam('debugmode')) {
                  echo 'C:Line: '.var_export($lLine, true).BR;
                }
              }
            }
          }
        }

        if ($this -> getParam('debugmode')) {
          echo 'RET:'.var_export($lRet, true).BR;
        }
        #return false;
        #$lHdl = fopen('inc/req/req'.date('Y-m-d-H-i-s').'.xml', 'w+');
        #ob_start();
        #var_dump($lRet);
        #$dump = ob_get_clean();
        #fwrite($lHdl, $dump);
        #fclose($lHdl);
      }

      return $lRet;

    }

  }

  public static function getItemHash($aArr) {
    $lRet = 'D'.$aArr['date'].'U'.$aArr['uid'].'T'.$aArr['typ'].'C'.$aArr['comment'].'IV'.$aArr['internal_version'];
    // $lRet.= date('Y-m-d-H-i-s');
    $lRet = sha1($lRet);
    return $lRet;
  }

  public static function updateHistory($aMid, $aSrc, $aJid) {
    //$lAnnotsByApi = true;    // Kommentar aus Annotationen zusammensetzen
    $lAnnotsByApi = false;   // Kommentar nicht aus Annotationen zusammensetzen

    $lApl = new CApp_Apl_Loop($aSrc, $aJid, 'apl', $aMid);

    // Get Webcenter project id from shadow table
    $lSql = 'SELECT wec_prj_id FROM al_job_shadow_'.$aMid.' WHERE jobid='.esc($aJid);
    $lWecPid = CCor_Qry::getStr($lSql);

    if (empty($lWecPid)) {
      return FALSE;
    }
    $lDebug = FALSE;
    $lDownloadAnnotations = TRUE;
    $lUpd = new CApi_Wec_Updatehistory($aSrc, $aJid, $lWecPid, $lDebug, $lDownloadAnnotations);

    // load hashes from local history to prevent importing an item twice
    $lHisArr = array();
    $lSql = 'SELECT add_data FROM al_job_his WHERE 1 ';
    $lSql.= 'AND src='.esc($aSrc).' ';
    $lSql.= 'AND mand='.esc($aMid).' ';
    $lSql.= 'AND src_id='.esc($aJid);
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lAdd = $lRow['add_data'];
      if (!empty($lAdd)) {
        $lAddArr = unserialize($lAdd);
        if (!empty($lAddArr['hash'])) {
          $lHisArr[] = $lAddArr['hash'];
        }
      }
    }

    $lIgn = 0;

    $lArr = $lUpd -> getHistoryArray();
    $lHis = new CJob_His($aSrc, $aJid);

    if (!empty($lArr)) {
      foreach ($lArr as $lRow) {
        // item already imported?
        $lHash = CApi_Wec_Query_History::getItemHash($lRow);
        if (in_array($lHash, $lHisArr)) {
          $lIgn++;
          continue;
        }

        if (isset($lRow['userid'])) {
          $lUid = $lRow['userid'];
        } else {
          $lUid = $lUpd -> mapUser($lRow['uid']);
        }
        #echo ($lRow['uid'].' = '.$lUid.LF);
        $lHis -> setUser($lUid);
        $lHis -> setDate($lRow['date']);
        if ($lAnnotsByApi) {
          $lCom = $lApl -> getCurrentUserComment($lUid);
          $lCom = cat($lCom, $lRow['comment'], LF.LF);
        } else {
          $lCom = NULL;
        }
        $lSub = lan('wec.comment');
        $lTyp = $lRow['typ'];

        switch ($lTyp) {
          case htAplOk :
            $lSub = lan('apl.approval');
            $lApl -> setState($lUid, CApp_Apl_Loop::APL_STATE_APPROVED, $lCom, NULL, true);
            break;
          case htAplNok :
            $lSub = lan('apl.amendment');
            $lApl -> setState($lUid, CApp_Apl_Loop::APL_STATE_AMENDMENT, $lCom, NULL, true);
            break;
          case htAplCond :
            $lSub = lan('apl.conditional');
            $lApl -> setState($lUid, CApp_Apl_Loop::APL_STATE_CONDITIONAL, $lCom, NULL, true);
            break;
        }
        $lAdd = array('hash' => $lHash);
        $lHis -> add($lRow['typ'], $lSub, $lRow['comment'], $lAdd);
        if (htWecComment == $lRow['typ']) {
          if ($lAnnotsByApi) {
            $lApl -> addToComment($lUid, $lRow['comment']);
          }
        }
      }
    }
    #echo $lImp.' imported, '.$lIgn.' ignored';
    $lCli = new CApi_Wec_Robot();
    $lCli -> loadConfig();
    $lCli -> logout();
    return TRUE;
  }


}