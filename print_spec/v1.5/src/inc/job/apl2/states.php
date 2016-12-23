<?php
class CInc_Job_Apl2_States extends CCor_Ren {
  
  public function __construct($aLoop) {
    $this->mLoop = $aLoop;
    $this->mLid = $aLoop['id'];
    $this->mSid = 'l'.$aLoopId;
    $this->mUid = CCor_Usr::getAuthId();
    $this->mCanSetStatus = false;
  }
  
  protected function getCont() {
    $lRet = '';
    $lRet = '<div class="bc-apl-loop indent sid'.$this->mSid.'" data-sub="'.$this->mSid.'">';
    $lRet.= $this->getHeader();
    
    $lDis = (true) ? 'block' : 'none';
    $lTog = (true) ? '' : ' tg';
    $lRet.= '<div class="box'.$lTog.'" id="sub-'.$this->mSid.'" style="padding:2em; display:'.$lDis.'">';
    
    $lRet.= '<div class="fl">';
    $lRet.= $this->getImage();
    $lRet.= '</div>';
    
    $lRet.= '<div class="fl indent">';
    $lRet.= $this->getStates();
    $lRet.= '</div>';
    
    if ($this->mCanSetStatus) {
      $lRet.= '<div class="fl indent">';
      $lRet.= $this->getApprovalButtons();
      $lRet.= '</div>';
    }
    
    $lRet.= '<div class="clr"></div>';
    
    $lRet.= '</div>';
    $lRet.= '</div>';
    return $lRet;
  }
  
  protected function getImage() {
    $lRet = '';
    $lRet.= img('img/ico/64/mime-xls.png');
    $lMen = new CHtm_Menu('File Actions');
    //$lMen->addTh2($this->mSub['file_name'].' [ID'.$this->mSub['file_version'].']');
    $lMen->addItem('#', 'View online');
    $lMen->addItem('#', 'Download');
    $lMen->addItem('#', 'Download and lock');
    $lRet.= $lMen->getContent();
    return $lRet;
  }
  
  protected function getHeader() {
    $lRet = '';
    $lRet.= '<div class="th3 cp p4" onclick="Flow.Std.tog(\'sub-'.$this->mSid.'\')">';
    $lRet.= 'Without Sub';
    $lRet.= '</div>';
    return $lRet;
  }
  
  protected function loadStates() {
    return $this->mLoop->loadStates();
  }
  
  protected function getStates() {
    $lRet = '';
    $lRows = $this->loadStates();
    if (empty($lRows)) {
      return '';
    }
    $lRet = '<table class="tbl">';
    $lRet.= $this->getStatesHeader();
    $lRet.= $this->getStatesRows($lRows);
    $lRet.= '</table>'.LF;

    return $lRet;
  }
  
  protected function getStatesHeader() {
  	$lRet = '<tr>';
  	$lRet.= '<td class="th3 w16">&nbsp;</td>';
  	$lRet.= '<td class="th3 w16">&nbsp;</td>';
  	$lRet.= '<td class="th3 w200">Name</td>';
  	$lRet.= '<td class="th3 w400">Comment</td>';
  	$lRet.= '<td class="th3 w200">Task</td>';
  	$lRet.= '<td class="th3 w80">Actions</td>';
  	$lRet.= '</tr>';
  	return $lRet;
  }
  
  protected function getStatesRows($aRows) {
  	$lRet = '';
  	$lOldGroup = null;
  	foreach ($aRows as $lRow) {
  	  $lMe = ($lRow['user_id'] == $this->mUid);
  	  if ($lMe && $lRow['status'] == 0) {
  	    $this->mCanSetStatus = true;
  	    $this->mSetId = $lRow['id'];
  	  }
  	  $lGru = $lRow['gru_id'];
  	  if ($lGru != $lOldGroup) {
  	      $lOldGroup = $lGru;
    	  $lRet.= '<tr>';
    	  $lRet.= '<td class="td1 ac">'.$this->getStatusImage($lRow['status']).'</td>';
    	  $lRet.= '<td class="td1 ac">'.$lRow['pos'].'</td>';
    	  $lCls = ($lMe) ? ' cy' : '';
    	  $lRet.= '<td class="td1'.$lCls.'">'.$lRow['name'].'</td>';
    	  $lRet.= '<td class="td1">'.$lRow['comment'].'</td>';
    	  $lRet.= '<td class="td1">'.$lRow['task'].'</td>';
    	  $lRet.= '<td class="td1">'.$this->getStatusActionMenu($lRow).'</td>';
    	  $lRet.= '</tr>'.LF;
  	  }
  	}
  	return $lRet;
  }
  
  protected function getStatusImage($aState) {
    $lRet = img('img/ico/16/flag-0'.$aState.'.gif');
    return $lRet;
  }
  
  protected function getStatusActionMenu($aRow) {
    $lMen = new CHtm_Menu('Actions');
    $lMen->addTh2($aRow['name']);
    $lMen->addItem('#', 'Add user...');
    $lMen->addItem('#', 'Send Email...');
    $lMen->addTh2('Admin [ID '.$aRow['id'].']');
    $lMen->addItem('#', 'Delete');
    $lMen->addItem('javascript:Flow.apl.resetStatus('.$aRow['id'].','.$this->mSid.')', 'Reset Status');
    $lMen->addItem('#', 'Replace user with...');
    $lMen->addItem('#', 'Edit raw data...');
    return $lMen->getContent(); 
  }
  
  protected function getApprovalButtons() {
    $lRet = '';
    $lAtt = array('class' => 'btn w200');
    $lLnk = 'index.php?act=job-apl2.apl&src='.$this -> mSrc.'&jid='.$this -> mJobId.'&flag=';
    
    $lAplButtons = $this->getButtonArray();
    if (!empty($lAplButtons)) {
      foreach ($lAplButtons as $lAplKey => $lAplBtn) {
        if ($lAplKey == 6) continue;
        $lName = lan('apl.'.$lAplBtn);
        $lRet.= btn($lName, 'Flow.apl.setStatus('.$this->mSetId.','.$lAplKey.',"'.$lName.'",'.$this->mSid.')', 'img/ico/16/flag-0'.$lAplKey.'.gif', 'button', $lAtt).BR.BR;
      }
    }
    return $lRet;
  }
  
  protected function getButtonArray() {
  	return CCor_Cfg::get('buttons.apl', array());
  }
}