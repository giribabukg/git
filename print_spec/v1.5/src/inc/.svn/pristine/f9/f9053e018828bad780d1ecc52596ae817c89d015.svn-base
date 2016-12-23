<?php
class CInc_Job_Typ_Cos_Cnt extends CCor_Cnt {

  protected $mSrc   = 'typ';

  public function __construct(ICor_Req $aReq, $aSrc, $aMod, $aAct) { //neu: $aSrc Aufrufe!!
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mSrc = $aSrc;
    $this -> mTitle = lan('job-cos.menu');

    $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');
    $this -> mCrpId = $lCrp[$this -> mSrc];

  }

  protected function actStd() {
    $lJobId = $this -> getReq('jobid');
    $lPag = $this -> getReq('page');

    $lJob = new CJob_Typ_Dat($this -> mSrc);
    $lJob -> load($lJobId);

    $lVie = new CJob_Typ_Header($this -> mSrc, $lJob, $this -> mCrpId);
    $lRet = $lVie -> getContent();

    $lVie = new CJob_Typ_Tabs($this -> mSrc, $lJobId, 'cos');
    $lRet.= $lVie -> getContent();

    $lFrm = new CJob_Typ_Cos_List($this -> mSrc, $lJobId, $lJob);
    $lRet.= $lFrm -> getContent();

    $this -> render($lRet);
  }

  protected function actScos() {
    $lJobId = $this -> getReq('jobid');
    $lOld = $this -> getReq('old');
    $lNew = $this -> getReq('val');

    $lArr = array();
    foreach ($lOld as $lKey => $lOldVal) {
      $lNewVal = $lNew[$lKey];
      if ($lOldVal != $lNewVal) {
        $lArr[$lKey] = $lNewVal;
      }
    }
    if (!empty($lArr)) {
      $lQry = new CApi_Alink_Query_Setartcalc($lJobId, $lArr);
      $lQry -> query();
    }
    $this -> redirect('index.php?act=job-'.$this -> mSrc.'-cos&jobid='.$lJobId);
  }

}