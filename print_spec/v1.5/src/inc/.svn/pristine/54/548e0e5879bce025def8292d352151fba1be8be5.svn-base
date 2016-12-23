<?php
class CInc_Job_Art_Sub_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('job-sub.menu');
  }

  protected function getStdUrl() {
    $lJid = $this -> mReq -> jobid;
    return 'index.php?act='.$this -> mMod.'&jobid='.$lJid;
  }

  protected function actStd() {
    $lJid = $this -> getReq('jobid');

    #$lQry = new CCor_Qry('SELECT * FROM al_job_art WHERE id='.$lJid);
    $lJob = new CCor_Dat();

    $lVie = new CJob_Art_Header($lJob);
    $lRet = $lVie -> getContent();

    $lVie = new CJob_Art_Tabs($lJid, 'sub');
    $lRet.= $lVie -> getContent();

    $lVie = new CJob_Art_Sub_List($lJid);
    $lRet.= $lVie -> getContent();

    $this -> render($lRet);
  }

  protected function actEdt() {
    $lJid = $this -> getInt('jobid');
    $lId  = $this -> getInt('id');

    $lQry = new CCor_Qry('SELECT * FROM al_job_pro_'.intval(MID).' WHERE id='.$lJid);
    $lJob = $lQry -> getDat();

    $lQry = new CCor_Qry('SELECT * FROM al_job_sub_'.intval(MID).' WHERE id='.$lId);
    $lSub = $lQry -> getDat();

    $lVie = new CJob_Pro_Header($lJob);
    $lRet = $lVie -> getContent();

    $lVie = new CJob_Pro_Sub_Form('job-pro-sub.sedt', $lJid, $lJob);
    $lVie -> setParam('jobid', $lJid);
    $lVie -> setParam('sid', $lId);
    $lVie -> getWizard($lSub['wiz_id']);
    $lVie -> assignVal($lSub);
    $lRet.= $lVie -> getContent();

    $this -> render($lRet);
  }

  protected function actSedt() {
    $lJid = $this -> getInt('jobid');
    $lSid = $this -> getInt('sid');
    $lPag = $this -> getReq('page');

    $lMod = new CJob_Pro_Sub_Mod($lSid);
    $lMod -> getPost($this -> mReq);
    $lMod -> update();

    $this -> redirect('index.php?act=job-pro-sub&jobid='.$lJid);
  }


  protected function actNew() {
    $lJid = $this -> getInt('jobid');
    $lWiz = $this -> getInt('wiz');

    $lQry = new CCor_Qry('SELECT * FROM al_job_pro_'.intval(MID).' WHERE id='.$lJid);
    $lJob = $lQry -> getDat();

    $lVie = new CJob_Pro_Header($lJob);
    $lRet = $lVie -> getContent();

    $lVie = new CJob_Pro_Sub_Form('job-pro-sub.snew', $lJid, $lJob);
  #  $lVie -> setParam('jobid', $lJid);
  #  $lVie -> setParam('wiz', $lWiz);
  #  $lVie -> assignVal($lJob); // default: use project values
  #  $lVie -> getWizard($lWiz);
    $lRet.= $lVie -> getContent();

    $this -> render($lRet);
  }

  protected function actSnew() {
    $lJid = $this -> getInt('jobid');
    $lWiz = $this -> getInt('wiz');
    $lPag = $this -> getReq('page');

    $lMod = new CJob_Pro_Sub_Mod($lSid);
    $lMod -> getPost($this -> mReq);
    $lMod -> setVal('wiz_id', $lWiz);
    $lMod -> setVal('pro_id', $lJid);
    $lMod -> insert();

    $this -> redirect('index.php?act=job-pro-sub&jobid='.$lJid);
  }

  /* Wizard */

  protected function actWiz() {
    $lJid = $this -> getInt('jobid');
    $lWid = $this -> getInt('wiz');

    $lDat = array();
    $lSes = CCor_Ses::getInstance();
    $lSes['job-pro-sub.wiz.dat'] = $lDat;

    $this -> redirect('index.php?act=job-pro-sub.wizfrm&jobid='.$lJid.'&wiz='.$lWid.'&step=0');
  }

  protected function actWizfrm() {
    $lJid = $this -> getInt('jobid');
    $lWid = $this -> getInt('wiz');
    $lStp = $this -> getInt('step');

    $lSes = CCor_Ses::getInstance();
    $lDat = $lSes['job-pro-sub.wiz.dat'];

    $lVie = new CJob_Pro_Sub_Wiz($lJid, $lStp, $lWid, $lDat);
    $this -> render($lVie);
  }

  protected function getStepFromPost() {
    $lStp = $this -> getInt('step');
    $lReq = $this -> getReq('val');

    $lSes = CCor_Ses::getInstance();
    $lDat = $lSes['job-pro-sub.wiz.dat'];
    $this -> dump($lReq, 'REQUEST');
    foreach ($lReq as $lAli => $lArr) {
      if (!empty($lArr)) {
        foreach ($lArr as $lKey => $lVal) {
          if ('' != $lVal) {
            $lDat[$lStp][$lKey][$lAli] = $lVal;
          }
        }
      }
    }
    $lSes['job-pro-sub.wiz.dat'] = $lDat;
  }

  protected function actWizprev() {
    $lJid = $this -> getInt('jobid');
    $lStp = $this -> getInt('step');
    $lWid = $this -> getInt('wiz');

    $this -> getStepFromPost();

    $this -> redirect('index.php?act=job-pro-sub.wizfrm&jobid='.$lJid.'&wiz='.$lWid.'&step='.($lStp-1));
  }

  protected function actWiznext() {
    $lJid = $this -> getInt('jobid');
    $lStp = $this -> getInt('step');
    $lWid = $this -> getInt('wiz');

    $this -> getStepFromPost();

    $this -> redirect('index.php?act=job-pro-sub.wizfrm&jobid='.$lJid.'&wiz='.$lWid.'&step='.($lStp+1));
  }

  protected function mult($aSrc, $aArr) {
    $lRet = array();
    if (empty($aSrc)) {
      foreach ($aArr as $lKey => $lVal) {
        $lLin = array();
        foreach($lVal as $lAli => $lValue) {
          $lLin[$lAli] = $lValue;
        }
        $lRet[] = $lLin;
      }
    } else {
      foreach ($aSrc as $lRow) {
        foreach ($aArr as $lKey => $lVal) {
          $lLin = $lRow;
          foreach($lVal as $lAli => $lValue) {
            $lLin[$lAli] = $lValue;
          }
          $lRet[] = $lLin;
        }

      }
    }
    return $lRet;
  }

  protected function actWizfinish() {
    $lJid = $this -> getInt('jobid');
    $lStp = $this -> getInt('step');
    $lWid = $this -> getInt('wiz');

    $this -> getStepFromPost();

    $lSes = CCor_Ses::getInstance();
    $lDat = $lSes['job-pro-sub.wiz.dat'];

    $lJobs = array();
    foreach ($lDat as $lStp => $lArr) {
      $lJobs = $this -> mult($lJobs, $lArr);
    }
    foreach ($lJobs as $lJob) {
      $lMod = new CJob_Pro_Sub_Mod();
      $lMod -> setVal('pro_id', $lJid);
      $lMod -> setVal('wiz_id', $lWid);
      foreach ($lJob as $lKey => $lVal) {
        $lMod -> setVal($lKey, $lVal);
      }
      $lMod -> insert();
    }
    $this -> redirect('index.php?act=job-pro-sub&jobid='.$lJid);
  }


}