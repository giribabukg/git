<?php
class CInc_Arc_Base_Fil_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('job-fil.menu');
    $this->init();
  }

  /**
   * needs to be overwritten in job specific subclasses
   * @return string
   */
  protected function init() {
    $this->mSrc = '';
  }

  protected function getStdUrl() {
    $lJid = $this->getReq('jobid');
    return 'index.php?act='.$this -> mMod.'&jobid='.$lJid;
  }

  protected function actStd() {
    $lJid = $this -> getReq('jobid');
    $lPag = $this -> getReq('page');
    $lSub = $this -> getReq('sub');

    $lJob = new CArc_Dat($this -> mSrc);
    $lJob -> load($lJid);

    $lRet = '';

    $lClass = 'CJob_'.$this->mSrc.'_Header';
    $lVie = new $lClass($lJob);
    $lRet.= $lVie -> getContent();

    $lClass = 'CArc_'.$this->mSrc.'_Tabs';

    $lVie = new $lClass($lJid, 'fil');
    $lRet.= $lVie -> getContent();

    $lClass = 'CArc_'.$this->mSrc.'_Fil_List';
    $lVie = new $lClass($lJid, $lJob, $lSub);
    $lRet.= $lVie -> getContent();

    $this -> render($lRet);
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

  protected function actGet() {
    $lSrc = $this -> getReq('src');
    $lJid = $this -> getReq('jid');
    $lSub = $this -> getReq('sub');
    $lDiv = $this -> getReq('div');
    
    $lCls = 'CJob_Fil_Src_'.ucfirst($lSub);
    $lVie = new $lCls($lSrc, $lJid, $lSub, $lDiv, 'sub', 'arc', FALSE, FALSE);
    $lVie -> render();
    /*$lVie = new CArc_Fil_Files($lSrc, $lJid, $lSub, $lDiv);
    $lVie -> render();*/
    exit;
  }

  protected function actDel() {
    $lJid = $this -> getReq('jid');
    $lSub = $this -> getReq('sub');
    $lDiv = $this -> getReq('div');
    $lNam = $this -> getReq('name');

    if ($lSub != 'pdf') {
      $lFin = new CApp_Finder($this -> mSrc, $lJid);
      $lFil = $lFin -> getName($lNam, $lSub);
      if (is_readable($lFil)) {
        unlink($lFil);
        CCor_Usr::deleteJobFile($this -> mSrc, $lJid, $lSub, $lNam);
      }
      $lVie = new CArc_Fil_Files($this -> mSrc, $lJid, $lSub, $lDiv);
      $lVie -> render();
      exit;
    } else {
      $lQry = new CApi_Alink_Query('deleteFile');
      $lQry -> addParam('sid', MAND);
      $lQry -> addParam('jobid', $lJid);
      $lQry -> addParam('filename', $lNam);
      $lRes = $lQry -> query();
      $lErr = $lRes-> getVal('errno');
      if (0 == $lErr) {
        CCor_Usr::deleteJobFile($this -> mSrc, $lJid, $lSub, $lNam);
      }
      $lVie = new CArc_Fil_Files($this -> mSrc, $lJid, $lSub, $lDiv);
      $lVie -> render();
      exit;
    }
  }

  protected function actWecCreate() {
    $lJid = $this -> getReq('jid');

    $lWec = new CApp_Wec($this -> mSrc, $lJid);
    $lWecPrjId = $lWec -> createWebcenterProject();
  }

  protected function actWecUpl() {
    $lJid = $this -> getReq('jid');
    $lSub = $this -> getReq('sub');
    $lDiv = $this -> getReq('div');
    $lNam = $this -> getReq('name');

    if ($lSub != 'pdf') {
      $lVie = new CArc_Fil_Files($this -> mSrc, $lJid, $lSub, $lDiv);
      $lVie -> render();
      exit;
    } else {
      $lWec = new CApp_Wec($this -> mSrc, $lJid);
      $lWecPrjId = $lWec -> createWebcenterProject();

      $lQry = new CApi_Alink_Query('uploadToWebCenter');
      $lQry -> addParam('prjprefix', CCor_Cfg::get('wec.prjprefix'));
      $lQry -> addParam('sid', MAND);
      $lQry -> addParam('jobid', $lJid);
      $lQry -> addParam('filename', $lNam);
      $lRes = $lQry -> query();
      $lErr = $lRes-> getVal('errno');
      if (0 == $lErr) {
        CCor_Usr::uploadedJobFile($this -> mSrc, $lJid, $lSub, $lNam);
      }
      $lVie = new CArc_Fil_Files($this -> mSrc, $lJid, $lSub, $lDiv);
      $lVie -> render();
      exit;
    }
  }
}