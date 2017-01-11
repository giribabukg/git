<?php
class CInc_Job_Xchange_Cnt extends CCor_Cnt {
  
  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mSrc = $this -> getReq('src');
    $this -> mJid = $this -> getReq('jobid');
    $this -> mTitle = lan('xchange.menu');
    $this -> mUsr = CCor_Usr::getInstance();
    if (!$this -> mUsr -> canRead('job-'.$this->mSrc)) {
      $this -> denyAccess();
    }
  }
  
  protected function actStd() {
    $lFac = new CJob_Fac($this->mSrc, $this->mJid);
    $lJob = $lFac->getDat();
    $lTabs = $lFac->getTabs('xchange');
    $lRet = '';
    $lVie = $lFac -> getHeader();
    $lRet.= $lVie -> getContent();
    $lRet.= $lTabs->getContent();
    
    $lSelect = new CJob_Xchange_Select($this->mSrc, $this->mJid);
    $lSelect->setIds('all');
    $lRet.= $lSelect->getContent();
    $this->render($lRet);
  }
  

  protected function actDiff() {
    $lRowIds = $this->getReq('id');
    $lFac = new CJob_Fac($this->mSrc, $this->mJid);
    $lJob = $lFac->getDat();
    $lTabs = $lFac->getTabs('xchange');
    $lRet = '';
    $lVie = $lFac -> getHeader();
    $lRet.= $lVie -> getContent();
    $lRet.= $lTabs->getContent();
  
    $lSelect = new CJob_Xchange_Select($this->mSrc, $this->mJid);
    $lSelect->setIds($lRowIds);
    $lDiff = new CJob_Xchange_Diff($this->mSrc, $this->mJid, $lJob);
    $lDiff->setIds($lRowIds);
    
    $lRet.= CHtm_Wrap::wrap($lSelect, $lDiff);
    $this->render($lRet);
  }

}