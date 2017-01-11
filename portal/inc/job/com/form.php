<?php
/**
 * Jobs: Components - Formular
 *
 *  Description
 *
 * @package    JOB
 * @subpackage    Com
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 6390 $
 * @date $Date: 2014-11-07 15:30:02 +0100 (Fri, 07 Nov 2014) $
 * @author $Author: jwetherill $
 */
 class CInc_Job_Com_Form extends CJob_Form {

  protected $mSrc = 'com';
  protected $mJobId;

  public function __construct($aAct, $aJobId = 0, $aJob = NULL, $aPage = 'job') {
    
    $this -> mJobId = $aJobId;
    
       
    parent::__construct($this -> mSrc, $aAct, $aPage, $this -> mJobId);
    
    $lUsr = CCor_Usr::getInstance();
    $this -> mFla = 0;
    if (empty($aJob)) {
      if (!empty($this -> mJobId)) {
        $this -> mJob = new CJob_Com_Dat();
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

    $this -> setPat('val.id', $this -> mJobId);

    if (bitset($this -> mFla, jfOnhold)) {
      $this -> msg('This job is on hold', mtUser, mlWarn);
      $this -> mCanEdit = FALSE;
    }

    if (bitset($this -> mFla, jfCancelled)) {
      $this -> msg('This job is cancelled', mtUser, mlWarn);
      $this -> mCanEdit = FALSE;
    }

    // Can User Edit Job by CRP STATUS
    // if NOT set $this->mCanEdit = FALSE
    $this -> canStatusEdit();

    $this -> addPanel('act', lan('lib.actions'), '', 'job.act');
    if ($this -> mCanEdit) {
      $this -> addBtn('act', lan('lib.ok'), '', 'img/ico/16/ok.gif', 'submit', array('class' => 'btn w200' ));
    }
    $this -> addBtn('act', lan('lib.cancel'), 'go("index.php?act=job-'.$this -> mSrc.'")', 'img/ico/16/cancel.gif', 'button', array('class' => 'btn w200'));
    if (!empty($this -> mJobId)) {
      $this -> addBtn('act', lan('lib.print'), 'pop("index.php?act=job-'.$this -> mSrc.'.prn&jobid='.$this -> mJobId.'")', 'img/ico/16/print.gif', 'button', array('class' => 'btn w200'));
    }

    if ($this -> canAssign()) {
      $this -> addBtn('act', lan('job.assignprj'), 'go("index.php?act=job-'.$this -> mSrc.'.assignprj&jobid='.$this -> mJobId.'")', 'img/ico/16/next-hi.gif', 'button', array('class' => 'btn w200'));
    }
    // Button Get Project Timing
    if (!empty($this -> mAssignedProId) AND $lUsr->canRead('job.timing-from-prj')){
      $this -> addBtn('act', lan('job.gettimingfrmprj'), 'go("index.php?act=job-'.$this -> mSrc.'.setassignedprodat&jobid='.$this -> mJobId.'&src='.$this -> mSrc.'&proid='.$this -> mAssignedProId.'")', 'img/ico/16/clock_refresh.gif', 'button', array('class' => 'btn w200' ));
    }
    if ($this -> canAssignSkuSub()) {
      $this -> addBtn('act', lan('job.assignskusub'), 'go("index.php?act=job-'.$this -> mSrc.'.assignskusub&jobid='.$this -> mJobId.'")', 'img/ico/16/next-hi.gif', 'button', array('class' => 'btn w200'));
    }
    if (!empty($this -> mJobId) AND $lUsr->canInsert('email') ) {
      $this -> addBtn('act', lan('email.notif'), 'go("index.php?act=job-'.$this -> mSrc.'-his.newmail&jobid='.$this -> mJobId.'&src='.$this -> mSrc.'&frm=job")', 'img/ico/16/email.gif', 'button', array('class' => 'btn w200' ));
    }
    
    if (!empty($this -> mJobId)) {
      $this -> addPanel('stp', lan('crp-stp.menu'), '', 'job.stp');
      $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');
      $this -> mCrpId = $lCrp[$this -> mSrc];

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

    $this -> mTabs = new CJob_Com_Tabs($this -> mJobId, $aPage);

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
    if (($lUsr -> canEdit('job-wec-id')) && ($this -> mJobId) && (substr($this -> mJobId, 0, 1) <> 'A')) {
      $lAtt = array();
      $lAtt['class'] = 'btn w200';
      $this -> addBtn('act', lan('wec.menu'), 'go("index.php?act=job-com.wec&src=com&direct=1&jobid='.$this -> mJobId.'")', 'img/ico/16/wec.gif', 'button', $lAtt);
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
              $this -> addBtn('act', lan('wec.view'), 'go("'.$lUrl.'&src='.$this -> mSrc.'&jid='.$this -> mJobId.'","'.CCor_Cfg::get('wec.view', '').'")', 'img/ico/16/wecview.gif', 'button', $lAtt);
            } else if ($liwec > 1) {
              $this -> addBtn('act', lan('wec.view'), 'go("index.php?act=job-'.$this -> mSrc.'-fil&sub=wec&src='.$this -> mSrc.'&jobid='.$this -> mJobId.'")', 'img/ico/16/wecview.gif', 'button', $lAtt);
            }
          }
        }
      }
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