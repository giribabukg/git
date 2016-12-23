<?php
/**
 * Jobs: Step
 *
 *  Description
 *
 * @package    JOB
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 13177 $
 * @date $Date: 2016-03-30 20:38:25 +0800 (Wed, 30 Mar 2016) $
 * @author $Author: ahajali $
 */
class CInc_Job_Step extends CCor_Obj {

  protected $mSrc;
  protected $mJobId;
  protected $mJob;
  protected $mDat;
  // There is only different between CopyJobTO And CopyTaskTo
  // that CopyTaskTo copy to Job mit Projectassigment.
  public $mCopyJobTo = ''; // If Event 'Copy_Job' defined, set the target Jobtyp for Copy
  public $mCopyTaskTo = ''; // If Event 'Copy_Task' defined, set the target Jobtyp for Copy
  public $mMoveJobTo = '';
  protected $mStepInfos = array();
  public $mCrpStaDis = array();
  public $mCrpStaPro = array();
  protected $mSetAddUser = false;

  public function __construct($aSrc, $aJobId, $aJob = NULL) {
    $this -> mSrc   = $aSrc;
    $this -> mJobId = $aJobId;

    /* Bevor Webstatus zum 200 wechselt, werden Jobdetails geladen um bei der Copy to Archive zu benutzen. */
#    $this -> mDat = $this -> getDat($this -> mJobId);//Wenn es einen Job gibt, muß er ja nicht erneut geholt werden!
    if (NULL == $aJob) {
      #$this -> loadJob();
      $this -> mJob = new CCor_Dat();
      $this -> mDat = $this -> getDat($this -> mJobId);
    } else {
      $this -> mJob = $aJob;
      $this -> mDat = $this -> mJob;
    }
    #echo '<pre>---step.php---'.get_class().'---';var_dump($this -> mDat,$this -> mJob,'#############');echo '</pre>';

    $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');
    $lCrpId = $lCrp[$this -> mSrc];
    $this -> mCrpStaDis = CCor_Res::extract('status', 'display', 'crp', $lCrpId);
    $this -> mCrpStaPro = CCor_Res::extract('status', 'pro_con', 'crp', $lCrpId);
    $this->mUid = CCor_Usr::getAuthId();
  }
  
  public function setUid($aUid) {
    $this->mUid = $aUid;
  }

  public function setAddUser($aFlag = true) {
    $this -> mSetAddUser = $aFlag;
  }

  public function getSrc() {
    return $this -> mSrc;
  }

  public function getJobId() {
    return $this -> mJobId;
  }

  protected function addHistory($aType, $aSubject, $aMsg = '', $aAdd = '', $aStepId = '', $aFrom = '', $aTo = '') {
    $lHis = new CJob_His($this -> mSrc, $this -> mJobId);
    $lHis->setUser($this->mUid);

    $lSig = 0;
    $lRow = $this -> GetStepInfos($aStepId);
    $lFlags = intval($this->mStepInfos['flags']);
    if (bitset($lFlags, sfSignature)) {
      $lSig = CCor_Usr::getAuthId();
    }
    $lInsertId = $lHis -> add($aType, $aSubject, $aMsg, $aAdd, $aStepId, $aFrom, $aTo, $lSig);
    return $lInsertId;
  }

  public function addMigToHistory() {
    $this -> addHistory(htStatus, 'Migration to Archive');
  }

  public function getVal($aAlias) {
    return $this -> mJob[$aAlias];
  }

  public function getInt($aAlias) {
    return intval($this -> mJob[$aAlias]);
  }

  public function isValidStep($aStepId) {
    $lStp = intval($aStepId);
    $lSql = 'SELECT c.status FROM al_crp_status c,al_crp_step s WHERE 1 AND s.mand='.MID.' ';
    $lSql.= 'AND s.id='.$lStp.' ';
    $lSql.= 'AND s.from_id=c.id';
    $lNow = $this -> getInt('webstatus');
    $lOld = CCor_Qry::getInt($lSql);
    if ($lOld != $lNow) {
      return FALSE;
    }
    return TRUE;
  }

  public function checkRequiredFields($aStepId) {
    $lRet = TRUE;
    $lStp = intval($aStepId);
    $lQry = new CCor_Qry('SELECT f.name_'.LAN.',r.alias FROM al_crp_req r,al_fie f WHERE f.mand='.MID.' AND f.alias=r.alias AND r.step_id='.$lStp);
    foreach ($lQry as $lRow) {
      if (empty($this -> mJob[$lRow['alias']])) {
        $this -> msg('Please fill out the required field '.$lRow['name_'.LAN], mtUser, mlWarn);
        $lRet = FALSE;
      }
    }
    return $lRet;
  }

  public function setTiming($aDisplay, $aTime, $aStepInfo) {
    $lDis = intval($aDisplay);
    $lFti = 'fti_'.$lDis;
    $lLti = 'lti_'.$lDis;
    $lQry = new CCor_Qry('SELECT '.$lFti.' FROM al_job_shadow_'.intval(MID).' WHERE jobid='.esc($this -> mJobId));
    if ($lRow = $lQry -> getDat()) {
      $lSql = 'UPDATE al_job_shadow_'.intval(MID).' SET '.$lLti.'='.esc($aTime);
      $lTim = $lRow[$lFti];
      if (empty($lTim) OR $lTim == '0000-00-00 00:00:00') {
        $lSql.= ','.$lFti.'='.esc($aTime);
      }
      $lSql.= ' WHERE jobid='.esc($this -> mJobId);
      $lQry -> exec($lSql);
      $lUpdateReport = new CJob_Utl_Shadow();
      $lUpdateReport -> setTimingInReportTable($aStepInfo, $aTime, $this -> mJobId);
#      $lUpdateReport -> setTimingInReportTable($aDisplay, $aTime, $this -> mJobId);
    }
  }

  public function TestdoStep($aStepId, $aMsg = '', $aAdd = '', $aIgn = array()) {
    echo 'OK'.BR;
    echo $this -> mSrc. ' '.$this -> mJobId. ' '.'apl'.BR;
    $lApl = new CApp_Apl_Loop($this -> mSrc, $this -> mJobId, 'apl');
    echo $this -> mJobId.BR;
    echo $lApl -> isInAplLoop();
    echo 'HA';
    $lFac = new CJob_Fac($this -> mSrc, $this -> mJobId);
    $lJob = $lFac -> getDat();
    // var_dump($lJob);

    $this -> mJob = $lJob;
    // var_dump($this -> mJob);
      if (CCor_Cfg::get('wec.filestore') != '') {
        // xfdf Dateinen lesen
        $lAnn = new CJob_Apl_Page_Annotations($this -> mJob);

        $lArr = $lApl -> getAddData();
        if (!is_array($lArr)) $lArr = array();
        $lArr['xfdf'] = $lAnn -> getXml();
        $lApl -> setAddData($lArr);

        if (!empty($lArr['xfdf'])) {
          foreach($lArr['xfdf'] as $lKey => $lVal) {
            echo 'KEY:'.$lKey.' -> '.$lVal.BR;
            if (is_array($lVal)) {
              $ln = pathinfo($lVal['name']);
              $lFil = $ln['filename'].'_'.$lApl -> getMaxNum().'.xfdf';
              $lQry = new CApi_Alink_Query('putFile');
              $lQry -> addParam('sid', MAND);
              $lQry -> addParam('jobid', $this -> mJobId);
              $lQry -> addParam('filename', $lFil);
              $lQry -> addParam('data', base64_encode($lVal['xfdf']));
              $lQry -> addParam('mode', 2);
              $lRes = $lQry -> query();
            }
          }
        }
      }
  exit;
  }

  protected function arrayDepth($aArray) {
    if (is_array(reset($aArray))) {
      $lRet = $this -> arrayDepth(reset($aArray)) + 1;
    } else {
      $lRet = 1;
    }

    return $lRet;
  }

  public function doStep($aStepId, $aMsg = '', $aAdd = '', $aIgn = array(), $lMsg_Flag = array(), $aStartApl = FALSE) {
    $lStp = intval($aStepId);
    #echo '<pre>---step.php---'.get_class().'---';var_dump($aMsg,$lMsg_Flag,$aIgn,'#############');echo '</pre>';
    //22651 Project Critical Path Functionality
    $lRow = $this -> GetStepInfos($lStp);
    $lSta = intval($lRow['status']);
    $lFla = intval($lRow['flags']);
    $lControlPro = intval($lRow['pro_con']);
    $lCtrlProOld = intval($lRow['pro_con_old']);

    //--START: TTS-478 XFDF Dateien (Rüdiger)

    $lApl = new CApp_Apl_Loop($this -> mSrc, $this -> mJobId, 'apl', MID, $this -> mDat['webstatus']);
    if ($lApl -> isInAplLoop()) {
      if (CCor_Cfg::get('wec.filestore') != '') {
        // xfdf Dateinen lesen
        $lAnn = new CJob_Apl_Page_Annotations($this -> mDat);

        $lArr = $lApl -> getAddData();
        if (!is_array($lArr)) $lArr = array();
        $lArr['xfdf'] = $lAnn -> getXml();

        $this -> dbg('Update al_job_apl_loop.add_data from XFDF with '.$lArr['xfdf']);
        $lApl -> setAddData($lArr);
        
        $lWriter = CCor_Cfg::get('job.writer.default', 'alink');
        if ($lWriter == 'alink') {
          // xfdf auf PDF Ordner speichern
          if (!empty($lArr['xfdf']) && $this -> arrayDepth($lArr['xfdf']) < 3) {
            foreach($lArr['xfdf'] as $lKey => $lVal) {
              if (is_array($lVal)) {
                $ln = pathinfo($lVal['name']);
                $lFil = $ln['filename'].'_'.$lApl -> getMaxNum().'.xfdf';  // Dateiname + APL-Nummer + xfdf
                $lQry = new CApi_Alink_Query('putFile');
                $lQry -> addParam('sid', MAND);
                $lQry -> addParam('jobid', $this -> mJobId);
                $lQry -> addParam('filename', $lFil);
                $lQry -> addParam('data', base64_encode($lVal['xfdf']));
                $lRes = $lQry -> query();
              }
            }
          } else {
            foreach ($lArr['xfdf'] as $lOuterKey => $lOuterValue) {
              foreach ($lOuterValue as $lInnerKey => $lInnerValue) {
                if (is_array($lInnerValue)) {
                  $ln = pathinfo($lInnerValue['name']);
                  $lFil = $ln['filename'].'_'.$lApl -> getMaxNum().'_'.$lInnerValue['page'].'.xfdf';  // Dateiname + APL-Nummer + Seitennummer + xfdf
                  $lQry = new CApi_Alink_Query('putFile');
                  $lQry -> addParam('sid', MAND);
                  $lQry -> addParam('jobid', $this -> mJobId);
                  $lQry -> addParam('filename', $lFil);
                  $lQry -> addParam('data', base64_encode($lInnerValue['xfdf']));
                  $lRes = $lQry -> query();
                }
              }
            }
  
          }
        }
      }
    }

    //--STOPP: TTS-478

    $lUpd = array(
        'webstatus' => $lSta,
        'last_status_change' => date('Y-m-d H:i:s')
    );

    $lMod = $this -> getMod($this -> mJobId);
    if (bitset($lFla, sfStartApl)) { // wurde schon in der actCnf gestartet. Ein offener Loop wird zuerst geschlossen!
      $lUpd['apl'] =  CApp_Apl_Loop::APL_STATE_UNKNOWN;
    } elseif (bitset($lFla, sfCloseApl)) {
      $lApl -> closeLoops();
    }
    $lRet = $lMod -> forceUpdate($lUpd);
    if ($lRet && bitset($lFla, sfAutomatic)) { //only if auto-status change
      CJob_Utl_Shadow::reflectUpdate($this -> mSrc, $this -> mJobId, $lUpd);
    }
    $this -> dump($lUpd, 'Update-Array 1--------------');

    if ('FROM' == CCor_Cfg::get('ddl.view', 'TO')) {
      $lDisplay = 'from_display';
    } else {
      $lDisplay = 'display';
    }
    $this -> setTiming($lRow[$lDisplay], date('Y-m-d H:i:s'), $lRow);

    if (bitset($lFla, sfAmendDecide)) {
      if (isset($aAdd['amt'])) {
        $lAmt = $aAdd['amt'];
        if (('B' == $lAmt) or ('A' == $lAmt)) {
          $lRes = $this -> insertAk($aMsg);
          if ($lRes) {
            $aAdd['ak'] = $lRes;
          }
        }
      }
    }

    if (bitset($lFla, sfStartApl)) {
      #$lArr = array('src' => $this -> mSrc, 'jid' => $this -> mJobId);
      #CApp_Queue::add('wecstart', $lArr);
    }

    if (bitset($lFla, sfSelectAnnots)) {
      $lLoopId = $lApl ->getLastOpenLoop();
      $lUpdateAmendRoutCauseInReport = new CJob_Utl_Shadow();
      if(isset($aAdd['apl_amendment_cause_1'])){
        $lColumnRoutCause = 'apl_amendment_cause_1';
        $lApl -> setAplAmendRoutCause($lColumnRoutCause, $aAdd[$lColumnRoutCause], $lLoopId);
        $lUpdateAmendRoutCauseInReport->setAmendRoutCause($lColumnRoutCause, $aAdd[$lColumnRoutCause], $this -> mJobId);
      }
      if(isset($aAdd['apl_amendment_cause_2'])){
        $lColumnRoutCause = 'apl_amendment_cause_2';
        $lApl -> setAplAmendRoutCause($lColumnRoutCause, $aAdd[$lColumnRoutCause], $lLoopId);
        $lUpdateAmendRoutCauseInReport->setAmendRoutCause($lColumnRoutCause, $aAdd[$lColumnRoutCause], $this -> mJobId);
      }
      if(isset($aAdd['apl_amendment_cause_3'])){
        $lColumnRoutCause = 'apl_amendment_cause_3';
        $lApl -> setAplAmendRoutCause($lColumnRoutCause, $aAdd[$lColumnRoutCause], $lLoopId);
        $lUpdateAmendRoutCauseInReport->setAmendRoutCause($lColumnRoutCause, $aAdd[$lColumnRoutCause], $this -> mJobId);
      }
    }

    $lFrom = $lRow['from_status'];
    if ($this->mSetAddUser) {
      $lHisInsertId = $this -> addHistory(htComment, '{lan.apl.user}', $aMsg, $aAdd, $lStp, $lFrom, $lSta);
    } else {
      $lHisInsertId = $this -> addHistory(htStatus, $lRow['name_'.LAN], $aMsg, $aAdd, $lStp, $lFrom, $lSta);
    }
    
    if (isset($aAdd['mails_ids']) && !empty($aAdd['mails_ids'])) {
      $this -> addHisIdIntoMails($lHisInsertId, $aAdd['mails_ids']);
    }

    // al_job_sub aktualisieren
    if ('pro' != $this -> mSrc) {
      $lSql = 'UPDATE al_job_sub_'.MID.' SET webstatus="'.$lSta.'" WHERE jobid_'.$this -> mSrc.'="'.$this -> mJobId.'"';
    } else {
      $lSql = 'UPDATE al_job_sub_'.MID.' SET webstatus="'.$lSta.'" WHERE pro_id="'.$this -> mJobId.'"';
    }
    CCor_Qry::exec($lSql);

    $lDraftJobId = $this -> mJobId;
    $this -> dbg('trans: '.$lRow['trans']);
    if ($lRow['trans'] == 'anf2job') {
      $this -> copyAnfToJob();
    } elseif ($lRow['trans'] == 'job2arc') {
      $this -> copyJobToArc($lUpd);
    } elseif ($lRow['trans'] == 'pro2arc') {
      #$this -> copyJobToArc($lUpd);
    } elseif ( !empty($lRow['trans']) AND in_array($this -> mSrc, array('pro','sku'))) {
      // wurde vorher in doStep verarbeitet: in job/pro/step.php
      $this -> finishSub($lRow['trans']);
    }

    //22651 Project Critical Path Functionality
    #if (0 < $lCtrlProOld) {
    //alten Wert 'runterzaehlen', Datum aktualisieren, JobId aktualisieren
    $this -> decrementSub($lDraftJobId, $this -> mJobId, $lCtrlProOld, $lSta);
    #}
    if (0 < $lControlPro) {
      //neuen Wert 'raufzaehlen', Datum aktualisieren, JobId aktualisieren
      $this -> incrementSub($lDraftJobId, $this -> mJobId, $lControlPro, $lSta);
    }

    if (!empty($lRow['event'])) {
      $lDat = $this -> mDat;
      $lDat -> setnewJobId($this -> mJobId);
      $lMsg = array('subject' => $lRow['name_'.LAN], 'body' => $aMsg, 'add' => $aAdd);
      #echo '<pre>---step.php---'.get_class().'---';var_dump($aIgn,'#############');echo '</pre>';
      //ToDo: hier pruefen, ob $aIgn[0] vorhanden ist!!, sonst als array() setzen
      $lIgn = (isset($aIgn[0]) ? $aIgn[0] : array());
      $lEve = new CJob_Event($lRow['event'], $lDat, $lMsg, $lIgn, $lHisInsertId);
      $lMailType = ($aStartApl) ? mailAplInvite : mailJobEvents;
      $lEve -> setMailType($lMailType);
      $lEve -> execute();
      $this -> mCopyJobTo = $lEve -> getCopyJobTo();
      $this -> mCopyTaskTo = $lEve -> getCopyTaskTo();
      $this -> mMoveJobTo = $lEve -> getMoveJobTo();
    }

    // - START: 22784 Flags in CRP
    // Activation
    //-ToDo: Dürfen alle Events verwendet werden??
    $lAllFlags = CCor_Res::get('fla');
    if (!empty($lRow['flag_act'])) {
      $lUpd = array();
      $lDat = $this -> mDat;
      if ($lDraftJobId != $this -> mJobId) {
        $this -> mJob = new CCor_Dat();
        $lDat = $this -> getDat($this -> mJobId);
        $lMod = $this -> getMod($this -> mJobId);
      }
      $lFlag = explode(',', $lRow['flag_act']);
      foreach ($lFlag as $lF) {
        $lFlagEve = $lAllFlags[$lF];
        #echo '<pre>---step.php---'.get_class().'---';var_dump($lMsg_Flag[$lF],'#############');echo '</pre>';
        $lMsg = array(
            'subject' => lan('flag.activate').': '.$lFlagEve['name_'.LAN],
            'body' => $lMsg_Flag[$lF]
            #'body' => lan('flag.activate').': '.$lFlagEve['name_'.LAN].LF.$lMsg_Flag[$lF]
        );
        $this -> addHistory(htFlags + flEve_act, $lMsg['subject'], $lMsg['body'], '', $lStp, $lFrom, $lF);
        $lFlagId = $lFlagEve['id'];
        #echo '<pre>---step.php---'.get_class().'---';var_dump($aIgn[$lF],'#############');echo '</pre>';
        $lIgn = (isset($aIgn[$lF]) ? $aIgn[$lF] : array());
        $lEve = new CJob_Event($lFlagEve['eve_act'], $lDat, $lMsg, $lIgn, $lHisInsertId);
        $lEve -> setMailType(mailJobEvents);
        #echo '<pre>---step.php---'.get_class().'---';var_dump($lFlagEve,'#############');echo '</pre>';
        $lEve -> addFlagItems($lFlagId, $lStp);

        $lEve -> execute();
        if (empty($this -> mCopyTaskTo)) {
          $this -> mCopyTaskTo = $lEve -> getCopyTaskTo();
        }
        if (empty($this -> mCopyJobTo)) {
          $this -> mCopyJobTo = $lEve -> getCopyJobTo();
        }
        $lUpd[ $lFlagEve['alias'] ] = FLAG_STATE_ACTIVATE;
      }
      if (!empty($lUpd)) {
        $lMod -> forceUpdate($lUpd);
      }
      $this -> dump($lUpd, 'Update-Array 2--------------');

      // $this -> copyAnfToJob() kann das nicht erledigen!
      $lSql = 'UPDATE al_job_apl_loop SET jobid='.esc($this -> mJobId);
      $lSql.= ' WHERE jobid='.esc($lDraftJobId).' AND typ!=0 AND mand='.intval(MID).';';
      CCor_Qry::exec($lSql);
    }
    // - ENDE: 22784 Flags in CRP


    // darf es NICHT geben, die steps mandantenabhängig unterschiedlich sind!
    //$lFnc = 'doStep'.$lStp;
    //  if ($this -> hasMethod($lFnc)) {
    //    $this -> $lFnc($aMsg, $aAdd);
    //  }

    if (bitset($lFla, sfAmendDecide)) {
      // Back to Production
      $this -> incAmend($aMsg, $aAdd);

      // wurde vorher in onStatus verarbeitet, d.h. der erste Aufruf fehlt jetzt !!!
      if (in_array($this -> mSrc, array('art', 'rep'))) {
        CJob_Utl_Qty::addJob($this -> mSrc, $this -> mJobId, $lSta);
      }
    }

    // darf es NICHT geben, die status mandantenabhängig unterschiedlich sind!
    //$lFnc = 'onStatus'.$lSta;
    //  if ($this -> hasMethod($lFnc)) {
    //    $this -> $lFnc();
    //  }

    return TRUE;
  }
  
  public function doStepIndependent($aStepId, $aMsg = '', $aAdd = '', $aIgn = array()) {
    $lStp = intval($aStepId);

    $lSql = 'SELECT name_'.LAN.' AS name,event FROM al_crp_step WHERE id='.$lStp;
    $lQry = new CCor_Qry($lSql);
    $lRow = $lQry->getDat();
  
    $lHisInsertId = $this -> addHistory(htStatus, $lRow['name'], $aMsg, $aAdd, $lStp);
  
    if (!empty($lRow['event'])) {
      $lDat = $this -> mDat;
      $lIgn = (isset($aIgn[0]) ? $aIgn[0] : array());
      $lMsg = array('subject' => $lRow['name'], 'body' => $aMsg, 'add' => $aAdd);

      $lEve = new CJob_Event($lRow['event'], $lDat, $lMsg, $lIgn, $lHisInsertId);
      $lEve -> setMailType(mailJobEvents);
      $lEve -> execute();
    }
    return TRUE;
  }
  
  protected function addHisIdIntoMails($aHisId, $aMailsIds) {
    if (empty($aHisId)) return;
    if (empty($aMailsIds)) return;
    $lMails = implode(',', $aMailsIds);
    $lSql = 'UPDATE `al_sys_mails` SET `his_id`='.$aHisId.' WHERE  `id` IN ('.$lMails.');';
    CCor_Qry::exec($lSql);
  }

  //Die SQL-Abfrage wird an 2 Stellen genutzt
  public function GetStepInfos($aStepId) {
    $lSql = '';
    if (empty($this -> mStepInfos)) {
      $lSql.= 'SELECT c.status,c.display,c.report_map AS report_to, c2.report_map AS report_from,c2.status AS from_status,c2.display AS from_display,s.name_'.LAN;
      $lSql.= ',s.trans,s.event,s.flags,s.flag_act,s.flag_stp,c.pro_con,c2.pro_con as pro_con_old';
      $lSql.= ' FROM al_crp_status c, al_crp_status c2, al_crp_step s';
      $lSql.= ' WHERE s.id='.$aStepId.' AND c.id=s.to_id AND c2.id=s.from_id';
      $lQry = new CCor_Qry($lSql);
      $this -> mStepInfos = $lQry -> getDat();
    }
    #echo '<pre>---step.php---GetStepInfos'.get_class().'---';var_dump($lSql,$this -> mStepInfos,'#############');echo '</pre>';
    return $this -> mStepInfos;
  }

  public function ConfirmFlag($aConfFlag = '', $lCap = '', $aMsg = '', $aWebstatus = '') {
    //-ToDo: CConfirmFlag
    $lRet = FALSE;
    if (!empty($aConfFlag)) {
      $lAllFlags = CCor_Res::get('fla');
      $lUpd = array();
      $lDat = $this -> mDat;
      $lFlag = explode(',', $aConfFlag);
      foreach ($lFlag as $lF) {
        $lFlagEve = $lAllFlags[$lF];
        $lMsg = array(
                'subject' => lan('flag.confirm').': '.$lFlagEve['name_'.LAN],
                'body' => $aMsg
        );
        $lHisInsertId = $this -> addHistory(htFlags + flEve_conf, lan('flag.confirm').': '.$lFlagEve['name_'.LAN].' '.$lCap, $aMsg, '', 0, $aWebstatus, $lF);
        $lFlagId = $lFlagEve['id'];
        $lEve = new CJob_Event($lFlagEve['eve_conf'], $lDat, $lMsg, array(), $lHisInsertId);
        $lEve -> setMailType(mailJobEvents);
        $lEve -> execute();
        if (empty($this -> mCopyTaskTo)) {
          $this -> mCopyTaskTo = $lEve -> getCopyTaskTo();
        }
        if (empty($this -> mCopyJobTo)) {
          $this -> mCopyJobTo = $lEve -> getCopyJobTo();
        }
        $IsConf = CApp_Apl_Loop::isFlagConfirmed($this -> mSrc, $this -> mJobId, $lFlagId);
        #echo '<pre>---step.php---'.get_class().'---';var_dump($this -> mSrc, $this -> mJobId, $lFlagId,$IsConf,CApp_Apl_Loop::isFlagConfirmed($this -> mSrc, $this -> mJobId, $lFlagId),'#############');echo '</pre>';
        if ($IsConf) {
          $lUpd[ $lFlagEve['alias'] ] = FLAG_STATE_CONFIRMED;
        }
      }
      if (!empty($lUpd)) {
        $lMod = $this -> getMod($this -> mJobId);
        $lMod -> forceUpdate($lUpd);
      }
      $lRet = TRUE;
    }
    return $lRet;
  }

  public function StopFlags($aStpId) {//-ToDo: CheckMandatoryFlags
    $lNoStop = TRUE;
    $lStp = intval($aStpId);
    $lRow = $this -> GetStepInfos($lStp);
    #echo '<pre>---step.php---'.get_class().'---';var_dump($lRow,'#############');echo '</pre>';exit;

    $lFrom = $lRow['from_status'];

    // - START: 22784 Flags in CRP
    // Deactivation
    $lAllFlags = CCor_Res::get('fla');

    if (!empty($lRow['flag_stp'])) {

      $lFlagConfirmed = CApp_Apl_Loop::isFlagConfirmed($this -> mSrc, $this -> mJobId);

      $lDat = $this -> mDat;
      $lFlag = explode(',', $lRow['flag_stp']);
      $lPflichtFlags = array();
      foreach ($lFlag as $lF) {
        $lFlagEve = $lAllFlags[$lF];
        if ( bitset($lFlagEve['flags_conf'], flagMandatory) ) {// stoppe den CRP-Step!
          #echo '<pre>---step.php---'.get_class().'---';var_dump( $lF,'#############');echo '</pre>';
          if (isset($lFlagConfirmed[$lF]) AND !$lFlagConfirmed[$lF]) { //Flag is confirmed => doch kein STOP

            $lNoStop = FALSE;
            $lPflichtFlags[] = $lF;
            $this -> dbg($lFlagEve['name_'.LAN].' is mandatory!',mlWarn);
          }
        }
      }
      #echo '<pre>---step.php---'.get_class().'---';var_dump($lNoStop,$lFlagConfirmed,$lPflichtFlags,'#############');echo '</pre>';

      if ($lNoStop) {
        $lUpd = array();
        foreach ($lFlag as $lF) {
          //-ToDo: in apl_loop or states 'closen'
          $lFlagEve = $lAllFlags[$lF];
          $lMsg = array(
                  'subject' => lan('flag.deactiv').': '.$lFlagEve['name_'.LAN],
                  'body' => lan('flag.deactiv').': '.$lFlagEve['name_'.LAN]
          );
          $lHisInsertId = $this -> addHistory(htFlags + flEve_act, lan('flag.deactiv').': '.$lFlagEve['name_'.LAN], '', '', $lStp, $lF);
          $lFlagId = $lFlagEve['id'];
          $lEve = new CJob_Event($lFlagEve['eve_conf'], $lDat, $lMsg, '', $lHisInsertId);
          $lEve -> setMailType(mailJobEvents);
          $lEve -> closeFlags($lFlagId, $lStp);

          $lUpd[ $lFlagEve['alias'] ] = FLAG_STATE_CLOSED;

        }
        if (!empty($lUpd)) {
          $lMod = $this -> getMod($this -> mJobId);
          $lMod -> forceUpdate($lUpd);
        }

      } else {
        foreach ($lPflichtFlags as $lF) {
          $lFlagEve = $lAllFlags[$lF];
          if (0 < $lFlagEve['eve_mand']) {
            $lMsg = array(
                  'subject' => lan('flag.mandatory').': '.$lFlagEve['name_'.LAN],
                  'body' => lan('flag.mandatory').': '.$lFlagEve['name_'.LAN]
            );
            $lHisInsertId = $this -> addHistory(htStatus, lan('flag.mandatory').': '.$lFlagEve['name_'.LAN], '', '', $lStp, $lFrom);
            $lFlagId = $lFlagEve['id'];
            $lEve = new CJob_Event($lFlagEve['eve_mand'], $lDat, $lMsg, '', $lHisInsertId);
            $lEve -> setMailType(mailJobEvents);
            #$lEve -> addFlagItems($lFlagId, $lStp); // no new Invitation
            $lEve -> execute();
            if (empty($this -> mCopyTaskTo)) {
              $this -> mCopyTaskTo = $lEve -> getCopyTaskTo();
            }
            if (empty($this -> mCopyJobTo)) {
              $this -> mCopyJobTo = $lEve -> getCopyJobTo();
            }
          }
        }
      }
    }
    // - ENDE: 22784 Flags in CRP

    return $lNoStop;
  }

  //22651 Project Critical Path Functionality - Project CRP depends on job CRP:
  protected function decrementSub ($aDraftJobId, $aJobId, $aDecrementProStatus, $aJobWebstatus) {//($aIncrementProStatus, $aProId, $aSubId){
   $lSql = 'UPDATE al_job_pro_crp SET pro_status=0';
   if (0 < $aDecrementProStatus) {
     $lSql.= ', lti_'.$aDecrementProStatus.'='.esc(date('Y-m-d H:i:s'));
   }
   if ($aDraftJobId != $aJobId) {
     $lSql.= ',jobid='.esc($aJobId);
   }
   $lSql.= ',job_status='.esc($this -> mCrpStaDis[$aJobWebstatus]);
   $lSql.= ' WHERE mand='.MID.' AND src='.esc($this -> getSrc()).' AND jobid IN ('.esc($aDraftJobId).','.esc($aJobId).')';
   CCor_Qry::exec($lSql);
  }

  protected function incrementSub ($aDraftJobId, $aJobId, $aIncrementProStatus, $aJobWebstatus) {//($aIncrementProStatus, $aProId, $aSubId){
   $lSql = 'UPDATE al_job_pro_crp SET pro_status='.$aIncrementProStatus.', fti_'.$aIncrementProStatus.'='.esc(date('Y-m-d H:i:s'));
   if ($aDraftJobId != $aJobId) {
     $lSql.= ',jobid='.esc($aJobId);
   }
   $lSql.= ',job_status='.esc($this -> mCrpStaDis[$aJobWebstatus]);
   $lSql.= ' WHERE mand='.MID.' AND src='.esc($this -> getSrc()).' AND jobid IN ('.esc($aDraftJobId).','.esc($aJobId).')';
   CCor_Qry::exec($lSql);
  }

  public static function incJobToProCrpStatus($aSrc, $aJobId, $aStatus) {
    $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');
    $lCrpId = $lCrp[$aSrc];
    if (empty($lCrpId)) return;

    $lCrpStaPro = CCor_Res::extract('status', 'pro_con', 'crp', $lCrpId);
    if (!isset($lCrpStaPro[$aStatus])) return;

    $lStatus = $lCrpStaPro[$aStatus];
    $lIncStatus = $lStatus['pro_con'];

    $lSql = 'UPDATE al_job_pro_crp SET ';
    $lSql.= 'pro_status='.$lIncStatus.', ';
    $lSql.= 'fti_'.$lIncStatus.'='.esc(date('Y-m-d H:i:s'));
    $lSql.= ' WHERE mand='.MID;
    $lSql.= ' AND src='.esc($aSrc);
    $lSql.= ' AND jobid='.esc($aJobId);
    CCor_Qry::exec($lSql);
  }

  public function copyAnfToJob() {
    // Sonderfunktion unter CUST 75, dort wird eine Networkerbedingung bewußt überschrieben!
    $lOld = $this -> mJobId;
    $lMod = $this -> getMod($this -> mJobId);

    $lJid = (string)$lMod -> copyAnfToJob($this -> mJobId);
    if (!empty($lJid)) {
      $this -> dbg('copied: '.$lJid);
      $this -> mJobId = $lJid;
      $this -> onCopy($lOld, $lJid);

      $lUpd = array('last_status_change' => date('Y-m-d H:i:s'));
      $lMod = $this -> getMod($this -> mJobId);
      $lMod -> forceUpdate($lUpd);
    }
  }

  protected function getDat($aJobId) {
    return FALSE;
  }

  /**
   * @param $aUpd Jobfields to Update
   *
   * */
  public function copyJobToArc($aUpd = array()) {
    $lDat = $this-> mDat;
    $lJobId = $lDat-> getId();

    $lQry = new CCor_Qry();
    if(CCor_Cfg::get('xchange.available')){
      //update xchange table with status of archived
      $lSql = 'UPDATE al_xchange_jobs_'.intval(MID).' SET x_status='.esc("archived").' ';
      $lSql.= 'WHERE x_jobid='.esc($lJobId).';';
      $lSql = strip($lSql);
      $lQry -> query($lSql);
    }
    
    $lSql = 'UPDATE al_job_shadow_'.MID.' SET `webstatus`='.$aUpd['webstatus'].' WHERE `jobid`='.esc($lJobId).' LIMIT 1;';
    CCor_Qry::exec($lSql);
    if (CCor_Cfg::get('extended.reporting')) {
      $lSql = 'UPDATE al_job_shadow_'.MID.'_report SET `webstatus`='.$aUpd['webstatus'].' WHERE `jobid`='.esc($lJobId).' ORDER BY row_id DESC LIMIT 1;';
      CCor_Qry::exec($lSql);
    }

    // exiting archive tabel columns
    $lExistingColumns = array();
    if (!(empty($lJobId))) {
      $lArr = $lDat -> toArray();
      if (!empty($aUpd)) {
        foreach ($aUpd as $lAli => $lVal) {
          $lArr[$lAli] = $lVal;
        }
      }
      if (empty($lArr['src'])) {
        $lArr['src'] = $this -> getSrc();
      }
      $lArr['flags'] = $lDat -> getFlags();

      // Get existing columns from archive table
      $lTabelColumns = new CCor_Qry('SHOW COLUMNS FROM al_job_arc_'.MID);
      $lConvertDatetime = array();
      foreach ($lTabelColumns as $lRow) {
        if ('datetime' == $lRow['Type']) {
          $lConvertDatetime[$lRow -> Field] = TRUE;
        }
        $lExistingColumns[] = $lRow -> Field;
      }

      $lSql = 'INSERT INTO al_job_arc_'.MID.' SET ';
      foreach ($lArr as $lKey => $lVal) {
        if (!empty($lVal)) {
          // Ask if the column exists.
          if (in_array($lKey, $lExistingColumns)) {
            if (isset($lConvertDatetime[$lKey])) {
              $lTime = strtotime($lVal);
              $lVal = date('Y-m-d H:i:s', $lTime);
            }
            $lSql.= $lKey.'='.esc($lVal).',';
          }
        }
      }
      $lRet = FALSE;
      $lSql = strip($lSql);
      $lRet = $lQry -> query($lSql);
      $lWriter = CCor_Cfg::get('job.writer.default', 'alink');
      if ($lRet && $lWriter == 'portal') {
        $lSql = 'DELETE FROM `al_job_'.$this -> mSrc.'_'.MID.'` WHERE  `jobid`='.esc($lJobId).' LIMIT 1;';
        CCor_Qry::exec($lSql);
      }
      return $lRet;
    } else {
      return FALSE;
    }
  }

  protected function insertAk($aMsg) {
    return '';
    /*
    $lQry = new CApi_Alink_Query_Insertak($this -> mJobId, 'A', $aMsg);
    $lRes = $lQry -> query();
    $lJid = (string)$lRes -> getVal('jobid');
    return $lJid;
    */
  }

  protected function onCopy($aOldJobId, $aNewJobId) {
    $lOld = $aOldJobId;
    $lNew = $aNewJobId;

    $lQry = new CCor_Qry();

    $lSql = 'UPDATE al_job_sub_'.intval(MID).' SET jobid_'.$this -> mSrc.'='.esc($lNew).' ';
    $lSql.= 'WHERE jobid_'.$this -> mSrc.'='.esc($lOld).';';
    $lQry -> query($lSql);

    $lSql = 'UPDATE al_usr_recent SET jobid='.esc($lNew).' ';
    $lSql.= 'WHERE jobid='.esc($lOld).';';
    $lQry -> query($lSql);

    $lSql = 'UPDATE al_usr_bookmark SET jobid='.esc($lNew).' ';
    $lSql.= 'WHERE jobid='.esc($lOld).';';
    $lQry -> query($lSql);

    $lSql = 'UPDATE al_job_his SET src_id='.esc($lNew).' ';
    $lSql.= 'WHERE src_id='.esc($lOld).' AND mand='.intval(MID).';';
    $lQry -> query($lSql);

    $lSql = 'UPDATE al_job_files SET jobid='.esc($lNew).' ';
    $lSql.= 'WHERE jobid='.esc($lOld).';';
    $lQry -> query($lSql);

    $lCfg = CCor_Cfg::getInstance();
    $lOld_Files = $lCfg -> get('file.dir').'job'.DS.$this -> mSrc.DS.$lOld;
    if (file_exists($lOld_Files)) {
      $lNew_Files = $lCfg -> get('file.dir').'job'.DS.$this -> mSrc.DS.$lNew;
      rename($lOld_Files, $lNew_Files);
    }

    $lSql = 'UPDATE al_job_shadow_'.intval(MID).' SET jobid='.esc($lNew).' ';
    $lSql.= 'WHERE jobid='.esc($lOld).';';
    $lQry -> query($lSql);

    $lSql = 'UPDATE al_job_apl_loop SET jobid='.esc($lNew);//Deactivate Event ist bereits ausgefuehrt, activate noch nicht!
    $lSql.= ' WHERE jobid='.esc($lOld).' AND typ!=0 AND mand='.intval(MID).';';
    $lQry -> query($lSql);

    $lSql = 'UPDATE al_job_sku_sub_'.intval(MID).' SET job_id='.esc($lNew).' ';
    $lSql.= 'WHERE job_id='.esc($lOld).';';
    $lQry -> query($lSql);

    if (CCor_Cfg::get('extended.reporting')) {
    	$lSql = 'UPDATE al_job_shadow_'.intval(MID).'_report SET jobid='.esc($lNew).' ';
    	$lSql.= 'WHERE jobid='.esc($lOld).';';
    	$lQry -> query($lSql);
    }

    if(CCor_Cfg::get('xchange.available')){
    	$lSql = 'UPDATE al_xchange_jobs_'.intval(MID).' SET x_jobid='.esc($lNew).' ';
    	$lSql.= 'WHERE x_jobid='.esc($lOld).';';
    	$lQry -> query($lSql);
    }

    $lFin = new CApp_Finder($this -> mSrc, $aOldJobId);
    $lOld = $lFin -> getPath('doc');
    $lBas = $lFin -> getPath();

    if (file_exists($lOld)) {

      $lFin = new CApp_Finder($this -> mSrc, $aNewJobId);
      $lFin -> makeDir($lFin -> getPath());
      $lNew = $lFin -> getPath('doc');

      if (rename($lOld, $lNew)) {
        rmdir($lBas);
      }
    }

    //if Webcenter Jobtyp (wec.jobs), save in sys_queue to create Webcenter
    $lWecSrc = Array();
    $lWecSrc = CCor_Cfg::get('wec.jobs');
    if (empty($lWecSrc)){
      $this->dbg('Configuration variable wec.jobs missing',mlError);
    } else {
      if (in_array($this ->mSrc , $lWecSrc)){
        $this->createWebcenterPrj($aOldJobId,$aNewJobId);
        $this ->dbg('WebcenterProject is registered',mlInfo);
      } else {
        $this->dbg('For this Jobtype was no WebcenterPrj provided',mlInfo);
      }
    }
    // End. if Jobtyp is for Webcenter, write sys queue to create Webcenter

  }

  protected function incAmend($aMsg, $aAdd) {
    $lArr = array();
    $lArr['amend_count'] = 'amend_count+1';
    if (isset($aAdd['amt'])) {
      $lTyp = $aAdd['amt'];
      # TODO: switch amendmend type, increase amend_internal etc.
    }
    $lSql = 'UPDATE al_job_shadow_'.intval(MID).' SET ';
    foreach ($lArr as $lKey => $lVal) {
      $lSql.= $lKey.'='.$lVal.',';
    }
    $lSql = strip($lSql).' WHERE jobid='.esc($this -> mJobId);
    CCor_Qry::exec($lSql);
  }

  public function doExceptionStep($lSta, $lSub = '', $lMsg = '') {
    $lFrom = $this -> mDat['webstatus'];

    $lUpd = array('webstatus' => $lSta, 'last_status_change' => date('Y-m-d H:i:s'));
    $lMod = $this -> getMod($this -> mJobId);
    # $this -> dbg($lUpd);
    if ( $lMod -> forceUpdate($lUpd) ) {
      $this -> dbg($lUpd);
      $this -> addHistory(htStatus, $lSub, $lMsg, 0, 0, $lFrom, $lSta);
    }

    //$lStp = intval($aStepId);//kenne ich nicht, da ausser der Reihe!
    $lSql = 'SELECT * FROM al_job_pro_crp WHERE mand='.MID.' AND jobid='.esc($this -> mJobId).' AND src='.esc($this -> getSrc()); //alle ProIds
    #echo '<pre>---crp.php---'.get_class().'---';var_dump($lSql,'#############');echo '</pre>';
    $lQry = new CCor_Qry($lSql);
    $lRes = $lQry -> getAssoc();

    $lControlPro = intval($this -> mCrpStaPro[$lSta]);
    $lCtrlProOld = intval($lRes['pro_status']);
    //alten Wert 'runterzaehlen', Datum aktualisieren, JobId aktualisieren
    $this -> decrementSub($this -> mJobId, $this -> mJobId, $lCtrlProOld, $lSta);

    if (0 < $lControlPro) {
      //neuen Wert 'raufzaehlen', Datum aktualisieren, JobId aktualisieren
      $this -> incrementSub($this -> mJobId, $this -> mJobId, $lControlPro, $lSta);
    }

  }

  /**
   * Register 'wecprj' in Queue To Create Webcenter Projekt.
   * @param $aOldJobId int Old JobId by Copy
   * @param $aNewJobId int New JobId by Copy
   * */
  protected function createWebcenterPrj($aOldJobId,$aNewJobId){
    $lWecArr = array();
    $lWecArr['jid'] = (string)$aNewJobId;
    $lWecArr['src'] = $this -> mSrc;
    $lWecArr['name'] = intval($aNewJobId);
    $lWecArr['tpl']  = CApi_Wec_WebcenterTemplate::getTemplate($aOldJobId);

    CApp_Queue::add('wecprj', $lWecArr);

  }

  public function getCopyJobTo() {
    $lRet = $this -> mCopyJobTo;
    return $lRet;
  }

  public function getCopyTaskTo() {
    $lRet = $this -> mCopyTaskTo;
    return $lRet;
  }
  
  public function getMoveJobTo() {
    $lRet = $this -> mMoveJobTo;
    return $lRet;
  }
  }