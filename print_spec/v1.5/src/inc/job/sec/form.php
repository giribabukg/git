<?php
/**
 * Jobs: Sec - Formular
 *
 *  Description
 *
 * @package    JOB
 * @subpackage    Sec
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 6390 $
 * @date $Date: 2014-11-07 22:30:02 +0800 (Fri, 07 Nov 2014) $
 * @author $Author: jwetherill $
 */
class CInc_Job_Sec_Form extends CJob_Form {

  protected $mJobId;


  public function __construct($aAct, $aJobId = 0, $aJob = NULL, $aPage = 'job') {

    $this -> mJobId = $aJobId;


    error_log('.....CInc_Job_Sec_Form...__construct...$this -> mJobIdddd.....'.var_export($this -> mJobId,true)."\n",3,'logggg.txt');
    error_log('.....CInc_Job_Sec_Form...__construct...$aAct....'.var_export($aAct,true)."\n",3,'logggg.txt');
    parent::__construct('sec', $aAct, $aPage, $this -> mJobId);  //This will call loadTabs() and sets values in $this -> mTemplates and $this -> lDefaultTabs


    $lUsr = CCor_Usr::getInstance();

    $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');
    $this -> mCrpId = $lCrp[$this -> mSrc];
    $this -> mFla = 0;
  //  error_log('.....CInc_Job_Sec_Form...__construct...$aJob.....'.var_export($aJob,true)."\n",3,'logggg.txt');
 //   error_log('.....CInc_Job_Sec_Form...__construct...$this -> mJobId.....'.var_export($this -> mJobId,true)."\n",3,'logggg.txt');
    error_log('.....CInc_Job_Sec_Form.....__construct......@start.....$this -> mDoc.....'.var_export($this -> mDoc,true)."\n",3,'logggg.txt');
    if (empty($aJob)) {
      if (!empty($this -> mJobId)) {
//         error_log('.....CInc_Job_Sec_Form...__construct..in if123.....'."\n",3,'logggg.txt');
        $this -> mJob = new CJob_Sec_Dat();
        $this -> mJob -> load($this -> mJobId); 
        $this -> mFla = $this -> mJob -> getFlags();
      } else {
        $this -> mJob = new CCor_Dat();
      }
    } else {
      $this -> mJob = $aJob;
      if (!empty($this -> mJobId)) {
        error_log('.....CInc_Job_Sec_Form...__construct...$this -> mJob -> getFlags().....'.var_export($this -> mJob -> getFlags(),true)."\n",3,'logggg.txt');
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


    $this -> addBtn('act', lan('lib.cancel'), 'go("index.php?act=job-sec")', 'img/ico/16/cancel.gif', 'button', array('class' => 'btn w200' ));  //This addBtn also sets $this -> mPnl
    if (!empty($this -> mJobId)) {
      $this -> addBtn('act', lan('lib.print'), 'pop("index.php?act=job-sec.prn&jobid='.$this -> mJobId.'")', 'img/ico/16/print.gif', 'button', array('class' => 'btn w200' ));
    }


    if ($this -> canAssign()) {
      $this -> addBtn('act', lan('job.assignprj'), 'go("index.php?act=job-sec.assignprj&jobid='.$this -> mJobId.'")', 'img/ico/16/next-hi.gif', 'button', array('class' => 'btn w200' ));
    }


    // Button Get Project Timing
    if (!empty($this -> mAssignedProId) AND $lUsr->canRead('job.timing-from-prj')){
      $this -> addBtn('act', lan('job.gettimingfrmprj'), 'go("index.php?act=job-'.$this -> mSrc.'.setassignedprodat&jobid='.$this -> mJobId.'&src='.$this -> mSrc.'&proid='.$this -> mAssignedProId.'")', 'img/ico/16/clock_refresh.gif', 'button', array('class' => 'btn w200' ));
    }


    if ($this -> canAssignSkuSub()) {
      $this -> addBtn('act', lan('job.assignskusub'), 'go("index.php?act=job-sec.assignskusub&jobid='.$this -> mJobId.'")', 'img/ico/16/next-hi.gif', 'button', array('class' => 'btn w200' ));
    }


    if (!empty($this -> mJobId) AND $lUsr->canInsert('email') ) {
      $this -> addBtn('act', lan('email.notif'), 'go("index.php?act=job-'.$this -> mSrc.'-his.newmail&jobid='.$this -> mJobId.'&src='.$this -> mSrc.'&frm=job")', 'img/ico/16/email.gif', 'button', array('class' => 'btn w200' ));
    }

    if (!empty($this -> mJobId)) {
      $this -> addPanel('stp', lan('crp-stp.menu'), '', 'job.stp');
      $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');
      $this -> mCrpId = $lCrp[$this -> mSrc];

      // Ab welchem Status ist der Job im Archiv
      $lSql = 'SELECT `to_id` FROM `al_crp_step` WHERE `mand`='.MID.' AND `trans` LIKE '.esc("job2arc").' AND `crp_id`='.$this -> mCrpId;
      $lSta2Arc = CCor_Qry::getInt($lSql);
      if (FALSE !== $lSta2Arc) {
        $lSql = 'SELECT `status` FROM `al_crp_status` WHERE `id`='.$lSta2Arc;
        $this -> mWebSta2Arc = CCor_Qry::getInt($lSql); // FALSE oder ein Status
      } else { $this -> mWebSta2Arc = FALSE; }
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

      #echo '<pre>---form.php--$this -> mWebSta2Prod-';var_dump($this -> mWebSta2Prod, $this -> mWebSta2Prod, $lUsr -> canEdit('job.crp.chg'),'#############');echo '</pre>';
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

      // Korrekturumlauf Status rausfinden

      $this -> addPanel('jfl', lan('jfl.menu'), '', 'job.jfl');
      $this -> addFlagButtons();
    }

    $this -> mTabs = new CJob_Sec_Tabs($this -> mJobId, $aPage);

    $lTemplate = $this -> getTemplates();
    error_log('.....CInc_Job_Sec_Form...__construct...$lTemplate.....'.var_export($lTemplate,true)."\n",3,'logggg.txt');

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
    //error_log('.....CInc_Job_Sec_Form...__construct..$this -> mPag.....'.var_export($this -> mPag,true)."\n",3,'logggg.txt');

    // if (($lUsr -> canEdit('job-wec-id')) && ($this->mJobId) &&(substr($this->mJobId, 0, 1) != 'A')) {
    //   #$this -> addPart('det', 'wec', $this -> mSrc); // kommt über $lMandClass
    //   $lAtt = array();
    //   $lAtt['class'] = 'btn w200';
    //   $this -> addBtn('act', lan('wec.menu'), 'go("index.php?act=job-sec.wec&src='.$this -> mSrc.'&direct=1&jobid='.$this -> mJobId.'")', 'img/ico/16/wec.gif', 'button', $lAtt);
    // }
    //Der Button "Webcenter Viewer"
    //if ($lUsr -> canEdit('wec.view')) { // - Intouch
    // if ($this -> hasRole('per_prj_verantwortlich')){ // S+T
    // if ($this -> hasRole('per_prj_verantwortlich') OR $lUsr -> canEdit('wec.view')) {
    //   if (!empty($this -> mJob['wec_prj_id'])) {
    //     $lAtt = array();
    //     $lAtt['class'] = 'btn w200';
    //     $lRet = array();
    //     $lWec = new CApi_Wec_Client();
    //     $lWec -> loadConfig();
    //     $lQry = new CApi_Wec_Query_Doclist($lWec);
    //     //$lRes = $lQry -> getListByName($this -> mJob['jobnr']);
    //     $lRes = $lQry -> getList($this -> mJob['wec_prj_id']);
    //     if ($lRes) {
    //       $lRet = array();
    //       if (!empty($lRes)) {
    //         $liwec = 0;
    //         $lpid = '';
    //         foreach ($lRes as $lRow) {
    //           if ($lRow['viewer']) {
    //             $liwec++;
    //             $lRet[$lRow['wec_doc_id']] = $lRow['name'];
    //             $lpid = $lRow['projectid'];
    //             $lUrl = 'index.php?act=utl-wec.open';
    //             $lUrl.= '&pid='.$lpid;
    //             $lUrl.= '&doc='.urlencode($lRow['name']);
    //             $lUrl.= '&docid='.urlencode($lRow['wec_ver_id']);
    //           }
    //         }

    //         if ($liwec == 1) {
    //           $this -> addBtn('act', lan('wec.view'), 'go("'.$lUrl.'&src='.$this -> mSrc.'&jid='.$this -> mJobId.'","'.CCor_Cfg::get('wec.view', '').'")', 'img/ico/16/wecview.gif', 'button', $lAtt);
    //         } else if ($liwec > 1) {
    //           $this -> addBtn('act', lan('wec.view'), 'go("index.php?act=job-'.$this -> mSrc.'-fil&sub=wec&src='.$this -> mSrc.'&jobid='.$this -> mJobId.'")', 'img/ico/16/wecview.gif', 'button', $lAtt);
    //         }
    //       }
    //     }
    //   }
    // }
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