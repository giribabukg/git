<?php
/**
 * Title
 *
 * Description
 *
 * @package    package
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 */

class CInc_Job_Apl_Page_List extends CCor_Ren {

  protected $mTryOpenProtocol;

  public function __construct($aSrc, $aJobId, $aTryOpenProtocol = NULL) {
    #parent::__construct('job-apl-page');
    $this -> mColspan = ' colspan="8"';//sub.php: colspan + 1
    $this -> mSrc = $aSrc;
    $this -> mJid = $aJobId;
    $this -> mTryOpenProtocol = $aTryOpenProtocol;

    $lSql = 'SELECT * FROM al_job_apl_loop WHERE 1 ';
    $lSql.= 'AND src='.esc($this -> mSrc).' ';
    $lSql.= 'AND mand='.intval(MID).' ';
    $lSql.= 'AND typ LIKE "apl%" ';
    $lSql.= 'AND jobid='.esc($this -> mJid).' ORDER BY num DESC';
    $this -> mQry = new CCor_Qry($lSql);

    #$this -> addBtn(lan('lib.expandall'), 'Flow.Std.showAllTr()','img/ico/16/nav-down-lo.gif');
    #$this -> addBtn(lan('lib.collapseall'), 'Flow.Std.hideAllTr()','img/ico/16/nav-up-lo.gif');
  }

  public function ShowAplButtons() {
    $lLoopId = 0;
    $lShowAplBtnUntilConfirm = CCor_Cfg::get('job.apl.show.btn.untilconfirm');

    foreach ($this -> mQry as $lRow) {
      if ('open' == $lRow['status']) {
        $lLoopId = $lRow['id'];
      }
    }
    if (0 < $lLoopId) {
      $lUid = CCor_Usr::getAuthId();
      $lIds = array();
      $lNoDeny = array();
      $lSql = 'SELECT user_id,pos,status,comment,done,confirm FROM al_job_apl_states WHERE loop_id='.$lLoopId;
      // Deleted User sort out.
      $lSql.= ' AND del != "Y"';
      $lSql.= ' ORDER BY pos'; // wichtig, wenn user mehrfach (mit unterschiedl. pos) eingeladen ist
      $lQry = new CCor_Qry($lSql);
      #echo '<pre>---form.php---'.get_class().'---';var_dump($lSql,'#############');echo '</pre>';

      $lMinPos = MAX_SEQUENCE; // Behelfsvorbelegung
      foreach ($lQry as $lRow) {
        //brauche zur Anzeige distinct user_ids => können sich über backupuser-Fkt ändern und mehrfach vorkommen
        //Angezeigt werden muß die user_id mit der kleineren pos, da die agieren darf: $lSql.= ' ORDER BY pos';
        if (!isset($lIds[$lRow['user_id']])) {
          $lIds[$lRow['user_id']] = $lRow;
        }
        $lPos = $lRow['pos'];
        if (0 == $lRow['status'] AND $lMinPos > $lPos) {
          $lMinPos = $lPos;
        }
        if (("one" == $lRow['confirm'] AND "Y" == $lRow['done'] AND empty($lRow['comment'])) OR "-" == $lRow['done']) {//fuer eine Uebergangszeit, da es vorher "-" nicht gab.
        #if ("one" == $lRow['confirm'] AND "Y" == $lRow['done'] AND empty($lRow['comment'])) {
          $lNoDeny[$lRow['user_id']] = TRUE;
        }
      }
      #echo '<pre>---form.php---';var_dump($lIds[$lUid],$lMinPos,'#############');echo '</pre>';
      // welche Funktionalität man will -> config: != oder <
      //$lMinPos != $lIds[$lUid]['pos'] zeigt die Buttons nur an, solange man keinen Button bestätigt hat!
      //$lMinPos < $lIds[$lUid]['pos'] zeigt die Buttons, sobald man an der Reihe ist und auch weiter: Korrekturmögl.
      if($lShowAplBtnUntilConfirm) {
        $lShow = !isset($lIds[$lUid]) OR $lMinPos != $lIds[$lUid]['pos'];
      } else {
        $lShow = !isset($lIds[$lUid]) OR $lMinPos < $lIds[$lUid]['pos'];
      }
      if (!isset($lIds[$lUid]) OR $lMinPos < $lIds[$lUid]['pos'] OR isset($lNoDeny[$lUid])) {
        return FALSE;
      } else {
        return TRUE;
      }
    } else {
      return FALSE;
    }
  }

  protected function getCont() {
    $lRet= '<table cellpadding="2" cellspacing="0" class="tbl w100p">'.LF;
    $lRet.= '<tr>';
    $lRet.= '<td class="cap"'.$this -> mColspan.'>'.htm(lan('job-apl.menu')).'</td></tr>'.LF;

    foreach ($this -> mQry as $lRow) {
      $lLis = new CJob_Apl_Sub($lRow, $this -> mTryOpenProtocol);
      $lRet.= $lLis -> getBar();
      $lRet.= $lLis -> getList();
    }
    $lRet.= '</table>';

    return $lRet;
  }

}