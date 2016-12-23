<?php
/**
 * Title
 *
 * Description
 *
 * @package    package
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 */

class CInc_Job_Flag_List extends CHtm_List{#CCor_Ren {

  protected $mShowButton = TRUE;
  /**
   * Show Button 'Add User' or NOT
   * @var boolean
   */
 
  public function __construct($aSrc, $aJobId, $aStage = 'job', $aShowButton = TRUE, $aTyp = '', $aShowDelButton = FALSE) {
    parent::__construct($aStage.'-typ');
    $this -> mSrc = $aSrc;
    $this -> mTyp = $aTyp;
    $this -> mJobId = $aJobId;
    $this -> mJobMod = $aStage;
    $this -> mTitle = lan('job-flag.menu');
    $this -> mCapCls = 'th1';
    $this -> mColCnt = 1;
    $this -> mShowButton = $aShowButton;
    $this -> mShowDelButton = $aShowDelButton;
    if ($this -> mShowDelButton) {
      $this -> mColspan = ' colspan="8"';//sub.php: colspan + 1
    } else {
      $this -> mColspan = ' colspan="7"';//sub.php: colspan + 1
    }
    if (!$this -> mShowButton) {
      $this -> setAtt('class', 'tbl w800');
    }
    $this -> mUsr = CCor_Usr::getInstance();
    $this -> setAtt('width', '100%');
    $this -> getPriv('job-apl');

    $lSql = 'FROM `al_job_apl_loop` WHERE 1';
    $lSql.= ' AND `src`='.esc($this -> mSrc);
    if (!empty($this -> mTyp)) {
      $lSql.= ' AND `typ`='.esc($this -> mTyp);
    } else {
      $lSql.= ' AND `typ`!="apl"';
    }
    $lSql.= ' AND `mand`='.intval(MID);
    $lSql.= ' AND `jobid`='.esc($this -> mJobId);

    $lSqlCount = 'SELECT COUNT(*) '.$lSql;
    $this -> mCount = CCor_Qry::getInt($lSqlCount);
    
    #$lSqlGetStepId = 'SELECT `typ`, `step_id` '.$lSql;
    #$lSqlGetStepId.= ' AND `status`="open" ';
    #$this -> mAplStepId = CCor_Qry::getInt($lSqlGetStepId);

    $lSql = 'SELECT * '.$lSql;
    $this -> mQry = new CCor_Qry($lSql);

    if ($this -> mShowButton AND 0 < $this -> mCount AND $this -> mJobMod != 'arc') {
      $this -> addBtn(lan('lib.email.new'), 'go("index.php?act=job-flag.newmail&src='.$this -> mSrc.'&jobid='.$this -> mJobId.'")', 'img/ico/16/plus.gif');

      // Show Button 'Add User'
      #if ($this -> mUsr -> canInsert('flag.user') AND $this -> mAplStepId != FALSE){
      #  $this -> addBtn(lan('apl.add_user'), 'go("index.php?act=job-'.$this -> mSrc.'.step&sid='.$this -> mAplStepId.'&jobid='.$this -> mJobId.'&addUser=TRUE")', 'img/ico/16/plus.gif');
      #}

    }
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

      $lRet.= '<tr>';
      $lRet.= '<td class="th1"'.$this -> mColspan.'>'.htm(lan('job-flag.menu')).'</td></tr>'.LF;
    } else {
      $lRet = parent::getTag();
    }
    foreach ($this -> mQry as $lRow) {
      $lLis = new CJob_Flag_Sub($lRow,NULL,$this -> mSrc, $this -> mJobId,$this -> mJobMod, $this -> mShowDelButton);
      $lRet.= $lLis -> getBar();
      $lRet.= $lLis -> getList();
    }
    $lRet.= '</table>';

    return $lRet;

  }

}