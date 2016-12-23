<?php
class CJob_Pro_Sub_Assign extends CJob_Pro_Sub_List {

  public function __construct($aJobId,$aVariants) {
    $this -> mJobId = $aJobId;
    $this -> mVariants = $aVariants;
    $this -> mCanInsert = FALSE;
    $this -> mCanDelete = FALSE;
    parent::__construct($this -> mJobId);
    $this -> mTitle = lan('lib.mastervariant.sel');
    $this -> mStdLnk = 'index.php?act='.$this -> mMod.'.sassignmaster&amp;jobid='.$this -> mJobId.'&amp;sid='.$this -> mVariants.'&amp;id=';
    $this -> mOrdLnk = 'index.php?act='.$this -> mMod.'.ord&amp;jobid='.$this -> mJobId.'&amp;fie=';
    $this -> mCanInsert = FALSE;
    $this -> mIsNoArc = FALSE;
    $this -> mCanDelete = FALSE;
    $this -> mCanInsArt = FALSE;
    $this -> mCanInsRep = FALSE;
    $this -> mCanInsSec = FALSE;
    $this -> mCanInsMis = FALSE;
    $this -> mCanInsAdm = FALSE;
    $this -> mCanInsCom = FALSE;
    $this -> mCanInsTra = FALSE;
    $this -> mBtn = Array();
    $this -> mPnl = Array();
  }
  
  protected function getIterator() {
    $this -> mIte = new CCor_TblIte('al_job_sub_'.intval(MID), $this -> mWithoutLimit);
    $this -> mIte -> addCnd('pro_id='.$this -> mJobId);
    $this -> mIte -> addCnd('id !='.$this -> mVariants);
    $this -> mIte -> addCnd('is_master = "X"');
   
  }
  
  public function addDel(){
    $lRet = '';
    return $lRet;
  }
  
  protected function getTdSel() {
    $lRet = '';
    return $this -> tdc($lRet);
  }
  
  protected function getTdCpy() {
    $lRet = '';
    return $this -> tdc($lRet);
  }

  
}