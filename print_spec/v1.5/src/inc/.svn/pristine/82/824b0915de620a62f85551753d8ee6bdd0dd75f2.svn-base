<?php
/**
 * Approval Loop Webcenter Image List
 *
 * Shows an expandable list of Webcenter-Files with links to the Webcenter
 * Viewer.
 *
 * @package    Job
 * @subpackage Approval Loop
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 */

class CInc_Job_Apl_Page_Annotations extends CCor_Ren {

  protected $mApl;
  protected $mCntUserMsg;
  protected $mCntUserFile;
  protected $mCntAnnotation;
  protected $mPrjVerantw;
  protected $mUsrMaster;
  protected $mWithCheckboxes;
  protected $mDoneUserfiles;
  protected $mIsInAplLoop;
  protected $mUserlist;
  protected $mEnableTextedit;
  protected $mFormid;

  public function __construct($aJob, $aFormid = '') {
    $this -> mJob = $aJob;
    $this -> mCntUserMsg = 0;
    $this -> mCntUserFile = 0;
    $this -> mCntAnnotation = 0;
    $this -> mPrjVerantw = '';
    $this -> mUsrMaster = CCor_Usr::getAuthId();
    $this -> mWithCheckboxes = false;
    $this -> mEnableTextedit = false;
    $this -> mDoneUserfiles = array();
    $this -> mIsInAplLoop = false;
    $this -> mUserlist = array();
    $this -> mFormid = $aFormid;

    if (!empty($this -> mJob)) {
      $this -> mJid = $aJob['jobid'];
      $this -> mSrc = $aJob['src'];
      $this -> mWecPid = $this -> mJob['wec_prj_id'];
      $this -> mApl = new CApp_Apl_Loop($this -> mSrc, $this -> mJid, 'apl', MID, $this -> mJob['webstatus']);
      $this -> mPrjVerantw = $aJob[CCor_Cfg::get('wec.annotation.master', 'per_prj_verantwortlich')];
      $this -> mIsInAplLoop = $this -> mApl -> isInAplLoop($aJob);
      if ($this -> mIsInAplLoop) {
        $this -> mUserlist = $this -> mApl -> getAplUserlist();
        $this -> mAplId = $this->mApl->getLastOpenLoop();
      } else {
        $this -> mAplId = $this->mApl->getLastLoop();
        $lUsr = CCor_Usr::getInstance();
        $lus = array();
        $lus['uid'] = $this -> mUsrMaster;
        $lus['name'] = $lUsr -> getVal('fullname');
        $lus['email'] = $lUsr -> getVal('email');
        $this -> mUserlist[] = $lus;
      }
    }
    $this -> mWecAvail = CCor_Cfg::get('wec.available', true);
    $this -> mDalimAvail = CCor_Cfg::get('dalim.available', false);

    #error_reporting(E_ALL);
    #ini_set('display_errors', TRUE);
  }

  protected function getFiles() {
    if (isset($this->mFiles)) return $this->mFiles;

    $lRet = array();
    if (empty($this -> mWecPid)) return $lRet;
    $lWec = new CApi_Wec_Client();
    $lWec -> loadConfig();
    $lQry = new CApi_Wec_Query_Doclist($lWec);
    $lRet = $lQry -> getList($this -> mWecPid);
    if (empty($lRet)){
      $this -> dbg('WebcenterId: '.$this -> mWecPid. ' has no Document');
    }else{
      $this -> dbg('WebcenterId: '.$this -> mWecPid. ' Webcenter Document List: '.serialize($lRet));
    }
    $this -> mFiles = $lRet;
    return $lRet;
  }

  public function getXml() {
    if (isset($this->mXml)) return $this->mXml;

    $lRet = array();

    if (!empty($this -> mJob)) { // Keine Anzeige der Datei
      $lFil = $this -> getFiles();
      $lNr = 0;
      if (!empty($lFil)) {
        foreach ($lFil as $lRow) {
          if (empty($lRow['wec_ver_id'])) continue;
          $lQry = new CApi_Wec_Xfdf_Annotations('user');
          $this->dbg('FETCHING XFDF--------------------------------------------------------------');
          $lRes = $lQry -> getXmlByDocIdFromApi($this->mPid, $lRow['wec_ver_id']);
          if (!empty($lRes) && !is_array($lRes)) {
            $lDoc = array();
            $lDoc['id'] = $lRow['wec_ver_id'];
            $lDoc['name'] = utf8_encode($lRow['name']);
            $lDoc['xfdf'] = $lRes;
            $this -> dbg('Return Annotation from XFDF: '.serialize($lDoc));
            $lRet[$lRow['wec_ver_id']] = $lDoc;
          } elseif (!empty($lRes) && is_array($lRes)) {
            $lDoc = array();
            foreach ($lRes as $lKey => $lValue) {
              $lDoc[$lKey + 1]['id'] = $lRow['wec_ver_id'];
              $lDoc[$lKey + 1]['name'] = $lRow['name'];
              $lDoc[$lKey + 1]['page'] = $lKey + 1;
              $lDoc[$lKey + 1]['xfdf'] = $lValue;
            }
            $this -> dbg('Return Annotation from XFDF: '.serialize($lDoc));
            $lRet[$lRow['wec_ver_id']] = $lDoc;
          }
        }
      }
    }
    $this->mXml = $lRet;
    return $lRet;
  }

  protected function getDocXml($aDocVersionId) {
    $lXml = $this->getXml();
    return (isset($lXml[$aDocVersionId])) ? $lXml[$aDocVersionId] : array();
  }

  protected function arrayDepth($aArray) {
    if (is_array(reset($aArray))) {
      $lRet = $this -> arrayDepth(reset($aArray)) + 1;
    } else {
      $lRet = 1;
    }

    return $lRet;
  }

  public function getCont() {
    if (empty($this -> mJob)) return ''; // Do not display anything

    $lRet = '<div class="tbl w800">';
    $lRet.= '<div class="th1">'.htm(lan('lib.annotations')).'</div>';
    $lRet.= '<table cellpadding="4" cellspacing="0" class="frm">'.LF;

    if ($this->mDalimAvail) {
      $lRet.= $this->getDalimCont();
    }
    if ($this->mWecAvail) {
      $lRet.= $this->getWecCont();
    }

    $lRet.= '</table>'.LF;
    $lRet.= '</div>'.LF;
    return $lRet;
  }

  protected function getDalimCont() {
    $lRet = '';
    $lRes = $this->getDalimAnnotations();
    foreach ($lRes as $lItm) {
      $lRet.= $this->getCommentLine($lItm);
    }
    return $lRet;
  }

  protected function getWecCont() {

    $lRet = '';
    $lFil = $this -> getFiles();
    if (empty($lFil)) return '';

    $lUid = $this -> mUsrMaster;

    $lFilCnt = 0;
    foreach ($lFil as $lRow) {
      if (empty($lRow['wec_ver_id'])) continue;

      $lRes = $this->getDocXml($lRow['wec_ver_id']);

      if (empty($lRes)) continue;

      $lNam = '';
      if (count($lFil)>1) {
        $lNam = 'X';
      }

      if ($this -> arrayDepth($lRes) < 3) {
        foreach ($lRes as $lKey => $lVal) {
          if(!isset($lVal['uid'])) continue;
          if ($lUid !== $lVal['uid']) continue;
          if ($lNam !== '') {
            $lFilCnt++;
            $lNam = '';
          }
        }
      } else {
        foreach ($lRes as $lKey => $lVal) {
          foreach ($lVal as $lInnerKey => $lInnerValue) {
            if(!isset($lInnerValue['uid'])) continue;
            if ($lUid !== $lInnerValue['uid']) continue;
            if ($lNam !== '') {
              $lFilCnt++;
              $lNam = '';
            }
          }
        }
      }
    }

    foreach ($lFil as $lRow) {
      if (empty($lRow['wec_ver_id'])) continue;

      $lRes = $this->getDocXml($lRow['wec_ver_id']);

      if (empty($lRes)) continue;

      $lNam = '';
      if ($lFilCnt>1) {
        $lNam= '<tr>';
        $lNam.= '<td class="th3" colspan="4">';
        $lNam.= htm(lan('lib.file').': '.$lRow['name']);
        $lNam.= '</td>';
        $lNam.= '</tr>'.LF;
      }

      if ($this -> arrayDepth($lRes) < 3) {
        foreach ($lRes as $lKey => $lVal) {
          $lNr = -1;
          if ($lUid == $lVal['uid']) {
            if ($lNam !== '') {
              $lRet.= $lNam;
              $lNam = '';
            }
            $lItem = array();
            $lItem['date'] = $lVal['date'];
            if ($lNr != $lVal['nr']) {
              $lItem['nr'] = $lVal['nr'];
            }
            $lItem['comment'] = $lVal['comment'];

            $lRet.= $this->getCommentLine($lItem);
          }
        }
      } else {
        foreach ($lRes as $lKey => $lVal) {
          foreach ($lVal as $lInnerKey => $lInnerValue) {
            $lNr = -1;
            if ($lUid != $lInnerValue['uid']) continue;
            if ($lNam !== '') {
              $lRet.= $lNam;
              $lNam = '';
            }
            $lItem = array();
            $lItem['date'] = $lInnerValue['date'];
            if ($lNr != $lInnerValue['nr']) {
              $lItem['nr'] = $lInnerValue['nr'];
            }
            $lItem['key'] = $lKey + 1;
            $lItem['comment'] = $lInnerValue['comment'];

            $lRet.= $this->getCommentLine($lItem);
          }
        }
      }
    }

    return $lRet;
  }

  protected function getCommentLine($aRow) {
    $lRet.= '<tr>';
    $lRet.= '<td width=1%></td>';

    $lRet.= '<td width=4%>';
    if (!empty($aRow['nr'])) {
      $lRet.= $aRow['nr'].'.';
    }
    $lRet.= '</td>';

    $lRet.= '<td width=20%>';
    $lDat = new CCor_Datetime();
    $lDat -> setSql($aRow['date']);
    $lDate = $lDat -> getFmt(lan('lib.datetime.short'));
    $lRet.= htm($lDate);
    if (!empty($aRow['key'])) {
      $lRet.= NB.$aRow['key'];
    }
    $lRet.= '</td>';

    $lRet.= '<td width=75%>';
    $lRet.= nl2br(htm($aRow['comment']));
    $lRet.= '</td>';

    $lRet.= '</tr>';
    return $lRet;
  }

  public function setEnableTextedit($aWhat) {
    $this -> mEnableTextedit = $aWhat;
    return $this -> mEnableTextedit;
  }

  protected function enableTextedit() {
    return $this -> mEnableTextedit;
  }

  public function setWithCheckboxes($aWhat) {
    $this -> mWithCheckboxes = $aWhat;
    return $this -> mWithCheckboxes;
  }

  protected function enableCheckbox() {
    return $this -> mWithCheckboxes;
  }

  protected function optRowsByCol($aData, $aCols) {
    $l = strlen($aData);
    $n = substr_count($aData, LF);
    return intval($l / $aCols) + 1 + $n;
  }

  protected function getUserMsg($aUid, $aUname = "") {
    $lcolspan = '5';
    // ----------------------------------------------------------------
    // Benutzer Kommentare
    // ----------------------------------------------------------------
    // Projektverantwortlicher wird nicht genommen
    // ----------------------------------------------------------------
    if (!$this -> mIsInAplLoop) {
      $this -> mCntUserMsg++;
      $lRet = '';
      $lRet.= '<tr>';
      $lRet.= '<td class="nw" width=1%>&nbsp;</td>';
      $lRet.= '<td class="nw" width=4%>&nbsp;</td>';
      $lRet.= '<td class="nw" width=20%>&nbsp;</td>';
      $lRet.= '<td class="nw" width=75%>&nbsp;';
      $lAli = 'msg.'.$this -> mCntUserMsg;
      $lRet.= '<input type="hidden" name="old['.$lAli.']" value="'.htm($aUid).'" />';
      $lAli = 'msg.empty.'.$this -> mCntUserMsg;
      $lRet.= '<input type="hidden" name="old['.$lAli.']" value="1" />';
      $lRet.= '</td>';
      $lRet.= '</tr>';
      return $lRet;
    }

    $lMsg = $this -> mApl -> getCurrentUserComment($aUid);
    $lDat = $this -> mApl -> getCurrentUserDate($aUid);

    $lArr = $this -> mApl -> getAllComments();
    $lSta = '';
    if (empty($lArr)) return '';
    foreach ($lArr as $lRow) {
      if ($aUid == $lRow['user_id']) {
        $lSta = $lRow['status'];
        switch ($lSta) {
          case CApp_Apl_Loop::APL_STATE_AMENDMENT:
            $lSta = lan('apl.amendment');
            BREAK;
          case CApp_Apl_Loop::APL_STATE_APPROVED:
            $lSta = lan('apl.approval');
            BREAK;
          case CApp_Apl_Loop::APL_STATE_CONDITIONAL:
            $lSta = lan('apl.conditional');
            BREAK;
          default:
            $lSta = '';
        }
        BREAK;
      }
    }

    // ----------------------------------------------------------------
    // Leerer Kommentar wird nicht angezeigt
    // ----------------------------------------------------------------
    if ($lMsg == '') {
      $lchecked = '';
    } else {
      $lchecked = 'checked="checked"';
    }

    $this -> mCntUserMsg++;

    $lRet = '';

    if ($this -> mUsrMaster !== $aUid) {
      $lRet.= '<tr>';
      if ($lSta != '') {
        $lRet.= '<td class="th3" colspan="'.$lcolspan.'">'.lan('lib.msg').'</td>'.LF;
      } else {
        $lRet.= '<td class="th3" colspan="'.$lcolspan.'">'.lan('lib.msg').' ('.htm($lSta).')</td>'.LF;
      }
      $lRet.= '</tr>';
    }

    $lAli = 'msg.'.$this -> mCntUserMsg;
    $lName = $aUid; // = $this -> mApl -> getLastOpenLoop();
    $lRet.= '<tr>';
    $lRet.= '<td class="nw"  width="1%">';
    if ($this -> enableCheckbox()) {
      $lRet.= '<input type="hidden" name="old['.$lAli.']" value="'.htm($lName).'" />';
      if ($this -> mUsrMaster !== $aUid) {
        $lRet.= '<input type="checkbox" name="val['.$lAli.']" value="'.htm($lName).'" '.$lchecked.' />';
      }
      $lAli = 'msg.state.'.$this -> mCntUserMsg;
      $lRet.= '<input type="hidden" name="val['.$lAli.']" value="'.htm($lSta).'" />';
      $lAli = 'msg.date.'.$this -> mCntUserMsg;
      $lRet.= '<input type="hidden" name="val['.$lAli.']" value="'.htm($lDat).'" />';
      $lAli = 'msg.com.'.$this -> mCntUserMsg;
      $lRet.= '<input type="hidden" name="val['.$lAli.']" value="'.htm($lMsg).'" />';
      $lAli = 'msg.usr.'.$this -> mCntUserMsg;
      $lRet.= '<input type="hidden" name="val['.$lAli.']" value="'.htm($aUname).'" />';
    }
    $lRet.= '</td>'.LF;

    if ($this -> mUsrMaster !== $aUid) {
      $lRet.= '<td class="nw" width=4%>';
      $lRet.= '&nbsp;';
      $lRet.= '</td>';
      $lRet.= '<td class="nw" width=20%>';
      $lRet.= htm($lDat);
      $lRet.= '</td>';
      $lRet.= '<td  width=72%>';
      if ($this -> enableTextedit()) {
        $lAli = 'msg.edit.'.$this -> mCntUserMsg;
        $lRet.= '<textarea name="val['.$lAli.']" cols="90" rows="' . $this -> optRowsByCol($lMsg, 90). '" wrap="virtual" readonly="readonly">';
        $lRet.= htm($lMsg);
        $lRet.= '</textarea>';
      } else {
        if ($lMsg == '') {
          $lRet.= '&nbsp;';
        } else {
          $lRet.= htm($lMsg);
        }
      }
      $lRet.= '</td>';

      // Editierbutton oder nicht !
      $lRet.= '<td class="nw" width=3%>';
      if ($this -> enableTextedit()) {        // && ($lMsg != '')
        $lAlied = 'msg.edit.'.$this -> mCntUserMsg;
        $lAli = 'msg.btn.'.$this -> mCntUserMsg;
        $lAlicc = 'msg.com.'.$this -> mCntUserMsg;
        $lJs = 'javascript:Flow.Std.togfRdOnly(\''.$lAli.'\',\''.$lAlied.'\',\''.$lAlicc.'\')';
        $lAli = 'msg.btn.'.$this -> mCntUserMsg;
        $lRet.= btn('', $lJs, 'img/ico/16/edit.gif','button', array('name' => 'btn['.$lAli.']','value' => '0' ));
        $lRet.= btn('', $lJs, 'img/ico/16/cancel.gif','button', array('name' => 'lck['.$lAli.']', 'style' => 'display:none','value' => '0'));
        $lRet.= '<input type="hidden" name="val['.$lAli.']" value="0" />';
      } else {
        $lRet.= '&nbsp;';
      }
      $lRet.= '</td>';
    }
    $lRet.= '</tr>';

    if ($this -> mUsrMaster !== $aUid) {
      $lRet.= $this -> getUserFiles($aUid);
    }
    return $lRet;
  }

  protected function getUserFiles($aUid) {
    // ----------------------------------------------------------------
    // Benutzer Kommentare
    // ----------------------------------------------------------------
    // Projektverantwortlicher wird nicht genommen
    // ----------------------------------------------------------------
    if ($this -> mUsrMaster == $aUid) return '';

    $lFiles = $this -> mApl -> getCurrentUserFiles($aUid);
    // ----------------------------------------------------------------
    // Leerer Kommentar wird nicht angezeigt
    // ----------------------------------------------------------------
    if ($lFiles == '') return '';
    // Dateien nur einmal anzeigen
    if (isset($this -> mDoneUserfiles[$aUid]) && $this -> mDoneUserfiles[$aUid] == 1) return '';

    $lcolspan = '5';
    $lRet = '';
    $lRet.= '<tr>';
    $lRet.= '<td class="th3" colspan="'.$lcolspan.'">'.htm(lan('job-fil.menu')).'</td>'.LF;
    $lRet.= '</tr>';

    $lArr = explode(LF, $lFiles);
    foreach ($lArr as $lFile) {
      $lFile = trim($lFile);
      if ($lFile !== '') {
        $this -> mCntUserFile++;
        $lAli = 'fil.u'.$aUid.'.n'.$this -> mCntUserFile;
        $lName = $lFile;
        $lRet.= '<tr>';
        $lRet.= '<td class="nw" width=1%>';
        if ($this -> enableCheckbox()) {
          $lRet.= '<input type="hidden" name="old['.$lAli.']" value="'.htm($lName).'" />';
          $lRet.= '<input type="checkbox" name="val['.$lAli.']" value="'.htm($lName).'" checked="checked" />';
        }
        $lRet.= '</td>'.LF;
        $lRet.= '<td width=4%>';
        $lRet.= '</td>';
        $lRet.= '<td width=20%>';
        $lRet.= '&nbsp;';
        $lRet.= '</td>';
        $lRet.= '<td width=74%>';
        $lRet.= htm($lFile);
        $lRet.= '</td>';
        $lRet.= '<td width=1%>';
        $lRet.= '&nbsp;</td>';
        $lRet.= '</tr>';
      }
    }

    $this -> mDoneUserfiles[$aUid] = 1;
    return $lRet;
  }

  public function getHiddenElements() {
    $lRet = '';
    $lRet.= '<input type="hidden" name="cntusermsg" value="'.htm($this -> mCntUserMsg).'" />';
    $lRet.= '<input type="hidden" name="cntuserfile" value="'.htm($this -> mCntUserFile).'" />';
    $lRet.= '<input type="hidden" name="cntannotation" value="'.htm($this -> mCntAnnotation).'" />';
    $lRet.= '<input type="hidden" name="activexfdf" value="1" />';
    $lRet.= '<input type="hidden" name="prjmaster" value="'.$this -> mPrjVerantw.'" />';

    return $lRet;
  }

  public function reset() {
    $this -> mCntUserMsg = 0;
    $this -> mCntUserFile = 0;
    $this -> mCntAnnotation = 0;
  }

  public function getAnnotationList($aDoPrjMaster) {
    $lRet = '';
    $this->dbg('DoPrjMaster: '. $aDoPrjMaster . ': ' . $this -> mUsrMaster);
    if (!$aDoPrjMaster) {
      $lUid = $this -> mUsrMaster;
      foreach ($this -> mUserlist as $lUsr) {
        if ($lUid == $lUsr['uid']) {
          $lRet.= $this -> extAnnotationList($lUsr['uid'], $lUsr['name']);
        }
      }
      return $lRet;
    }

    foreach ($this -> mUserlist as $lUsr) {
      if ($this -> mUsrMaster != $lUsr['uid']) {
        #echo 'V'.$lUsr['uid'].BR;
        $lRet.= $this -> extAnnotationList($lUsr['uid'], $lUsr['name']);
      }
    }
    foreach ($this -> mUserlist as $lUsr) {
      if ($this -> mUsrMaster == $lUsr['uid']) {
        #echo 'N'.$lUsr['uid'].BR;
        $lRet.= $this -> extAnnotationList($lUsr['uid'], $lUsr['name']);
      }
    }
    return $lRet;
  }

  protected function extAnnotationList($aUid, $aUname) {
    $lRet = '';
    if (empty($this -> mJob)) return; // Keine Anzeige der Datei
    if ($this->mDalimAvail) {
      $lRet.= $this->extDalimAnnotationList($aUid, $aUname);
    }
    if ($this->mWecAvail) {
      $lRet.= $this->extWecAnnotationList($aUid, $aUname);
    }
    return $lRet;
  }

  protected function loadDalimAnnotations() {
    $lSql = 'SELECT id,user_id,doc,page,datum,content FROM al_dalim_notes WHERE jobid='.esc($this->mJid).' ';
    $lSql.= 'AND content!="" ';
    $lSql.= 'AND loop_id="'.intval($this -> mAplId).'" ';
    $lSql.= 'ORDER BY doc,datum ';
    
    $lOldDoc = '';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      if ($lOldDoc != $lRow['doc']) {
        $lNr = 1;
        $lOldDoc = $lRow['doc'];
      }
      $lUid = $lRow['user_id'];
      $lItm = array();
      $lItm['nr'] = $lNr;
      $lItm['uid'] = $lUid;
      $lItm['date'] = $lRow['datum'];
      $lItm['comment'] = $lRow['content'];
      $lItm['name'] = strrchr($lRow['doc'], DS);
      $lItm['wec_ver_id'] = md5($lRow['doc']);
      $lRes[] = $lItm;
      $lNr++;
    }
    return $lRes;
  }
  
  protected function getDalimAnnotations() {
    if (!$this->mDalimLoaded) {
      $this->mDalimAnnotations = $this->loadDalimAnnotations();
      $this->mDalimLoaded = true;
    }
    return $this->mDalimAnnotations;
  }

  protected function extDalimAnnotationList($aUid, $aUname) {
    $lRet = '';
    #$this -> mUsrMaster;
    $lColSpan = 5;
    $this->mColspan = 5;

    $lRet.= $this -> getUserHeader($aUname);
    $lRet.= $this -> getUserMsg($aUid, $aUname);

    $lHed = '<tr><td class="th3" colspan="'.$lcolspan.'">'.htm(lan('lib.annotations')).'</td></tr>'.LF;

    $lRet.= '<div class="tbl w100p">';
    $lRet.= '<table cellpadding="4" cellspacing="0" class="frm w100p">'.LF;
    $lRet.= '<tr>';
    $lRet.= '<td class="th2" colspan="'.$lColSpan.'">';
    $lRet.= htm($aUname);
    $lRet.= '</td>'.LF;
    $lRet.= '</tr>';
    
    $lRes = $this->getDalimAnnotations();
    $lRet.= $this->getExtCommentLines($aUid, $lRes, 1);

    $lRet.= '</table>'.LF;
    $lRet.= '</div>'.LF;
    return $lRet;
  }


  protected function getFileNameHeader($aName) {
    $lNam = '<tr>';
    $lNam.= '<td class="th3" colspan="'.$this->mColspan.'">';
    $lNam.= htm(lan('lib.file').': '.$lRow['name']);
    $lNam.= '</td>';
    $lNam.= '</tr>'.LF;
    return $lNam;
  }

  protected function getUserHeader($aUname) {
    $lRet = '<div class="tbl w800">';
    $lRet.= '<table cellpadding="4" cellspacing="0" class="frm">'.LF;
    $lRet.= '<tr>';
    $lRet.= '<td class="th2" colspan="'.$this->mColspan.'" width="100%">';
    $lRet.= htm($aUname);
    $lRet.= '</td>'.LF;
    $lRet.= '</tr>';
    return $lRet;
  }

  protected function getExtCommentLines($aUid, $aRes, $aPage) {
    $lRet = '';
    $lHed = '<tr><td class="th3" colspan="'.$this->mColspan.'">'.htm(lan('lib.annotations')).'</td></tr>'.LF;

    foreach ($aRes as $lKey => $lVal) {

      $lNr = -1;
      if(!isset($lVal['uid'])) continue;
      if ($aUid != $lVal['uid']) continue;
      if ($lHed !== '') {
        $lRet.= $lHed;
        $lHed = '';
      }
      if ($lNam !== '') {
        $lRet.= $lNam;
        $lNam = '';
        $lUid = '';
      }

      $lDat = new CCor_Datetime();
      $lDat -> setSql($lVal['date']);
      $lDate = $lDat -> getFmt(lan('lib.datetime.short'));

      $lComment = deHtm($lVal['comment']);
      $this -> mCntAnnotation++;
      $lAli = 'annots.'.$this -> mCntAnnotation;
      $lName = $aFileDescriptor;
      $lRow['wec_ver_id'].'.'.$lVal['name'];

      $lRet.= '<tr>';
      $lRet.= '<td class="nw" width=1%>';
      if ($this -> enableCheckbox()) {
        $lRet.= '<input type="hidden" name="old['.$lAli.']" value="'.htm($lName).'" />';
        $lRet.= '<input type="checkbox" name="val['.$lAli.']" value="'.htm($lName).'" checked="checked" />';
        $lAli = 'annots.usr.'.$this -> mCntAnnotation;
        $lRet.= '<input type="hidden" name="val['.$lAli.']" value="'.htm($aUid).'" />';
        $lAli = 'annots.page.'.$this -> mCntAnnotation;
        $lRet.= '<input type="hidden" name="val['.$lAli.']" value="'.htm($aPage).'" />';
        $lAli = 'annots.nr.'.$this -> mCntAnnotation;
        $lRet.= '<input type="hidden" name="val['.$lAli.']" value="'.htm($lVal['nr']).'" />';
        $lAli = 'annots.date.'.$this -> mCntAnnotation;
        $lRet.= '<input type="hidden" name="val['.$lAli.']" value="'.htm($lDate).'" />';
        $lAli = 'annots.com.'.$this -> mCntAnnotation;
        $lRet.= '<input type="hidden" name="val['.$lAli.']" value="'.htm($lComment).'" />';
      }
      $lRet.= '</td>'.LF;
      if ($lNr != $lVal['nr']) {
        $lNr = $lVal['nr'];
        $lRet.= '<td width=4%>';
        $lRet.= htm($lNr).'.';
        $lRet.= '</td>';
      }   else {
        $lRet.= '<td width=4%>';
        $lRet.= '</td>';
      }
      $lRet.= '<td width=20%>';
      $lRet.= htm($lDate);
      if (!empty($aPage)) {
        $lRet.= NB.' page '.$aPage;
      }
      $lRet.= '</td>';

      $lRet.= '<td width=72%>';
      if ($this -> enableTextedit()) {
        $lAli = 'ann.edit.'.$this -> mCntAnnotation;
        $lRet.= '<textarea name="val['.$lAli.']" cols="90" rows="' . $this -> optRowsByCol($lComment, 90). '" wrap="virtual" readonly="readonly">';
        $lRet.= htm($lComment);
        $lRet.= '</textarea>';
      } else {
        $lRet.= htm($lComment);
      }
      $lRet.= '</td>';

      $lRet.= '<td width=3%>';
      if ($this -> enableTextedit()) {
        $lAlied = 'ann.edit.'.$this -> mCntAnnotation;
        $lAli = 'ann.btn.'.$this -> mCntAnnotation;
        $lAlicc = 'annots.com.'.$this -> mCntAnnotation;
        $lJs = 'javascript:Flow.Std.togfRdOnly(\''.$lAli.'\',\''.$lAlied.'\',\''.$lAlicc.'\')';
        $lRet.= btn('', $lJs, 'img/ico/16/edit.gif','button', array('name' => 'btn['.$lAli.']','value' => '0' ));
        $lRet.= btn('', $lJs, 'img/ico/16/cancel.gif','button', array('name' => 'lck['.$lAli.']', 'style' => 'display:none','value' => '0'));
        $lRet.= '<input type="hidden" name="val['.$lAli.']" value="0" />';
      } else {
        $lRet.= '&nbsp;';
      }
      $lRet.= '</td>';

      $lRet.= '</tr>';

    }
    return $lRet;
  }


  protected function extWecAnnotationList($aUid, $aUname) {
    #return '';

    if (CCor_Cfg::get('wec.api.annotation')) return '';
    $this -> dbg('Read Annotations across XFDF Files');
    $lcolspan = '5';
    $lRet = '';
    if (!empty($this -> mJob)) { // Keine Anzeige der Datei
      $lFil = $this -> getFiles();
      # var_dump($lFil);
      # echo count($lFil).BR;

      $lRet.= '<div class="tbl w800">';
      $lRet.= '<table cellpadding="4" cellspacing="0" class="frm">'.LF;
      $lRet.= '<tr>';
      $lRet.= '<td class="th2" colspan="'.$lcolspan.'" width="100%">';
      $lRet.= htm($aUname);
      $lRet.= '</td>'.LF;
      $lRet.= '</tr>';
      $lRet.= $this -> getUserMsg($aUid, $aUname);

      $lHed = '<div class="th3">'.htm(lan('lib.annotations')).'</div>';

      $lHed = '<tr><td class="th3" colspan="'.$lcolspan.'">'.htm(lan('lib.annotations')).'</td></tr>'.LF;

      $lCn = 0;

      $lFirst = true;
      if (!empty($lFil)) {
        // Zunaechst werden die Dateien gezaehlt
        $lFilCnt = 0;
        foreach ($lFil as $lRow) {
          if (empty($lRow['wec_ver_id'])) continue;

          $lQry = new CApi_Wec_Xfdf_Annotations('user');
          $lRes = $lQry -> getListByDocId($lRow['wec_ver_id']);

          if (empty($lRes)) continue;

          $lNam = '';
          if (count($lFil)>1) {
            $lNam = 'X';
          }

          if ($this -> arrayDepth($lRes) < 3) {
          foreach ($lRes as $lKey => $lVal) {
            if(!isset($lVal['uid'])) continue;
            if ($aUid != $lVal['uid']) continue;
            if ($lNam !== '') {
              $lFilCnt++;
              $lNam = '';
            }
          }
          } else {
            foreach ($lRes as $lKey => $lVal) {
              foreach ($lVal as $lInnerKey => $lInnerValue) {
                if(!isset($lInnerValue['uid'])) continue;
                if ($aUid != $lInnerValue['uid']) continue;
                if ($lNam !== '') {
                  $lFilCnt++;
                  $lNam = '';
        }
              }
            }
          }
        }

        foreach ($lFil as $lRow) {
          $lUid = '';

          if (empty($lRow['wec_ver_id'])) continue;

          $lQry = new CApi_Wec_Xfdf_Annotations('user');
          $lRes = $lQry -> getListByDocId($lRow['wec_ver_id']);
          $lIsMultipage = $lQry -> getIsMultipage();

          if (empty($lRes)) continue;

          $lNam = '';
          if ((count($lFil)>1) && ($lFilCnt>1)) {
            $lNam= '<tr>';
            $lNam.= '<td class="th3" colspan="'.$lcolspan.'">';
            $lNam.= htm(lan('lib.file').': '.$lRow['name']);
            $lNam.= '</td>';
            $lNam.= '</tr>'.LF;
          }

          $lUcom = false;

//           if (!$lIsMultipage) {
//             $lRes[] = $lRes;
//           }

          if ($this -> arrayDepth($lRes) < 3) {
          foreach ($lRes as $lKey => $lVal) {
              $lNr = -1;
            if(!isset($lVal['uid'])) continue;
            if ($aUid != $lVal['uid']) continue;
            if (true) {
              if ($lHed !== '') {
                $lRet.= $lHed;
                $lHed = '';
              }
              if ($lNam !== '') {
                $lRet.= $lNam;
                $lNam = '';
                $lUid = '';
              }

              $lDat = new CCor_Datetime();
              $lDat -> setSql($lVal['date']);
              $lDate = $lDat -> getFmt(lan('lib.datetime.short'));

                $lPage = 0;

              $lComment = deHtm($lVal['comment']);
              $this -> mCntAnnotation++;
              $lAli = 'annots.'.$this -> mCntAnnotation;
              $lName = $lRow['wec_ver_id'].'.'.$lVal['name'];

              $lRet.= '<tr>';
              $lRet.= '<td class="nw" width=1%>';
              if ($this -> enableCheckbox()) {
                $lRet.= '<input type="hidden" name="old['.$lAli.']" value="'.htm($lName).'" />';
                $lRet.= '<input type="checkbox" name="val['.$lAli.']" value="'.htm($lName).'" checked="checked" />';
                $lAli = 'annots.usr.'.$this -> mCntAnnotation;
                $lRet.= '<input type="hidden" name="val['.$lAli.']" value="'.htm($aUid).'" />';
                  $lAli = 'annots.page.'.$this -> mCntAnnotation;
                  $lRet.= '<input type="hidden" name="val['.$lAli.']" value="'.htm($lPage).'" />';
                $lAli = 'annots.nr.'.$this -> mCntAnnotation;
                $lRet.= '<input type="hidden" name="val['.$lAli.']" value="'.htm($lVal['nr']).'" />';
                $lAli = 'annots.date.'.$this -> mCntAnnotation;
                $lRet.= '<input type="hidden" name="val['.$lAli.']" value="'.htm($lDate).'" />';
                $lAli = 'annots.com.'.$this -> mCntAnnotation;
                $lRet.= '<input type="hidden" name="val['.$lAli.']" value="'.htm($lComment).'" />';
              }
              $lRet.= '</td>'.LF;
              if ($lNr != $lVal['nr']) {
                $lNr = $lVal['nr'];
                $lRet.= '<td width=4%>';
                $lRet.= htm($lNr).'.';
                $lRet.= '</td>';
              }   else {
                $lRet.= '<td width=4%>';
                $lRet.= '</td>';
              }
              $lRet.= '<td width=20%>';
              $lRet.= htm($lDate);
              $lRet.= '</td>';

              $lRet.= '<td width=72%>';
              if ($this -> enableTextedit()) {
                $lAli = 'ann.edit.'.$this -> mCntAnnotation;
                $lRet.= '<textarea name="val['.$lAli.']" cols="90" rows="' . $this -> optRowsByCol($lComment, 90). '" wrap="virtual" readonly="readonly">';
                $lRet.= htm(trim($lComment));
                $lRet.= '</textarea>';
              } else {
                $lRet.= htm($lComment);
              }
              $lRet.= '</td>';

              $lRet.= '<td width=3%>';
              if ($this -> enableTextedit()) {
                $lAlied = 'ann.edit.'.$this -> mCntAnnotation;
                $lAli = 'ann.btn.'.$this -> mCntAnnotation;
                $lAlicc = 'annots.com.'.$this -> mCntAnnotation;
                $lJs = 'javascript:Flow.Std.togfRdOnly(\''.$lAli.'\',\''.$lAlied.'\',\''.$lAlicc.'\')';
                $lRet.= btn('', $lJs, 'img/ico/16/edit.gif','button', array('name' => 'btn['.$lAli.']','value' => '0' ));
                $lRet.= btn('', $lJs, 'img/ico/16/cancel.gif','button', array('name' => 'lck['.$lAli.']', 'style' => 'display:none','value' => '0'));
                $lRet.= '<input type="hidden" name="val['.$lAli.']" value="0" />';
              } else {
                $lRet.= '&nbsp;';
              }
              $lRet.= '</td>';

              $lRet.= '</tr>';
            }
          }
          } else {
            foreach ($lRes as $lKey => $lVal) {
              foreach ($lVal as $lInnerKey => $lInnerValue) {
                $lNr = -1;
                if(!isset($lInnerValue['uid'])) continue;
                if ($aUid != $lInnerValue['uid']) continue;
                if (true) {
                  if ($lHed !== '') {
                    $lRet.= $lHed;
                    $lHed = '';
        }
                  if ($lNam !== '') {
                    $lRet.= $lNam;
                    $lNam = '';
                    $lUid = '';
      }

                  $lDat = new CCor_Datetime();
                  $lDat -> setSql($lInnerValue['date']);
                  $lDate = $lDat -> getFmt(lan('lib.datetime.short'));

                  $lPage = $lKey + 1;

                  $lComment = deHtm($lInnerValue['comment']);
                  $this -> mCntAnnotation++;
                  $lAli = 'annots.'.$this -> mCntAnnotation;
                  $lName = $lRow['wec_ver_id'].'.'.$lInnerValue['name'];

                  $lRet.= '<tr>';
                  $lRet.= '<td class="nw" width=1%>';
                  if ($this -> enableCheckbox()) {
                    $lRet.= '<input type="hidden" name="old['.$lAli.']" value="'.htm($lName).'" />';
                    $lRet.= '<input type="checkbox" name="val['.$lAli.']" value="'.htm($lName).'" checked="checked" />';
                    $lAli = 'annots.usr.'.$this -> mCntAnnotation;
                    $lRet.= '<input type="hidden" name="val['.$lAli.']" value="'.htm($aUid).'" />';
                    $lAli = 'annots.page.'.$this -> mCntAnnotation;
                    $lRet.= '<input type="hidden" name="val['.$lAli.']" value="'.htm($lPage).'" />';
                    $lAli = 'annots.nr.'.$this -> mCntAnnotation;
                    $lRet.= '<input type="hidden" name="val['.$lAli.']" value="'.htm($lInnerValue['nr']).'" />';
                    $lAli = 'annots.date.'.$this -> mCntAnnotation;
                    $lRet.= '<input type="hidden" name="val['.$lAli.']" value="'.htm($lDate).'" />';
                    $lAli = 'annots.com.'.$this -> mCntAnnotation;
                    $lRet.= '<input type="hidden" name="val['.$lAli.']" value="'.htm($lComment).'" />';
                  }
                  $lRet.= '</td>'.LF;
                  if ($lNr != $lInnerValue['nr']) {
                    $lNr = $lInnerValue['nr'];
                    $lRet.= '<td width=4%>';
                    $lRet.= htm($lNr).'.';
                    $lRet.= '</td>';
                  }   else {
                    $lRet.= '<td width=4%>';
                    $lRet.= '</td>';
                  }
                  $lRet.= '<td width=20%>';
                  $lRet.= htm($lDate) . ' &nbsp; '.$lPage;
                  $lRet.= '</td>';

                  $lRet.= '<td width=72% class="nw">';
                  if ($this -> enableTextedit()) {
                    $lAli = 'ann.edit.'.$this -> mCntAnnotation;
                    $lRet.= '<textarea name="val['.$lAli.']" cols="90" rows="' . $this -> optRowsByCol($lComment, 90). '" wrap="virtual" readonly="readonly">';
                    $lRet.= htm($lComment);
                    $lRet.= '</textarea>';
                  } else {
                    $lRet.= htm($lComment);
                  }
                  $lRet.= '</td>';

                  $lRet.= '<td width=3%>';
                  if ($this -> enableTextedit()) {
                    $lAlied = 'ann.edit.'.$this -> mCntAnnotation;
                    $lAli = 'ann.btn.'.$this -> mCntAnnotation;
                    $lAlicc = 'annots.com.'.$this -> mCntAnnotation;
                    $lJs = 'javascript:Flow.Std.togfRdOnly(\''.$lAli.'\',\''.$lAlied.'\',\''.$lAlicc.'\')';
                    $lRet.= btn('', $lJs, 'img/ico/16/edit.gif','button', array('name' => 'btn['.$lAli.']','value' => '0' ));
                    $lRet.= btn('', $lJs, 'img/ico/16/cancel.gif','button', array('name' => 'lck['.$lAli.']', 'style' => 'display:none','value' => '0'));
                    $lRet.= '<input type="hidden" name="val['.$lAli.']" value="0" />';
                  } else {
                    $lRet.= '&nbsp;';
                  }
                  $lRet.= '</td>';

                  $lRet.= '</tr>';
                }
              }
            }
          }

        }
      }
      $lRet.= '</table>'.LF;
      $lRet.= '</div>'.LF;
    }
    return $lRet;
  }

}
