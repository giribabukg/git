<?php
/**
 * ToDo: Description
 *
 * @author Geoffrey Emmans <emmans@qbf.de>
 * @package JOB
 * @subpackage TYP
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 6 $
 * @date $Date: 2012-02-21 16:50:56 +0800 (Tue, 21 Feb 2012) $
 * @author $Author: gemmans $
 */
class CInc_Job_Typ_Form extends CJob_Form {

  protected $mJobId;
  protected $mCrpId;
  protected $mSrc = 'typ';

  public function __construct($aSrc, $aAct, $aCrpId = 0, $aJobId = 0, $aJob = NULL, $aPage = 'job') {
    
    $this -> mJobId = $aJobId;
    $lUsr = CCor_Usr::getInstance();
    parent::__construct($this -> mSrc, $aAct, $aPage, $this -> mJobId);
    $this -> mCrpId = $aCrpId;
    echo '<pre>---form.php---';var_dump($aSrc,$this -> mCrpId,'#############');echo '</pre>';
    
    $this -> mFla = 0;
    if (empty($aJob)) {
      if (!empty($this -> mJobId)) {
        $this -> mJob = new CJob_Typ_Dat($this -> mSrc);
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

    // Can User Edit Job by CRP STATUS
    // if NOT set $this->mCanEdit = FALSE
    $this -> canStatusEdit();

    $this -> addPanel('act', lan('lib.actions'), '', 'job.act');
    if ($this -> mCanEdit) {
      $this -> addBtn('act', lan('lib.ok'), '', 'img/ico/16/ok.gif', 'submit', array('class' => 'btn w200' ));
    }
    $this -> addBtn('act', lan('lib.cancel'), 'go("index.php?act=job-".$this -> mSrc)', 'img/ico/16/cancel.gif', 'button', array('class' => 'btn w200' ));
    if (!empty($this -> mJobId)) {
      $this -> addBtn('act', lan('lib.print'), 'pop("index.php?act=job-".$this -> mSrc.".prn&jobid='.$this -> mJobId.'")', 'img/ico/16/print.gif', 'button', array('class' => 'btn w200' ));
    }
    if ($this -> canAssign()) {
      $this -> addBtn('act', lan('job.assignprj'), 'go("index.php?act=job-".$this -> mSrc.".assignprj&jobid='.$this -> mJobId.'")', 'img/ico/16/next-hi.gif', 'button', array('class' => 'btn w200' ));
    }
    // Button Get Project Timing
    if (!empty($this -> mAssignedProId) AND $lUsr->canRead('job.timing-from-prj')){
      $this -> addBtn('act', lan('job.gettimingfrmprj'), 'go("index.php?act=job-'.$this -> mSrc.'.setassignedprodat&jobid='.$this -> mJobId.'&src='.$this -> mSrc.'&proid='.$this -> mAssignedProId.'")', 'img/ico/16/clock_refresh.gif', 'button', array('class' => 'btn w200' ));
    }
    if ($this -> canAssignSkuSub()) {
      $this -> addBtn('act', lan('job.assignskusub'), 'go("index.php?act=job-".$this -> mSrc.".assignskusub&jobid='.$this -> mJobId.'")', 'img/ico/16/next-hi.gif', 'button', array('class' => 'btn w200' ));
    }
    if (!empty($this -> mJobId) AND $lUsr->canInsert('email') ) {
      $this -> addBtn('act', lan('email.notif'), 'go("index.php?act=job-'.$this -> mSrc.'-his.newmail&jobid='.$this -> mJobId.'&src='.$this -> mSrc.'&frm=job")', 'img/ico/16/plus.gif', 'button', array('class' => 'btn w200' ));
    }

    if (!empty($this -> mJobId)) {
      $this -> addPanel('stp', lan('crp-stp.menu'), '', 'job.stp');
#      $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');
#      $this -> mCrpId = $lCrp[$this -> mSrc];

      // Ab welchem Status ist der Job im Archiv
      $lSql = 'SELECT `to_id` FROM `al_crp_step` WHERE `mand`='.MID.' AND `trans` LIKE '.esc("job2arc").' AND `crp_id`='.$this -> mCrpId;
      $lSta2Arc = CCor_Qry::getInt($lSql);
      if (FALSE !== $lSta2Arc) {
        $lSql = 'SELECT `status` FROM `al_crp_status` WHERE `id`='.$lSta2Arc;
        $this -> mWebSta2Arc = CCor_Qry::getInt($lSql); // FALSE oder ein Status
      } else {
        $this -> mWebSta2Arc = FALSE;
      }
      if (FALSE !== $this -> mWebSta2Arc) {$lArchivStatus = $this -> mWebSta2Arc;} else {$lArchivStatus = STATUS_ARCHIV;}

      // Ab welchem Status ist der Job in Produktion (existiert die JobId != A000...)
      $this -> mWebSta2Prod = $lArchivStatus;
      $lSql = 'SELECT `to_id` FROM `al_crp_step` WHERE `mand`='.MID.' AND `trans` LIKE '.esc("anf2job").' AND `crp_id`='.$this -> mCrpId;
      $lQry = new CCor_Qry($lSql);
      foreach ($lQry as $lRow) { // Es kann mehrere Status geben, ab wann eine JobId exisitiert :(
        $lSql = 'SELECT `status` FROM `al_crp_status` WHERE `id`='.$lRow['to_id'];
        $lSta2Prod = CCor_Qry::getInt($lSql);
        #echo '<pre>---form.php---';var_dump('$lSta2Prod=',$lSta2Prod,$lRow['to_id'],'#############');echo '</pre>';
        if (FALSE !== $lSta2Prod) {
          $this -> mWebSta2Prod = ($this -> mWebSta2Prod > $lSta2Prod ? $lSta2Prod : $this -> mWebSta2Prod);
        }
      }
      if ($lArchivStatus === $this -> mWebSta2Prod) {
        $this -> mWebSta2Prod = FALSE;
      }
      $lSta = intval($this -> mJob['webstatus']);
      $lUsr = CCor_Usr::getInstance();
      #echo '<pre>---form.php--$this -> mWebSta2Prod-';var_dump($this -> mWebSta2Prod, $this -> mWebSta2Prod, $lUsr -> canEdit('job.crp.chg'),'#############');echo '</pre>';
      if (FALSE !== $this -> mWebSta2Prod AND $this -> mWebSta2Prod <= $lSta AND $lUsr -> canEdit('job.crp.chg')) {
        $this -> addPanel('chg', lan('crp-stp.menu'), '', 'job.stp');
        $this -> addButton('chg', $this -> getSetStatusButtonMenu());
      }

      $this -> addRoles();
      $this -> addStatusButtons();

      $lShowAplButtons = CCor_Cfg::get('show.form.apl', true);
      echo '<pre>---form.php---';var_dump('=',$lShowAplButtons,'#############');echo '</pre>';
      if ($lShowAplButtons) {
        // Korrekturumlauf Status rausfinden
        $lSql = 'SELECT status FROM al_crp_status WHERE mand='.MID.' AND crp_id='.$this -> mCrpId.' AND apl=1';
        echo '<pre>---form.php---';var_dump($this -> mCrpId,$lSql,'#############');echo '</pre>';
        
        $this -> lAplstatus = CCor_Qry::getInt($lSql);
        $this -> addAplButtons($this -> lAplstatus);
      }

      $this -> addPanel('jfl', lan('jfl.menu'), '', 'job.jfl');
      $this -> addFlagButtons();
    }

    $this -> mTabs = new CJob_Typ_Tabs($this -> mJobId, $aPage);

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

    
    // Button "Webcenter Projekt anlegen"
    if (($lUsr -> canEdit('job-wec-id')) && ($this->mJobId) &&(substr($this->mJobId, 0, 1) != 'A')) {
      $lAtt = array();
      $lAtt['class'] = 'btn w200';
      $this -> addBtn('act', lan('wec.menu'), 'go("index.php?act=job-'.$this -> mSrc.'.wec&src='.$this -> mSrc.'&direct=1&jobid='.$this -> mJobId.'")', 'img/ico/16/ml-2.gif', 'button', $lAtt);
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