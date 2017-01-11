<?php
class CInc_Job_Sec_Cnt extends CJob_Cnt {

  protected $mSrc = 'sec';

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mAva = fsSec;
  }

  protected function actStd() {
    $lVie = new CJob_Sec_List();
    error_log('.....CInc_Job_Sec_Cnt.....actStd.....actStd.....'.var_export($lVie,true)."\n",3,'logggg.txt');
    $this -> render($lVie);
  }

  protected function actEdt() {
    $lJobId = $this -> getReq('jobid');
    error_log('.....CInc_Job_Sec_Cnt.....actEdt.....$lJobId.....'.var_export($lJobId,true)."\n",3,'logggg.txt');
    
    $lPag = $this -> getReq('page', 'job');
    error_log('.....CInc_Job_Sec_Cnt.....actEdt.....$lPag.....'.var_export($lPag,true)."\n",3,'logggg.txt');
    $this -> checkComparison($this -> mSrc, $lJobId);
    $this -> checkArc($this -> mSrc, $lJobId);

    $lJob = new CJob_Sec_Dat();
    if ($lJob -> load($lJobId)) {
      error_log('.....CInc_Job_Sec_Cnt.....actEdt.....in if.......'."\n",3,'logggg.txt');
      if ($lUrl = $lJob -> redirectUrl()) {
        error_log('.....CInc_Job_Sec_Cnt.....actEdt.....in if2.......'."\n",3,'logggg.txt');
        $this -> msg('Note: The job '.jid($lJobId).' is a different job type', mtUser, mlWarn);
        $this -> redirect($lUrl);
      }
      $lJob -> updateShadow();
      $lJob -> addRecentJob();
    } else {
      $this -> msg('Job '.$lJobId.' not found', mtUser, mlError);
      $this -> redirect();
    }

    //These following three contents are for content of the page only.  That is middle of the page only.

    $lVie = new CJob_Sec_Header($lJob);   //Getting html for critical path like flow
    $lRet = $lVie -> getContent();

    $lVie = new CJob_Sec_Tabs($lJobId, $lPag);  //Getting Tab details of tabs. Like Details, History, and Files.
    $lRet.= $lVie -> getContent();  

    error_log('.....CInc_Job_Sec_Cnt.....actEdt1.....$lJobId.....'.var_export($lJobId,true)."\n",3,'logggg.txt');
    error_log('.....CInc_Job_Sec_Cnt.....actEdt1.....$lJob.....'.var_export($lJob,true)."\n",3,'logggg.txt');
    error_log('.....CInc_Job_Sec_Cnt.....actEdt1.....$lPag.....'.var_export($lPag,true)."\n",3,'logggg.txt');

    $lFrm = new CJob_Sec_Form('job-sec.sedt', $lJobId, $lJob, $lPag);   //Then that three column table part which includes form, right side control
    $lRet.= $lFrm -> getContent();  // inga content kedaikum bothea..... ella replace aagi direct html aaah thaa kedaikuthu....

    //error_log('.....CInc_Job_Sec_Cnt.....actEdt.....$lRet.....'.var_export($lRet,true)."\n",3,'logggg.txt');

    $lRet.= $this -> getWecLogout();  //NOTHING COMES HERE.

    //error_log('.....CInc_Job_Sec_Cnt.....actEdt.....$this -> getWecLogout().....'.var_export($this -> getWecLogout(),true)."\n",3,'logggg.txt');
    error_log('.....CJob_Sec_Form...actEdt...$this -> mDoc....at end of function......'.var_export($this -> mDoc,true)."\n",3,'logggg.txt');
    error_log('.....CJob_Sec_Form...actEdt...$this -> mPat....at end of function......'.var_export($this -> mPat,true)."\n",3,'logggg.txt');    
    $this -> render($lRet);
  }

  protected function actSur() {
    $lSKUID = $this -> getInt('skuid');

    $lQry = new CCor_Qry('SELECT * FROM al_job_sku_'.intval(MID).' WHERE id='.esc($lSKUID));
    $lSKU = $lQry -> getDat();

    $lVie = new CJob_Sec_Tabs(0);
    $lRet = $lVie -> getContent();

    $lFrm = new CJob_Sec_Form('job-sec.ssur', 0, $lSKU);
    $lFrm -> setParam('skuid', $lSKUID);
    $lRet.= $lFrm -> getContent();
    $this -> render($lRet);
  }

  protected function actSsur() {
    $lSKUID = $this -> getInt('skuid');
    $lPage = $this -> getReq('page', 'job');

    $lMod = new CJob_Sec_Mod();
    $lMod -> getPost($this -> mReq);
    if ($lMod -> insert()) {
      $lJobID = $lMod -> getInsertId();

      $lResult = CCor_Qry::getStr('SELECT * FROM al_job_sku_sub_'.intval(MID).' WHERE sku_id='.esc($lSKUID).' AND job_id='.esc($lJobID));
      if (!$lResult) {
        CCor_Qry::exec('INSERT IGNORE INTO al_job_sku_sub_'.intval(MID).' (job_id, sku_id, src) VALUES ('.esc($lJobID).', '.esc($lSKUID).', '.esc($this -> mSrc).')');
      }

      $this -> redirect('index.php?act=job-sec.edt&jobid='.$lJobID.'&page='.$lPage);
    }
    $this -> redirect('index.php?act=job-sec.new');
  }

  protected function actNsub() {
    $lJobId = $this -> getReq('jobid');
    $lSrc = $this -> getReq('src');

    $lObj = 'CJob_'.$lSrc.'_Dat';
    $lJob = new $lObj();
    $lJob -> load($lJobId);
    $lJob['jobid'] = '';

    $lVie = new CJob_Sec_Header($lJob);
    $lRet = $lVie -> getContent();

    $lVie = new CJob_Sec_Tabs();
    $lRet.= $lVie -> getContent();

    $lFrm = new CJob_Sec_Form('job-sec.snsub', 0, $lJob);
    $lFrm -> setParam('main_no', strip($lJobId));
    $lRet.= $lFrm -> getContent();

    $this -> render($lRet);
  }

  protected function actSnsub() {
    $lPag = $this -> getReq('page', 'job');
    $lMai = $this -> getReq('main_no');

    $lMod = new CJob_Sec_Mod();
    $lMod -> getPost($this -> mReq);
    if ($lMod -> insert()) {
      $lJobId = $lMod -> getInsertId();
      $this -> redirect('index.php?act=job-Sec.edt&jobid='.$lJobId.'&page='.$lPag);
    }
    $this -> redirect('index.php?act=job-sec.new');
  }
  
}