<?php
class CInc_Arc_His_Cnt extends CJob_His_Cnt {
  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
  }

  protected function getStdUrl() {
    echo "getStdUrl";
    $lJid = $this -> mReq -> jobid;
    return 'index.php?act='.$this -> mMod.'&jobid='.$lJid;
  }

  protected function actStd() {
    $lJid = $this -> getReq('jobid');

    $lJob = new CArc_Dat($this->mSrc);
    $lJob -> load($lJid);

    $lRet = '';
    $lJobHeader = "CJob_".$this->mSrc."_Header";
    $lVie = new $lJobHeader($lJob);
    $lRet.= $lVie -> getContent();

    $lJobTabs = "CJob_".$this->mSrc."_Tabs";
    $lVie = new $lJobTabs($lJid, 'his');
    $lRet.= $lVie -> getContent();

    $lVie = new CArc_His_List($this->mSrc, $lJid);
    $lRet.= $lVie -> getContent();

    $this -> render($lRet);
  }

  protected function actNew() {
    $lJid = $this -> getReq('jobid');

    $lRet = '';

    $lJob = new CArc_Dat($this->mSrc);
    $lJob -> load($lJid);

    $lJobHeader = "CJob_".$this->mSrc."_Header";
    $lVie = new $lJobHeader($lJob);
    $lRet.= $lVie -> getContent();

    $lVie = new CJob_His_Form($this->mSrc, $lJid, 'snew', 'arc');
    $lRet.= $lVie -> getContent();

    $this -> render($lRet);
  }
  
  protected function actSnew() {
    $lJid = $this -> getReq('jobid');
    $lMod = new CJob_His_Mod($this->mSrc, $lJid);
    $lMod -> getPost($this -> mReq, FALSE);
    $lMod -> insert();
    $this -> redirect();
  }

  protected function actEdt() {
    $lId  = $this -> getReq('id');
    $lJid = $this -> getReq('jobid');

    $lRet = '';

    $lJob = new CArc_Dat($this->mSrc);
    $lJob -> load($lJid);

    $lJobHeader = "CJob_".$this->mSrc."_Header";
    $lVie = new $lJobHeader($lJob);
    $lRet.= $lVie -> getContent();

    $lVie = new CJob_His_Form($this->mSrc, $lJid, 'sedt', 'arc');
    $lVie -> load($lId);
    $lRet.= $lVie -> getContent();

    $this -> render($lRet);
  }

  protected function actSedt() {
    $lJid = $this -> getReq('jobid');
    $lMod = new CJob_His_Mod($this->mSrc, $lJid);
    $lMod -> getPost($this -> mReq);
    $lMod -> update();
    $this -> redirect();
  }

  protected function actPrnitm() {
    echo "actPrntitm";
    $lId = $this -> getInt('id');
    $lJid = $this -> getReq('jobid');

    $lJob = new CArc_Dat($this->mSrc);
    $lJob -> load($lJid);

    $lRet = '';
    $lJobHeader = "CJob_".$this->mSrc."_Header";
    $lHdr = new $lJobHeader($lJob);
    $lHdr -> hideMenu();
    $lRet.= $lHdr -> getContent().BR;

    $lHis = new CJob_His_Single($lId);
    $lRet.= $lHis -> getContent();

    $lPag = new CUtl_Page();
    $lPag -> setPat('pg.cont', $lRet);
    $lPag -> setPat('pg.lib.wait', htm(lan('lib.wait')));
    $lPag -> setPat('pg.title', htm(lan('job-his.menu')));
    $lPag -> setPat('pg.js', '<script type="text/javascript">window.print()</script>');

    echo $lPag -> getContent();
    exit;
  }

  protected function actNewmail() {
    $lJobId = $this -> getReq('jobid');
    $lSrc = $this -> getReq('src');
    $lFrm = $this -> getReq('frm');
    $lMailRequestId = $this -> getReq('emlid');

    $lRet = '';

    $lJob = new CArc_Dat($lSrc, $lJobId);
    $lJob -> load($lJobId);

    $lJobHeader = "CJob_".$this->mSrc."_Header";
    $lVie = new $lJobHeader($lJob);
    $lRet.= $lVie -> getContent();

    $lVie = new CJob_His_Mailform($lSrc, $lJobId, $lJob, $lFrm, $lMailRequestId, 'arc');
    $lRet.= $lVie -> getContent();

    $this -> render($lRet);
  }

  protected function actSnewmail() {
    $lJobId = $this -> getReq('jobid');
    $lSrc = $this -> getReq('src');
    $lFrm = $this -> getReq('frm');
    $lUsrRes = CCor_Res::get('usr');
    $lVal = $this -> getReq('val');
    $lResponseVal = $lVal['responce'];
    $lResponse = ($lResponseVal == 'on') ? 1:0;
    $lMailRequestId = $this -> getReq('emlid');

    $lTo = array();
    $lArr = (isset($lVal['uid'])) ? $lVal['uid'] : array();
    if (!empty($lArr)) {
      foreach($lArr as $lUid) {
        if (isset($lUsrRes[$lUid])) {
          $lUsr = $lUsrRes[$lUid];
          $lTo[$lUsr['email']] = $lUsr['id'];
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

    $lMod = new CJob_His_Mod($lSrc, $lJobId, htMail);
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
    $lSenderId = $lUsr -> getVal('id');

    if (!empty($lTo)) {
      foreach ($lTo as $lKey => $lVal) {
        $lToName = $lUsrRes[$lVal]['first_lastname'];
        $lMai = new CApi_Mail_Item($lEmail, $lName, '', '', $lSub, $lOrg);
        $lMai -> setSenderID($lSenderId);
        $lMai -> setJobId($lJobId);
        $lMai -> setJobSrc($lSrc);
        $lMai -> setMailType(mailJobNotification);
        if ($lKey == $lVal) {
          $lMai -> setTo($lKey);
        } else {
          $lMai -> setTo($lKey, $lToName);
        }
        if (is_numeric($lVal)) {
          $lMai -> setReciverId($lUsrRes[$lVal]['id']);
          $lMai -> setMailNeedResponse($lResponse);
        }
        $lMai -> insert();
      }
      if (isset($lMailRequestId) AND $lMailRequestId != 0) $this -> resetResponse($lMailRequestId);
    }
    $this -> redirect('index.php?act=arc-'.$lSrc.'-his&jobid='.$lJobId);
  }

  protected function actFilter() {
    //Get Values
    $lJid = $this -> getReq('jobid');
    $lJobHisType = $this -> getReq('filterBy');
    //Show History
    $lJob =  new CArc_Dat($this->mSrc);
    $lJob -> load($lJid);

    $lJobHeader = "CJob_".$this->mSrc."_Header";
    $lVie = new $lJobHeader($lJob);
    $lRet.= $lVie -> getContent();

    $lJobTabs = "CJob_".$this->mSrc."_Tabs";
    $lVie = new $lJobTabs($lJid, 'his');
    $lRet.= $lVie -> getContent();

    $lVie =  new CJob_His_List($this->mSrc, $lJid, 'arc', $lJobHisType);
    $lRet .= $lVie -> getContent();
    $this -> render($lRet);

  }
}