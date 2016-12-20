<?php
class CInc_Job_Pro_Cnt extends CJob_Cnt {

  protected $mSrc = 'pro';

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);

    $this -> mAva = fsPro;
    $this -> mMmKey = 'job-pro';
  }

  protected function actStd() {
    $lVie = new CJob_Pro_List();
    $this -> render($lVie);
  }

  protected function actSub() {
    $lPid = $this -> getInt('jid');
    $lVie = new CJob_Pro_Sub($lPid);
    $lVie -> render();
  }

  protected function actEdt() {
    $lJid = $this -> getInt('jobid');
    $lPag = $this -> getReq('page');

    $this -> checkArc($this -> mSrc, $lJid);

    $lJob = new CJob_Pro_Dat();
    if ($lJob -> load($lJid)) {
      $lJob -> addRecentJob();
    } else {
      $this -> msg('Pro '.$lJid.' not found', mtUser, mlError);
      $this -> redirect();
    }

    $lVie = new CJob_Pro_Header($lJob);
    $lRet = $lVie -> getContent();

    $lVie = new CJob_Pro_Tabs($lJid, $lPag);
    $lRet.= $lVie -> getContent();

    $lFrm = new CJob_Pro_Form($lJid, $lPag);
    $lRet.= $lFrm -> getContent();

    $this -> render($lRet);
  }

  public function actNew() {
    $lJob = new CCor_Dat();

    $lVie = new CJob_Pro_Tabs(0);
    $lRet = $lVie -> getContent();

    $lFrm = new CJob_Pro_Form(0, 'job', 'job-pro.snew');
    $lRet.= $lFrm -> getContent();

    $this -> render($lRet);
  }

  public function actSnew() {
    $lPag = $this -> getReq('page', 'job');

    $lMod = new CJob_Pro_Mod();
    $lMod -> getPost($this -> mReq);
    if ($lMod -> insert()) {
      $lJid = $lMod ->getInsertId();
      $this -> redirect('index.php?act=job-pro.edt&jobid='.$lJid.'&page='.$lPag);
    }
    $this -> redirect('index.php?act=job-pro.new');
  }

  protected function actStep() {
    $lJid = $this -> getInt('jobid');
    $lStp = $this -> getInt('sid');

    $lFla = intval(CCor_Qry::getStr('SELECT flags FROM al_crp_step WHERE mand='.MID.' AND id='.$lStp));
    if (bitset($lFla, sfComment)) {
      $this -> stepDialog();
    } else {
      $this -> redirect('index.php?act=job-pro.cnf&jobid='.$lJid.'&sid='.$lStp);
    }
  }

  protected function stepDialog() {
    $lJid = $this -> getInt('jobid');
    $lStp = $this -> getInt('sid');

    $lJob = new CJob_Pro_Dat();
    $lJob -> load($lJid);

    $lHdr = new CJob_Pro_Header($lJob);
    $lRet = $lHdr -> getContent();

    $lDlg = new CJob_Dialog($this -> mSrc, $lJid, $lStp, $lJob);
    $lRet.= $lDlg -> getContent();

    $this -> render($lRet);
  }

  protected function actCnf() {
    $lJid = $this -> getInt('jobid');
    $lSid = $this -> getInt('sid');
    $lPag = $this -> getReq('page');
    $lVal = $this -> getReq('val');
    $lMsg = (isset($lVal['msg'])) ? $lVal['msg'] : '';

    $lAdd = array();
    if (isset($lVal['amt'])) {
      $lAdd['amt'] = $lVal['amt'];
    }
    if (isset($lVal['cause'])) {
      $lAdd['cause'] = $lVal['cause'];
    }
    if (isset($_FILES['val'])) {
      $lFin = new CApp_Finder($this -> mSrc, $lJid);
      $lDir = $lFin -> getPath('doc');
      $lUpl = new CCor_Upload();
      $lRes = $lUpl -> uploadIndex('file', $lDir);
      if ($lRes) {
        $lAdd['fil'] = $lRes;
        CCor_Usr::insertJobFile($this -> mSrc, $lJid, 'doc', $lRes);

        $lArr = $_FILES['val'];
        $lNam = $lArr['name']['file'];
        $lHis = new CApp_His($this -> mSrc, $lJid);
        $lMsg = sprintf(lan('filupload.success'),$lNam);
        $lHis -> add(htFileupload, lan('filupload.his.msg'), $lMsg);
      }
    }

    // Ignore any email-notifications?
    $lOld = $this -> getReq('act_old'); // liefert sonst NULL zurück => Fehler bei foreach!
    $lAct = $this -> getReq('act_new');
    $lIgn = array();

    if (!empty($lOld)) {
      foreach ($lOld as $lKeyOuter => $lValueOuter) {
        $lIgn[$lKeyOuter] = array();
        foreach ($lValueOuter as $lKeyInner => $lValueInner) {
          if (!isset($lAct[$lKeyOuter][$lKeyInner])) {
            $lIgn[$lKeyOuter][] = $lKeyInner;
          }
        }
      }
    }

    $lObj = new CJob_Pro_Step($lJid);
    $lObj -> doStep($lSid, $lMsg, $lAdd, $lIgn);
    $this -> redirect('index.php?act=job-pro.edt&jobid='.$lJid.'&page='.$lPag);
  }

  protected function actDel() {
    $lJobId = $this -> getReq('id');
    $lSql = 'UPDATE al_job_pro_'.MID.' SET del="Y" WHERE id='.$lJobId;
    CCor_Qry::exec($lSql);
    $this -> redirect();
  }
}