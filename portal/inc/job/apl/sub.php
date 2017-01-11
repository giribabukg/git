<?php
/**
 * Title
 *
 * Description
 *
 * @package    package
 * @version $Rev: 12923 $
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 */

class CInc_Job_Apl_Sub extends CCor_Ren {

  protected $mProtocol;
  protected $mXfdf;
  protected $mLineBreak;
  protected $mProtocolOnly;
  protected $mUsrArr;
  // If $mShowDeleteButton = TRUE, show delete Button
  public    $mShowDelButton = FALSE;

  public function __construct($aRow, $aTryOpenProtocol=NULL, $aSrc = NULL, $aJobId=NULL, $aStage = 'job', $aShowDelButton = FALSE, $aJob = NULL) {
    $this -> mSrc = $aSrc;
    $this -> mJid = $aJobId;
    $this -> mJobObj = $aJob;
    $this -> mJobMod = $aStage;
    $this -> mRow = $aRow;
    $this -> mLid = intval($aRow['id']);
    $this -> mXfdf = false;
    $this -> mProtocol = false;
    $this -> mLineBreak = '';
    $this -> mTryOpenProtocol = $aTryOpenProtocol;
    $this -> mColspan = ' colspan="8"';//genutzt auch in apl/list + apl/page/list
    $this -> mStdLink = 'index.php?act=job-apl';
    $this -> mDelLnk = $this -> mStdLink.'.del&amp;src='.$this->mSrc.'&amp;jobid='.$this->mJid.'&amp;statesid=';
    $this -> mProtocolOnly = false;
    $this -> mUsr = CCor_Usr::getInstance();
    $this -> mShowDelButton = $aShowDelButton;

    $this -> mUsrArr = CCor_Res::extract('id', 'fullname', 'usr');

    $this -> mWecAvail = CCor_Cfg::get('wec.available', true);
    $this -> mDalimAvail = CCor_Cfg::get('dalim.available', false);

    $lAdd = $this -> mRow['add_data'];
    if (!empty($lAdd)) {
      $lArr = unserialize($lAdd);
      // xfdf auf PDF Ordner speichern
      if (!empty($lArr['xfdf'])) {
        $this -> mXfdf = $lArr['xfdf'];
      }
      // Umlaufprotokoll
      if (!empty($lArr['protocol'])) {
        $this -> mProtocol = $lArr['protocol'];
      }
    }
    $this -> mExpandComments = false;
    $this->mUseDalimDisplayId = CCor_Cfg::get('dalim.displayid', false);
  }

  protected function getCont() {
    $lRet = '';
    if ($this -> mProtocolOnly) {
      $this -> getBar();
      $this -> getList();
      $lRet.= $this -> getProtocolCont();
      $lRet.= LF;
    } else {
      $lRet.= LF.'<table cellpadding="2" cellspacing="0" class="tbl">'.LF;
      $lRet.= $this -> getBar();
      $lRet.= $this -> getList();
      $lRet.= '</table>'.LF;
    }
    return $lRet;
  }

  public function getBar() {
    $lDat = new CCor_Date();
    $lNum = getNum('t');
    $lRet = '<tr id="'.$lNum.'" class="grp">';
    $lRet.= '<td class="th2 w16">&nbsp;</td>';
    $lAplTypes = CCor_Res::extract('code', 'name', 'apltypes');
    $lAplType = $lAplTypes[$this -> mRow['typ']];

    if ($this -> mProtocol === false) {
      $lRet.= '<td class="th2"'.$this -> mColspan.'>';
      if ($this -> mUsr -> canRead('job-apl.approval')) {
        $lRet.= '<a href="javascript:Flow.Std.togAllChildTr(\''.$lNum.'\',\'dum\',\'grp\')" class="db">';
        $lRet.= lan('job-apl.menu').' - '.$lAplType.' '.$this -> mRow['num'];
        $lDat -> setSql($this -> mRow['start_date']);
        if (!$lDat -> isEmpty()) {
          $lRet.= ', '.lan('lib.createdate').' '.$lDat -> getFmt(lan('lib.date.long'));
        }
        $lDat -> setSql($this -> mRow['ddl']);
        if (!$lDat -> isEmpty()) {
          $lRet.= ' '.lan('lib.deadline').' '.$lDat -> getFmt(lan('lib.date.long'));
        }
        $lRet.= '</a>';
      } else {
        $lRet.= lan('job-apl.menu').' - '.$lAplType.' '.$this -> mRow['num'];
      }
      $lRet.= '</td>';
    } else {
      $lRet.= '<td class="th2"'.$this -> mColspan.'>';
      $lRet.= LF.'<table class="np nb" width="100%">';
      $lRet.= '<td class="th2 np nb" width="55%">';
      if ($this -> mUsr -> canRead('job-apl.approval')) {
        $lRet.= '<a href="javascript:Flow.Std.togAllChildTr(\''.$lNum.'\',\'dum\',\'grp\')" class="db">';
        $lRet.= lan('job-apl.menu').' - '.$lAplType.' '.$this -> mRow['num'];
        $lDat -> setSql($this -> mRow['start_date']);
        if (!$lDat -> isEmpty()) {
          $lRet.= ', '.lan('lib.createdate').' '.$lDat -> getFmt(lan('lib.date.long'));
        }
        $lDat -> setSql($this -> mRow['ddl']);
        if (!$lDat -> isEmpty()) {
          $lRet.= ' '.lan('lib.deadline').' '.$lDat -> getFmt(lan('lib.date.long'));
        }
        $lRet.= '</a>';
      } else {
        $lRet.= lan('job-apl.menu').' - '.$lAplType.' '.$this -> mRow['num'];
      }
      $lRet.= '</td>';

      $lu = new CCor_Anyusr($this -> mProtocol['user']);
      $this -> mProtocol['tagid'] = getNum('ptl');
      $this -> mProtocol['header'] = lan('job-apl.protocol').' '.lan('job-apl.menu').' - '.$lAplType.' '.$this -> mRow['num'].': '.$this -> mProtocol['date'].', '.$lu -> getVal('fullname'); // $lu -> getVal('lastname').' ,'.$lu -> getVal('fistname');

      $lRet.= '<td class="th2 np nb" width="45%">';
      if ($this -> mUsr -> canRead('job-apl.report')) {
        $lRet.= '<a href="javascript:Flow.Std.togTr(\''.$this -> mProtocol['tagid'].'\')" class="db">';
        $lRet.= $this -> mProtocol['header'];
        $lRet.= '</a>';
      } else {
        $lRet.= $this -> mProtocol['header'];
      }
      $lRet.= '</td>';
      $lRet.= '</table>';
      $lRet.= '</td>';
    }

    $lRet.= '</tr>'.LF;
    return $lRet;
  }

  protected function sortMultiArrayByKey($argArray, $argKey, $argOrder = SORT_ASC) {
    $key_arr = array();

    foreach ($argArray as $key => $row){
      $key_arr[$key] = $row[$argKey];
    }

    array_multisort($key_arr, $argOrder, $argArray);

    return $argArray;
  }

  protected function arrayDepth($aArray) {
    if (is_array(reset($aArray))) {
      $lRet = $this -> arrayDepth(reset($aArray)) + 1;
    } else {
      $lRet = 1;
    }

    return $lRet;
  }

  protected function getAnnotations($aUid) {
    $lRet = '';
    if ($this->mDalimAvail) {
      $lRet.= $this->getDalimAnnotations($aUid);
    }
    if ($this->mWecAvail) {
      $lRet.= $this->getWecAnnotations($aUid);
    }
    return $lRet;

  }

  protected function getAnnotationLine($aUid, $aRes, $aPage = 0) {
    if (empty($aRes)) return '';
    if (false === $aRes) return '';
    $lRet = '';
    $lDate = new CCor_Datetime();
    $this -> mLineBreak = '';
    foreach ($aRes as $lAnnVal) {
      if (is_array($lAnnVal)) {
        if (isset($lAnnVal['uid'])) {
          if ($aUid == $lAnnVal['uid']) {
            $lDate -> setSql($lAnnVal['date']);
            $lfDate = $lDate -> getFmt(lan('lib.datetime.short'));
            $lRet .= $this -> mLineBreak.lan('lib.annotation').' '.$lAnnVal['nr'].' : '.$lfDate;
            if (!empty($aPage)) {
              $lRet.= ' ('.htm(lan('nav.page')).' '.$aPage.')';
            }
            $lRet.= LF.trim($lAnnVal['comment']).LF;
            $this -> mLineBreak = '------------------------------------------------------------------------------------------------------------------'.LF;
          }
        }
      }
    }
    return $lRet;
  }

  protected function loadDalimAnnotations() {
    if ($this->mUseDalimDisplayId) {
      CApi_Dalim_Utils::checkFields();
      $lSql = 'SELECT id,num,parent_id,user_id,doc,page,datum,content FROM al_dalim_notes WHERE jobid='.esc($this->mJid).' ';
      $lSql.= 'AND content!="" ';
      $lSql.= 'AND loop_id="'.intval($this -> mLid).'" ';
      $lSql.= 'ORDER BY doc,page,datum ';
    } else {
    $lSql = 'SELECT id,user_id,doc,page,datum,content FROM al_dalim_notes WHERE jobid='.esc($this->mJid).' ';
    $lSql.= 'AND content!="" ';
    $lSql.= 'AND loop_id="'.intval($this -> mLid).'" ';
    $lSql.= 'ORDER BY doc,datum ';
    }

    $lRes = array();
    $lOldDoc = '';
    $lQry = new CCor_Qry($lSql);
    
    $lRows = array();
    foreach ($lQry as $lRow) {
      $lRows[$lRow['id']] = $lRow;
    }

    $lComments = array();
    foreach ($lRows as $lRow) {
      if ($lOldDoc != $lRow['doc']) {
        $lNr = 1;
        $lOldDoc = $lRow['doc'];
      }
      $lUid = $lRow['user_id'];
      $lItm = array();
      if ($this->mUseDalimDisplayId) {
        $lItm['nr'] = $lRow['num'];
        $lParent = $lRow['parent_id'];
        if (!empty($lParent) && isset($lRows[$lParent])) {
          $lParentRow = $lRows[$lParent];
          $lParentNum = $lParentRow['num'];
          $lBaseKey = $lRow['doc'].'_'.$lRow['page'].'_'.$lParentNum.'_';
          $lCount = 1;
          while (isset($lComments[$lBaseKey.$lCount])) {
            $lCount++;
          }
          $lItm['nr'] = $lParentRow['num'].'.'.$lCount;
          $lComments[$lBaseKey.$lCount] = true;
        }
      } else {
      $lItm['nr'] = $lNr;
      }
      $lItm['uid'] = $lUid;
      $lItm['page'] = $lRow['page'];
      $lItm['date'] = $lRow['datum'];
      $lItm['comment'] = $lRow['content'];
      $lItm['name'] = strrchr($lRow['doc'], DS);
      $lRes[$lUid][$lRow['page']][] = $lItm;
      $lNr++;
    }
    return $lRes;
  }

  protected function getDalimAnnotations($aUid) {
    if (!$this->mDalimLoaded) {
      $this->mDalimAnnotations = $this->loadDalimAnnotations();
      $this->mDalimLoaded = true;
    }
    $lRes = (empty($this->mDalimAnnotations[$aUid])) ? array() : $this->mDalimAnnotations[$aUid];
    $lRet = '';
    foreach ($lRes as $lPage => $lRows) {
      $lRet.= $this->getAnnotationLine($aUid, $lRows, $lPage);
  }
    return $lRet;
  }

  protected function getWecAnnotations($aUid) {
    //Viewed in apl -> in der getList
    $lRet = '';
    $this -> mLineBreak = '';

    if ($this -> mXfdf !== FALSE) {

      foreach ($this -> mXfdf as $lKey => $lVal) {
        if (is_array($lVal) && $this -> arrayDepth($this -> mXfdf) < 3) {
          $lDat = new CUtl_Wec_Xfdf($lVal['xfdf'], '', '');
          $lXRes = $lDat -> parse('string');
          $lRet.= $this->getAnnotationLine($aUid, $lXRes);

        } else {
          foreach ($lVal as $lInnerKey => $lInnerValue) {
            $lDat = new CUtl_Wec_Xfdf($lInnerValue['xfdf'], '', '');
            $lXRes = $lDat -> parse('string');
            $lRet.= $this->getAnnotationLine($aUid, $lXRes, $lInnerKey);
          }
        }
      }
    }

    return $lRet;
  }

  protected function getProtocolParserUser($aArr) {
    $lRet = '';
    if (is_array($aArr)) {
      #$lRet.='<pre>';
      #$lRet.= var_export($aArr, true);
      #$lRet.='</pre>';

      $lRetMsg = ''; // APL Approval Comments
      $lRetAnn = ''; // Webcenter Annotations
      $lRetFil = ''; // Files
      $lWithContent = FALSE;

      $lRetMsg.= '<tr>';
      $lRetMsg.= '<td class="th2"'.$this -> mColspan.'>' . htm($aArr['name']) . '</td>'.LF;
      $lRetMsg.= '</tr>';

      if (empty($aArr['comment.status'])) {
        $lchecked = '';
      } else {
        if ($aArr['comment.status'] == '[ ]') {
          $lchecked = '';
        } else {
          $lchecked = 'checked="checked"';
        }
      }

      if (!empty($lchecked)) { // 23477: APL report optimization: APLpage- view only checked comments //in add_data stehen nur noch die gecheckten Eintraege
        $lchecked.= ' disabled="disabled"';
        $lWithContent = TRUE;

        if (empty($aArr['state'])) {
          $lSta = '----';
        } else {
          $lSta = $aArr['state'];
        }

        if (empty($aArr['comment.text'])) {
          $lMsg = '';
        } else {
          $lMsg = $aArr['comment.text'];
        }

        if (empty($aArr['comment.date'])) {
          $lDat = '';
        } else {
          $lDat = $aArr['comment.date'];
        }

        $lRetMsg.= '<tr>';
        if ($lSta == '----') {
          $lRetMsg.= '<td class="th3"'.$this -> mColspan.'>'.lan('lib.msg').'</td>'.LF;
        } else {
          $lRetMsg.= '<td class="th3"'.$this -> mColspan.'>'.lan('lib.msg').' ('.htm($lSta).')</td>'.LF;
        }
        $lRetMsg.= '</tr>';

        // Checkbox fuer Kommentar
        $lRetMsg.= '<tr>';
        $lRetMsg.= '<td class="nw"  width="1%"><input type="checkbox" '.$lchecked.' /></td>'.LF;
        $lRetMsg.= '<td class="nw" width=4%>&nbsp;</td>';
        $lRetMsg.= '<td class="nw" width=20%>' . htm($lDat) . '</td>';
        $lRetMsg.= '<td  width=75%>' . ($lMsg == '' ? '&nbsp;' : htm($lMsg)) . '</td>';
        $lRetMsg.= '<td>' . (!empty($aArr['comment.changed']) ? img('img/ico/16/edit.gif') : '') . '</td>';
        $lRetMsg.= '</tr>';
      }//end_if (!empty($lchecked))

      // Annotationen
      if (!empty($aArr['annotations'])) {

        $lAnns = $aArr['annotations'];

        foreach($lAnns  as $lAnn) {
          $lNr = '';
          if (empty($lAnn['status'])) {
            $lchecked = '';
          } else {
            if ($lAnn['status'] == '[ ]') {
              $lchecked = '';
            } else {
              $lchecked = 'checked="checked"';
            }
          }
          if (!empty($lchecked)) { // 23477: APL report optimization: APLpage- view only checked comments
            $lchecked.= ' disabled="disabled"';
            $lWithContent = TRUE;

            if (empty($lRetAnn)) { // Header
              $lRetAnn.='<tr><td class="th3"'.$this -> mColspan.'>'.htm(lan('lib.annotations')).'</td></tr>'.LF;
            }

            $lAnnText = str_replace(LF, '<br>', htm($lAnn['text']));
            if ($lNr != $lAnn['nr']) {
              $lNr  = htm($lAnn['nr']);
              $lPkt = '.';
            } else {
              $lNr  = '';
              $lPkt = '';
            }

            if (isset($lAnn['page']) AND 0 < $lAnn['page']) {
              $lPage = '  ('.htm($lAnn['page']).')';
            } else {
              $lPage = '';
            }
            $lRetAnn.= '<tr>';
            $lRetAnn.= '<td class="nw" width=1%><input type="checkbox" '.$lchecked.' /></td>'.LF;
            $lRetAnn.= '<td width=4%>'  . $lNr . $lPkt. '</td>';
            $lRetAnn.= '<td width=20%>' . htm($lAnn['date']) . $lPage. '</td>';
            $lRetAnn.= '<td width=75%>' . $lAnnText . '</td>';
            $lRetAnn.= '<td>' . (!empty($lAnn['changed']) ? img('img/ico/16/edit.gif') : '') . '</td>';
            $lRetAnn.= '</tr>';
          }//end_if (!empty($lchecked))
        }//end_foreach($lAnns  as $lAnn)
      }//end_if (!empty($aArr['annotations']))

      // Files
      if (!empty($aArr['files'])) {

        $lFiles = $aArr['files'];
        foreach($lFiles  as $lFile) {
          if (empty($lFile['status'])) {
            $lchecked = '';
          } else {
            if ($lFile['status'] == '[ ]') {
              $lchecked = '';
            } else {
              $lchecked = 'checked="checked"';
            }
          }
          #if (!empty($lchecked)) { // 23477: APL report optimization: APLpage- view only checked comments
          $lchecked.= ' disabled="disabled"';
          $lWithContent = TRUE;

          if (empty($lRetFil)) { // Header
            $lRetFil.='<tr><td class="th3"'.$this -> mColspan.'>'.htm(lan('job-fil.menu')).'</td></tr>'.LF;
          }

          $lRetFil.= '<tr>';
          $lRetFil.= '<td class="nw" width=1%><input type="checkbox" '.$lchecked.' /></td>'.LF;
          $lRetFil.= '<td width=4%>&nbsp;</td>';
          $lRetFil.= '<td width=20%>&nbsp;</td>';
          $lRetFil.= '<td width=75%>' . htm($lFile['name']) . '</td>';
          $lRetFil.= '</tr>';
          #}//end_if (!empty($lchecked))
        }//end_foreach($lFiles  as $lFile)
      }//end_if (!empty($aArr['files']))

      if ($lWithContent) {
        #$lRet.= '<div class="tbl w800">'.LF;
        $lRet.= '<table cellpadding="4" cellspacing="0" class="frm" width="100%">'.LF;

        $lRet.= $lRetMsg;
        $lRet.= $lRetAnn;
        $lRet.= $lRetFil;

        $lRet.= '</table>';
        # $lRet.= '</div>';
      }//end_if ($lWithContent)

    }//end_if (is_array($aArr))

    return $lRet;
  }

  protected function getProtocol() {
    $lRet = '';
    $this -> mLineBreak = '';
    if ($this -> mProtocol !== false) {
      $lIc = 0;
      $lActUser = $this -> mProtocol['user'];
      $lDate = new CCor_Datetime();
      if (!empty($this -> mProtocol['list'])) {
        $lList = $this -> mProtocol['list'];
        $lAdm = '';
        foreach ($lList as $lVal) {
          if ($lVal['uid'] !== $lActUser) {
            $lRet.= $this -> getProtocolParserUser($lVal);
          } else {
            $lAdm = $this -> getProtocolParserUser($lVal);
          }
        }
        $lRet.= $lAdm;
      }
    }
    return $lRet;
  }

  protected function getProtocolCont($aJob = NULL) {
    $lJob = $aJob ? $aJob : $this -> mJobObj;

    $lRet = '';
    if ($this -> mProtocol !== FALSE) {
      $lSta = $this -> mRow['status'];
      if ('open' == $lSta OR (!empty($this -> mTryOpenProtocol) && $this -> mTryOpenProtocol == $this -> mLid)) {
        $lRet.= '<tr class="ptl hi" style="display:table-row" id="'.$this -> mProtocol['tagid'].'" width="100%">';
      } else {
        $lRet.= '<tr class="ptl" style="display:none" id="'.$this -> mProtocol['tagid'].'" width="100%">';
      }
      $lRet.= '<td class="td1 w16">&nbsp;</td>';
      $lRet.= '<td'.$this -> mColspan.'>'.LF;

      // -------------------------------------------------------------
      $lRet.= '<table class="th1 np nb" width="100%">';
      $lRet.= '    <tr>';
      $lRet.= '        <td class="th1 np nb" width="50%">';
      $lRet.= '&nbsp;';
      $lRet.= '        </td>';
      $lRet.= '        <td class="th1 np nb" rowspan="5">';
      $lRet.= '&nbsp;';
      if (!$this -> mProtocolOnly) {
        $lRet.= btn(lan('lib.print'), 'pop("index.php?act=job-apl-page.prnProtocol&prtid='.$this -> mLid.'&src='.$this -> mSrc.'&jid='.$this -> mJid.'")', 'img/ico/16/print.gif', 'button', array('class' => 'btn w100'));
      }
      $lRet.= '        </td>';
      $lRet.= '    </tr>'.LF;
      // JOBID
      $lRet.= '    <tr>';
      $lRet.= '        <td class="th1 np nb" width="50%">';
      $lRet.= '&nbsp;&nbsp;&nbsp;JobID: '.$lJob['jobid'];
      $lRet.= '        </td>';
      $lRet.= '    </tr>'.LF;
      // KEYWORDS
      $lRet.= '    <tr>';
      $lRet.= '        <td class="th1 np nb" width="50%">';
      $lRet.= '&nbsp;&nbsp;&nbsp;Keyword: '.$lJob['stichw'];
      $lRet.= '        </td>';
      $lRet.= '    </tr>'.LF;
      // HEADER
      $lRet.= '    <tr>';
      $lRet.= '        <td class="th1 np nb" width="50%">';
      $lRet.= '&nbsp;&nbsp;&nbsp;'.$this -> mProtocol['header'];
      $lRet.= '        </td>';
      $lRet.= '    </tr>'.LF;
      $lRet.= '    <tr>';
      $lRet.= '        <td class="th1 np nb" width="50%">';
      $lRet.= '&nbsp;';
      $lRet.= '        </td>';
      $lRet.= '    </tr>'.LF;
      $lRet.= '    <tr>';
      $lRet.= '        <td class="td1 np nb" colspan="2">';
      $lRet.= $this -> getProtocol();
      $lRet.= '        </td>';
      $lRet.= '    </tr>'.LF;
      $lRet.= '</table>';
      // -------------------------------------------------------------

      $lRet.= '</td>';
      $lRet.= '</tr>';
    }
    return $lRet;
  }

  protected function getListSql() {
    $lSql = 'SELECT * FROM al_job_apl_states WHERE 1';
    $lSql.= ' AND inv="Y"';
    // Deleted User sort out.
    $lSql.= ' AND del != "Y"';
    $lSql.= ' AND loop_id='.$this -> mLid;
    $lSql.= ' ORDER BY pos';
    return $lSql;
  }

  public function getList() {
    $lRet = '';
    $lSta = $this -> mRow['status'];
    $lDat = new CCor_Date();

    $lSql = $this->getListSql();
    $lQry = new CCor_Qry($lSql);

    // sequentieller APL: die Aufgaben werden in der Reihenfolge von 'pos' ausgeführt
    $lGruArr = CCor_Res::extract('id', 'name', 'gru');

    $lMinPos = MAX_SEQUENCE; // Behelfsvorbelegung
    $lAplUserArr = array();
    foreach ($lQry as $lRow) {
      //brauche zur Anzeige distinct user_ids => können sich über backupuser-Fkt ändern und mehrfach vorkommen
      //Angezeigt werden muß die user_id mit der kleineren pos, da die agieren darf: $lSql.= ' ORDER BY pos';
      if ((0 == $lRow['gru_id'] OR 'all' == $lRow['confirm']) AND !isset($lAplUserArr[ $lRow['user_id'] ])) {
        $lAplUserArr[ $lRow['user_id'] ] = $lRow;
      } elseif (0 < $lRow['gru_id']) {
        if (!empty($lRow['comment']) OR !isset($lAplUserArr["G".$lRow['gru_id']])) {
          $lAplUserArr["G".$lRow['gru_id']] = $lRow;
        }
      }
      $lPos = $lRow['pos'];
      // 0 == $lRow['status']: User hat seine Apl-Aufgaben ausgeführt
      // Suche die nächst kleinste Position >= 0, bei der der User noch seiner Aufgabe nachkommen muß.
      if (0 == $lRow['status'] AND $lMinPos > $lPos) {
        #if ('Y' == $lRow['wait'] AND $lMinPos > $lPos) {
        $lMinPos = $lPos;
      }
    }
    foreach ($lAplUserArr as $lRow) {
      $this -> mDelLink = $this -> mDelLnk.$lRow['id'];
      $this -> mDelLink.= '&usrid='.$lRow['user_id'];
      $this -> mDelLink.= '&loopid='.$this -> mLid;

      if ($this -> mUsr -> canRead('job-apl.approval')) {
        if ('open' == $lSta) {
          $lRet.= '<tr class="dum hi" style="display:table-row">';
        } else {
          $lRet.= '<tr class="dum" style="display:none">';
        }
        $lAnn = $this -> getAnnotations($lRow['user_id']);
        $lMsg = trim($lRow['comment']);
        if (empty($lMsg)) {
          $lMsg = $lAnn;
        } else {
          $lMsg = $lAnn . $this -> mLineBreak . lan('lib.msg').':'.LF.$lMsg;
        }
        if (empty($lMsg)) {
          $lRet.= '<td class="td1 w16">&nbsp;</td>';
        } else {
          $lNum = getNum('tr');
          $lRet.= '<td class="td1 ac w16">';
          $lRet.= '<a href="javascript:Flow.Std.togTr(\''.$lNum.'\')" class="nav">...</a>';
          $lRet.= '</td>';
        }
        $lRet.= '<td class="td1 ac w16">';
        $lRet.= img('img/ico/16/flag-0'.$lRow['status'].'.gif');
        $lRet.= '</td>';
        $lRet.= '<td class="td1 ac w16">'.$lRow['position'].'</td>';
        //$lMinPos can do, >$lMinPos wait!
        if ('open' == $lSta) {
          if ($lMinPos < $lRow['pos']) {
            $Process = 'wait';
          } elseif ($lMinPos == $lRow['pos'] AND 0 == $lRow['status']) {#'Y' == $lRow['wait']) {
            $Process = 'doit';
          } else {
            $Process = 'done';
          }
        } else {
          $Process = 'past';
        }

        // Flag for User can be deleted or not.
        $lCanUserDeleted = FALSE;
        if (0 == $lRow['status']){
          // If user has not given his comment, can be deleted
          $lCanUserDeleted = TRUE;
        }

        $lRet.= '<td class="td1 ac w16">';
        $lRet.= img('img/ico/16/process_'.$Process.'.gif', array('alt' => lan('sequence.'.$Process)));
        $lRet.= '</td>';

        $lRet.= '<td class="td1 nw">';
        $lDat -> setSql($lRow['datum']);
        $lRet.= $lDat -> getFmt(lan('lib.date.long'));
        $lRet.= '</td>';
        $lRet.= '<td class="td1 nw">';

        if (isset($lGruArr[$lRow['gru_id']])) {
          $lGruName = htm($lGruArr[$lRow['gru_id']]);
          if ('all' == $lRow['confirm']) {
            $lRet.= '('.$lGruName.') '.htm($lRow['name']);
          } else {
            $this -> mDelLink.= '&gruid='.$lRow['gru_id'];// $lCanUserDeleted entscheidet ueber die Anzeige d. Links!
            if (!empty($lMsg)) {
              $lRet.= '('.$lGruName.') '.htm($lRow['name']);
            } else {
              $lRet.= $lGruName;
            }
          }
        } else {
          $lRet.= htm($lRow['name']);

          // Name der Backup-person, wenn sie als Vertretung auftritt.
          #$lName = explode(') ',$lRow['name']);//will nicht nach '(...)' suchen...
          #$lRet.= ((isset($this -> mUsrArr[$lRow['user_id']]) AND isset($lName[1]) AND $lName[1] != $this -> mUsrArr[$lRow['user_id']]) ? ' ('.lan('Vertretung:').' '.htm($this -> mUsrArr[$lRow['user_id']]).')' : '');
          $lNam = explode(') ',$lRow['name']);//will nicht nach '(...)' suchen...
          if (isset($lNam[1])) {
            $lName = $lNam[1];
          } else {
            $lName = $lNam[0];
          }
          if (isset($this -> mUsrArr[$lRow['user_id']]) AND $lName != $this -> mUsrArr[$lRow['user_id']]) {
            $lFillIn = ' ('.lan('FillIn:').' '.htm($this -> mUsrArr[$lRow['user_id']]).')';
          } else {
            $lFillIn = '';
          }
          $lRet.= $lFillIn;
        }

        $lRet.= '</td>';
        $lRet.= '<td class="td1 w100p">';
        $lRet.= shortStr($lMsg);
        $lRet.= NB.'</td>';
        $lRet.= $this -> getTdChk($lRow);
        $lRet.= NB.'</td>';
        $lRet.= $this -> getTdDel($lRow, $lCanUserDeleted);
        $lRet.= '</tr>'.LF;
        if (!empty($lMsg)) {
          $lStyle = ($this -> mExpandComments) ? 'table-row' : 'none';
          $lRet.= '<tr style="display:'.$lStyle.'" id="'.$lNum.'">';
          $lRet.= '<td class="td1 tg">&nbsp;</td>';
          $lRet.= '<td class="td1 p8"'.$this -> mColspan.'>';
          $lRet.= nl2br(htm($lMsg));
          $lRet.= '</td>';
          $lRet.= '</tr>'.LF;
        }
      }
    }

    if ($this -> mUsr -> canRead('job-apl.report')) {
      // ----------------------------------------------------------------------------------------------
      // Protokoll holen falls vorhanden
      // ----------------------------------------------------------------------------------------------
      $lRet.= $this -> getProtocolCont();
    }
    return $lRet;
  }

  public function getSingleProtocol($aJob = NULL) {
    $lJob = $aJob;
    $this -> mProtocolOnly = TRUE;

    $this -> getBar();
    $this -> getList();

    $lRet = '';
    $lRet.= $this -> getProtocolCont($lJob);

    return $lRet;
  }

  public function getTdDel($aRow,$aCanUserDeleted = FALSE){
    $lRet = '';
    $lRet.= '<td class="td1 nw">'.LF;
    if ($aCanUserDeleted
        && $this -> mShowDelButton
        && $this ->mUsr->canDelete('apl.user')) {
      $lRet.= '<a class="nav" href="javascript:Flow.Std.cnf(\''.$this -> mDelLink.'\', \'cnfDel\')">';
      $lRet.= img('img/ico/16/del.gif');
      $lRet.= '</a>';
    }else{
      $lRet.= NB;
    }
    $lRet.= '</td>'.LF;
    return $lRet;


  }

  protected function getTdChk($aRow) {
    $lRet = '';
    $lRet.= '<td class="td1 nw">'.LF;
    if ($this->showCheck($aRow['user_id'])) {
      #$lRet.= '<a>Check</a>';
      $lChkListUrl = 'index.php?act=utl-chk&jobid='.$this -> mJid.'&uid='.$aRow['user_id'].'&src='.$this -> mSrc;
      $lRet.= '<a href="#" target="_self" onclick="pop(\''.$lChkListUrl.'\')">'.img('img/ico/16/fie.gif').'</a>';
    }
    $lRet.= '</td>'.LF;
    return $lRet;
  }

  protected function showCheck($aUid) {
    $lSql = 'SELECT user_id FROM al_job_chk WHERE user_id ='.$aUid.' AND src_id = '.esc($this -> mJid);
    $lResult = CCor_Qry::getInt($lSql);
    return $lResult;
  }

}
