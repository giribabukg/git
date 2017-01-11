<?php
class CInc_Job_Art_Apl_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('job-apl.menu');
  }

  protected function actStd() {
    $lJid = $this -> getReq('jobid');
    $lPag = $this -> getReq('page');

    $lJob = new CJob_Art_Dat();
    $lJob -> load($lJid);

    $lVie = new CJob_Art_Header($lJob);
    $lRet = $lVie -> getContent();

    $lVie = new CJob_Art_Tabs($lJid, 'apl');
    $lRet.= $lVie -> getContent();

    $lFrm = new CJob_Apl_List('art', $lJid);
    $lRet.= $lFrm -> getContent();

    $lFrm = new CJob_Art_Apl_Form('job-art-apl.sedt', $lJid, $lJob, $lPag);
    $lRet.= $lFrm -> getContent();

    $this -> render($lRet);
  }

  protected function actNewmail() {
    $lJid = $this -> getReq('jobid');

    $lRet = '';

    $lJob = new CJob_Art_Dat();
    $lJob -> load($lJid);

    $lVie = new CJob_Art_Header($lJob);
    $lRet.= $lVie -> getContent();

    $lVie = new CJob_Apl_Mailform('art', $lJid, $lJob);
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

    $lMod = new CJob_His_Mod('art', $lJid, htMail);
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

    $this -> redirect('index.php?act=job-art-apl&jobid='.$lJid);
  }

}