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

class CInc_Job_Flag_Sub extends CCor_Ren {

  protected $mCrpFlags = array();
  protected $mProtocol;
  protected $mXfdf;
  protected $mLineBreak;
  protected $mProtocolOnly;
  protected $mUsrArr;
  // If $mShowDeleteButton = TRUE, show delete Button
  public    $mShowDelButton = FALSE;

  public function __construct($aRow, $aTryOpenProtocol=NULL, $aSrc=NULL, $aJobId=NULL, $aStage='job', $aTyp='', $aShowDelButton = FALSE) {
    $this -> mSrc = $aSrc;
    $this -> mTyp = $aTyp;
    $this -> mJobId = $aJobId;
    $this -> mJobMod = $aStage;
    $this -> mRow = $aRow;
    $this -> mLid = intval($aRow['id']);
    $this -> mXfdf = false;
    $this -> mProtocol = false;
    $this -> mLineBreak = '';
    $this -> mTryOpenProtocol = $aTryOpenProtocol;
    $this -> mShowDelButton = $aShowDelButton;
    if ($this -> mShowDelButton) {
      $this -> mColspan = ' colspan="7"';//genutzt auch in apl/list + apl/page/list
    } else {
      $this -> mColspan = ' colspan="6"';//genutzt auch in apl/list + apl/page/list
    }
    $this -> mStdLink = 'index.php?act=job-apl';
    $this -> mDelLnk = $this -> mStdLink.'.del&amp;src='.$this->mSrc.'&amp;jobid='.$this->mJobId.'&amp;statesid=';
    $this -> mProtocolOnly = false;
    $this -> mUsr = CCor_Usr::getInstance();

    $this -> mUsrArr = CCor_Res::extract('id', 'fullname', 'usr');

    $this -> mAllFlags = CCor_Res::get('fla');

    $this -> getCrpFlags();
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

  protected function getCrpFlags() {
    $lFlagId = '';
    $lFlagClass = new CApp_Apl_Loop($this -> mSrc, $this -> mJobId, $lFlagId, MID, 0);
    $this -> mCrpFlags = $lFlagClass -> getAllFlags($this -> mSrc, $this -> mJobId);
  }

  public function getBar() {
    $lRet = '';
    $lDat = new CCor_Date();
    if (isset($this -> mAllFlags[$this -> mRow['typ']])) {
      $lFlagId = $this -> mRow['typ'];
      $lFlagEve = $this -> mAllFlags[$lFlagId];

      $lNum = getNum('t');
      $lRet.= '<tr id="'.$lNum.'" class="grp">';

      if ($this -> mProtocol === false AND isset($this -> mCrpFlags[$lFlagId])) {

        $lNr = ($this -> mCrpFlags[$lFlagId] ? flEve_conf : flEve_act) ;
        $lImg = $lFlagEve['eve_'.$lNr.'_ico'];

        $lRet.= '<td class="th2 w16">';
        $lRet.= img('img/flag/'.$lImg.'.gif');
        $lRet.= '</td>';
        $lRet.= '<td class="th2"'.$this -> mColspan.'>';
        $lRet.= '<a href="javascript:Flow.Std.togAllChildTr(\''.$lNum.'\',\'dum\',\'grp\')" class="db">';
        $lRet.= $lFlagEve['name_'.LAN];#.' '.$this -> mRow['num'];

        $lDat -> setSql($this -> mRow['start_date']);
        if (!$lDat -> isEmpty()) {
          $lRet.= ', '.lan('lib.createdate').' '.$lDat -> getFmt(lan('lib.date.long'));
        }
        if (CApp_Apl_Loop::APL_LOOP_CLOSED == $this -> mRow['status']) {
          $lRet.= ' '.lan('lib.stopdate');
          $lDat -> setSql($this -> mRow['close_date']);
          if (!$lDat -> isEmpty()) {
            $lRet.= ' '.$lDat -> getFmt(lan('lib.date.long'));
          }
        }

        $lRet.= '</a>';
        $lRet.= '</td>';
      }

      $lRet.= '</tr>'.LF;
    }
    return $lRet;
  }

  public function getList() {
    $lRet = '';
    $lSta = $this -> mRow['status'];

    // Flag for User can be deleted or not.
    $lCanUserDeleted = FALSE;

    $lSql = 'SELECT * FROM al_job_apl_states WHERE 1';
    $lSql.= ' AND inv="Y"';
    // Deleted User sort out.
    $lSql.= ' AND del="N"';
    if (!empty($this -> mTyp)) {
      $lSql.= ' AND `typ`='.esc($this -> mTyp);
    } else {
      $lSql.= ' AND `typ`!="apl"';
    }
    $lSql.= ' AND `mand`='.intval(MID);
    $lSql.= ' AND loop_id='.$this -> mLid;
    $lSql.= ' ORDER BY pos';

    $lDat = new CCor_Date();
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

      if ('open' == $lSta) {
        $lRet.= '<tr class="dum hi" style="display:table-row">';
      } else {
        $lRet.= '<tr class="dum" style="display:none">';
      }
      $lAnn = '';#$this -> getAnnotations($lRow['user_id']);
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
      if (!empty($this -> mTyp)) {
        // APL
        $lImg = 'img/ico/16/flag-0'.$lRow['status'].'.gif';
      } elseif (empty($this -> mTyp) AND isset($this -> mAllFlags[ $lRow['typ']])) {
        $lFlagEve = $this -> mAllFlags[ $lRow['typ'] ];
        $lImg = 'img/flag/';
        switch($lRow['status']) {
          case CApp_Apl_Loop::APL_STATE_AMENDMENT :
            $lImg.= $lFlagEve['amend_ico'];
            BREAK;
          case CApp_Apl_Loop::APL_STATE_CONDITIONAL :
            $lImg.= $lFlagEve['condit_ico'];
            BREAK;
          case CApp_Apl_Loop::APL_STATE_APPROVED :
            $lImg.= $lFlagEve['approv_ico'];
            BREAK;
          default:
            $lImg.= 'flag-00';
        }
        $lImg.= '.gif';
      } else {
        $lImg = 'img/flag/flag-00.gif';
      }
      $lRet.= img($lImg);
      $lRet.= '</td>';

      $lRet.= '<td class="td1 ac w16">'.$lRow['position'].'</td>';
      //$lMinPos can do, >$lMinPos wait!
      if ('open' == $lSta) {
        if ($lMinPos < $lRow['pos']) {
          $Process = 'wait';
          $lCanUserDeleted = TRUE;
        } elseif ($lMinPos == $lRow['pos'] AND 0 == $lRow['status']) {
          #'Y' == $lRow['wait']) {
          $Process = 'doit';
        // If user has given no Comments, User can be deleted.
        $lCanUserDeleted = ($lRow['done'] == 'Y') ? FALSE : TRUE;
        } else {
          $Process = 'done';
        }
      } else {
        $Process = 'past';
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
      if ($this -> mShowDelButton) {
        $lRet.= $this -> getTdDel($lRow, $lCanUserDeleted);
      }
      $lRet.= '</tr>'.LF;
      if (!empty($lMsg)) {
        $lRet.= '<tr style="display:none" id="'.$lNum.'">';
        $lRet.= '<td class="td1 tg">&nbsp;</td>';
        $lRet.= '<td class="td1 p8"'.$this -> mColspan.'>';
        $lRet.= nl2br(htm($lMsg));
        $lRet.= '</td>';
        $lRet.= '</tr>'.LF;
      }
    }

    // ----------------------------------------------------------------------------------------------
    // Protokoll holen falls vorhanden
    // ----------------------------------------------------------------------------------------------
    $lRet.= $this -> getProtocolCont();
    return $lRet;
  }

  public function getTdDel($aRow, $aCanUserDeleted = FALSE){
    $lRet = '';
    $lRet.= '<td class="td1 nw">'.LF;
    if ($aCanUserDeleted AND $this -> mUsr -> canDelete('apl.user')) {
      $lRet.= '<a class="nav" href="javascript:Flow.Std.cnf(\''.$this -> mDelLink.'\', \'cnfDel\')">';
      $lRet.= img('img/ico/16/del.gif');
      $lRet.= '</a>';
    }else{
      $lRet.= NB;
    }
    $lRet.= '</td>'.LF;
   return $lRet;
  }

  protected function getProtocolCont() {
    $lRet = '';
    if ($this -> mProtocol !== false) {
      $lSta = $this -> mRow['status'];
      if (!empty($this -> mTryOpenProtocol) && $this -> mTryOpenProtocol==$this -> mLid) {
        $lRet.= '<tr class="ptl hi" style="display:table-row" id="'.$this -> mProtocol['tagid'].'" width="100%">';
      } else if ('open' == $lSta) {
        $lRet.= '<tr class="ptl hi" style="display:table-row" id="'.$this -> mProtocol['tagid'].'" width="100%">';
      } else {
        $lRet.= '<tr class="ptl" style="display:none" id="'.$this -> mProtocol['tagid'].'" width="100%">';
      }
      $lRet.= '<td class="td1 w16">&nbsp;</td>';
      $lRet.= '<td'.$this -> mColspan.'>';
      // -------------------------------------------------------------
      $lRet.= LF.'<table class="th1 np nb" width="100%">';
      // -------------------------------------------------------------
      $lRet.= '<tr><td class="th1 np nb" colspan="2">&nbsp;</td></tr>'.LF;
      // -------------------------------------------------------------
      $lRet.= '<tr>';
      $lRet.= '<td class="th1 np nb" width="50%">';
      $lRet.= '&nbsp;&nbsp;&nbsp;'.$this -> mProtocol['header'];
      $lRet.= '</td>';
      $lRet.= '<td class="th1 np nb" width="50%">';
      $lRet.= '&nbsp;';
      if (!$this -> mProtocolOnly) {
        $lRet.= btn(lan('lib.print'), 'pop("index.php?act=job-apl-page.prnProtocol&prtid='.$this -> mLid.'")', 'img/ico/16/print.gif', 'button', array('class' => 'btn w100' ));
      }
      $lRet.= '</td>';
      $lRet.= '</tr>'.LF;
      // -------------------------------------------------------------
      $lRet.= '<tr>';
      $lRet.= '<td class="th1 np nb" colspan="2">';
      $lRet.= '&nbsp;';
      $lRet.= '</td>';
      $lRet.= '</tr>'.LF;
      // -------------------------------------------------------------
      $lRet.= '<tr>';
      $lRet.= '<td class="td1 np nb" colspan="2">';
      $lRet.= $this -> getProtocol();
      $lRet.= '</td>';
      $lRet.= '</tr>'.LF;
      $lRet.= '</table>';
      // -------------------------------------------------------------
      $lRet.= '</td>';

      $lRet.= '</tr>';
    }
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

  protected function getProtocolParserUser($aArr) {
    $lRet = '';
    if (is_array($aArr)) {
      $lRet.= '<table cellpadding="4" cellspacing="0" class="frm" width="100%">'.LF;
      $lRet.= '<tr>';
      $lRet.= '<td class="th2"'.$this -> mColspan.'>';
      $lRet.= htm($aArr['name']);
      $lRet.= '</td>'.LF;
      $lRet.= '</tr>';

      if (empty($aArr['state'])) {
        $lSta = '----';
      } else {
        $lSta = $aArr['state'];
      }

      if (empty($aArr['comment.status'])) {
        $lchecked = '';
      } else {
        if ($aArr['comment.status'] == '[ ]') {
          $lchecked = '';
        } else {
          $lchecked = 'checked="checked"';
        }
      }
      $lchecked.= ' disabled="disabled"';

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

      $lRet.= '<tr>';
      if ($lSta == '----') {
        $lRet.= '<td class="th3"'.$this -> mColspan.'>'.lan('lib.msg').'</td>'.LF;
      } else {
        $lRet.= '<td class="th3"'.$this -> mColspan.'>'.lan('lib.msg').' ('.htm($lSta).')</td>'.LF;
      }
      $lRet.= '</tr>';

      // Checkbox fuer Kommentar
      $lRet.= '<tr>';
      $lRet.= '<td class="nw"  width="1%">';
      $lRet.= '<input type="checkbox" '.$lchecked.' />';
      $lRet.= '</td>'.LF;

      $lRet.= '<td class="nw" width=4%>';
      $lRet.= '&nbsp;';
      $lRet.= '</td>';
      $lRet.= '<td class="nw" width=20%>';
      $lRet.= htm($lDat);
      $lRet.= '</td>';
      $lRet.= '<td  width=75%>';
      if ($lMsg == '') {
        $lRet.= '&nbsp;';
      } else {
        $lRet.= htm($lMsg);
      }
      $lRet.= '</td>';
      $lRet.= '<td>';
      if (!empty($aArr['comment.changed'])) {
        $lRet.= img('img/ico/16/edit.gif');
      }
      $lRet.= '</td>';
      $lRet.= '</tr>';

      // Annotationen
      if (!empty($aArr['annotations'])) {
        $lRet.='<tr><td class="th3"'.$this -> mColspan.'>'.htm(lan('lib.annotations')).'</td></tr>'.LF;

        $lAnns = $aArr['annotations'];
        $lNr = '';
        foreach($lAnns  as $lAnn) {
          if (empty($lAnn['status'])) {
            $lchecked = '';
          } else {
            if ($lAnn['status'] == '[ ]') {
              $lchecked = '';
            } else {
              $lchecked = 'checked="checked"';
            }
          }
          $lchecked.= ' disabled="disabled"';

          $lRet.= '<tr>';
          $lRet.= '<td class="nw" width=1%>';
          $lRet.= '<input type="checkbox" '.$lchecked.' />';
          $lRet.= '</td>'.LF;

          $lAnnText = str_replace(LF, '<br>', htm($lAnn['text']));
          if ($lNr != $lAnn['nr']) {
            $lNr = $lAnn['nr'];
            $lRet.= '<td width=4%>';
            $lRet.= htm($lNr).'.';
            $lRet.= '</td>';
            $lRet.= '<td width=20%>';
            $lRet.= htm($lAnn['date']);
            $lRet.= '</td>';
            $lRet.= '<td width=75%>';
            $lRet.= $lAnnText;
            $lRet.= '</td>';
          }   else {
            $lRet.= '<td width=4%>';
            $lRet.= '</td>';
            $lRet.= '<td width=20%>';
            $lRet.= htm($lAnn['date']);
            $lRet.= '</td>';
            $lRet.= '<td width=75%>';
            $lRet.= $lAnnText;
            $lRet.= '</td>';
          }
          $lRet.= '<td>';
          if (!empty($lAnn['changed'])) {
            $lRet.= img('img/ico/16/edit.gif');
          }
          $lRet.= '</td>';
          $lRet.= '</tr>';

        }
      }

      // Files
      if (!empty($aArr['files'])) {
        $lRet.='<tr><td class="th3"'.$this -> mColspan.'>'.htm(lan('job-fil.menu')).'</td></tr>'.LF;

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
          $lchecked.= ' disabled="disabled"';

          $lRet.= '<tr>';
          $lRet.= '<td class="nw" width=1%>';
          $lRet.= '<input type="checkbox" '.$lchecked.' />';
          $lRet.= '</td>'.LF;
          $lRet.= '<td width=4%>';
          $lRet.= '&nbsp;';
          $lRet.= '</td>';
          $lRet.= '<td width=20%>';
          $lRet.= '&nbsp;';
          $lRet.= '</td>';
          $lRet.= '<td width=75%>';
          $lRet.= htm($lFile['name']);
          $lRet.= '</td>';

          $lRet.= '</tr>';
        }
      }
      $lRet.= '</table>';

    }
    return $lRet;
  }

  public function getSingleProtocol() {
    $this -> mProtocolOnly = true;
    $this -> getBar();
    $this -> getList();
    $lRet = '';
    $lRet.= $this -> getProtocolCont();
    return $lRet;
  }
}