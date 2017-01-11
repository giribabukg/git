<?php
class CInc_Job_Apl2_List extends CCor_Ren {
  
  public function __construct($aSrc, $aJid) {
    $this->mSrc = $aSrc;
    $this->mJid = $aJid;
    
    $this->getPrefs();
    
    $this->loadLoops();
  }
  
  protected function getPrefs() {
    $lUsr = CCor_Usr::getInstance();
    $this->mShowAllSubloops = $lUsr->getPref('job-apl.showallsub');
  }
  
  protected function loadLoops() {
    $this->mAplJob = new CApl_Job();
    $this->mAplJob->setJob($this->mSrc, $this->mJid);
    $lLoops = $this->mAplJob->getLoops('apl');
    if (!empty($lLoops)) {
      $lTmp = array();
      foreach ($lLoops as $lKey => $lRow) {
        $lAplType = $lRow['typ'];
        $lNum = (isset($lTypeNum[$lAplType])) ? ($lTypeNum[$lAplType] +1) : 1;
        $lTypeNum[$lAplType] = $lNum;
        $lRow['_num'] = $lNum;
        $lTmp[$lKey] = $lRow;
      }
      $lLoops = array_reverse($lTmp, true);
    }
    $this->mAplLoops = $lLoops; 
  }
  
  protected function getCont() {
    $lRet = '';
    $lRet.= $this->getHeader();
    $lRet.= $this->getLoops();
    $lRet.= $this->getFooter();
    return $lRet;
  }
  
  protected function getHeader() {
    $lRet = '';
    $lRet.= '<div class="bc-apl">';

    $lRet.= '<div class="cap">';
    $lRet.= htm('Workflows');
    $lRet.= '</div>';    
    
    $lRet.= '<div class="frm p8">';
    
    $lMen = new CHtm_Menu('View', "options nav");
    $lMen->addTh1('View Options');
    $lMen->addJsItem('Flow.apl.collapse(\'.bc-loop-cont,.bc-prefix-cont\')', 'Hide all');
    $lMen->addJsItem('Flow.apl.expandParents(\'.bc-loop-cont,.bc-prefix-cont\')', 'Show all');
    $lMen->addJsItem('Flow.apl.collapse(\'.bc-sub-closed\')', 'Hide Closed Workflows');
    $lMen->addJsItem('Flow.apl.expandParents(\'.bc-state-active\')', 'Show my active');
    $lMen->addTh2('Subloops');
    $lImg = ($this->mShowAllSubloops) ? 'ico/16/ok.gif' : 'd.gif'; 
    $lMen->addItem('index.php?act=job-apl2.showprevious&src='.$this->mSrc.'&jobid='.$this->mJid, 'Show Previous', $lImg);
    $lRet.= $lMen->getContent();
    
    /*
    $lRet.= '<a href="javascript:Flow.apl.collapse(\'.bc-loop-cont,.bc-prefix-cont\')" class="nav">Hide all</a>|';
    $lRet.= '<a href="javascript:Flow.apl.expandParents(\'.bc-loop-cont,.bc-prefix-cont\')" class="nav">Show all</a>|';
    $lRet.= '<a href="javascript:Flow.apl.collapse(\'.bc-sub-closed\')" class="nav">Hide closed</a>|';
    $lRet.= '<a href="javascript:Flow.apl.expandParents(\'.bc-sub-closed\')" class="nav">Show closed</a>|';
    
    $lRet.= '<a href="javascript:Flow.apl.collapse(\'.bc-sub-open\')" class="nav">Hide open</a>|';
    $lRet.= '<a href="javascript:Flow.apl.expandParents(\'.bc-sub-open\')" class="nav">Show open</a>|';
    
    $lRet.= '<a href="javascript:Flow.apl.expandParents(\'.bc-state-mine\')" class="nav">Show mine</a>|';
    $lRet.= '<a href="javascript:Flow.apl.expandParents(\'.bc-state-active\')" class="nav">Show my active</a>|';
    */
    #$lRet.= '<div class="cap">Approval Workflows</div>';
    $lRet.= '</div>';
    return $lRet;
  }
  
  protected function getFooter() {
    $lRet = '';
    $lRet.= '</div>';
    return $lRet;
  }  
  
  protected function getAplTypes() {
    $lSql = 'SELECT code, name FROM al_apl_types WHERE mand='.MID;
    $lQry = new CCor_Qry($lSql);
    $lAplTypes = array('all'=>'All APL Types');
    foreach ($lQry as $lKey => $lVal) {
      $lAplTypes[$lVal['code']] = $lVal['name'];
    }
    return $lAplTypes;
  }
  
  protected function getLoops() {
    if (empty($this->mAplLoops)) {
      return '';
    }
    $lArrTypes = $this->getAplTypes();
    
    $lRet = '';
    foreach ($this->mAplLoops as $lLoop) {
      #$lAplType = $lLoop['typ'];
      #if ($lAplType != $lOld) {
        #$lRet.= '<div class="cap">';
        #$lName = isset($lArrTypes[$lAplType]) ? $lArrTypes[$lAplType] : 'Approval Workflows';
        #$lRet.= htm($lName);
        #$lRet.= '</div>';
      #}
      $lView = new CJob_Apl2_Loop($lLoop, $lLoop['_num']);
      $lRet.= $lView->getContent();
    }
    return $lRet;
  }

}