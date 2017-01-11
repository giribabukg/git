<?php
class CInc_Job_Tra_Fil_Cnt extends CCor_Cnt {

  protected $mSrc = 'tra';

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

    $lJob = new CJob_Tra_Dat();
    $lJob -> load($lJid);

    $lRet = '';

    $lVie = new CJob_Tra_Header($lJob);
    $lRet.= $lVie -> getContent();

    $lVie = new CJob_Tra_Tabs($lJid, 'fil');
    $lRet.= $lVie -> getContent();

    $lVie = new CJob_Tra_Fil_List($lJid, $lJob, $lSub);
    $lRet.= $lVie -> getContent();

    $this -> render($lRet);
  }

  protected function actGet() {
    $lSrc = strip_tags($this -> getReq('src'));
    $lJid = strip_tags($this -> getReq('jid'));
    $lSub = strip_tags($this -> getReq('sub'));
    $lDiv = strip_tags($this -> getReq('div'));
    $lAge = strip_tags($this -> getReq('age'));

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