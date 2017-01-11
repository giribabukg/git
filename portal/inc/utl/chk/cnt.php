<?php
class CInc_Utl_Chk_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct = 'std') {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = htm(lan('chk.menu'));
    
    $this->mSrc = $this -> mReq -> getVal('src');
    $this->mJobId =$this -> mReq -> getVal('jobid');
    $this->mLoopId =$this -> mReq -> getVal('loopid');
    $aUid = $this -> mReq ->getVal('uid');
    $lUsr = CCor_Usr::getInstance();
    $lUid = $lUsr -> getId();
    $this->mUid = (is_null($aUid)) ? $lUid : $aUid;
  }
  
  protected function actStd() {
    $this->printForm();
  }
  
  protected function actEdt() {
    $lChk = new CApp_Chk_Check($this -> mSrc, $this -> mJobId);
    $lUserItemsToCheck = $lChk -> doUserHasCheckList();
    if (!empty($lUserItemsToCheck)) {
      $this -> insertItemsToJobChk($lUserItemsToCheck);
      $this -> printForm(TRUE);
    }
  }
  

  /**
   * Check if the items for this user for this job are already in al_job_chk
   * if not then add them
   * @param array $aChkItems
   */
  protected function insertItemsToJobChk($aChkItemsArray) {    
    $lUser = CCor_Usr::getInstance();
    $lUid = $lUser -> getId();
    
    $lSql = 'SELECT * FROM al_job_chk WHERE src_id='.$this -> mJobId;
    $lSql.= ' AND src="'.$this -> mSrc.'"';
  #  $lSql.= ' AND loop_id='.$this -> mLoopId;
    $lSql.= ' AND user_id='.$lUid;
    $lResult = CCor_Qry::getStr($lSql);
    if (empty($lResult)) {
      foreach ($aChkItemsArray as $lChkItems) {
        $lSql = 'INSERT INTO `al_job_chk` (`src`, `src_id`, `loop_id`, `check_id`, `user_id`) VALUES ('.esc($this -> mSrc).', '.esc($this -> mJobId).', '.esc($this -> mLoopId).', '.esc($lChkItems['id']).', '.esc($lUid).');';
        CCor_Qry::exec($lSql);
      }
    }
  }

  
  protected function actSedt() {
    $lSrc = $this->mReq->getVal('mSrc');
    $lJobId = $this->mReq->getVal('mJobId');
    $lLoopId = $this->mReq->getVal('mLoopId');
    $lItems = $this->mReq->getVal('mCheckItems');
    $lUser = CCor_Usr::getInstance();
    $lUid = $lUser -> getId();
    
    $lFac = new CJob_Fac($lSrc,$lJobId);
    $lMod = $lFac -> getMod($lJobId);

    foreach ($_POST as $lKey => $lVal) {
      if (is_integer($lKey)) {
        $lSql = 'UPDATE `al_job_chk` SET `status`='.esc($lVal);
        $lSql.= ' WHERE  `src`='.esc($lSrc);
        $lSql.= ' AND `src_id`='.esc($lJobId);
        $lSql.= ' AND `check_id`='.esc($lKey);
        $lSql.= ' AND `user_id`='.$lUid;
       # $lSql.= ' AND `loop_id`='.esc($lLoopId);
        CCor_Qry::exec($lSql);
        $lCheckItemName = CCor_Qry::getStr('SELECT name_'.LAN.' FROM al_chk_items WHERE id='.$lKey);
        if ($lVal == 'ok') $lMod -> addHistory(htChkOk, $lCheckItemName, lan('check.ok'));
        if ($lVal == 'nok') $lMod -> addHistory(htChkReject, $lCheckItemName, lan('check.nok'));
      }
    }
    echo "<script>";
    echo 'window.close()';
    echo "</script>";
  }
  
  protected function printForm($aWithButtons=FALSE) {
    $lVie = new CUtl_Chk_Form('utl-chk.sedt', $this -> mTitle, NULL, $this->mSrc, $this->mJobId, $this->mLoopId, $this->mUid, $aWithButtons);
    $lVie ->load();
    
    $lPag = new CUtl_Page();
    $lPag -> setPat('pg.cont', $lVie -> getContent());
    $lPag -> setPat('pg.title', $this -> mTitle);
    $lPag -> setPat('pg.lib.wait', htm(lan('lib.wait')));
    echo $lPag -> getContent();
    
    #$this -> render($lVie);
  }
}