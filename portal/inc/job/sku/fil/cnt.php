<?php
class CInc_Job_Sku_Fil_Cnt extends CCor_Cnt {

  protected $mSrc = 'sku';

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('job-fil.menu');
    $this -> mMmKey = 'job-sku';
  }

  protected function getStdUrl() {
    $lJid = $this -> mReq -> jobid;
    return 'index.php?act='.$this -> mMod.'&jobid='.$lJid;
  }

  protected function actStd() {
    $lSKUID = $this -> getInt('jobid');
    $lSub = $this -> getReq('sub');

    $lSKU = new CJob_Sku_Dat();
    $lSKU -> load($lSKUID);

    $lRet = '';

    $lVie = new CJob_Sku_Header($lSKU);
    $lRet.= $lVie -> getContent();

    $lVie = new CJob_Sku_Tabs($lSKUID, 'fil');
    $lRet.= $lVie -> getContent();

    $lVie = new CJob_Sku_Fil_List($lSKUID, $lSKU, $lSub);
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
    $lVie = new $lCls($lSrc, $lJid, $lSub, $lDiv, 'sub', $lAge, False);
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