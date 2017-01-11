<?php
class CInc_Job_Btnpanel extends CCor_Ren {
  
  public function __construct() {
    $this -> mPnl = array();
  }
  
  public function & addPanel($aKey, $aCaption, $aCont = '', $aPer = NULL) {
    $lPnl = array();
    $lPnl['cap'] = $aCaption;
    $lPnl['cnt'] = $aCont;
    $lPnl['per'] = $aPer;
    $this -> mPnl[$aKey] = $lPnl;
    return $lPnl;
  }
  
  public function addCont($aKey, $aContent) {
    if (isset($this -> mPnl[$aKey])) {
      $this -> mPnl[$aKey]['cnt'].= $aContent;
    } else {
      $this -> dbg('Panel '.$aKey.' not set', mlError);
    }
  }
  
  public function addBtn($aKey, $aCaption, $aAction = '', $aImg = '', $aType = 'button', $aAttr = array()) {
    $lBr = (THEME === 'default' ? BR.BR : BR);
    $this -> addCont($aKey, btn($aCaption, $aAction, $aImg, $aType, $aAttr).$lBr);
  }
  
  public function addButton($aKey, $aBtn) {
    $this -> addCont($aKey, $aBtn.BR.BR);
  }
    
  protected function getPanelCont($aPnl) {
    $lPnl = new CHtm_Panel($aPnl['cap'], $aPnl['cnt'], $aPnl['per']);
    if(THEME === 'default'){
      $lRet = $lPnl -> getHead(array('class' => 'frm p8 c'));
    } else {
      $lPnl->setAtt('class', 'th1 w200');
      $lRet = $lPnl->getHead(array('class' => 'p4 w200'));
    }
    $lRet.= $aPnl['cnt'];
    $lRet.= '</div></div>';
    return $lRet;
  }
  
  protected function getCont() {
    if (empty($this -> mPnl)) {
      return '';
    }
    $lRet = $this -> getComment('start');
    $lRet.= '<div class="jobBtnPanel" style="width:99%">'.LF;
    foreach ($this -> mPnl as $lKey => $lPnl) {
      if (empty($lPnl['cnt'])) continue;
      $lRet.= $this -> getPanelCont($lPnl);
    }
    $lRet.= '</div>'.LF;
    $lRet.= $this -> getComment('end');
    
    return $lRet;
  }
  
  
}