<?php
class CInc_Job_Ser_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);

    $this -> mTitle = lan('job-ser.menu');
    $this -> mUsr = CCor_Usr::getInstance();
    $this -> setProtection('*', 'ser', rdRead);
    $this -> mMmKey = 'job-ser';
    $this -> mAva = fsArt;
  }

  protected function getStdUrl() {
    return 'index.php?act=job-ser.show';
  }

  protected function actStd() {
    $lVie = new CJob_Ser_Wrap();
    $this -> render($lVie);
  }

  protected function actSer() {
    $this -> mReq -> expect('val');
    $lReq = $this -> getReq('val');
    $lArr = array();
    foreach ($lReq as $lKey => $lVal) {
      if ('' === $lVal) continue;
      $lArr[$lKey] = $lVal;
    }
    $lUsr = CCor_Usr::getInstance();
    $lUsr -> setPref($this -> mMod.'.ser', $lArr);
    $lUsr -> setPref($this -> mMod.'.page', 0);
    $this -> redirect();
  }

  protected function actShow() {
    $lVie = new CJob_Ser_Wrap(TRUE);
    $this -> render($lVie);
  }

  protected function actFpr() {
    $lVie = new CHtm_Fpr($this -> mMod.'.sfpr');
    $lDef = CCor_Res::get('fie');

    $lArr = array();
    foreach ($lDef as $lFie) {
      $lFla = intval($lFie['flags']);
      $lAva = intval($lFie['avail']);
      if (bitSet($lAva, $this -> mAva)) {
        if (bitSet($lFla, ffList)) {
          // If Jobfield has Read Flag active, ask for User READ-RIGHT (combination of 'fie_' and Alias).
          // If User has no READ-RIGHT, Jobfield not shown in the list.
          $lFieRight = 'fie_'.$lFie['alias'];
          if (bitset($lFla, ffRead) && !$this -> mUsr -> canRead($lFieRight)) {
            continue;
          }
          $lArr[$lFie['id']] = $lFie['name_'.LAN];
        }
      }
    }
    $lVie -> setSrc($lArr);
    $lUsr = CCor_Usr::getInstance();
    $lVie -> setSel($lUsr -> getPref($this -> mPrf.'.cols'));
    $this -> render($lVie);
  }

  protected function actSfpr() {
    $lDst = $this -> mReq -> getVal('dst');
    $lUsr = CCor_Usr::getInstance();
    $lUsr -> setPref($this -> mPrf.'.cols', implode(',', $lDst));
    $this -> redirect('index.php?act=job-ser');
  }

  protected function actSpr() {
    $lVie = new CHtm_Fpr($this -> mMod.'.sspr');
    $lVie -> setTitle(lan('lib.opt.spr'));
    $lDef = CCor_Res::get('fie');

    $lArr = array();
    foreach ($lDef as $lFie) {
      $lFla = intval($lFie['flags']);
      $lAva = intval($lFie['avail']);
      if (bitSet($lAva, $this -> mAva)) {
        if (bitSet($lFla, ffSearch)) {
          // If Jobfield has Read Flag active, ask for User READ-RIGHT (combination of 'fie_' and Alias).
          // If User has no READ-RIGHT, Jobfield not shown in the list.
          $lFieRight = 'fie_'.$lFie['alias'];
          if (bitset($lFla, ffRead) && !$this -> mUsr -> canRead($lFieRight)) {
            continue;
          }
          $lArr[$lFie['id']] = $lFie['name_'.LAN];
        }
      }
    }
    $lVie -> setSrc($lArr);
    $lUsr = CCor_Usr::getInstance();
    $lVie -> setSel($lUsr -> getPref($this -> mPrf.'.sfie'));
    $this -> render($lVie);
  }

  protected function actSspr() {
    $lDst = $this -> mReq -> getVal('dst');
    $lUsr = CCor_Usr::getInstance();
    $lUsr -> setPref($this -> mPrf.'.sfie', implode(',', $lDst));
    $this -> redirect('index.php?act=job-ser');
  }
}