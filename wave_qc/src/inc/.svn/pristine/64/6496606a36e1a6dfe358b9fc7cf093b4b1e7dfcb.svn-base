<?php
class CInc_Job_Xchange_Diff extends CCor_Ren {
  
  public function __construct($aSrc, $aJid, $aJob) {
    $this->mSrc = $aSrc;
    $this->mJid = $aJid;
    $this->mJob = $aJob;
    $this->mFields = array();
    $this->addFields();
  }
  
  protected function addFields() {
    $this->addField('id', 'MessageID');  
    //$this->addField('x_jobid', 'JobID');  
    //$this->addField('x_src');  
    $this->addField('x_import_date', 'Import');  
    $this->addField('x_assign_date', 'Assigned');

    $lFields = new CXchange_Jobfields();
    foreach ($lFields as $lAlias => $lRow) {
      $this->addField($lAlias, $lRow['caption']);
    }
  }
  
  protected function addField($aAlias, $aCaption = null) {
    $lCap = (is_null($aCaption)) ? $aAlias : $aCaption;
    $this->mFields[$aAlias] = $lCap;
  }
  
  public function setIds($aIds) {
    $this->mIds = array();
    if (empty($aIds)) return;
    foreach ($aIds as $lId) {
      $lSafeId = intval($lId);
      $this->mIds[$lSafeId] = $lSafeId;
    }
  }
  
  protected function loadRows() {
    $this->mRows = array();
    if (empty($this->mIds)) return;
    $lSql = 'SELECT * FROM al_xchange_jobs_'.MID.' ';
    $lSql.= 'WHERE id IN ('.implode(',', $this->mIds).')';
    //echo $lSql;
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $this->mRows[] = $lRow;
    }
    //var_dump($this->mRows);
  }
  
  protected function getCont() {
    $this->loadRows();
    $lRet = '';
    $lRet.= '<table class="tbl" cellpadding="6">'.LF;
    
    $lRet.= $this->getHeader('Field');
    $lRet.= $this->getFields();
   
    $lRet.= '</table>'.LF;
    return $lRet;
  }
  
  protected function getHeader($aCaption = NB) {
    $lRet = '<tr>';
    $lRet.= '<td class="th2">'.$aCaption.'</td>';
    foreach ($this->mRows as $lRow) {
      $lRet.= '<td class="th2">&nbsp;</td>';
    }
    $lRet.= '<td class="th2">Job</td>';
    $lRet.= '</tr>'.LF;
    return $lRet;
  }
  
  protected function getFields() {
    foreach ($this->mFields as $lAlias => $lCaption) {
      $lRet.= $this->getFieldRow($lAlias, $lCaption);
    }
    return $lRet;
  }
  
  protected function getFieldRow($aField, $aCaption) {
    $lInternal = (substr($aField,0,2) == 'x_');
    if ('id' == $aField) {
      $lInternal = true;
    }
    $lRet = '<tr>';
    $lRet.= '<td class="td2 b">'.$aCaption.'</td>';
    $lOld = null;
    $lIsFirst = true;
    foreach ($this->mRows as $lRow) {
      $lVal = $lRow[$aField];
      $lCss = (!$lIsFirst && !$lInternal && ($lVal != $lOld)) ? ' cy' : ''; 

      $lRet.= '<td class="td1'.$lCss.'">';
      $lRet.= htm($lVal);
      $lRet.= '</td>';
      
      $lOld = $lVal;
      $lIsFirst = false;
    }

    $lVal = $this->mJob[$aField];
    if ('id' == $aField) {
      $lVal = '';
    }
    $lCss = (!$lIsFirst && !$lInternal && ($lVal != $lOld)) ? ' cy' : ''; 

    $lRet.= '<td class="td2'.$lCss.'">';
    $lRet.= htm($lVal);
    $lRet.= '</td>';

    $lRet.= '</tr>'.LF;
    return $lRet;
  }
  
  
  

}