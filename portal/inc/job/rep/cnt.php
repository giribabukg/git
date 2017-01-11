<?php
class CInc_Job_Rep_Cnt extends CJob_Cnt {

  protected $mSrc = 'rep';

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mAva = fsRep;
  }

  protected function actStd() {
    $lVie = new CJob_Rep_List();
    $this -> render($lVie);
  }

  protected function actEdt() {
    $lJobId = $this -> getReq('jobid');
    $lPag = $this -> getReq('page', 'job');

    $this -> checkComparison($this -> mSrc, $lJobId);
    $this -> checkArc($this -> mSrc, $lJobId);

    $lJob = new CJob_Rep_Dat();
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

    $lVie = new CJob_Rep_Header($lJob);
    $lRet = $lVie -> getContent();

    $lVie = new CJob_Rep_Tabs($lJobId, $lPag);
    $lRet.= $lVie -> getContent();

    $lFrm = new CJob_Rep_Form('job-rep.sedt', $lJobId, $lJob, $lPag);
    $lRet.= $lFrm -> getContent();

    $lRet.= $this -> getWecLogout();
    $this -> render($lRet);
  }

  protected function actSub() {
    $lProId = $this -> getInt('pid');
    $lSid = $this -> getInt('sid');

    $lQry = new CCor_Qry('SELECT * FROM al_job_pro_'.intval(MID).' WHERE id='.$lProId);
    $lJob = $lQry -> getDat();

    $lViewProjektJoblist = CCor_Cfg::get('view.projekt.joblist', TRUE);
    // !$lViewProjektJoblist: it works with ProjectItems (S+T View) - Copy Content from Subproject
    if (!$lViewProjektJoblist AND !empty($lSid)){
      $lQry -> query('SELECT * FROM al_job_sub_'.intval(MID).' WHERE id='.$lSid);
      $lSub = $lQry -> getDat();
      foreach ($lSub as $lKey => $lVal) {
        if (!empty($lVal)) {
          $lJob[$lKey] = $lVal;
        }
      }
      $lArt = $lSub['jobid_art'];
      if (!empty($lArt)) {//MOP2010: in grauen Vorzeiten konnte ein Rep aus einem Master(art) kopiert werden
        $lArtJob = new CJob_Art_Dat();
        $lArtJob -> load($lArt);
        foreach ($lArtJob as $lKey => $lVal) {
          if (!empty($lVal)) {
            $lJob[$lKey] = $lVal;
          }
        }
      }
    }

    $lVie = new CJob_Rep_Tabs(0);
    $lRet = $lVie -> getContent();

    $lJob['webstatus'] = 0; // avoid edit by status check
    $lFrm = new CJob_Rep_Form('job-rep.ssub', 0, $lJob);
    $lFrm -> setParam('pid', $lProId);
    $lFrm -> setParam('sid', $lSid);
    $lFrm -> setParam('old[jobid_pro]', $lProId);
    $lFrm -> setParam('val[jobid_pro]', $lProId);
    if (!empty($lArt)) {
      $lFrm -> setParam('old[jobid_art]', $lArt);
      $lFrm -> setParam('val[jobid_art]', $lArt);
    }
    $lRet.= $lFrm -> getContent();

    $this -> render($lRet);
  }

  protected function actSur() {
    $lSKUID = $this -> getInt('skuid');

    $lQry = new CCor_Qry('SELECT * FROM al_job_sku_'.intval(MID).' WHERE id='.esc($lSKUID));
    $lSKU = $lQry -> getDat();

    $lVie = new CJob_Rep_Tabs(0);
    $lRet = $lVie -> getContent();

    $lFrm = new CJob_Rep_Form('job-'.$this -> mSrc.'.ssur', 0, $lSKU);
    $lFrm -> setParam('skuid', $lSKUID);
    $lRet.= $lFrm -> getContent();
    $this -> render($lRet);
  }

  protected function actSsur() {
    $lSKUID = $this -> getInt('skuid');
    $lPage = $this -> getReq('page', 'job');

    $lMod = new CJob_Rep_Mod();
    $lMod -> getPost($this -> mReq);
    if ($lMod -> insert()) {
      $lJobID = $lMod -> getInsertId();

      $lResult = CCor_Qry::getStr('SELECT * FROM al_job_sku_sub_'.intval(MID).' WHERE sku_id='.esc($lSKUID).' AND job_id='.esc($lJobID));
      if (!$lResult) {
        CCor_Qry::exec('INSERT IGNORE INTO al_job_sku_sub_'.intval(MID).' (job_id, sku_id, src) VALUES ('.esc($lJobID).', '.esc($lSKUID).', '.esc($this -> mSrc).')');

        $lProID = CCor_Qry::getStr('SELECT * FROM al_job_sku_sur_'.intval(MID).' WHERE sku_id='.esc($lSKUID));
        $this -> redirect('index.php?act=job-'.$this -> mSrc.'.sassignprj&jobid='.$lJobID.'&pid='.$lProID);
      }

      $this -> redirect('index.php?act=job-rep.edt&jobid='.$lJobID.'&page='.$lPage);
    }
    $this -> redirect('index.php?act=job-rep.new');
  }

  protected function actNsub() {
    $lJobId = $this -> getReq('jobid');
    $lSrc = $this -> getReq('src');

    $lObj = 'CJob_'.$lSrc.'_Dat';
    $lJob = new $lObj();
    $lJob -> load($lJobId);
    $lJob['jobid'] = '';

    $lVie = new CJob_Rep_Header($lJob);
    $lRet = $lVie -> getContent();

    $lVie = new CJob_Rep_Tabs();
    $lRet.= $lVie -> getContent();

    $lFrm = new CJob_Rep_Form('job-rep.snsub', 0, $lJob);
    $lFrm -> setParam('main_no', strip($lJobId));
    $lRet.= $lFrm -> getContent();

    $this -> render($lRet);
  }

  protected function actSnsub() {
    $lPag = $this -> getReq('page', 'job');
    $lMai = $this -> getReq('main_no');

    $lMod = new CJob_Rep_Mod();
    $lMod -> getPost($this -> mReq);
    if ($lMod -> insert()) {
      $lJobId = $lMod -> getInsertId();
      $this -> redirect('index.php?act=job-rep.edt&jobid='.$lJobId.'&page='.$lPag);
    }
    $this -> redirect('index.php?act=job-rep.new');
  }
 

}
