<?php
class CInc_Job_Art_Form extends CJob_Form {

  protected $mJobId;

  public function __construct($aAct, $aJobId = 0, $aJob = NULL, $aPage = 'job') {
    
    $this -> mJobId = $aJobId;
          
    parent::__construct('art', $aAct, $aPage, $this -> mJobId);
    $lUsr = CCor_Usr::getInstance();
    $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');
    $this -> mCrpId = $lCrp[$this -> mSrc];
    
    $this -> mFla = 0;
    if (empty($aJob)) {
      if (!empty($this -> mJobId)) {
        $this -> mJob = new CJob_Art_Dat();
        $this -> mJob -> load($this -> mJobId);
        $this -> mFla = $this -> mJob -> getFlags();
      } else {
        $this -> mJob = new CCor_Dat();
      }
    } else {
      $this -> mJob = $aJob;
      if (!empty($this -> mJobId)) {
        $this -> mFla = $this -> mJob -> getFlags();
      }
    }

    $lKnr = $this -> mJob['net_knr'];
    if (empty($lKnr)) {
      $this -> mJob['net_knr'] = CCor_Cfg::get(MAND.'.def.knr');
    }
    
   $lStat = $this -> mJob['status'];
    if (($lStat == 'RE') or ($lStat == 'RS') or ($lStat == 'G')) {
        $this -> mCanEdit  = FALSE;
    }

    $this -> setPat('val.id', $this -> mJobId);

    if (bitset($this -> mFla, jfOnhold)) {
      $this -> msg('This job is on hold', mtUser, mlWarn);
      $this -> mCanEdit = FALSE;
    }

    if (bitset($this -> mFla, jfCancelled)) {
      $this -> msg('This job is cancelled', mtUser, mlWarn);
      $this -> mCanEdit = FALSE;
    }
    
    //notused $lSta = intval($this -> mJob['webstatus']);
    //notused: $lSid = CCor_Qry::getInt('SELECT id FROM al_crp_status WHERE mand='.MID.' AND crp_id='.$this -> mCrpId.' AND status='.$lSta);
    
      
    // Can User Edit Job by CRP STATUS
    // if NOT set $this->mCanEdit = FALSE
    $this -> canStatusEdit();
    
    $this -> addPanel('act', lan('lib.actions'), '', 'job.act');
    if ($this -> mCanEdit) {
      $this -> addBtn('act', lan('lib.ok'), '', 'img/ico/16/ok.gif', 'submit', array('class' => 'btn w200' ));
    }
    $this -> addBtn('act', lan('lib.cancel'), 'go("index.php?act=job-art")', 'img/ico/16/cancel.gif', 'button', array('class' => 'btn w200' ));
    if (!empty($this -> mJobId)) {
      $this -> addBtn('act', lan('lib.print'), 'pop("index.php?act=job-art.prn&jobid='.$this -> mJobId.'")', 'img/ico/16/print.gif', 'button', array('class' => 'btn w200' ));
    }
    if ($this -> canAssign()) {
      $this -> addBtn('act', lan('job.assignprj'), 'go("index.php?act=job-art.assignprj&jobid='.$this -> mJobId.'")', 'img/ico/16/next-hi.gif', 'button', array('class' => 'btn w200' ));
    }
    //Button for creating/refreshing Question Lists
    if($lUsr->canInsert('questions') && CCor_Cfg::get('job.'.$this->mSrc.'.questionLists', false)) {
      $this -> addBtn('act', lan('question.create'), 'go("index.php?act=job-'.$this->mSrc.'.createQL&jobid='.$this -> mJobId.'&src='.$this->mSrc.'")', '<i class="ico-w16 ico-w16-mt-4"></i>', 'button', array('class' => 'btn w200' ));
    }
    //Button for importing Questions Lists from Ref Job
    if($lUsr->canEdit('refJob.import') && CCor_Cfg::get('job-'.$this->mSrc.'.refAlias', "") !== "") {
      $lRefAlias = CCor_Cfg::get('job-'.$this->mSrc.'.refAlias', "");
      if($this->mJob->__get($lRefAlias) != 0) {
        $this -> addBtn('act', lan('refJob.import'), 'go("index.php?act=job-'.$this->mSrc.'.refImport&jobid='.$this -> mJobId.'&refjobid='.$this->mJob->__get($lRefAlias).'&src='.$this->mSrc.'")', '<i class="ico-w16 ico-w16-copy"></i>', 'button', array('class' => 'btn w200' ));
      }
    }
    // Button Get Project Timing
    if (!empty($this -> mAssignedProId) AND $lUsr->canRead('job.timing-from-prj')){
      $this -> addBtn('act', lan('job.gettimingfrmprj'), 'go("index.php?act=job-'.$this -> mSrc.'.setassignedprodat&jobid='.$this -> mJobId.'&src='.$this -> mSrc.'&proid='.$this -> mAssignedProId.'")', 'img/ico/16/clock_refresh.gif', 'button', array('class' => 'btn w200' ));
    }
    if ($this -> canAssignSkuSub()) {
      $this -> addBtn('act', lan('job.assignskusub'), 'go("index.php?act=job-art.assignskusub&jobid='.$this -> mJobId.'")', 'img/ico/16/next-hi.gif', 'button', array('class' => 'btn w200' ));
    }
    if (!empty($this -> mJobId) AND $lUsr->canInsert('email') ) {
      $this -> addBtn('act', lan('email.notif'), 'go("index.php?act=job-'.$this -> mSrc.'-his.newmail&jobid='.$this -> mJobId.'&src='.$this -> mSrc.'&frm=job")', 'img/ico/16/email.gif', 'button', array('class' => 'btn w200' ));
    }

    if (!empty($this -> mJobId)) {
      $this -> addPanel('stp', lan('crp-stp.menu'), '', 'job.stp');
      

      // Ab welchem Status ist der Job in Produktion (existiert die JobId != A000...)
      $lSql = 'SELECT `to_id` FROM `al_crp_step` WHERE `mand`='.MID.' AND `trans` LIKE '.esc("anf2job").' AND `crp_id`='.$this -> mCrpId;
      $lSta2Prod = CCor_Qry::getInt($lSql);
      if (FALSE !== $lSta2Prod) {
        $lSql = 'SELECT `status` FROM `al_crp_status` WHERE `id`='.$lSta2Prod;
        $this -> mWebSta2Prod = CCor_Qry::getInt($lSql);
      } else { $this -> mWebSta2Prod = FALSE; }
      // Ab welchem Status ist der Job im Archiv
      $lSql = 'SELECT `to_id` FROM `al_crp_step` WHERE `mand`='.MID.' AND `trans` LIKE '.esc("job2arc").' AND `crp_id`='.$this -> mCrpId;
      $lSta2Arc = CCor_Qry::getInt($lSql);
      if (FALSE !== $lSta2Arc) {
        $lSql = 'SELECT `status` FROM `al_crp_status` WHERE `id`='.$lSta2Arc;
        $this -> mWebSta2Arc = CCor_Qry::getInt($lSql);
      } else { $this -> mWebSta2Arc = FALSE; }
      $lSta = intval($this -> mJob['webstatus']);
      if (FALSE !== $this -> mWebSta2Prod AND $this -> mWebSta2Prod <= $lSta AND $lUsr -> canEdit('job.crp.chg')) {
        $this -> addPanel('chg', lan('crp-stp.menu'), '', 'job.stp');
        $this -> addButton('chg', $this -> getSetStatusButtonMenu());
      }
            
      $this -> addRoles();
      $this -> addStatusButtons();

      $lShowAplButtons = CCor_Cfg::get('show.form.apl', true);
      if ($lShowAplButtons) {
        // Korrekturumlauf Status rausfinden
        $lSql = 'SELECT status FROM al_crp_status WHERE mand='.MID.' AND crp_id='.$this -> mCrpId.' AND apl=1';
        $lQry = new CCor_Qry($lSql);
        $this -> lAplstatus = $lQry -> getImplode('status');
        $this -> addAplButtons($this -> lAplstatus);
      }

      $this -> addPanel('jfl', lan('jfl.menu'), '', 'job.jfl');
      $this -> addFlagButtons();
    }

    $this -> mTabs = new CJob_Art_Tabs($this -> mJobId, $aPage);

    $lTemplate = $this -> getTemplates();

    foreach($lTemplate as $lSite => $lTempl) {
      $this -> addPage($lSite);
      foreach($lTempl as $lTpl => $lSrc) {
        if(!empty($lSrc)){
          $this -> addPart($lSite, $lTpl, $lSrc);
        } else  {
          $this -> addPart($lSite, $lTpl);
        }
      }
    }

    $lUsr = CCor_Usr::getInstance();
    //echo substr($this->mJobId, 0, 1); exit;
    
    
    // Das Button "Webcenter Projekt anlegen"
    if (($lUsr -> canEdit('job-wec-id')) && ($this->mJobId) && (substr($this->mJobId, 0, 1) <> 'A')) {
      //echo $this->mJobId.BR;
      //echo $lUsr -> canEdit('job-wec-id').BR;
      //echo lan('wec.menu').BR;
      #$this -> addPart('det', 'wec', 'rep'); // kommt über $lMandClass
      $lAtt = array();
      $lAtt['class'] = 'btn w200';
      $this -> addBtn('act', lan('wec.menu'), 'go("index.php?act=job-art.wec&src=art&direct=1&jobid='.$this -> mJobId.'")', 'img/ico/16/wec.gif', 'button', $lAtt);
    }
    //Der Button "Webcenter Viewer"
    //if ($lUsr -> canEdit('wec.view')) { // - Intouch
    // if ($this -> hasRole('per_prj_verantwortlich')){ // S+T
    if ($this -> hasRole('per_prj_verantwortlich') OR $lUsr -> canEdit('wec.view')) {
      if (!empty($this -> mJob['wec_prj_id'])) {
        $lAtt = array();
        $lAtt['class'] = 'btn w200';
        
        $lRet = array();
        $lWec = new CApi_Wec_Client();
        $lWec -> loadConfig();
        $lQry = new CApi_Wec_Query_Doclist($lWec);
        //$lRes = $lQry -> getListByName($this -> mJob['jobnr']);
        $lRes = $lQry -> getList($this -> mJob['wec_prj_id']);
        if ($lRes) {
          $lRet = array();
          if (!empty($lRes)) {
            $liwec = 0;
            $lpid = '';
            foreach ($lRes as $lRow) {
              if ($lRow['viewer']) {
                $liwec++;
                $lRet[$lRow['wec_doc_id']] = $lRow['name'];
                $lpid = $lRow['projectid'];
                $lUrl = 'index.php?act=utl-wec.open';
                $lUrl.= '&pid='.$lpid;
                $lUrl.= '&doc='.urlencode($lRow['name']);
                $lUrl.= '&docid='.urlencode($lRow['wec_ver_id']);
              }
            }
            if ($liwec == 1) {
              $this -> addBtn('act', lan('wec.view'), 'go("'.$lUrl.'&src='.$this -> mSrc.'&jid='.$this -> mJobId.'","'.CCor_Cfg::get('wec.view', '').'")', 'img/ico/16/wecview.gif', 'button', $lAtt);
            } else if ($liwec > 1) {
              $this -> addBtn('act', lan('wec.view'), 'go("index.php?act=job-'.$this -> mSrc.'-fil&sub=wec&src='.$this -> mSrc.'&jobid='.$this -> mJobId.'")', 'img/ico/16/wecview.gif', 'button', $lAtt);
            }
          }
        }
      }
    }
    $lTabs  = CCor_Cfg::get('job.mask.tabs');
    if (in_array('det', $lTabs)) {
      $this -> addPart('det', 'des', 'rep');
    }
  }

  public function setJob($aJob) {
    $this -> mJob = $aJob;
  }

  protected function onBeforeContent() {
    parent::onBeforeContent();
    $lSides = $this -> mJob['druckdurchgang'];
    $lDis = ($lSides > 1) ? 'block' : 'none';
    $this -> setPat('frm.co2', $lDis);
    $lDis = ($lSides > 2) ? 'block' : 'none';
    $this -> setPat('frm.co3', $lDis);
  }

}