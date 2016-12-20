<?php
class CInc_Job_Sec_Fil_Cnt extends CCor_Cnt {

  protected $mSrc = 'sec';

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('job-fil.menu');
  }

  protected function getStdUrl() {
    $lJid = $this -> mReq -> jobid;
    return 'index.php?act='.$this -> mMod.'&jobid='.$lJid;
  }

  protected function actStd() {
    $lJid = $this -> getReq('jobid');
    $lPag = $this -> getReq('page');
    $lSub = $this -> getReq('sub');

    $lJob = new CJob_Sec_Dat();
    $lJob -> load($lJid);

    $lRet = '';

    $lVie = new CJob_Sec_Header($lJob);
    $lRet.= $lVie -> getContent();

    $lVie = new CJob_Sec_Tabs($lJid, 'fil');
    $lRet.= $lVie -> getContent();

    $lVie = new CJob_Sec_Fil_List($lJid, $lJob, $lSub);
    $lRet.= $lVie -> getContent();

    $this -> render($lRet);
  }

  protected function actGet() {
    $lSrc = $this -> getReq('src');
    $lJid = $this -> getReq('jid');
    $lSub = $this -> getReq('sub');
    $lDiv = $this -> getReq('div');
    $lAge = $this -> getReq('age');

    $lCls = 'CJob_Fil_Src_'.ucfirst($lSub);
    $lVie = new $lCls($lSrc, $lJid, $lSub, $lDiv, 'sub', $lAge);
    $lVie -> render();
    exit;
  }

  protected function actOrd() {
    $this -> mReq -> expect('fie');
    $this -> mReq -> expect('sub');
    $lFie = $this -> mReq -> getVal('fie');
    $lSub = $this -> mReq -> getVal('sub');
    $lUsr = CCor_Usr::getInstance();
    $lUsr -> setPref($this -> mPrf.'.'.$lSub.'.ord', $lFie);
    $this -> redirect(NULL, array('sub' => $lSub));
  }

  protected function actWecCreate() {
    $lJid = $this -> getReq('jid');

    $lWec = new CApp_Wec($this -> mSrc, $lJid);
    $lWecPrjId = $lWec -> createWebcenterProject();
  }
}