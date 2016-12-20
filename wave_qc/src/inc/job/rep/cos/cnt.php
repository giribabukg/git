<?php
class CInc_Job_Rep_Cos_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('job-cos.menu');
  }

  public function actStd() {
    $lJid = $this -> getReq('jobid');
    $lPag = $this -> getReq('page');

    $lJob = new CJob_Rep_Dat();
    $lJob -> load($lJid);

    $lVie = new CJob_Rep_Header($lJob);
    $lRet = $lVie -> getContent();

    $lVie = new CJob_Rep_Tabs($lJid, 'cos');
    $lRet.= $lVie -> getContent();

    $lFrm = new CJob_Rep_Cos_List($lJid, $lJob);
    $lRet.= $lFrm -> getContent();

    $this -> render($lRet);
  }

  protected function actScos() {
    $lJid = $this -> getReq('jobid');
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
      $lQry = new CApi_Alink_Query_Setartcalc($lJid, $lArr);
      $lQry -> query();
    }
    $this -> redirect('index.php?act=job-rep-cos&jobid='.$lJid);
  }

}