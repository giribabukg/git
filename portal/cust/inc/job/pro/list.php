<?php
/*class CCust_Job_Pro_List extends CInc_Job_Pro_List {

  protected function onBeforeContent() {
    $lRet = parent::onBeforeContent();
    $this -> mIte = $this -> mIte -> getArray('jobid');
    $lProIds = array_keys($this -> mIte);

    //22651 Project Critical Path Functionality
    if (empty($lProIds)) {
      $lProIds = array();
    }
    $lProCrp = CJob_Pro_Crp::getInstance($lProIds);

    $this -> mProStatus = $lProCrp -> getProStatus();
    $this -> mProStatusAll = $lProCrp -> getProStatusAll(); //with "afore & after" jobs
    $this -> mJobsAmount = $lProCrp -> getProjectsAmount();
    $this -> mSubAmount = $lProCrp -> getSubAmount();

    #$this -> mStatusClosedMax = $lProCrp -> getStatusClosedMax();
    $this -> mAutoProStatus    = $lProCrp -> getAutoProStatus();
    $this -> mStatusClosed = $lProCrp -> getStatusClosed();
    $this -> mViewJoblist      = $lProCrp -> getViewJoblist();
    $this -> mNoStatusFromStep = $lProCrp -> getNoStatusFromStep();

    #$this -> m1LastStatusNoStep = $lProCrp -> get1StatusNoFromStep();

    $this -> mCrpStatus = CCor_Res::extract('status', 'display', 'crp', $this -> mCrpId);

    return $lRet;
  }

} */