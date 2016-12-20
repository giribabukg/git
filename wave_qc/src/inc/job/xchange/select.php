<?php
class CInc_Job_Xchange_Select extends CCor_Ren {
  
  public function __construct($aSrc, $aJid, $aAct = 'job-xchange.diff') {
    $this->mSrc = $aSrc;
    $this->mJid = $aJid;
    $this->mDateFmt = lan('lib.datetime.short');
    $this->setHidden('act', $aAct);
    $this->setHidden('src', $aSrc);
    $this->setHidden('jobid', $aJid);
    $this->mIds = array();
  }
  
  public function setHidden($aKey, $aVal) {
    $this->mPar[$aKey] = $aVal;
  }
  
  public function setIds($aIds) {
    if ('all' == $aIds) {
      $this->mIds = 'all';
      return;
    }
    $this->mIds = array();
    if (empty($aIds)) return;
    foreach ($aIds as $lId) {
      $lSafeId = intval($lId);
      $this->mIds[$lSafeId] = $lSafeId;
    }
  }
  
  protected function loadRows() {
    $lSql = 'SELECT * FROM al_xchange_jobs_'.MID.' ';
    $lSql.= 'WHERE x_jobid='.esc($this->mJid).' ';
    $lSql.= 'AND x_src='.esc($this->mSrc).' ';
    $lSql.= 'ORDER BY x_import_date';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lRet[] = $lRow;
    }
    return $lRet;
  }
  
  protected function getFormTag() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="get">'.LF;
    foreach ($this->mPar as $lKey => $lVal) {
      $lRet.= '<input type="hidden" name="'.$lKey.'" value="'.htm($lVal).'" />'.LF;
    }
    return $lRet;
  }
  
  protected function getCont() {
    $this->mRows = $this->loadRows();
    $this->mCls = 'td1';
    
    $lRet = '';
    $lRet.= $this->getFormTag();
    $lRet.= '<table class="tbl w300" cellpadding="6">';
    $lRet.= '<tr>';
    $lRet.= '<td class="th2">ID</td>';
    $lRet.= '<td class="th2">&nbsp;</td>';
    $lRet.= '<td class="th2">Import</td>';
    $lRet.= '<td class="th2">Assigned</td>';
    $lRet.= '</tr>'.LF;
    
    if (empty($this->mRows)) {
      return $lRet.'</table>';
    }
    
    $this->mCount = 1;
    foreach ($this->mRows as $lId => $lRow) {
      $lRet.= $this->getRow($lRow);
    }
    
    $lRet.= $this->getButtons();
    
    $lRet.= '</table>'.LF;
    $lRet.= '</form>'.LF;
    return $lRet;
  }
  
  protected function getRow($aRow) {
    $lRet = '';
    $lRet.= '<tr>';
    $lNum = getNum('c');
    
    $lRet.= '<td class="'.$this->mCls.' ar">';
    $lRet.= '<label for="'.$lNum.'">';
    #$lRet.= $this->mCount.'.';
    $lRet.= $aRow['id'];
    $lRet.= '</label>';
    $lRet.= '</td>'.LF;
    
    $lRet.= '<td class="'.$this->mCls.' w16 ac">';
    $lId = $aRow['id'];
    if ('all' == $this->mIds) {
      $lCheck = ' checked="checked"';
    } else {
      $lCheck = (in_array($lId, $this->mIds)) ? ' checked="checked"' : '';
    } 
    $lRet.= '<input type="checkbox" id="'.$lNum.'"'.$lCheck.' name="id[]" value="'.$aRow['id'].'">';
    $lRet.= '</td>'.LF;
    
    $lRet.= '<td class="'.$this->mCls.'">';
    $lRet.= '<label for="'.$lNum.'">';
    $lRet.= $this->fmtDate($aRow['x_import_date']);
    $lRet.= '</label>';
    $lRet.= '</td>'.LF;
    
    $lRet.= '<td class="'.$this->mCls.'">';
    $lRet.= '<label for="'.$lNum.'">';
    $lRet.= $this->fmtDate($aRow['x_assign_date']);
    $lRet.= '</label>';
    $lRet.= '</td>'.LF;
    $lRet.= '</tr>'.LF;
    
    $this->mCls = ($this->mCls == 'td1') ? 'td2' : 'td1';
    $this->mCount++;
    return $lRet;
  }
  
  protected function fmtDate($aDate) {
    $lDat = new CCor_Datetime($aDate);
    return $lDat->getFmt($this->mDateFmt);
  }
  
  protected function getButtons() {
    $lRet = '';
    $lRet.= '<tr>';
    $lRet.= '<td class="frm ar p16" colspan="4">';
    $lRet.= btn(lan('lib.ok'), '', 'img/ico/16/ok.gif', 'submit', $aBtnAtt).NB;
    $lRet.= '</td>';
    $lRet.= '</tr>'.LF;
    return $lRet; 
  }

}