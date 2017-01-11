<?php
/**
 * ToDo: Description
 *
 * @author Geoffrey Emmans <emmans@qbf.de>
 * @package JOB
 * @subpackage TYP
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 4126 $
 * @date $Date: 2014-04-09 20:50:40 +0200 (Wed, 09 Apr 2014) $
 * @author $Author: gemmans $
 */
class CInc_Job_Typ_Cnt extends CJob_Cnt {

  protected $mSrc   = 'typ';
  protected $mAva   = '';
  protected $mCrpId = 0;
  protected $mArtwork = false;

  public function __construct(ICor_Req $aReq, $aSrc, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mSrc = $aSrc;
   echo '<pre>---CJob_Typ_Cnt---';var_dump($this -> mSrc,'#############');echo '</pre>';
    $this -> mAva = fs.ucfirst($this -> mSrc);

    $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');
    $this -> mCrpId = $lCrp[$this -> mSrc];

    $lArtwork = CCor_Cfg::get('code_artwork');
    if ($lArtwork == $this -> mSrc) {
      $this -> mArtwork = true;
    }

  }

  protected function actStd() {
    $lRender = new CJob_Typ_List($this -> mSrc, $this -> mCrpId);
    $this -> render($lRender);
  }

  protected function actEdt() {
    $lJobId = $this -> getReq('jobid');
    $lPag = $this -> getReq('page', 'job');

    $this -> checkArc($this -> mSrc, $lJobId);

    $lJob = new CJob_Typ_Dat($this -> mSrc);
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

    $lRender = new CJob_Typ_Header($this -> mSrc, $lJob, $this -> mCrpId);
    $lRet = $lRender -> getContent();

    $lRender = new CJob_Typ_Tabs($this -> mSrc, $lJobId, $lPag);
    $lRet.= $lRender -> getContent();

    $lFrm = new CJob_Typ_Form($this -> mSrc, '.sedt', $this -> mCrpId, $lJobId, $lJob, $lPag);
    $lRet.= $lFrm -> getContent();

    $this -> render($lRet);
  }

  protected function actSnew() {
    $lPag = $this -> getReq('page', 'job');

    $lMod = new CJob_Typ_Mod($this -> mSrc);
    $lMod -> getPost($this -> mReq);

    if ($lMod -> insert()) {
      $lJobId = $lMod -> getInsertId();
      if ($lAssignedProId != FALSE){ // After Save, the job will be  assigned to Project.
        $this ->dbg('Job ist assigned to ProjectId '.$lAssignedProId);
        $this -> redirect('index.php?act=job-'.$this -> mSrc.'.sassignprj&jobid='.$lJobId.'&pid='.$lAssignedProId);
      }else {
        $this -> redirect('index.php?act=job-'.$this -> mSrc.'.edt&jobid='.$lJobId.'&page='.$lPag);
      }
    }
    $this -> redirect('index.php?act=job-'.$this -> mSrc.'.new');
  }
  
  protected function actSur() {
    $lSKUID = $this -> getInt('skuid');

    $lQry = new CCor_Qry('SELECT * FROM al_job_sku_'.intval(MID).' WHERE id='.esc($lSKUID));
    $lSKU = $lQry -> getDat();

    $lVie = new CJob_Typ_Tabs(0);
    $lRet = $lVie -> getContent();

    $lFrm = new CJob_Typ_Form('job-typ.ssur', 0, $lSKU);
    $lFrm -> setParam('skuid', $lSKUID);
    $lRet.= $lFrm -> getContent();
    $this -> render($lRet);
  }

  protected function actSsur() {
    $lSKUID = $this -> getInt('skuid');
    $lPage = $this -> getReq('page', 'job');

    $lMod = new CJob_Typ_Mod();
    $lMod -> getPost($this -> mReq);
    if ($lMod -> insert()) {
      $lJobID = $lMod -> getInsertId();

      $lResult = CCor_Qry::getStr('SELECT * FROM al_job_sku_sub_'.intval(MID).' WHERE sku_id='.esc($lSKUID).' AND job_id='.esc($lJobID));
      if (!$lResult) {
        CCor_Qry::exec('INSERT IGNORE INTO al_job_sku_sub_'.intval(MID).' (job_id, sku_id, src) VALUES ('.esc($lJobID).', '.esc($lSKUID).', '.esc($this -> mSrc).')');
      }

      $this -> redirect('index.php?act=job-typ.edt&jobid='.$lJobID.'&page='.$lPage);
    }
    $this -> redirect('index.php?act=job-typ.new');
  }

  protected function actNsub() {
    $lJobId = $this -> getReq('jobid');
    $lSrc = $this -> getReq('src');

    $lObj = 'CJob_'.$lSrc.'_Dat';
    $lJob = new $lObj();
    $lJob -> load($lJobId);
    $lJob['jobid'] = '';

    $lRender = new CJob_Typ_Header($this -> mSrc, $lJob, $this -> mCrpId);
    $lRet = $lRender -> getContent();

    $lRender = new CJob_Typ_Tabs($this -> mSrc);
    $lRet.= $lRender -> getContent();

    $lFrm = new CJob_Typ_Form($this -> mSrc, '.snsub', $this -> mCrpId, 0, $lJob);
    $lFrm -> setParam('main_no', strip($lJobId));
    $lRet.= $lFrm -> getContent();

    $this -> render($lRet);
  }

  protected function actSnsub() {
    $lPag = $this -> getReq('page', 'job');
    $lMai = $this -> getReq('main_no');

    $lMod = new CJob_Typ_Mod($this -> mSrc);
    $lMod -> getPost($this -> mReq);
    if ($lMod -> insert()) {
      $lJobId = $lMod -> getInsertId();
      $this -> redirect('index.php?act=job-'.$this -> mSrc.'.edt&jobid='.$lJobId.'&page='.$lPag);
    }
    $this -> redirect('index.php?act=job-'.$this -> mSrc.'.new');
  }

}