<?php
class CInc_Job_Art_Cnt extends CJob_Cnt {

  protected $mSrc = 'art';

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mAva = fsArt;
  }

  protected function actStd() {
    $lVie = new CJob_Art_List();
    $this -> render($lVie);
  }

  protected function actWecTest() {
    $lJobId = $this -> getReq('jobid');
    $lDebug = $this -> getReq('debug');
    $lwec = new CInc_Job_Fil_Src_Wec('art', $lJobId, '', $lDebug);
    echo '<pre>';
    print_r('--------- W E C --------------------------------------'.LF);
    print_r($lwec);
    print_r('------------------------------------------------------'.LF);
    $lls = $lwec -> getFileList();
    print_r('--------- W E C getFilelist --------------------------'.LF);
    print_r($lls);
    echo '</pre>';
  }
  
  protected function actEdt() {
    $lJobId = $this -> getReq('jobid');
    $lPag = $this -> getReq('page', 'job');

    $this -> checkArc($this -> mSrc, $lJobId);

    $lJob = new CJob_Art_Dat();
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

    $lVie = new CJob_Art_Header($lJob);
    $lRet = $lVie -> getContent();

    $lVie = new CJob_Art_Tabs($lJobId, $lPag);

    $lRet.= $lVie -> getContent();

    $lFrm = new CJob_Art_Form('job-art.sedt', $lJobId, $lJob, $lPag);
    $lRet.= $lFrm -> getContent();
    $lRet.= $this -> getWecLogout();
    $this -> render($lRet);
  }

  protected function actSedt() {
    $lCid = $this -> getReq('clientid');
    parent::checkMand($lCid);

    $lJobId = $this -> getReq('jobid');
    $lStp = $this -> getReq('step');
    $lPag = $this -> getReq('page', 'job');

    $lMod = new CJob_Art_Mod($lJobId);
    $lMod -> getPost($this -> mReq);
    if($lMod -> update()) {
      if($lStp > 0){
        $this -> redirect('index.php?act=job-art.step&sid='.$lStp.'&jobid='.$lJobId);
        exit;
      }
    }
    $this -> redirect('index.php?act=job-art.edt&jobid='.$lJobId.'&page='.$lPag);
  }

  protected function actNew() {
    $lJob = new CCor_Dat();

    $lVie = new CJob_Art_Tabs(0);
    $lRet = $lVie -> getContent();

    $lFrm = new CJob_Art_Form('job-art.snew');
    $lRet.= $lFrm -> getContent();

    $this -> render($lRet);
  }

  protected function actSnew() {
    $lPag = $this -> getReq('page', 'job');
    // JobId: #22823 Copy Task
    // Nach Anlegen der nue Job, wird es an referenz Projekt bzw. Projekt-Item zugeordnet.
    $lAssignedProId = $this-> getReqInt('AssignedProId');
    $lAssignedProItemId = $this-> getReqInt('AssignedProItemId');

    $lMod = new CJob_Art_Mod();
    $lMod -> getPost($this -> mReq);
    if ($lMod -> insert()) {
      $lJobId = $lMod -> getInsertId();
      if ($lAssignedProId != FALSE){ // After Save, the job will be  assigned to Project.
        // neu Job wird an Projekt zugeordnet.
        $this ->dbg('Job is assigned to ProjectId '.$lAssignedProId);
        $lUrl = 'index.php?act=job-art.sassignprj&jobid='.$lJobId.'&pid='.$lAssignedProId;
        if ($lAssignedProItemId){
          //Job wird an Projekt Item zugeordnet, weil in der ProjektItem der Spalte "jobid_[jobtyp]" frei ist.
          $lUrl.='&prjitmid='.$lAssignedProItemId;
        }
        $this -> redirect($lUrl); // Send neu Job zu Projekt Zuordnung.
      }else {
        $this -> redirect('index.php?act=job-art.edt&jobid='.$lJobId.'&page='.$lPag);
      }
    }
    $this -> redirect('index.php?act=job-art.new');
  }

  protected function actDel() {
    $lJobId = $this -> getReq('id');
    $lQry = new CApi_Alink_Query_UpdateJob($lJobId);
    $lQry -> addField('webstatus', 0);
    if ($lQry -> query()) {
      $this -> msg('Job '.$lJobId.' deleted', mtAdmin, mlInfo);
    }

    //22651 Project Critical Path Functionality
    $this -> JobDelete($lJobId);

    $this -> redirect();
  }

  protected function actSub() {
    $lProId = $this -> getInt('pid');
    $lSid = $this -> getInt('sid');
    $lSrc = $this -> getVal('src');
    $lIsMaster = $this -> getVal('ismaster');
    $lMasterId = $this -> getVal('masterid');
    
    $lQry = new CCor_Qry('SELECT * FROM al_job_pro_'.intval(MID).' WHERE id='.$lProId);
    $lJob = $lQry -> getDat();

    $lFie = CCor_Res::extract('alias','typ', 'fie');
    $lViewProjektJoblist = CCor_Cfg::get('view.projekt.joblist', TRUE);
    // !$lViewProjektJoblist: it works with ProjectItems (S+T View) - Copy Content from Subproject
    if (!$lViewProjektJoblist AND !empty($lSid)){
      $lQry -> query('SELECT * FROM al_job_sub_'.intval(MID).' WHERE id IN ('.addslashes($lSid).')');
      $lFirst = TRUE;
      foreach($lQry as $lRow) {
        foreach ($lRow as $lKey => $lVal) {
          if (!empty($lVal)) {
            if ($lFirst) {
              $lJob[$lKey] = $lVal;
              $lFirst = FALSE;
            } else {
              if (empty($lJob[$lKey])) {
                $lJob[$lKey] = $lVal;
              } else {
                $lTyp = (isset($lFie[$lKey])) ? $lFie[$lKey] : '';
                if ('string' == $lTyp) {
                  $lArr = explode(',', $lJob[$lKey]);
                  $lArr[] = $lVal;
                  $lJob[$lKey] = implode(',', array_unique($lArr));
                }
              }
            }
          }
        }
      }
    }

    $lVie = new CJob_Art_Tabs(0);
    $lRet = $lVie -> getContent();

    $lFrm = new CJob_Art_Form('job-art.ssub', 0, $lJob);
    $lFrm -> setParam('pid', $lProId);
    $lFrm -> setParam('sid', $lSid);
    $lFrm -> setParam('src', $lSrc);
    if ($lIsMaster != ''){
      $lFrm -> setParam('old[is_master]', $lIsMaster);
      $lFrm -> setParam('val[is_master]', $lIsMaster);
    }
    if ($lMasterId != ''){
      $lFrm -> setParam('old[master_id]', $lMasterId);
      $lFrm -> setParam('val[master_id]', $lMasterId);
    }
    $lFrm -> setParam('old[jobid_pro]', $lProId);
    $lFrm -> setParam('val[jobid_pro]', $lProId);

    $lRet.= $lFrm -> getContent();
    $this -> render($lRet);
  }

  protected function actSsub() {
    $lProId = $this -> getInt('pid');
    $lSid = $this -> getInt('sid');
    $lPag = $this -> getReq('page', 'job');
    $lSrc = $this -> getVal('src');
    
    $lQry = new CCor_Qry('SELECT * FROM al_job_sub_'.intval(MID).' WHERE id='.$lSid);
    $lRow = $lQry -> getDat();
    
    $lMod = new CJob_Art_Mod();
    $lMod -> getPost($this -> mReq);
    if ($lMod -> insert()) {
      $lJobId = $lMod -> getInsertId();
                
      $lViewProjektJoblist = CCor_Cfg::get('view.projekt.joblist', TRUE);
      if ($lViewProjektJoblist){
         $lModSub = new CJob_Pro_Sub_Mod();
         $lModSub -> getPost($this -> mReq);
         $lModSub -> setVal('wiz_id', 3);
         $lModSub -> setVal('pro_id', $lProId);
         $lModSub -> setVal('jobid_'.$this -> mSrc, $lJobId);
         $lModSub -> setVal('src', $this -> mSrc);
         $lModSub -> setVal('webstatus', 10);
         $lModSub -> insert();
      } else {
        // !$lViewProjektJoblist: it works with ProjectItems (S+T View) - Copy Content from Subproject
        $lSql = 'UPDATE al_job_sub_'.intval(MID).' SET jobid_art="'.addslashes($lJobId).'" WHERE id IN ('.addslashes($lSid).')';
        CCor_Qry::exec($lSql);

        //22651 Project Critical Path Functionality
        $lMod -> insertIntoProjectStatusInfo($lJobId, $lProId, $lSid);

        $lMas = (string)$lRow['is_master'];
        $this -> dbg($lMas);
        if ('Y' == $lMas) $lSrc = 'mas';
      }
      CJob_Pro_Mod::reportDraft($lProId, $this -> mSrc, $lJobId);

      $this -> redirect('index.php?act=job-art.edt&jobid='.$lJobId.'&page='.$lPag);
    }
    $this -> redirect('index.php?act=job-art.new');
  }

  protected function actSur() {
    $lSKUID = $this -> getInt('skuid');

    $lQry = new CCor_Qry('SELECT * FROM al_job_sku_'.intval(MID).' WHERE id='.esc($lSKUID));
    $lSKU = $lQry -> getDat();

    $lVie = new CJob_Art_Tabs(0);
    $lRet = $lVie -> getContent();

    $lFrm = new CJob_Art_Form('job-art.ssur', 0, $lSKU);
    $lFrm -> setParam('skuid', $lSKUID);
    $lRet.= $lFrm -> getContent();
    $this -> render($lRet);
  }

  protected function actSsur() {
    $lSKUID = $this -> getInt('skuid');
    $lPage = $this -> getReq('page', 'job');
    
    $lMod = new CJob_Art_Mod();
    $lMod -> getPost($this -> mReq);
    if ($lMod -> insert()) {
      $lJobID = $lMod -> getInsertId();
      
      $lResult = CCor_Qry::getStr('SELECT * FROM al_job_sku_sub_'.intval(MID).' WHERE sku_id='.esc($lSKUID).' AND job_id='.esc($lJobID));
      if (!$lResult) {
        CCor_Qry::exec('INSERT IGNORE INTO al_job_sku_sub_'.intval(MID).' (job_id, sku_id, src) VALUES ('.esc($lJobID).', '.esc($lSKUID).', '.esc($this -> mSrc).')');
      }

      $this -> redirect('index.php?act=job-art.edt&jobid='.$lJobID.'&page='.$lPage);
    }
    $this -> redirect('index.php?act=job-art.new');
  }

  protected function actNsub() {
    $lJobId = $this -> getReq('jobid');
    $lSrc = $this -> getReq('src');

    $lObj = 'CJob_'.$lSrc.'_Dat';
    $lJob = new $lObj();
    $lJob -> load($lJobId);
    $lJob['jobid'] = '';

    $lVie = new CJob_Art_Header($lJob);
    $lRet = $lVie -> getContent();

    $lVie = new CJob_Art_Tabs();
    $lRet.= $lVie -> getContent();

    $lFrm = new CJob_Art_Form('job-art.snsub', 0, $lJob);
    $lFrm -> setParam('main_no', strip($lJobId));
    $lRet.= $lFrm -> getContent();

    $this -> render($lRet);
  }

  protected function actSnsub() {
    $lPag = $this -> getReq('page', 'job');
    $lMai = $this -> getReq('main_no');

    $lMod = new CJob_Art_Mod();
    $lMod -> getPost($this -> mReq);
    if ($lMod -> insert()) {
      $lJobId = $lMod -> getInsertId();
      $this -> redirect('index.php?act=job-art.edt&jobid='.$lJobId.'&page='.$lPag);
    }
    $this -> redirect('index.php?act=job-art.new');
  }

}