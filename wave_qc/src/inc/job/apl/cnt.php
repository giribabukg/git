<?php
class CInc_Job_Apl_Cnt extends CCor_Cnt {

  protected $mSrcCnt = '';
  protected $mModCnt = 'job-';
  protected $mJobId = '';
  protected $mJobMod = 'job'; // Job or Arc
  protected $mUsr;

  protected $mCanOpenViewer = true;
  protected $mCanChangeAplState = true;

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    $this -> mModCnt = $aMod;
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('job-apl.menu');
    $this -> mSrcCnt = $this -> getReq('src', '');
    $this -> mJobId = $this -> getReq('jobid', '');
    $this -> mJobMod = $this -> getReq('mod','job'); // Default is "job".
    $this -> mMmKey = $this -> mJobMod; //highlight: Active jobs OR Archive

    $this -> mUsr = CCor_Usr::getInstance();
    if (empty($this -> mSrcCnt)) {
      $lMsg = get_class($this).': No Src! Mod: '.$aMod.', Act: '.$aAct.', JobId: '.(!empty($this -> mJobId) ? $this -> mJobId :'-');
      $this -> denyAccess($lMsg);
    } elseif (empty($this -> mJobId)) {
      $lMsg = get_class($this).': No JobId! Mod: '.$aMod.', Act: '.$aAct.', Src: '.(!empty($this -> mSrcCnt) ? $this -> mSrcCnt :'-');
      $this -> denyAccess($lMsg);
    } elseif (empty($this -> mJobMod)) {
      $lMsg = get_class($this).': No JobMod';
      $this -> denyAccess($lMsg);
    }

    $lBlockJobFromTheGroup = CCor_Cfg::get('block-apl-togroup');
    if ($lBlockJobFromTheGroup) {
      $lUid = CCor_Usr::getAuthId();
      $lApl = new CApp_Apl_Loop($this->mSrcCnt, $this->mJobId);
      $lBlockApl = CApp_Apl_Loop::APL_STATE_BLOCKFORTHEGROUP;
      $lApl -> updateGroupState($lUid, $lBlockApl, '');
    }
  }

  protected function getStdUrl() {
    return 'index.php?act='.$this -> mModCnt.'&src='.$this -> mSrcCnt.'&jobid='.$this -> mJobId;
  }

  protected function checkAplViewOptions($aJob) {
    // cust or mand might change $mCanOpenViewer or $mCanChangeAplState
    $lLis = new CJob_Apl_Page_List($this -> mSrcCnt, $this -> mJobId);
    $lShowButtons = $lLis -> ShowAplButtons();
    if (!$lShowButtons) {
      $this->mCanOpenViewer = true;
    }
    /* Example:
    $lUsr = CCor_Usr::getInstance();
    $lApl = new CApp_Apl_Loop($this->mSrcCnt, $this->mJobId);
    $lIsActiveAplUser = $lApl->isUserActiveNow(CCor_Usr::getAuthId());
    if (!$lIsActiveAplUser) {
      if (!$lUsr->isMemberOf(506)) { // admins
        $this->mCanOpenViewer = false;
      }
      $this->mCanChangeAplState = false;
    }
    */
  }

  protected function actStd() {
    $lPag = $this -> getReq('page');

    // Dat Class :CJob_[Src]_Dat() OR CArc_Dat([Src])
    // Header Class : CJob_[Src]_Header($lJob)
    // Tab Class : Cjob_[Src]_Tabs() OR CArc_[Src]_Tabs()
    $lClassName = 'C'.$this->mJobMod.'_'.$this->mSrcCnt  ;

    // Dat Class
    if ($this->mJobMod == 'job'){
      $lClass = $lClassName.'_Dat';
      $lJob = new $lClass();
    } else {
      $lClass = 'C'.$this->mJobMod.'_Dat';
      $lJob = new $lClass($this ->mSrcCnt);
    }

    $lJob -> load($this -> mJobId);

    // Header Class
    $lClass = 'CJob_'.$this ->mSrcCnt.'_Header';
    $lVie = new $lClass($lJob);

    $this->checkAplViewOptions($lJob);

    //My Approvals: an-/ab-schaltbar
    $lShowMyApprovals = CCor_Cfg::get('show.myapprovals', TRUE);
    if ($lShowMyApprovals) {
      $lLnk = 'index.php?act=job-apl&amp;jobid=';
      $lMen = new CJob_Apl_Page_Menu($this -> mSrcCnt, $this -> mJobId, $lLnk);
      $lVie -> addPanel($lMen -> getContent());
    }
    $lRet = $lVie -> getContent();

    // Tabs Class
    $lClass = $lClassName.'_Tabs';
    $lVie = new $lClass($this -> mJobId, 'apl');
    $lRet.= $lVie -> getContent();

    $lLis = new CJob_Apl_List($this -> mSrcCnt, $this -> mJobId, $this -> mJobMod, TRUE, 'apl', TRUE, $lJob);

    // Anzeige der APL-Buttons
    // If Archive Job, No APL Buttons
    $lBtn = '';
    if ($this -> mJobMod != 'arc') {
      if ($this->mCanChangeAplState) {
        $lBtn = new CJob_Apl_Form($this -> mSrcCnt, 'job-'.$this -> mSrcCnt.'-apl.sedt', $this -> mJobId, $lJob, $lPag);
      }
    }
    if ($this -> mUsr -> canRead('job-wec')) {
      if ($this -> mJobMod != 'arc') {
        $lImg = new CJob_Apl_Page_Images($lJob, 'job', $this->mCanOpenViewer); // linke Seite: Dateien
      } else {
        $lImg = new CJob_Apl_Page_Images($lJob, 'arc'); // linke Seite: Dateien
      }
      if (!$this->mCanOpenViewer) {
        $lImg->setCanOpenViewer(false);
      }
    } else {
      $lImg = '';
    }
    $lRet.= CJob_Apl_Wrap::wrap($lImg, $lLis, $lBtn);
    $lRet.= $this->getWecLogout();
    $this -> render($lRet);
  }

  protected function actNewmail() {
    $lRet = '';

    $lClass = 'CJob_'.$this -> mSrcCnt.'_Dat';
    $lJob = new $lClass();
    $lJob -> load($this -> mJobId);

    $lClass = 'CJob_'.$this -> mSrcCnt.'_Header';
    $lVie = new $lClass($lJob);
    $lRet = $lVie -> getContent();

    $lVie = new CJob_Apl_Mailform($this -> mSrcCnt, $this -> mJobId, $lJob);
    $lRet.= $lVie -> getContent();

    $this -> render($lRet);
  }

  private function getFree($aVal) {
    $lVal = trim($aVal);
    if (empty($lVal)) return 0;
    if (preg_match('/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/', $lVal)) {
      return array($lVal => $lVal);
    }
    if (preg_match('/(.+)[ ]?<(.+)>/', $lVal, $lArr)) {
      $this -> dbg('Multi name '.$lArr[1].'/email '.$lArr[2].' found');
      return array('email' => trim($lArr[2]), 'name' => trim($lArr[1]));
    }
    return 0;
  }

  protected function actSnewmail() {
    $lUsrRes = CCor_Res::get('usr');
    $lVal = $this -> getReq('val');

    $lTo = array();
    $lArr = (isset($lVal['uid'])) ? $lVal['uid'] : array();
    if (!empty($lArr)) {
      foreach($lArr as $lUid) {
        if (isset($lUsrRes[$lUid])) {
          $lUsr = $lUsrRes[$lUid];
          $lTo[$lUsr['email']] = $lUsr['first_lastname'];
        }
      }
    }

    foreach ($lVal as $lKey => $lValue) {
      if (strpos($lKey, 'inpMailAddr') >= 0) {
        $lFree = $this -> getFree($lVal[$lKey]);

        if (!empty($lFree)) {
          $lTo[$lFree['email']] = isset($lFree['name']) ? $lFree['name'] : '';
        }
      }
    }

    $lMod = new CJob_His_Mod($this -> mSrcCnt, $this -> mJobId, htMail);
    $lMod -> getPost($this -> mReq, FALSE);
    $lMsg = $lMod -> getVal('msg');
    $lOrg = $lMsg;
    $lSub = $lMod -> getVal('subject');
    if (!empty($lTo)) {
      $lMsg.= LF.LF.LF.'Sent to:'.LF;
      foreach ($lTo as $lKey => $lVal) {
        if ($lKey == $lVal) {
          $lMsg.= '- '.$lKey.LF;
        } else {
          $lMsg.= '- '.$lVal.' <'.$lKey.'>'.LF;
        }
      }
    }
    $lMsg = trim($lMsg);
    $lMod -> setVal('msg', $lMsg);
    $lMod -> insert();

    $lUsr = CCor_Usr::getInstance();
    $lEmail = $lUsr -> getVal('email');
    $lName  = $lUsr -> getVal('first_lastname');
    if (!empty($lTo)) {
      foreach ($lTo as $lKey => $lVal) {
        $lMai = new CApi_Mail_Item($lEmail, $lName, '', '', $lSub, $lOrg);
        if ($lKey == $lVal) {
          $lMai -> setTo($lKey);
        } else {
          $lMai -> setTo($lKey, $lVal);
        }
        $lMai -> insert();
      }
    }

    $this -> redirect();
  }

  /**
   * Del User ot Groups from existing APL
   * @return unknown_type
   */
  public function actDel() {
    $lStatesId = $this -> mReq -> getInt('statesid');
    $lUsrId = $this -> mReq -> getInt('usrid');
    $lLoopId = $this -> mReq -> getInt('loopid');
    $lGruId = $this -> mReq -> getInt('gruid');

    $lApl = new CApp_Apl_Loop($this -> mSrcCnt, $this -> mJobId, 'apl');
    $lApl -> delAplUser($lStatesId, $lUsrId, $lGruId, $lLoopId);
    $this -> redirect();
  }
}