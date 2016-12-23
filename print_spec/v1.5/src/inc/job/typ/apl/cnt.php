<?php
class CInc_Job_Typ_Apl_Cnt extends CCor_Cnt {

  protected $mSrc   = 'typ';

  public function __construct(ICor_Req $aReq, $aSrc, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mSrc = $aSrc;
    $this -> mTitle = lan('job-apl.menu');

    $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');
    $this -> mCrpId = $lCrp[$this -> mSrc];
  }

  protected function actStd() {
    $lJid = $this -> getReq('jobid');
    $lPag = $this -> getReq('page');

    $lJob = new CJob_Typ_Dat($this -> mSrc);
    $lJob -> load($lJid);

    $lVie = new CJob_Typ_Header($this -> mSrc, $lJob);
    $lRet = $lVie -> getContent();

    $lVie = new CJob_Typ_Tabs($this -> mSrc, $lJid, 'apl');
    $lRet.= $lVie -> getContent();

    $lFrm = new CJob_Apl_List($this -> mSrc, $lJid);
    $lRet.= $lFrm -> getContent();

    #$lApl = new CApp_Apl_Loop('typ', $lJid, 'apl');
    #$lApl -> createLoop('2008-07-01');

    $lFrm = new CJob_Typ_Apl_Form('job-'.$this -> mSrc.'-apl.sedt', $lJid, $lJob, $lPag);
    $lRet.= $lFrm -> getContent();

    $this -> render($lRet);
  }

  protected function actNewmail() {
    $lJid = $this -> getReq('jobid');

    $lRet = '';

    $lJob = new CJob_Typ_Dat($this -> mSrc);
    $lJob -> load($lJid);

    $lVie = new CJob_Typ_Header($this -> mSrc, $lJob, $this -> mCrpId);
    $lRet.= $lVie -> getContent();

    $lVie = new CJob_Apl_Mailform($this -> mSrc, $lJid, $lJob);
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
    $lJid = $this -> getReq('jobid');
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
    $lFree = $this -> getFree($lVal['to1']);
    if (!empty($lFree)) {
      $lTo[$lFree['email']] = $lFree['name'];
    }
    $lFree = $this -> getFree($lVal['to2']);
    if (!empty($lFree)) {
      $lTo[$lFree['email']] = $lFree['name'];
    }
    $lFree = $this -> getFree($lVal['to3']);
    if (!empty($lFree)) {
      $lTo[$lFree['email']] = $lFree['name'];
    }

    $lMod = new CJob_His_Mod('typ', $lJid, htMail);
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
   # $lMod -> insert(); //braucht nicht dokumentiert zu werden.

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

    $this -> redirect('index.php?act=job-'.$this -> mSrc.'-apl&jobid='.$lJid);
  }

}