<?php

class CUtl_Chk_Form extends CHtm_Form {

  public function __construct($aAct, $aCaption, $aCancel = NULL, $aSrc, $aJobId, $aLoopId, $aUid, $aWithButtons=FALSE) {
    parent::__construct($aAct, $aCaption, $aCancel);
    $this -> mSrc = $aSrc;
    $this -> mJobId = $aJobId;
    $this -> mLoopId = $aLoopId;
    $this -> mUid = $aUid;
    $this -> mWithButtons = $aWithButtons;
    $this -> getJobFromShadow();

    $this->setParam('mSrc', $this -> mSrc);
    $this->setParam('mJobId', $this -> mJobId);
    $this->setParam('mLoopId', $this -> mLoopId);
    
    $lAnyUsr = new CCor_Anyusr($this -> mUid);
    $lAnyUserName = $lAnyUsr -> getFullName();
    $this -> mCap = $aCaption.' - '.$lAnyUserName;

  }

  public function load() {
    
  }
    
  protected function getFieldForm() {
    $lUser = CCor_Usr::getInstance();
    $lUid = $this->mUid; #$lUser -> getId();
    $lRet = '';
  
    /*$lRet.= '<table cellpadding="2" cellspacing="0" class="tbl w100p">'.LF;
    $lRet.= '<tr><td class="th1" colspan="3">Checklist</td></tr>'.LF;
  */
    $lRet.= '<tr><td>'.lan('check.ok').'</td>';
    $lRet.= '<td>'.lan('check.nok').'</td>';
    $lRet.= '<td>'.lan('check.item.to.check').'</td></tr>';
    
//     $lSql = 'SELECT * FROM al_job_chk as a, al_chk_itm as b WHERE';
//     $lSql.= ' a.src_id='.esc($this -> mJobId);
//     $lSql.= ' AND a.src='.esc($this -> mSrc);
//  #   $lSql.= ' AND a.loop_id='.esc($this -> mLoopId);
//     $lSql.= ' AND a.user_id='.$lUid;
//     $lSql.= ' AND a.check_id=b.id';

    $lSql = 'SELECT * FROM al_job_chk as A, al_chk_items as B';
    $lSql.= ' WHERE A.src_id='.esc($this -> mJobId);
    $lSql.= ' AND A.src='.esc($this -> mSrc);
    $lSql.= ' AND A.user_id='.$lUid;
    $lSql.= ' AND A.check_id=B.id';
    $lSql.= ' ORDER BY B.ord_no';

    $lQry = new CCor_Qry($lSql);
  
    foreach ($lQry as $lRow) {
      $lId  = $lRow['id'];
      $lSta = $lRow['status'];
      $lCheckOk = '';
      $lCheckNOk = '';
      if ($lSta == 'ok') $lCheckOk = 'checked';
      if ($lSta == 'nok') $lCheckNOk = 'checked';
  
      $lRet.= '<tr>';
      $lRet.= '<td class="td1 w16">';
      $lRet.= '<input type="radio" name="'.$lId.'" value="ok" '.$lCheckOk.'>';
      $lRet.= '</td>';
  
      $lRet.= '<td class="td1 w16">';
      $lRet.= '<input type="radio" name="'.$lId.'" value="nok" '.$lCheckNOk.'>';
      $lRet.= '</td>';
  
      $lRet.= '<td class="td1">';
      $lRet.= htm($lRow['name_'.LAN]);
      $lRet.= '</td>';
    }
  
    $lRet.= '</table>'.BR;
  
  
    return $lRet;
  }
  
  protected function getButtons($aBtnAtt = array(), $aBtnTyp = 'button') {
    if (!$this -> mWithButtons) return;
    $lRet = '<div class="btnPnl">'.LF;
    $lRet.= btn(lan('lib.ok'), '', 'img/ico/16/ok.gif', 'submit', $aBtnAtt).NB;
    $lRet.= btn(lan('lib.reset'), '', 'img/ico/16/cancel.gif', 'reset', $aBtnAtt).NB;
    if (!empty($this -> mCancel)) {
      $lRet.= btn(lan('lib.cancel'), "window.close()", 'img/ico/16/cancel.gif');
    }
    $lRet.= '</div>'.LF;
    return $lRet;
  }
  
  protected function getJobFromShadow() {
    $lSql = 'SELECT * FROM al_job_shadow_'.MID.' WHERE jobid='.esc($this -> mJobId);
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $this -> mJob = $lRow; 
    }
  }
  
  protected function getJobField($aAlias) {
    $lField = CCor_Res::getByKey('alias', 'fie',  array('alias' => $aAlias));
    return $lField[$aAlias]['name_'.LAN];
  } 
  
  protected function getTitle() {
    $lRet = '<div class="th1">'.htm($this -> mCap).'</div>'.LF;
    $lRet.= '<div class="th3">'.$this -> getJobField('stichw').': '.$this -> mJob['stichw'].'</div>'.LF;
    $lRet.= '<div class="th3">'.$this -> getJobField('jobnr').': '.$this -> mJobId.'</div>'.LF;
    return $lRet;
  }
}