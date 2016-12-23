<?php
/**
 * Title
 *
 * Description
 *
 * @package    package
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 */

class CInc_Job_Apl_List extends CHtm_List{#CCor_Ren {

  protected $mShowButton = TRUE;
  /**
   * Show Button 'Add User' or NOT
   * @var boolean
   */

  public function __construct($aSrc, $aJobId, $aStage = 'job', $aShowButton = TRUE, $aTyp = 'apl', $aShowDelButton = FALSE, $aJob = NULL) {
    parent::__construct($aStage.'-'.$aSrc.'-apl');
    $this -> mSrc = $aSrc;
    $this -> mTyp = $aTyp;
    $this -> mJid = $aJobId;
    $this -> mJobObj = $aJob;
    $this -> mJobMod = $aStage;
    $this -> mTitle = lan('job-apl.menu');
    $this -> mCapCls = 'th1';
    $this -> mColCnt = 1;
    $this -> mColspan = ' colspan="9"';//sub.php: colspan + 1
    $this -> mShowButton = $aShowButton;
    $this -> mShowDelButton = $aShowDelButton;
    if (!$this -> mShowButton) {
      $this -> setAtt('class', 'tbl w800');
    }
    $this ->mUsr = CCor_Usr::getInstance();
    $this -> setAtt('width', '100%');
    $this -> getPriv('job-apl');

    $lSql = ' FROM al_job_apl_loop WHERE 1';
    $lSql.= ' AND src='.esc($this -> mSrc);
    $lSql.= ' AND typ LIKE '.esc($this -> mTyp.'%');
    $lSql.= ' AND mand='.intval(MID).' ';
    $lSql.= ' AND jobid='.esc($this -> mJid);

    $lSqlCount = 'SELECT COUNT(*)'.$lSql;
    $this -> mCount = CCor_Qry::getInt($lSqlCount);

    $lSqlGetStepId = 'SELECT step_id'.$lSql;
    $lSqlGetStepId.= ' AND status="open" ';
    $this -> mAplStepId = CCor_Qry::getInt($lSqlGetStepId);

    $lSql = 'SELECT *'.$lSql.' ORDER BY id DESC, num';
    $this -> mQry = new CCor_Qry($lSql);

    if ($this -> mShowButton AND 0 < $this -> mCount AND $this-> mUsr-> canInsert('job-apl') && $this->mJobMod != 'arc') {
      $this -> addBtn(lan('lib.email.new'), 'go("index.php?act=job-apl.newmail&src='.$this -> mSrc.'&jobid='.$this -> mJid.'")', 'img/ico/16/plus.gif');
    }
    // Show Button 'Add User'
    if ($this ->mUsr->canInsert('apl.user')
        AND $this -> mShowButton
        AND 0 < $this -> mCount
        AND $this -> mAplStepId != FALSE
        AND $this->mJobMod != 'arc'){
        $this -> addBtn(lan('apl.user'), 'go("index.php?act=job-'.$this->mSrc.'.step&sid='.$this -> mAplStepId.'&jobid='.$this -> mJid.'&addUser=TRUE")', 'img/ico/16/plus.gif');
    }
  }

  public function setShowButton($aFlag = true) {
    $this->mShowButton = $aFlag;
  }

  public function getBody() {
    $lRet = '';
    if ($this -> mShowButton) {
      $lRet.= '<table cellpadding="2" cellspacing="0" class="tbl w100p">'.LF;
      // Email Button
      $lRet.= '<tr>'.LF;
      $lRet.= '<td class="sub"'.$this -> mColspan.'>';
      $lRet.= $this -> getSubHeaderContent();
      $lRet.= '</td>';
      $lRet.= '</tr>'.LF;

      #$lRet.= '<tr>';
      #$lRet.= '<td class="th1"'.$this -> mColspan.'>'.htm(lan('job-apl.menu')).'</td></tr>'.LF;
    } else {
      $lRet = parent::getTag();
    }
    foreach ($this -> mQry as $lRow) {
      $lLis = new CJob_Apl_Sub($lRow, NULL, $this -> mSrc, $this -> mJid, $this -> mJobMod, $this -> mShowDelButton, $this -> mJobObj);
      $lRet.= $lLis -> getBar();
      $lRet.= $lLis -> getList();
    }
    $lRet.= '</table>';

    return $lRet;
  }
}