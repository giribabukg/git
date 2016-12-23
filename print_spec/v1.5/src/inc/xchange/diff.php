<?php
class CInc_Xchange_Diff extends CCor_Ren {
  
  public function __construct($aXchangeId, $aJob = null) {
    $this->mMid = MID;
    $this->mXid = intval($aXchangeId);
    $this->loadXid($this->mXid);
    $this->setJob($aJob);
    $this->mJobFie = CCor_Res::getByKey('alias', 'fie');
    $this->mDict = new CXchange_Jobfields();
    $this->mFie = array();
    $this->mPlain = new CHtm_Fie_Plain();
    $this->init();
  }
  
  protected function init() {
    $this->addDefaultFields();
  }
  
  protected function addDefaultFields() {
    foreach ($this->mDict as $lAlias => $lRow) {
      $this->addField($lAlias, $lRow['caption']);
    }
  }
  
  public function addField($aAlias, $aCaption = null) {
    $lCaption = $aCaption;
    if (empty($aCaption)) {
      $lFie = $this->mJobFie[$aAlias];
      $lCaption = $lFie['name_'.LAN]; 
    }
    $this->mFie[$aAlias] = $lCaption;
  }
  
  public function setJob($aJob) {
    $this->mJob = $aJob;
    //var_dump($aJob);
    $this->mSrc = $aJob['src'];
    $this->mJid = $aJob['jobid'];
  }
  
  public function loadJob($aSrc, $aJobId) {
    $lFac = new CJob_Fac($aSrc, $aJobId);
    $lJob = $lFac->getDat();
    //var_dump($lJob);
    $this->setJob($lJob);
  }
  
  public function loadXid($aXid) {
    $this->mXid = intval($aXid);
    $lSql = 'SELECT * FROM al_xchange_jobs_'.$this->mMid.' WHERE id='.$this->mXid;
    $lQry = new CCor_Qry($lSql);
    $this->mXJob = $lQry->getDat();
    #var_dump($this->mXJob);
  }
  
  protected function getCont() {
    $lRet = '';
    $lRet.= $this->getHeader();
    $lRet.= $this->getFields();
    $lRet.= $this->getSelectAll();
    $lRet.= $this->getFooter();
    return $lRet;
  }
  
  protected function getHeader() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="xchange.sassign" />'.LF;
    $lRet.= '<input type="hidden" name="src" value="'.$this->mSrc.'" />'.LF;
    $lRet.= '<input type="hidden" name="jid" value="'.$this->mJid.'" />'.LF;
    $lRet.= '<input type="hidden" name="xid" value="'.$this->mXid.'" />'.LF;
    $lRet.= '<table class="tbl">'.LF;
    $lRet.= '<tr><td class="cap" colspan="5">Comparison</td></tr>'.LF;
    $lRet.= '<tr>';
    $lRet.= '<td class="th2 w16">&nbsp;</td>';
    $lRet.= '<td class="th2 w150">Field</td>';
    #$lDate = new CCor_Datetime($this->mXJob['x_import_date']);
    #$lRet.= '<td class="th2">'.$lDate->getFmt('d.m.Y H:i').'</td>';
    $lRet.= '<td class="th2 w200">Job (ID '.$this->mJid.')</td>';
    $lRet.= '<td class="th2 w200">Integrate (ID '.$this->mXid.')</td>';
    $lRet.= '</tr>'.LF;
    return $lRet;
  }
  
  protected function getSelectAll() {
    $lRet = '';
    $lRet.= '<tr>';
    $lRet.= '<td class="td2 ac p4">';
    $lRet.= img('ico/16/check-hi.gif');
    $lRet.= '</td>';
    
    $lRet.= '<td class="td2">';
    $lRet.= '<a onclick="Flow.diff.togCheck(\'.app-check\')" class="nav">Check/Uncheck all</a>';
    $lRet.= '</td>';
    
    $lRet.= '<td class="td2" colspan="2">';
    $lRet.= '<a onclick="Flow.diff.togSame(this)" class="nav">Show/Hide same</a>';
    $lRet.= '</td>';
    
    $lRet.= '</tr>'.LF;
    return $lRet;
  }
  
  protected function getFooter() {
    $lRet = '';
    $lBtnAtt = array('class' => 'btn w100');
    $lRet.= '<tr><td colspan="4" class="frm p8 ar">'.LF;
    $lRet.= btn(lan('lib.ok'), '', 'img/ico/16/ok.gif', 'submit', $aBtnAtt).NB;
    $lRet.= btn(lan('lib.cancel'), "go('index.php?act=xchange')", 'img/ico/16/cancel.gif', 'button');
    $lRet.= '</td></tr>'.LF;
    $lRet.= '</table>'.LF;
    $lRet.= '</form>'.LF;
    return $lRet;
  }
  
  protected function getFields() {
    $lRet = '';
    foreach ($this->mFie as $lAlias => $lCap) {
      $lRet.= $this->getFieldContent($lAlias, $lCap);
    }
    return $lRet;
  }
  
  protected function isChecked($aAlias) {
    return false;
  }
  
  protected function getFieldContent($aAlias, $aCaption) {
    $lRet = '';
    $lNum = getNum('r');
    $lXchVal = (isset($this->mXJob[$aAlias])) ? $this->mXJob[$aAlias] : '';
    $lJobVal = (isset($this->mJob[$aAlias]))  ? $this->mJob[$aAlias]  : '';
    //var_dump($this->mJob);
    
    $lIsSame = ((string)$lJobVal == (string)$lXchVal);
    
    $lRet.= '<tr class="hi">'.LF;

    $lRet.= '<td class="td2 ac p4">'.LF;
    
    $lLabel = '';
    $lLabelEnd = '';
    if ($lIsSame) {
      $lRet.= img('ico/16/flag-03.gif');
    } else {
      $lLabel = '<label for="'.$lNum.'" class="cp">';
      $lLabelEnd = '</label>';
      $lCheck = $this->isChecked($aAlias) ? ' checked="checked"' : '';
      $lRet.= '<input type="checkbox"'.$lCheck.' name="fie['.$aAlias.']" id="'.$lNum.'" class="app-check" />'.LF;
      $lRet.= '<input type="hidden" name="old['.$aAlias.']" value="'.htm($lJobVal).'" />'.LF;
      $lRet.= '<input type="hidden" name="val['.$aAlias.']" value="'.htm($lXchVal).'" />'.LF;
    }
    $lRet.= '</td>';
    
    $lRet.= '<td class="td2 p4">'.LF;
    $lRet.= $lLabel; 
    $lRet.= htm($aCaption);
    $lRet.= $lLabelEnd;
    $lRet.= '</td>'.LF;
    
    
    $lRet.= '<td class="td1 p4">'.LF;
    $lRet.= $lLabel;
    $lDisplay = $lJobVal;

    if (isset($this->mJobFie[$aAlias])) {
      $lDef = $this->mJobFie[$aAlias];
      $lDisplay = $this->mPlain->getPlain($lDef, $lDisplay);
    }
    $lRet.= htm($lDisplay);
    $lRet.= $lLabelEnd;
    $lRet.= '</td>'.LF;

    $lDisplay = $lXchVal;
    if (isset($this->mJobFie[$aAlias])) {
      $lDef = $this->mJobFie[$aAlias];
      $lDisplay = $this->mPlain->getPlain($lDef, $lDisplay);
    }

    if ($lIsSame) {
      $lRet.= '<td class="td1 p4">'.LF;
      $lRet.= $lLabel;
      $lRet.= htm($lDisplay);
      $lRet.= $lLabelEnd;
      $lRet.= '</td>'.LF;
    } else {
      $lRet.= '<td class="td1 cy p4">'.LF;
      $lRet.= $lLabel;
      $lRet.= htm($lDisplay);
      $lRet.= $lLabelEnd;
      $lRet.= '</td>'.LF;
    }
    
    $lRet.= '</tr>'.LF;
    return $lRet;
  }
  
}