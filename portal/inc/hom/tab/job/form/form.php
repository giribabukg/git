<?php
class CInc_Hom_Tab_Job_Form_Form extends CCust_Job_Form {

  protected $mJobID;
  protected $mSrc;
  protected $mCode;

  public function __construct($aJobID, $aSrc, $aCode) {

    $this -> mJobID = $aJobID;
    $this -> mSrc = $aSrc;
    $this -> mCode = $aCode;

    parent::__construct($this -> mSrc, 'job-'.$this -> mSrc.'.sedt', $this -> mCode, $this -> mJobID);

    // get tabs for the current job type
    $lCfg = CCor_Cfg::getInstance();
    $this -> lDefaultTabs = $lCfg -> get('job.mask.tabs');

    // get templates for the current job type
    $FormTpl = new CJob_Formtpl();
    $this -> mTemplates = $FormTpl -> mTemplates;

    // Webcenter ProjektId nur für Jobs "art, rep" und mit dem Recht "job-wec-id" verknüpft.
    // Falls es in der Job-Maske keine Reiter "Details (det)" gibt, soll es unter "Identifikation (job)" angezeigt werden.
    $lUsr = CCor_Usr::getInstance();
    if (in_array($this -> mSrc, array('rep', 'art')) AND $lUsr -> canEdit('job-wec-id')) {
      if (in_array('det', $this -> lDefaultTabs)) {
        $this -> mTemplates['rep']['det']['wec'] = 'rep';
      } else {
        $this -> mTemplates['rep']['job']['wec'] = 'rep';
      }
    } else {
      unset( $this -> mTemplates['rep']['job']['wec'] );
    }

    $lTabSlave = CCor_Qry::getArrImp('SELECT link, code, subtype FROM al_tab_slave WHERE mand='.MID.' AND code="'.$this -> mCode.'"');
    list($lLink, $lCode, $lSubType) = explode(',', $lTabSlave);
    
    $lAdditionalTab = array($lCode => array($lLink => $lSubType));
    if(isset($this -> mTemplates[$lSubType])){
      $this -> mTemplates[$lSubType] = array_merge($this -> mTemplates[$lSubType], $lAdditionalTab);
    }else{
      $this -> mTemplates[$lSubType] = $lAdditionalTab;
    }
    
    

    $lUsr = CCor_Usr::getInstance();
    $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');
    $this -> mCrpID = $lCrp[$this -> mSrc];

    $this -> mFla = 0;
    if (empty($aJob)) {
      if (!empty($this -> mJobID)) {
        $lClassName = 'CJob_'.ucfirst($this -> mSrc).'_Dat';
        $this -> mJob = new $lClassName();
        $this -> mJob -> load($this -> mJobID);
        $this -> mFla = $this -> mJob -> getFlags();
      } else {
        $this -> mJob = new CCor_Dat();
      }
    } else {
      $this -> mJob = $aJob;
      if (!empty($this -> mJobID)) {
        $this -> mFla = $this -> mJob -> getFlags();
      }
    }

    $lKnr = $this -> mJob['net_knr'];
    if (empty($lKnr)) {
      $this -> mJob['net_knr'] = CCor_Cfg::get(MAND.'.def.knr');
    }

    $lStat = $this -> mJob['status'];
    if (($lStat == 'RE') or ($lStat == 'RS') or ($lStat == 'G')) {
      $this -> mCanEdit = FALSE;
    }

    $this -> setPat('val.id', $this -> mJobID);

    if (bitset($this -> mFla, jfOnhold)) {
      $this -> msg('This job is on hold', mtUser, mlWarn);
      $this -> mCanEdit = FALSE;
    }

    if (bitset($this -> mFla, jfCancelled)) {
      $this -> msg('This job is cancelled', mtUser, mlWarn);
      $this -> mCanEdit = FALSE;
    }

    //notUsed: $lSta = intval($this -> mJob['webstatus']);
    //notUsed: $lSid = CCor_Qry::getInt('SELECT id FROM al_crp_status WHERE mand='.MID.' AND crp_id='.$this -> mCrpID.' AND status='.$lSta);

    $this -> canStatusEdit();

    $this -> addPanel('act', lan('lib.actions'), '', 'job.act');
    if ($this -> mCanEdit) {
      $this -> addBtn('act', lan('lib.ok'), '', 'img/ico/16/ok.gif', 'submit', array('class' => 'btn w200' ));
    }
    $this -> addBtn('act', lan('lib.cancel'), 'go("index.php?act=job-"'.$this -> mSrc.')', 'img/ico/16/cancel.gif', 'button', array('class' => 'btn w200' ));
    if (!empty($this -> mJobID)) {
      $this -> addBtn('act', lan('lib.print'), 'pop("index.php?act=job-'.$this -> mSrc.'.prn&jobid='.$this -> mJobID.'")', 'img/ico/16/print.gif', 'button', array('class' => 'btn w200' ));
    }
    if ($this -> canAssign()) {
      $this -> addBtn('act', lan('job.assignprj'), 'go("index.php?act=job-'.$this -> mSrc.'.assignprj&jobid='.$this -> mJobID.'")', 'img/ico/16/next-hi.gif', 'button', array('class' => 'btn w200' ));
    }
    if (!empty($this -> mAssignedProId) AND $lUsr -> canRead('job.timing-from-prj')) {
      $this -> addBtn('act', lan('job.gettimingfrmprj'), 'go("index.php?act=job-'.$this -> mSrc.'.setassignedprodat&jobid='.$this -> mJobID.'&src='.$this -> mSrc.'&proid='.$this -> mAssignedProId.'")', 'img/ico/16/clock_refresh.gif', 'button', array('class' => 'btn w200' ));
    }
    if ($this -> canAssignSkuSub()) {
      $this -> addBtn('act', lan('job.assignskusub'), 'go("index.php?act=job-'.$this -> mSrc.'.assignskusub&jobid='.$this -> mJobID.'")', 'img/ico/16/next-hi.gif', 'button', array('class' => 'btn w200' ));
    }
    if (!empty($this -> mJobID) AND $lUsr -> canInsert('email')) {
      $this -> addBtn('act', lan('email.notif'), 'go("index.php?act=job-'.$this -> mSrc.'-his.newmail&jobid='.$this -> mJobID.'&src='.$this -> mSrc.'&frm=job")', 'img/ico/16/plus.gif', 'button', array('class' => 'btn w200' ));
    }

    if (!empty($this -> mJobID)) {
      $this -> addPanel('stp', lan('crp-stp.menu'), '', 'job.stp');

      // productive job?
      $lSql = 'SELECT `to_id` FROM `al_crp_step` WHERE `mand`='.MID.' AND `trans` LIKE '.esc("anf2job").' AND `crp_id`='.$this -> mCrpID;
      $lSta2Prod = CCor_Qry::getInt($lSql);
      if (FALSE !== $lSta2Prod) {
        $lSql = 'SELECT `status` FROM `al_crp_status` WHERE `id`='.$lSta2Prod;
        $this -> mWebSta2Prod = CCor_Qry::getInt($lSql);
      } else {
        $this -> mWebSta2Prod = FALSE;
      }

      // archived job?
      $lSql = 'SELECT `to_id` FROM `al_crp_step` WHERE `mand`='.MID.' AND `trans` LIKE '.esc("job2arc").' AND `crp_id`='.$this -> mCrpID;
      $lSta2Arc = CCor_Qry::getInt($lSql);
      if (FALSE !== $lSta2Arc) {
        $lSql = 'SELECT `status` FROM `al_crp_status` WHERE `id`='.$lSta2Arc;
        $this -> mWebSta2Arc = CCor_Qry::getInt($lSql);
      } else {
        $this -> mWebSta2Arc = FALSE;
      }

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
        $lSql = 'SELECT status FROM al_crp_status WHERE mand='.MID.' AND crp_id='.$this -> mCrpID.' AND apl=1';
        $lQry = new CCor_Qry($lSql);
        $this -> lAplstatus = $lQry -> getImplode('status');
        $this -> addAplButtons($this -> lAplstatus);
      }

      $this -> addPanel('jfl', lan('jfl.menu'), '', 'job.jfl');
      $this -> addFlagButtons();
    }

    $lClassName = 'CJob_'.ucfirst($this -> mSrc).'_Tabs';
    $this -> mTabs = new $lClassName($this -> mJobID, $this -> mCode);

    $lTemplate = $this -> getTemplates();

    foreach ($lTemplate as $lSite => $lTempl) {
      $this -> addPage($lSite);
      foreach ($lTempl as $lTpl => $lSrc) {
        if (!empty($lSrc)) {
          $this -> addPart($lSite, $lTpl, $lSrc);
        } else {
          $this -> addPart($lSite, $lTpl);
        }
      }
    }

    $lUsr = CCor_Usr::getInstance();

    // Das Button "Webcenter Projekt anlegen"
    if (($lUsr -> canEdit('job-wec-id')) && ($this -> mJobID) && (substr($this -> mJobID, 0, 1) <> 'A')) {
      $lAtt = array();
      $lAtt['class'] = 'btn w200';
      $this -> addBtn('act', lan('wec.menu'), 'go("index.php?act=job-'.$this -> mSrc.'.wec&src='.$this -> mSrc.'&direct=1&jobid='.$this -> mJobID.'")', 'img/ico/16/ml-2.gif', 'button', $lAtt);
    }

    if ($this -> hasRole('per_prj_verantwortlich') OR $lUsr -> canEdit('wec.view')) {
      if (!empty($this -> mJob['wec_prj_id'])) {
        $lAtt = array();
        $lAtt['class'] = 'btn w200';
        
        $lRet = array();
        $lWec = new CApi_Wec_Client();
        $lWec -> loadConfig();
        $lQry = new CApi_Wec_Query_Doclist($lWec);
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
              $this -> addBtn('act', lan('wec.view'), 'go("'.$lUrl.'&src='.$this -> mSrc.'&jid='.$this -> mJobID.'","'.CCor_Cfg::get('wec.view', '').'")', 'img/ico/16/wecview.gif', 'button', $lAtt);
            } else if ($liwec > 1) {
              $this -> addBtn('act', lan('wec.view'), 'go("index.php?act=job-'.$this -> mSrc.'-fil&sub=wec&src='.$this -> mSrc.'&jobid='.$this -> mJobID.'")', 'img/ico/16/wecview.gif', 'button', $lAtt);
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