<?php
class CInc_Job_Typ_Fil_Cnt extends CCor_Cnt {

  protected $mSrc = 'typ';

  public function __construct(ICor_Req $aReq, $aSrc, $aMod, $aAct) { //neu: $aSrc Aufrufe!!
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mSrc = $aSrc;
    $this -> mTitle = lan('job-fil.menu');
  }

  protected function getStdUrl() {
    $lJobId = $this -> mReq -> jobid;
    return 'index.php?act='.$this -> mMod.'&jobid='.$lJobId;
  }

  protected function actStd() {
    $lJobId = $this -> getReq('jobid');
    $lPag = $this -> getReq('page');
    $lSub = $this -> getReq('sub');

    $lJob = new CJob_Typ_Dat($this -> mSrc);
    $lJob -> load($lJobId);

    $lRet = '';

    $lVie = new CJob_Typ_Header($this -> mSrc, $lJob);
    $lRet.= $lVie -> getContent();

    $lVie = new CJob_Typ_Tabs($this -> mSrc, $lJobId, 'fil');
    $lRet.= $lVie -> getContent();

    $lVie = new CJob_Typ_Fil_List($this -> mSrc, $lJobId, $lJob, $lSub);
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
}