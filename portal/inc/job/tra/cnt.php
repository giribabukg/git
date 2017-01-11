<?php
class CInc_Job_Tra_Cnt extends CJob_Cnt {

  protected $mSrc = 'tra';

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mAva = fsTra;
  }

  protected function actStd() {
    $lVie = new CJob_Tra_List();
    $this -> render($lVie);
  }

  protected function actEdt() {
    $lJobId = $this -> getReq('jobid');
    $lPag = $this -> getReq('page', 'job');

    $this -> checkComparison($this -> mSrc, $lJobId);
    $this -> checkArc($this -> mSrc, $lJobId);

    $lJob = new CJob_Tra_Dat();
    if ($lJob -> load($lJobId)) {
      if ($lUrl = $lJob -> redirectUrl()) {
        $this -> msg('Note: The job '.jid($lJobId).' is a different job type', mtUser, mlWarn);
        $this -> redirect($lUrl);
      }
      $lJob -> updateShadow();
      $lJob -> addRecentJob();
    } else {
      $this -> msg('Job '.$lJobId.' not found', mtUser, mlError);
      $this -> redirect();
    }

    $lVie = new CJob_Tra_Header($lJob);
    $lRet = $lVie -> getContent();

    $lVie = new CJob_Tra_Tabs($lJobId, $lPag);
    $lRet.= $lVie -> getContent();

    $lFrm = new CJob_Tra_Form('job-'.$this -> mSrc.'.sedt', $lJobId, $lJob, $lPag);
    $lRet.= $lFrm -> getContent();

    if (in_array($this -> mSrc, CCor_Cfg::get('wec.jobs'))) {
      $lRet.= $this -> getWecLogout();
    }

    $this -> render($lRet);
  }

  protected function actSur() {
    $lSKUID = $this -> getInt('skuid');

    $lQry = new CCor_Qry('SELECT * FROM al_job_sku_'.intval(MID).' WHERE id='.esc($lSKUID));
    $lSKU = $lQry -> getDat();

    $lVie = new CJob_Tra_Tabs(0);
    $lRet = $lVie -> getContent();

    $lFrm = new CJob_Tra_Form('job-tra.ssur', 0, $lSKU);
    $lFrm -> setParam('skuid', $lSKUID);
    $lRet.= $lFrm -> getContent();
    $this -> render($lRet);
  }

  protected function actSsur() {
    $lSKUID = $this -> getInt('skuid');
    $lPage = $this -> getReq('page', 'job');

    $lMod = new CJob_Tra_Mod();
    $lMod -> getPost($this -> mReq);
    if ($lMod -> insert()) {
      $lJobID = $lMod -> getInsertId();

      $lResult = CCor_Qry::getStr('SELECT * FROM al_job_sku_sub_'.intval(MID).' WHERE sku_id='.esc($lSKUID).' AND job_id='.esc($lJobID));
      if (!$lResult) {
        CCor_Qry::exec('INSERT IGNORE INTO al_job_sku_sub_'.intval(MID).' (job_id, sku_id, src) VALUES ('.esc($lJobID).', '.esc($lSKUID).', '.esc($this -> mSrc).')');
      }

      $this -> redirect('index.php?act=job-tra.edt&jobid='.$lJobID.'&page='.$lPage);
    }
    $this -> redirect('index.php?act=job-tra.new');
  }

  protected function actNsub() {
    $lJobId = $this -> getReq('jobid');
    $lSrc = $this -> getReq('src');

    $lObj = 'CJob_'.$lSrc.'_Dat';
    $lJob = new $lObj();
    $lJob -> load($lJobId);
    $lJob['jobid'] = '';

    $lVie = new CJob_Tra_Header($lJob);
    $lRet = $lVie -> getContent();

    $lVie = new CJob_Tra_Tabs();
    $lRet.= $lVie -> getContent();

    $lFrm = new CJob_Tra_Form('job-'.$this -> mSrc.'.snsub', 0, $lJob);
    $lFrm -> setParam('main_no', strip($lJobId));
    $lRet.= $lFrm -> getContent();

    $this -> render($lRet);
  }

  protected function actSnsub() {
    $lPag = $this -> getReq('page', 'job');
    $lMai = $this -> getReq('main_no');

    $lMod = new CJob_Tra_Mod();
    $lMod -> getPost($this -> mReq);
    if ($lMod -> insert()) {
      $lJobId = $lMod -> getInsertId();
      $this -> redirect('index.php?act=job-'.$this -> mSrc.'.edt&jobid='.$lJobId.'&page='.$lPag);
    }
    $this -> redirect('index.php?act=job-'.$this -> mSrc.'.new');
  }
  
}