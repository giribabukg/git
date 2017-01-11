<?php
class CInc_Job_Assign_Cnt extends CJob_Cnt {

  protected $mSrc = 'assign';

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    $lModArr = explode('-', $aMod);
    if (!empty($lModArr[1])) {
      $this -> mSrcCnt = $lModArr[1];
    }

    $this -> mReq = & $aReq;
    $this -> mMod = $aMod;
    $this -> mPrf = $aMod;
    $this -> mAct = $aAct;
    $this -> mPro = array();
    $this -> mTitle = 'Title';
    $this -> mMmKey = substr($aMod, 0, 3);

    $this -> mTitle = lan($aMod.'.menu');
    $this -> mUsr = CCor_Usr::getInstance();
    $this -> mAva = fsPro;
  }

  protected function actFpr() {
    $lSrc = $this -> mReq -> getVal('src');
    $lJobId = $this -> mReq -> getVal('jobid');
    $lPrjId = $this -> mReq -> getVal('prjid');

    $lVie = new CHtm_Fpr($this -> mMod.'.sfpr', 'job-'.$lSrc.'.assignprj&jobid='.$lJobId.'&prjid='.$lPrjId);
    $lDef = CCor_Res::get('fie');

    $lArr = array();
    foreach ($lDef as $lFie) {
      $lFla = intval($lFie['flags']);
      $lAva = intval($lFie['avail']);
      if (bitSet($lAva, $this -> mAva)) {
        if (bitSet($lFla, ffList)) {
          $lFieRight = 'fie_'.$lFie['alias'];
          if (bitset($lFla, ffRead) && !$this -> mUsr -> canRead($lFieRight)) {
            continue;
          }
          $lArr[$lFie['id']] = $lFie['name_'.LAN];
        }
      }
    }

    $lVie -> setSrc($lArr);
    $lVie -> setParam('jobsrc', $lSrc); // as >src< is used excessively and neither in the same domain nor for the same property we have to use >jobsrc< here 
    $lVie -> setParam('jobid', $lJobId);
    $lVie -> setParam('prjid', $lPrjId);

    $lUsr = CCor_Usr::getInstance();
    $lVie -> setSel($lUsr -> getPref($this -> mPrf.'.cols'));

    $lPag = CHtm_Page::getInstance();
    $lPag -> addJs($lVie -> getTooltips());
    $this -> render($lVie);
  }

  protected function actSfpr() {
    $lDst = $this -> mReq -> getVal('dst');
    $lSrc = $this -> mReq -> getVal('jobsrc'); // as >src< is used excessively and neither in the same domain nor for the same property we have to use >jobsrc< here
    $lJobId = $this -> mReq -> getVal('jobid');
    $lPrjId = $this -> mReq -> getVal('prjid');

    $lUsr = CCor_Usr::getInstance();
    if (!empty($lDst)) {
      $lDstStr = implode(',', $lDst);
    } else {
      $lDstStr = '';
    }
    $lUsr -> setPref($this -> mPrf.'.cols', $lDstStr);
    $this -> redirect('index.php?act=job-'.$lSrc.'.assignprj&jobid='.$lJobId.'&prjid='.$lPrjId);
  }

  protected function actSpr() {
    $lSrc = $this -> mReq -> getVal('src');
    $lJobId = $this -> mReq -> getVal('jobid');
    $lPrjId = $this -> mReq -> getVal('prjid');

    $lVie = new CHtm_Fpr($this -> mMod.'.sspr', 'job-'.$lSrc.'.assignprj&jobid='.$lJobId.'&prjid='.$lPrjId);
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
          if (bitset($lFla,ffRead) && !$this -> mUsr -> canRead($lFieRight)) {
            continue;
          }

          $lArr[$lFie['id']] = $lFie['name_'.LAN];
        }
      }
    }

    $lVie -> setSrc($lArr);
    $lVie -> setParam('jobsrc', $lSrc); // as >src< is used excessively and neither in the same domain nor for the same property we have to use >jobsrc< here
    $lVie -> setParam('jobid', $lJobId);
    $lVie -> setParam('prjid', $lPrjId);

    $lUsr = CCor_Usr::getInstance();
    $lVie -> setSel($lUsr -> getPref($this -> mPrf.'.sfie'));

    $lPag = CHtm_Page::getInstance();
    $lPag -> addJs($lVie -> getTooltips());
    $this -> render($lVie);
  }

  protected function actSspr() {
    $lDst = $this -> mReq -> getVal('dst');
    $lSrc = $this -> mReq -> getVal('jobsrc'); // as >src< is used excessively and neither in the same domain nor for the same property we have to use >jobsrc< here
    $lJobId = $this -> mReq -> getVal('jobid');
    $lPrjId = $this -> mReq -> getVal('prjid');

    $lUsr = CCor_Usr::getInstance();
    $lUsr -> setPref($this -> mPrf.'.sfie', implode(',', $lDst));
    $this -> redirect('index.php?act=job-'.$lSrc.'.assignprj&jobid='.$lJobId.'&prjid='.$lPrjId);
  }

  protected function actLpp() {
    $this -> mReq -> expect('src');
    $this -> mReq -> expect('jobid');
    $this -> mReq -> expect('prjid');
    $this -> mReq -> expect('lpp');

    $lSrc = $this -> mReq -> getVal('src');
    $lJobId = $this -> mReq -> getVal('jobid');
    $lPrjId = $this -> mReq -> getVal('prjid');
    $lLpp = $this -> mReq -> getVal('lpp');

    $lUsr = CCor_Usr::getInstance();
    $lUsr -> setPref($this -> mPrf.'.lpp', $lLpp);
    $lUsr -> setPref($this -> mPrf.'.page', 0);
    $this -> redirect('index.php?act=job-'.$lSrc.'.assignprj&jobid='.$lJobId.'&prjid='.$lPrjId);
  }

  protected function actOrd() {
    $this -> mReq -> expect('src');
    $this -> mReq -> expect('jobid');
    $this -> mReq -> expect('prjid');
    $this -> mReq -> expect('fie');

    $lSrc = $this -> mReq -> getVal('src');
    $lJobId = $this -> mReq -> getVal('jobid');
    $lPrjId = $this -> mReq -> getVal('prjid');
    $lFie = $this -> mReq -> getVal('fie');

    $lUsr = CCor_Usr::getInstance();
    $lUsr -> setPref($this -> mPrf.'.ord', $lFie);
    $this -> redirect('index.php?act=job-'.$lSrc.'.assignprj&jobid='.$lJobId.'&prjid='.$lPrjId);
  }

  protected function actSer() {
    $this -> mReq -> expect('src');
    $this -> mReq -> expect('jobid');
    $this -> mReq -> expect('prjid');
    $this -> mReq -> expect('val');

    $lSrc = $this -> mReq -> getVal('src');
    $lJobId = $this -> mReq -> getVal('jobid');
    $lPrjId = $this -> mReq -> getVal('prjid');
    $lVal = $this -> getReq('val', array());
    $lSer = array();
    foreach ($lVal as $lKey => $lVal) {
      if ('' === $lVal) continue;
      $lSer[$lKey] = $lVal;
    }

    $lUsr = CCor_Usr::getInstance();
    $lUsr -> setPref($this -> mMod.'.ser', $lSer);
    $lUsr -> setPref($this -> mMod.'.page', 0);
    $this -> redirect('index.php?act=job-'.$lSrc.'.assignprj&jobid='.$lJobId.'&prjid='.$lPrjId);
  }
  
  protected function actClser() {
    $lSrc = $this -> mReq -> getVal('src');
    $lJobId = $this -> mReq -> getVal('jobid');
    $lPrjId = $this -> mReq -> getVal('prjid');

    $lUsr = CCor_Usr::getInstance();
    $lUsr -> setPref($this -> mMod.'.ser', '');
    $lUsr -> setPref($this -> mMod.'.page', 0);
    $this -> redirect('index.php?act=job-'.$lSrc.'.assignprj&jobid='.$lJobId.'&prjid='.$lPrjId);
  }

  protected function actPage() {
    $lSrc = $this -> mReq -> getVal('src');
    $lJobId = $this -> mReq -> getVal('jobid');
    $lPrjId = $this -> mReq -> getVal('prjid');
    $lPag = $this -> mReq -> getInt('page');

    $lUsr = CCor_Usr::getInstance();
    $lUsr -> setPref($this -> mPrf.'.page', $lPag);
    $this -> redirect('index.php?act=job-'.$lSrc.'.assignprj&jobid='.$lJobId.'&prjid='.$lPrjId);
  }
}