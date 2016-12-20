<?php
class CInc_Job_Dialog extends CHtm_Form {

  protected  $mUserFiles;
  protected  $mUpload;
  // "JobId", die ueber die URL uebertragen wird '&jobid='
  protected  $mIdField = 'jobid';
  protected  $mDbIdField = 'jobid';


  /**
   * $mAddUser = TRUE, If it si not new APL but Add User to opened APL
   */
  public     $mAddUser = FALSE;

  /**
   * It is TRUE,If one element of $mAplNotifyArr is TRUE
   */
  public     $mApl_Notify;

  /**
   * If Invited Alias is a Group, Add to Array 'mApl_Rol2GruId'
   */
  public     $mApl_Rol2GruId;

  /**
   * It is TRUE, IF event email_apl AND APL.
   */
  public      $mAplNotif;

  /**
   * only !empty if bitset($this -> mFla, sfStartApl)
   * if evenet email_rol AND APL, mAplNotifyArr['rol'] = TRUE
   * if evenet email_usr AND APL, mAplNotifyArr['usr'] = TRUE
   *
   */
  public     $mAplNotifyArr = array();

  /**
   * All User
   */
  public     $mArrUsr;

  /**
   * Apl User and GroupList on the right side.
   */
  public     $mAplSrc;

   /**
   * If mDialogStep = 2, one step more in dialog.
   */
  public     $mDialogStep;

   /**
   * Invited Apl User and GroupList on the left side.
   */
  public     $mDst;

  /**#@+
   * @var array
   */
  public     $mDurationArr     = array();
  public     $mDurationInDates = array();

  /**
   * @var integer
   */
  public     $mDurationTime = 1;
  /*

   * @var string
   */
  /**
   * if event ist 'email_apl', combinition of ActId.TemplId.Position
   */
  public     $mEmailInfo_Apl;

  /**
   * if event ist 'email_apl', set position
   */
  public     $mEmailInfo_AplPos;
  // Chosen User or Groups to APL. Right Selectbox.
  public     $mInvite;

  /**
   * APL Flag. If True, it is APL.
   */
  public     $mIsApl = false;

  /**
   * Already Invited User and Grouplist
   */
  public     $mOpenLoopInvitedUser = array();

  /**
   * Email to Role, Invited Roles Alias
   */
  public     $mNoActiveRolArrAli;

  /**
   * CRP Step Infos
   */
  public     $mStpDef;

  /**
   * CRP Step Id
   */
  public     $mStepId;

  protected $mPrependForm = '';
  protected $mAppendForm  = '';

  /**
   * CRP Step Flag
   */
  public     $mFla;

  // Is this an APL where Templates have to be selected?
  protected $mIsCountryApl = false;
  protected $mCountryAplField = 'country';
  protected $mCanDeselectCountry = false;

  public function __construct($aSrc, $aJobId, $aStepId, $aJob = NULL, $aAddUser = FALSE, $aAplType='apl') {

    $this -> mSrc = $aSrc;
    $this -> mAplType = $aAplType;
    $this -> mJobId = $aJobId;
    $this -> mJob = $aJob;  // enthält Werte für die Auftragsfelder: $this -> mFac
    $this -> mAddUser = $aAddUser;

    if ($this -> mAddUser){
      // Get Last Open LoopId
      // Get already invited user and Grouplist.
      $this -> mLastOpenLoopId = $this -> getLastOpenLoop();
      if ($this -> mLastOpenLoopId != ''){
        // Get already invited user and Grouplist.
        $this -> mOpenLoopInvitedUser = $this -> getOpenLoopInvitedUser($this -> mLastOpenLoopId);
      }
    }

    $this -> mUpload = '';
    $this -> mUserFiles = '';
    # echo '<pre>---dialog.php---';var_dump($this -> mJob,'#############');echo '</pre>';
    $this -> mStepId = intval($aStepId);

    $lReq = new CCor_Req();
    $lReq -> loadRequest();
    $this -> mDialogStep = $lReq -> getInt('dialog');// TTS-481 Dialog wird zweimal aufgerufen.
    $this -> mNoChoice = $lReq -> getInt('not', -1);// TTS-481 in DropDown nichts ausgewählt
    // Chosed User or Groups to APL.
    $lInvite = $lReq -> getVal('dst');
    if (empty($lInvite)) {
      $this -> mInvite = array();
    } else {
      $this -> mInvite = $lInvite;
    }

    //$lQry = new CCor_Qry('SELECT * FROM al_crp_step WHERE mand='.MID.' AND id='.$this -> mStepId);
    $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');
    $this -> mCrpId = $lCrp[$this -> mSrc];
    $lCrpSteps = CCor_Res::get('crpstep', $this -> mCrpId);
    $lCrpStep = $lCrpSteps[$this -> mStepId];
    $lRow = $lCrpStep; // $lQry -> getDat();

    // Caption
    $lCap = $lRow['name_'.LAN];
    $this -> mFla = intval($lRow['flags']);

    if (bitset($this -> mFla, sfComment) AND bitset($this -> mFla, sfStartApl)) {
      $this -> mIsApl = true;
      $this -> setParam('apl', TRUE);
    }
    $this -> mStpDef = $lRow;

    parent::__construct('job-'.$aSrc.'.cnf', $lCap, 'job-'.$aSrc.'.edt&'.$this -> getIdField().'='.$this -> mJobId);
    $this -> setAtt('style', 'width:800px');

    $this -> setParam($this -> getIdField(), $this -> mJobId);
    $this -> setParam('sid', $this -> mStepId); // NICHT gleich der sid's weiter unten aus al_eve_act!!!
    $this -> setParam('webstatus', $lReq -> getInt('webstatus'));

    if ($this ->mAddUser){
      $this -> setParam('addUser', true);
    }

    $this -> mArrUsr    = CCor_Res::get('usr');
    $this -> mArrFie    = CCor_Res::extract('alias', 'name_'.LAN, 'fie');
    $this -> mArrFieTyp = CCor_Res::extract('alias', 'typ', 'fie');

    #$this -> mRolNotif = FALSE;//old, new = $this -> mAplNotifyArr['rol']
    $this -> mAplNotifyArr = array();// only !empty if bitset($this -> mFla, sfStartApl)
    $this -> mAplNotifyArr['rol'] = FALSE;
    $this -> mAplNotifyArr['gru'] = FALSE;
    $this -> mAplNotifyArr['usr'] = FALSE;
    $this -> mApl_Notify = FALSE;
    $this -> mAplNotif = FALSE;
    $this -> mApl_Rol2GruId  = array();
    $this -> mNotWithAplNotifications = true;
    $this -> mEmailInfo  = array(0 => ' '); // fuer's DropDown
    $this -> mEmailInfo_Apl = -1;

    $this -> mNot = array();

    if (bitset($this -> mFla, sfComment)) {
      $this -> loadNotifications();
      $this -> loadUpload();

      foreach ($this -> mAplNotifyArr as $lEmail_Eve) {
        if ($lEmail_Eve) {
          $this -> mApl_Notify = TRUE;
          break;
        }
      }

      if ($this -> mApl_Notify) {
        //only if bitset($this -> mFla, sfStartApl)
        // APL: Neues Sprungziel, da die ausgewählten User den Rollen zugeordnet werden müsssen
        if (empty($this -> mDialogStep)) {
          $this -> setParam('act', 'job-'.$this -> mSrc.'.step');
          $this -> setParam('dialog', 2);
        } else { // echo 'Dialog: Step 2';
          if ($this -> mAplNotif) {
            $this -> mEmailInfo[$this -> mEmailInfo_Apl] = lan('email2Apl');//==Auswahl des Templates für email_apl
          }
        }
      }

      $this -> showCommentBox();

      if (bitset($this -> mFla, sfAmendDecide)) {
        $this -> addDef(fie('amt', lan('job.amend.type'), 'tselect', array('dom' => 'amt')));
        $this -> setVal('amt', 'A');
        $this -> addDef(fie('cause', lan('job.amend.root'), 'memo', NULL, array('style' => 'width:500px;', 'rows' => '18')));
      }

      /**
       * JobId: 23398
       * Define a rout cause for amendments
       * Additional selection field with a helptable behind, to define route cause.
       */
      if (bitset($this -> mFla, sfSelectAnnots)) {
        $lArrRoutCauseDomain = CCor_Cfg::get('apl.amendment.causes',Array());
        if (!empty($lArrRoutCauseDomain)){
          if (isset($lArrRoutCauseDomain['apl_amendment_cause_1']) AND $lArrRoutCauseDomain['apl_amendment_cause_1'] != '' ){
            // 1. Amendment Cause is defined and linked in Helptable Dom.
            $lDom['dom']= $lArrRoutCauseDomain['apl_amendment_cause_1'];
            $this -> addDef(fie('apl_amendment_cause_1', lan('apl_amendment_cause_1'), 'tselect', $lDom));
          }
          if (isset($lArrRoutCauseDomain['apl_amendment_cause_2']) AND $lArrRoutCauseDomain['apl_amendment_cause_2'] != '' ){
            // 2. Amendment Cause is defined and linked in Helptable Dom.
            $lDom['dom']= $lArrRoutCauseDomain['apl_amendment_cause_2'];
            $this -> addDef(fie('apl_amendment_cause_2', lan('apl_amendment_cause_2'), 'tselect', $lDom));
          }
          if (isset($lArrRoutCauseDomain['apl_amendment_cause_3']) AND $lArrRoutCauseDomain['apl_amendment_cause_3'] != '' ){
            // 3. Amendment Cause is defined and linked in Helptable Dom.
            $lDom['dom']= $lArrRoutCauseDomain['apl_amendment_cause_3'];
            $this -> addDef(fie('apl_amendment_cause_3', lan('apl_amendment_cause_3'), 'tselect', $lDom));
          }
        }else {
          $this -> dbg('Amendment root cause variable is empty or not defined',mlInfo);
        }
      }


      if (bitset($this -> mFla, sfStartApl)) {
      	$lMsg = $this -> getAplSummary();
      	$this -> addDef(fie('ddl_korrekturumlauf', lan('apl.ddl'), 'date', NULL, array('class' => 'inp w100')));

      	if (CCor_Cfg::get('rewrite.ddl.timig')) {
      		// calculates the ddl based on duration time - used in 23275/3 APL Timing
      		$lNewStart = $this -> mDurationInDates[$this -> mDurationTime]['date'];
      		$this -> setVal('ddl_korrekturumlauf', date('Y-m-d',$lNewStart));
      		//in old wird sonst der gleiche Wert geschrieben wie im Val, um spaeter Unterschiede feststellen zu koennen
      		//hier wird aber bereits ein neuer Wert im Formular angezeigt, der sich vom alten unterscheidet.
      		//dieser muss mittels update gespeichert werden, diese Aenderung kann in d. Historie
      		$this -> setDifferentOld('ddl_korrekturumlauf', $this -> mJob['ddl_korrekturumlauf']);
      	} else {
      		$this -> setVal('ddl_korrekturumlauf', $this->mJob['ddl_korrekturumlauf']);
      	}
      }
      if (bitset($this -> mFla, sfCloseApl)) {
        $lMsg = $this -> getAplSummary();
        $this -> setVal('msg', $lMsg);
      }
    }
    #echo '<pre>---dialog.php--construct-$this -> mInvite,$this -> mDialogStep,bitset($this -> mFla, sfStartApl),$this -> mAplNotifyArr,$this -> mNot';var_dump($this -> mInvite,$this -> mDialogStep,bitset($this -> mFla, sfStartApl),$this -> mAplNotifyArr,$this -> mNot,'#############');echo '</pre>';
    if (bitset($this -> mFla, sfSignature)) {
      $lSigForm = $this->getSignatureForm();
      $this->appendForm($lSigForm);
    }
  }

  protected function getSignatureForm() {
    $lId = uniqid();

    $lRet = '';

    $lRet.= '<div class="p16">';

    $lRet.= '<table cellpadding="2">';
    $lRet.= '<tr><td>Username</td>';
    $lRet.= '<td><input type="text" class="inp200" id="sig_user" name="sig[user_'.$lId.']" /></td></tr>';
    $lRet.= '<tr><td>Password</td>';
    $lRet.= '<td><input type="password" class="inp200" id="sig_pass" name="sig[pass_'.$lId.']" /></td></tr>';

    $lRet.= '</table>';
    $lRet.= '</div>';

    $lJs = '';
    $lJs.= 'jQuery(function() {'.LF;
    $lJs.= 'var lForm = jQuery("#'.$this -> mFrmId.'");'.LF;
    $lJs.= 'lForm.submit(function(aEvent) {'.LF;
    $lJs.= 'aEvent.preventDefault();'.LF;
    $lJs.= 'var user = jQuery("#sig_user").val();'.LF;
    $lJs.= 'var pass = jQuery("#sig_pass").val();'.LF;
    $lJs.= 'var params = {"user":user, "pass":pass};'.LF;
    $lJs.= 'jQuery.post("index.php?act=ajx.checkCredentials", params, function(aData){'.LF;
    $lJs.= 'if ("ok" == aData) {'.LF;
    $lJs.= '  lForm.unbind("submit");'.LF;
    $lJs.= '  lForm.submit();'.LF;
    $lJs.= '} else {'.LF;
    $lJs.= '  jQuery("#sig_user").addClass("cr");jQuery("#sig_pass").addClass("cr");'.LF;
    $lJs.= '  alert("Invalid username or password!");'.LF;
    $lJs.= '}'.LF;

    $lJs.= '}); //callback'.LF;
    $lJs.= '}); //submit'.LF;
    $lJs.= '}); //ondocready'.LF;
    $lPag = CHtm_Page::getInstance();
    $lPag->addJs($lJs);

    $lPnl = new CHtm_Panel('Signature', $lRet);
    //$lPnl -> setDivAtt('class', '');
    $lPnl -> setAtt('class', 'th2');
    $lPnl -> setDivAtt('class', 'frm');
    $lRet = $lPnl -> getContent();

    return $lPnl -> getContent();
  }

  protected function showCommentBox() {
    $this -> addDef(fie('msg', lan('lib.msg'), 'memo', NULL, array('style' => 'width:500px;', 'rows' => '18')));
  }

  public function prependForm($aCont) {
    $this->mPrependForm.= $aCont;
  }

  public function appendForm($aCont) {
    $this->mAppendForm.= $aCont;
  }

  public function setIdField($aIdField) {
    # "JobId", die ueber die URL uebertragen wird '&jobid='
    $this -> mIdField = $aIdField;
  }
  public function setDbIdField($aDbIdField) {
    # "JobId", die in der DB gespeichert wird 'WHERE jobid='
    $this -> mDbIdField = $aDbIdField;
  }
  public function getIdField() {
    # "JobId", die ueber die URL uebertragen wird '&jobid='
    return $this -> mIdField;
  }
  public function getDbIdField() {
    # "JobId", die in der DB gespeichert wird 'WHERE jobid='
    return $this -> mDbIdField;
  }

  protected function getAnnotations() {
    $lRet = '';
    if (bitset($this -> mFla, sfSelectAnnots)) {
      if (CCor_Cfg::get('wec.api.annotation')) {
        $this->dbg('Not supported with WebCenter API annotations (wec.api.annotation)');
        return '';
      }
      // Annotationen
      $lAnn = new CJob_Apl_Page_Annotations($this->mJob, $this -> mFrmId);
      $lUsr = CCor_Usr::getInstance();
      $lAnn -> setEnableTextedit($lUsr -> canEdit('wec.revice.annotation'));
      $lAnn -> setWithCheckboxes(true);
      $lRet.= $lAnn -> getAnnotationList(true).LF;
      $lRet.= $lAnn -> getHiddenElements().LF;

      $lPnl = new CHtm_Panel(lan('lib.annotations'), $lRet, 'crp.dlg.ann');
      $lPnl -> setAtt('class', 'th2');
      $lPnl -> setDivAtt('class', '');
      $lRet = $lPnl -> getContent();

    }
    return $lRet;
  }

  protected function loadFlagNotifications($aFlagId, $lFlagEve) {
    $lEve = $lFlagEve;
    if (empty($lEve)) return;
    #$this -> mNot = array();
    $this -> mNot[$aFlagId] = array();
    $this -> mGru2AplRole = array();

    $this -> mAllActions = CCor_Res::get('action');
    if (isset($this -> mAllActions[$lEve])) {
      $lActionArr = $this -> mAllActions[$lEve];
      foreach ($lActionArr as $lRow) {
        $lTyp = $lRow['typ'];//=Eve_ActionTyp
        #echo '<pre>---loadNotifications---'.get_class().'---';var_dump($lRow,'#############');echo '</pre>';
        switch ($lTyp) {
          case 'email_apl':
            break;
          case 'email_rol':
            $this -> addRoleNotify($lRow, $aFlagId);
            break;
          case 'email_usr':
            $this -> addUserNotify($lRow, $aFlagId);
            break;
          case 'email_gru':
            $this -> addGroupNotify($lRow, $aFlagId);
            break;
          case 'email_gruasrole':
            $this -> addGroupAsRoleNotify($lRow, $aFlagId);
            break;
          case 'email_gpm':
            $this -> addGpmNotify($lRow, $aFlagId);
            break;
        }
      }
    }
    ksort($this -> mNot[$aFlagId]);
      #echo '<pre>---dialog.php---loadFlagNotifications ';var_dump($this -> mNot,'#############');echo '</pre>';

  }

  /*
   * calculates the ddl based on (each accumulated duration time 'in past') + duration time of user
   * used in 23275/3 APL Timing
   * @param integer $aPos in database starts with 0, not with 1!, so we don't need to count ($aPos +1) -1 to get the afore pos
   * @param integer $aDur duration time of user
   * @return string Ddl Date
  */
  protected function getDdl4User($aPos, $aDur) {

    $lNewDur = 0;
    if (!empty($this -> mDurationArr)) {
      foreach ($this -> mDurationArr as $lPos => $MaxDur) {
        if ($lPos <= $aPos) {
          $lNewDur += $MaxDur;
        } else {
          break;
        }
      }
    }
    $lCountDur = $lNewDur + $aDur;
    if (isset($this -> mDurationInDates[$lCountDur])) {
    $lDdl4User = $this -> mDurationInDates[$lCountDur]['date'];
    $lDdl4User = date('Y-m-d', $lDdl4User);
    } else {
      $lDdl4User = date('Y-m-d');
    }
    #echo '<pre>#####-getDdl4User--dialog.php---'.get_class().'---';var_dump($aPos+1, "$aDur + $lNewDur = $lCountDur",$lDdl4User,'#############');echo '</pre>';
    return $lDdl4User;
  }//end_function getDdl4User

  protected function conditionsCheck($aEvents) {
    foreach ($aEvents as $lKey => $lVal) {
      if (!empty($lVal['cond_id'])) {
        $lReg = new CInc_App_Condition_Registry();
        $lCnd = $lReg->loadFromDb($lVal['cond_id']);
        $lCnd->setContext('data', $this->mJob);
        if (!$lCnd->isMet()) {
          unset($aEvents[$lKey]);
          $this->dbg('Cond '.$lVal['cond_id'].' is not met');
          continue;
        } else {
          $this->dbg('Cond '.$lVal['cond_id'].' is met');
        }
      }
    }
    return $aEvents;
  }

  protected function loadNotifications() {
    $lEve = $this -> mStpDef['event'];
    if (empty($lEve)) return;
    #$this -> mNot = array();
    $this -> mNot[0] = array();
    $this -> mGru2AplRole = array();

    $lRolArrAli = array();// Invited Role Aliase
    $this -> mNoActiveRolArrAli = array();
    //$lSql = 'SELECT * FROM `al_eve_act` WHERE `mand`='.MID.' AND `eve_id`='.esc($lEve);
    $this -> mAllActions = CCor_Res::get('action');
    if (isset($this -> mAllActions[$lEve])) {
      $lActionArr = $this -> conditionsCheck($this -> mAllActions[$lEve]);

      /*
       * calculates the ddl based on duration time
       * used in 23275/3 APL Timing
      */
      $lFunc = CEve_Act_Cnt::countDurationTime($lActionArr);
      if (!empty($lFunc['val'])) {
        $this -> mDurationTime = $lFunc['val'];
        $this -> mDurationArr  = $lFunc['all'];
        $this -> mDurationInDates = CCor_Date::getWorkdays($this -> mDurationTime);
      }
      #echo '<pre>---dialog.php---'.get_class().'---';var_dump($this -> mDurationTime,$this -> mDurationArr,$this -> mDurationInDates,'#############');echo '</pre>';
      foreach ($lActionArr as $lRow) {
        $lPar = unserialize($lRow['param']);
        if (isset($lPar['sid']) AND !is_numeric($lPar['sid'])) {
          if (!isset($lPar['inv']) OR (isset($lPar['inv']) AND 'y' == $lPar['inv'])) {
            // email_rol: sid=alias(Role), email_gru: sid=GruId, email_usr: sid=UsrId, email_apl/gpm => no sid
            $lRolArrAli[] = esc($lPar['sid']);
          } else {
            $this -> mNoActiveRolArrAli[] = $lPar['sid'];
          }
        }
      }
    }
    if(!empty($lRolArrAli)) {
      $lSql = 'SELECT `alias`,`param` FROM `al_fie` WHERE `mand`='.MID.' AND `alias` IN ('.implode(',',$lRolArrAli).')';
      $lQry = new CCor_Qry($lSql);
      foreach ($lQry as $lRow) {
        if(!empty($lRow['param'])) {
          $lpar = toArr($lRow['param']);
          // If Group Feld, add to Apl Rol to GroupId
          if (isset($lpar['gru'])) {
            $this -> mApl_Rol2GruId[$lRow['alias']] = $lpar['gru'];
            $this -> mEmailInfo[$lpar['gru']] = $this -> mArrFie[$lRow['alias']]; // ergibt die Bezeichnung
          }
        }
      }
    }
    if (isset($this -> mAllActions[$lEve])) {

      $lActionArr = $this -> conditionsCheck($this -> mAllActions[$lEve]);
      foreach ($lActionArr as $lRow) {
        $lTyp = $lRow['typ'];//=Eve_ActionTyp
        #echo '<pre>---loadNotifications---'.get_class().'---';var_dump($lRow,'#############');echo '</pre>';
        switch ($lTyp) {
          case 'email_apl':
            if (bitset($this -> mFla, sfStartApl)) $this -> mAplNotif = TRUE;//needed, if APL and not only a email_apl (+ email_rol)
            $lPar = unserialize($lRow['param']);

            $lDdl4User = $this -> getDdl4User($lRow['pos'], $lRow['dur']);
            $this -> mEmailInfo_Apl = $lRow['id'].'.'.$lPar['tpl'].'.'.$lRow['pos'].'.'.$lRow['dur'].'.'.$lDdl4User;
            $this -> mEmailInfo_AplPos = $lRow['pos'];
            break;
          case 'email_rol':
            if (bitset($this -> mFla, sfStartApl)) $this -> mAplNotifyArr['rol'] = TRUE;//needed, if APL
            $this -> addRoleNotify($lRow);
            break;
          case 'email_usr':
            if (bitset($this -> mFla, sfStartApl)) $this -> mAplNotifyArr['usr'] = TRUE;//needed, if APL
            $this -> addUserNotify($lRow);
            break;
          case 'email_gru':
            if (bitset($this -> mFla, sfStartApl)) $this -> mAplNotifyArr['gru'] = TRUE;//needed, if APL
            $this -> addGroupNotify($lRow);
            break;
          case 'email_gruasrole':
            if (bitset($this -> mFla, sfStartApl)) $this -> mAplNotifyArr['gru'] = TRUE;//needed, if APL
            $this -> addGroupAsRoleNotify($lRow);
            break;
          case 'email_gpm':
            $this -> addGpmNotify($lRow);
            break;
        }
      }
    }
    ksort($this -> mNot[0]);
    #  echo '<pre>---dialog.php---loadNotifications <- addAplRoleNotify: $this -> mGru2AplRole';var_dump($this -> mGru2AplRole,$this -> mNot,'#############');echo '</pre>';

  }

  protected function addRoleNotify($aRow, $aFlagId = 0) {
    // kommt aus SELECT * FROM `al_eve_act` ...
    $lPar = unserialize($aRow['param']);
    $lAli = $lPar['sid'];
    #echo '<pre>---dialog.php--$this -> mArrFie['.$lAli.'],$this -> mJob['.$lAli.']-';var_dump($this -> mArrFie[$lAli],$this -> mJob[$lAli],'#############');echo '</pre>';
    if (!isset($this -> mArrFie[$lAli])) return;
    $lRol = $this -> mArrFie[$lAli];
    if (!empty($this -> mInvite) AND 0 == $aFlagId) {//nur im APL
      $this -> addAplRoleNotify($aRow, $aFlagId);
    }
    if (empty($this -> mInvite) OR (0 < $aFlagId) OR (!empty($this -> mInvite) AND isset($lPar['inv']) AND 'n' == $lPar['inv'])) {
      if (!isset($this -> mJob[$lAli]) OR empty($this -> mJob[$lAli])) return;
      $lUid = $this -> mJob[$lAli];
      if (empty($this -> mArrUsr[$lUid])) return;
      $lUsr = $this -> mArrUsr[$lUid];
      # echo '<pre>---------------------dialog.php---addRoleNotify $lAli,$lRol,$lUid,$lUsr';var_dump($lAli,$lRol,$lUid,$lUsr,'####################################');echo '</pre>';

      $lRet = new CCor_Dat();
      $lRet['name']  = $lUsr['fullname'];
      $lRet['email'] = $lUsr['email'];
      $lRet['role']  = $lRol;
      $lRet['check'] = $lPar['def'];
      $lRet['act']   = $aRow['id'];
      $lRet['tpl']   = $lPar['tpl'];
      if (!isset($lPar['inv'])) {
        $lPar['inv'] = 'y';
      }
      $lRet['inv']   = $lPar['inv'];
      $lRet['pos']   = $aRow['pos'];
      $lDdl4User     = $this -> getDdl4User($aRow['pos'], $aRow['dur']);
      $lRet['tpl_pos'] = 'U.'.$lUid.'.'.$lPar['tpl'].'.'.$aRow['pos'].'.'.$aRow['dur'].'.'.$lDdl4User;
      if (isset($lPar['inv']) AND 'n' == $lPar['inv']) {
        $lRet['tpl_pos'].= '.n';
      }
      #echo '<pre>---dialog.php---addRoleNotify---';var_dump($lRet,$aRow,'#############');echo '</pre>';
      $this -> mNot[$aFlagId][$lRol.' '.$lUsr['fullname']] = $lRet;
    }
  }

  protected function addAplRoleNotify($aRow, $aFlagId = 0) {
    $lPar = unserialize($aRow['param']);
    $lAli = $lPar['sid'];

    $lRet = new CCor_Dat();
    $lRet['sid']   = $lAli;
    $lRet['check'] = $lPar['def'];
    $lRet['act']   = $aRow['id'];
    $lRet['tpl']   = $lPar['tpl'];
    if (!isset($lPar['inv'])) {
      $lPar['inv'] = 'y';
    }
    $lRet['inv']   = $lPar['inv'];
    $lRet['pos']   = $aRow['pos'];
    $lRet['dur']   = $aRow['dur'];
    if ('y' == $lPar['inv']) {
      $this -> mGru2AplRole[$this -> mApl_Rol2GruId[$lAli]] = $lRet;
    }
  }

  protected function addUserNotify($aRow, $aFlagId = 0) {
    $lPar = unserialize($aRow['param']);
    $lUid = $lPar['sid'];
    if (empty($this -> mArrUsr[$lUid])) return;
    $lUsr = $this -> mArrUsr[$lUid];

    $lRet = new CCor_Dat();
    $lRet['name']  = $lUsr['fullname'];
    $lRet['email'] = $lUsr['email'];
    $lRet['role']  = lan('lib.user');
    $lRet['check'] = $lPar['def'];
    $lRet['act']   = $aRow['id'];
    $lRet['tpl']   = $lPar['tpl'];
    if (!isset($lPar['inv'])) {
      $lPar['inv'] = 'y';
    }
    $lRet['inv']   = $lPar['inv'];
    $lRet['pos']   = $aRow['pos'];
    $lDdl4User     = $this -> getDdl4User($aRow['pos'], $aRow['dur']);
    $lRet['tpl_pos'] = 'U.'.$lUid.'.'.$lPar['tpl'].'.'.$aRow['pos'].'.'.$aRow['dur'].'.'.$lDdl4User.'.'.$lRet['inv'];
    #echo '<pre>---dialog.php---addUserNotify---';var_dump($lRet,$aRow,'#############');echo '</pre>';
    $this -> mNot[$aFlagId]['ZZ '.$lUsr['fullname']] = $lRet;
  }

  protected function addGroupNotify($aRow, $aFlagId = 0) {
    $lPar = unserialize($aRow['param']);
    $lGid = $lPar['sid'];
    $lGruArr = CCor_Res::extract('id', 'name', 'gru');
    if (empty($lGruArr[$lGid])) return;
    $lGru = $lGruArr[$lGid];

    $lRet = new CCor_Dat();
    $lRet['name']  = $lGru;
    $lRet['email'] = '...';
    $lRet['role']  = 'Group';
    $lRet['check'] = $lPar['def'];
    if (!isset($lPar['inv'])) {
      $lPar['inv'] = 'y';
    }
    $lRet['inv']   = $lPar['inv'];
    #$lRet['confirm'] = $lPar['confirm'];
    $lRet['act']   = $aRow['id'];#'G.'.$lGid;#$aRow['id'];//wird beim "NICHT-Einladen" der Flags gebraucht.
    $lRet['pos']   = $aRow['pos'];
    $lDdl4User     = $this -> getDdl4User($aRow['pos'], $aRow['dur']);
    $lRet['tpl_pos'] = 'G.'.$lGid.'.'.$aRow['id'].'.'.$lPar['tpl'].'.'.$aRow['pos'].'.'.$aRow['dur'].'.'.$lDdl4User;
    if (isset($lPar['confirm'])) {
      $lRet['tpl_pos'].= '.'.$lPar['confirm'];
    }
    $lRet['tpl_pos'].= '.'.$lRet['inv'];
    #echo '<pre>---dialog.php---addGroupNotify---';var_dump($lRet,$aRow,'#############');echo '</pre>';
    $this -> mNot[$aFlagId]['Z  '.$lGru] = $lRet;
  }

  /**
   * Email to Group as Role.
   * GroupId is loaded from Job.
   */
  protected function addGroupAsRoleNotify($aRow, $aFlagId = 0) {
    $lPar = unserialize($aRow['param']);
    $lFie = $lPar['sid'];
    if (!isset($this -> mArrFie[$lFie])) return;
    if (!isset($this -> mJob[$lFie])) return;
    // GroupId as Role
    $lGid = $this -> mJob[$lFie];
    $lGruArr = CCor_Res::extract('id', 'name', 'gru');
    if ((!isset($lGruArr[$lGid])) OR (empty($lGruArr[$lGid]))) return;
    $lGru = $lGruArr[$lGid];
    #$lRol = $this -> mArrFie[$lFie];
    //var_dump($lPar);

    if ( isset($lPar['members']) && ('on' == $lPar['members']) ) {
      $lUsr = CCor_Res::get('usr', array('gru' => $lGid));
      foreach ($lUsr as $lUid => $lRow) {
        $lRet = new CCor_Dat();
        $lRet['name']  = $lRow['fullname'];
        $lRet['email'] = $lRow['email'];
        $lRet['role']  = $lGru;
        $lRet['check'] = $lPar['def'];
        if (!isset($lPar['inv'])) {
          $lPar['inv'] = 'y';
        }
        $lRet['inv']   = $lPar['inv'];
        #$lRet['confirm'] = $lPar['confirm'];
        $lRet['act']   = $aRow['id'].'-'.$lUid;#'G.'.$lGid;#$aRow['id'];//wird beim "NICHT-Einladen" der Flags gebraucht.
        $lRet['tpl']   = $lPar['tpl'];
        $lRet['pos']   = $aRow['pos'];
        $lDdl4User = $this -> getDdl4User($aRow['pos'], $aRow['dur']);
        $lRet['tpl_pos'] = 'U.'.$lUid.'.'.$lPar['tpl'].'.'.$aRow['pos'].'.'.$aRow['dur'].'.'.$lDdl4User.'.'.$lRet['inv'];

        #echo '<pre>---dialog.php---addUserNotify---';var_dump($lRet,$aRow,'#############');echo '</pre>';
        $this -> mNot[$aFlagId]['ZZ '.$lRow['fullname']] = $lRet;
      }
    } else {
      $lRet = new CCor_Dat();
      $lRet['name']  = $lGru;
      $lRet['email'] = '...';
      $lRet['role']  = 'Group';#$lRol
      $lRet['check'] = $lPar['def'];
      if (!isset($lPar['inv'])) {
        $lPar['inv'] = 'y';
      }
      $lRet['inv']   = $lPar['inv'];
      #$lRet['confirm'] = $lPar['confirm'];
      $lRet['act']   = $aRow['id'];#'G.'.$lGid;#$aRow['id'];//wird beim "NICHT-Einladen" der Flags gebraucht.
      $lRet['tpl']   = $lPar['tpl'];
      $lRet['pos']   = $aRow['pos'];
      $lDdl4User = $this -> getDdl4User($aRow['pos'], $aRow['dur']);
      $lRet['tpl_pos'] = 'G.'.$lGid.'.'.$aRow['id'].'.'.$lPar['tpl'].'.'.$aRow['pos'].'.'.$aRow['dur'].'.'.$lDdl4User;
      if (isset($lPar['confirm'])) {
        $lRet['tpl_pos'].= '.'.$lPar['confirm'];
      }
      $lRet['tpl_pos'].= '.'.$lRet['inv'];
      #echo '<pre>---dialog.php---addGroupAsRoleNotify---';var_dump($lRet,$aRow,'#############');echo '</pre>';
      $this -> mNot[$aFlagId]['ZZZ '.$lGru] = $lRet;
    }
  }

  protected function addGpmNotify($aRow, $aFlagId = 0) {
    $lPar = unserialize($aRow['param']);

    // Keine Anzeige im Formular! Nur wenn val.griesson_id existiert (Prüfung in sender) werden sie versendet
    $lRet = new CCor_Dat();
    $lRet['name']  = 'Versand der Gpm-Benachrichtigung';
    $lRet['check'] = $lPar['def'];
    $lRet['act']   = $aRow['id'];

    $this -> mNot[$aFlagId][$lRet['name']] = $lRet;
  }

  protected function getForm() {
    $lRet = $this->mPrependForm;

    if ($this -> mApl_Notify AND empty($this -> mDialogStep)) {
      $this->dbg('getForm: getAplForm');
      $lRet.= $this -> getAplForm();
      // $lRet.= $this -> getAnnotations();
      $lShowFlagComments = FALSE;
    } elseif ($this -> mApl_Notify AND !empty($this -> mDialogStep)) {
      $this->dbg('getForm: getAPL_Notifications');
      $lRet.= $this -> getAPL_Notifications();
      $lRet.= $this -> getAnnotations();
      $lRet.= '<div class="th2">'.htm(lan('lib.msg')).'</div>';
      $lRet.= parent::getForm();
      $lShowFlagComments = TRUE;
    } else {
      $this->dbg('getForm: getNotifications');
      if (bitset($this -> mFla, sfComment)) {
        $lRet.= $this -> getNotifications();
        $lRet.= $this -> getAnnotations();

        if (bitset($this -> mFla, sfStartApl)) {
          $lRet.= $this -> getAplForm();
        }
        $lRet.= '<input type="hidden" name="emailinfoapl" value="'.$this -> mEmailInfo_Apl.'" />';
        $lRet.= '<div class="th2">'.htm(lan('lib.msg')).'</div>';
        $lRet.= parent::getForm();
      }
      $lShowFlagComments = TRUE;
    }

    ###########################################################################################
    if ($lShowFlagComments) {

      //[1135] Add-on zu 'Flags in Critical Path'
      //Abfrage, ob in diesem Step ein Flag aktiviert wird und wenn ja welcher: foreach- Schleife
      $lFlagStr = $this -> mStpDef['flag_act'];
      #echo '<pre>---dialog.php---'.get_class().'---';var_dump($this -> mStpDef,$lFlagStr,'#############');echo '</pre>';
      if (!empty($lFlagStr)) {
        $lAll_Flag = explode(',', $lFlagStr);
        $this -> mAllFlags = CCor_Res::get('fla');
        foreach ($lAll_Flag as $lFlagId) {
          if (isset($this -> mAllFlags[$lFlagId])) {
            $lFlag = $this -> mAllFlags[$lFlagId];

            $lRet.= '<div class="th2">'.lan('lib.msg').' Start: '.$lFlag['name_'.LAN].'</div>';

            $this -> loadFlagNotifications($lFlagId, $lFlag['eve_act']);
            $lRet.= $this -> getFlagNotifications($lFlagId);
            $this -> mFie = array(); // oh, ist das BOESE!!!
            $this -> addDef(fie('msg_Flag', lan('lib.msg').': '.$lFlag['name_'.LAN], 'memo', NULL, array('style' => 'width:500px;', 'rows' => '18', 'array_key' => $lFlagId)));
            $lRet.= parent::getForm();
          }
        }
      }
    }
    ###########################################################################################

    $lRet.= $this->mAppendForm;
    return $lRet;
  }

  protected function getAplForm() {
    if ($this->mIsCountryApl) {
      return '';
    }
    $lRet = '';
    $this -> mAplSrc = array();

    $lGruArr = CCor_Res::extract('code', 'id', 'gru');
    $lGru2Invite = CCor_Cfg::get('invite.apl', array());
    $lGruStr = isset($lGruArr[MANDATOR]) ? $lGruArr[MANDATOR] : array(); // alle member of MANDATOR können grundsätzlich eingeladen werden.
    foreach ($lGru2Invite as $lGru) {
      if (isset($lGruArr[$lGru])){
        $lGruStr.= ','.$lGruArr[$lGru];
      }
    }
    //Alle Mitglieder von Gruppen 'MANDATOR' und CCor_Cfg::get('invite.apl', array());
    $lUsrArr = CCor_Res::get('usr', array('gru' => $lGruStr));
    foreach ($lUsrArr as $lKey => $lRow) {
      $this -> mAplSrc[$lKey] = $lRow['departm_fullname'];
    }

    if (!empty($this -> mNot[0])) {
      foreach ($this -> mApl_Rol2GruId as $lK => $lV) {
        $lUid = $this -> mJob[$lK];
        if (isset($this -> mArrUsr[$lUid])) {
          $lDep = '('.$this -> mArrFie[$lK].')';
          $lUsr = $this -> mArrUsr[$lUid];
          $this -> mAplSrc[$lUid] = cat($lDep, $lUsr['fullname']);
        }
      }
    }
    asort($this -> mAplSrc);
    $this -> mDst = array();

    // Invite All job fiels mit 'per_', if they not in the $lNoAplInvite
    $lNoAplInvite = CCor_Cfg::get('apl.noinvite.alias', array());
    foreach ($this -> mArrFie as $lAli => $lNam) { // CCor_Res::extract('alias', 'name_'.LAN, 'fie')
      // Take only jobfields with type 'uselect'.
      if (0 === strpos($lAli, 'per_') AND 'uselect' == $this -> mArrFieTyp[$lAli]) {
        if (!in_array($lAli, $lNoAplInvite)) {
          $this -> addAplUsr($lAli);
        }
      }
    }

    // Invite all Roles, if they not in the  $lNoAplInvite
    $lSql = 'SELECT * FROM `al_rol` WHERE `mand`='.MID;
    $lQry_Rol = new CCor_Qry($lSql);
    foreach ($lQry_Rol as $lRow) {
      if (!in_array($lRow['alias'], $lNoAplInvite)) {
        $this -> addAplUsr($lRow['alias']);
      }
    }
    #$this -> unsetAplUsr('per_prj_verantwortlich');
    #echo '<pre>---dialog.php---';var_dump($this -> mDst,'#############');echo '</pre>';


    // You can invite Groups to an apl (viewed on the right side)
    // need a config-Var to switch it on/off = not empty/empty
    $lGroup2Invite = CCor_Cfg::get('job.apl.parent.invitedgroups', array());
    if(!empty($lGroup2Invite)) {
      $lGruStr = implode(',', $lGroup2Invite);

      $lSql = 'SELECT * FROM `al_gru` WHERE `mand`='.MID.' AND parent_id IN ('.$lGruStr.')';
      $lQry_Rol = new CCor_Qry($lSql);
      foreach ($lQry_Rol as $lRow) {
        $this -> mAplSrc['G'.$lRow['id']] = $lRow['name']; // left side
        $lGruIds[$lRow['id']] = $lRow['name'];
      }
      $this -> addAplGru(); // right side
    }
    #echo '<pre>---dialog.php---';var_dump($this -> mDst,$this -> mAplSrc,'#############');echo '</pre>';


    //SonderFunktion unter Cust_75 und Cust_340, waere so aber auch mandantenspezifisch moegl.
    $this -> AddMoreAplUsrWithinRoles();


    $lRet.= $this -> getSelection();

    $lPnl = new CHtm_Panel(lan('job-apl.menu'), $lRet, 'crp.dlg.apl');
    $lPnl -> setDivAtt('class', '');
    $lRet = $lPnl -> getContent();

    return $lRet;
  }

  protected function AddMoreAplUsrWithinRoles() {
    //SonderFunktion unter Cust_75 und Cust_340
  }

  /**
   * Invite all Groups which is defined in the Job
   * @return set $this->mDst.
   */
  protected function addAplGru() {
    $lArrFiePar = CCor_Res::extract('alias','param','fie');
    $lArrAliGru = array();
    foreach ($lArrFiePar as $lAli => $lPar) {
      if (!empty($lPar) AND FALSE !== strpos($lPar, 'gid')) {//gid=GruId der Elterngruppe
        if (!empty($this -> mJob[$lAli])) {
          $this -> mDst['G'.$this -> mJob[$lAli]] = 'G'.$this -> mJob[$lAli]; // right side
        }
      }
    }
  }

  /**
   *  Invite User to APL
   * @param string $aAlias
   * @return set $this->mDst
   */
  protected function addAplUsr($aAlias) {
    if (empty($this -> mJob[$aAlias])) return;
    if (in_array($aAlias, $this -> mNoActiveRolArrAli)) return;

    /*
     * JobId #23232
     * There is lot of groups by Intouch which called with 'per_' and has same Id like user.
     * Therefore es must be checked if jobfield has a type 'uselect' or not.
     */
    if ('uselect' == $this -> mArrFieTyp[$aAlias]){
      // Add user to APL.Right side.
      $this -> mDst[$this -> mJob[$aAlias]] = $this -> mJob[$aAlias];
    }else{
      return;
    }
  }

  protected function unsetAplUsr($aAlias) {
    unset($this -> mDst[$this -> mJob[$aAlias]]);
  }

  protected function getAplSummary() {
    if (empty($this -> mJob)) {
      $lApl = new CApp_Apl_Loop($this -> mSrc, $this -> mJobId, 'apl');
    } else {
      $lApl = new CApp_Apl_Loop($this -> mSrc, $this -> mJobId, 'apl', MID, $this -> mJob['webstatus']);
    }
    return $lApl -> getCurrentUserComment(CCor_Usr::getAuthId());

    // ab hier alte Verson
    $lArr = $lApl -> getAllComments();

    if (empty($lArr)) return '';
    $lRet = '';
    $lFirst = true;
    foreach ($lArr as $lRow) {
      if ($lFirst) {
        $lFirst = false;
      } else {
        $lRet.= '----------------------------------------------------------------------'.LF;
      }
      $lSta = $lRow['status'];
      if (empty($lSta)) continue;
      switch ($lSta) {
        case CApp_Apl_Loop::APL_STATE_AMENDMENT:
          $lRet.= lan('apl.amendment');
          BREAK;
        case CApp_Apl_Loop::APL_STATE_APPROVED:
          $lRet.= lan('apl.approval');
          BREAK;
        case CApp_Apl_Loop::APL_STATE_CONDITIONAL:
          $lRet.= lan('apl.conditional');
          BREAK;
      }
      $lRet.= ' ('.date(lan('lib.datetime.short'), strToTime($lRow['datum'])).')';
      $lRet.= ': '.$lRow['name'].LF;
      $lCom = trim($lRow['comment']);
      if (!empty($lCom)) {
        $lRet.= $lCom.LF;
      }
      $lRet.= LF.LF;
    }
    return trim($lRet);
  }

  protected function getSelection() {
    $lRet = '<div class="frm p8">'.LF;
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0" width="100%">';
    $lRet.= '<tr>'.LF;

    $lRet.= '<td class="w300 p8">'.LF;
    $lRet.= $this -> getSrcPanel();
    $lRet.= '</td>'.LF;

    $lRet.= '<td class="w100 p16">'.LF;
    $lRet.= btn(lan('lib.add'), 'Flow.Std.fprSel(\'src\',\'dst\')', 'img/ico/16/nav-next-lo.gif', 'button', array('class' => 'btn w100')).BR.BR;
    $lRet.= btn(lan('lib.remove'), 'Flow.Std.fprSel(\'dst\',\'src\')', 'img/ico/16/nav-prev-lo.gif', 'button', array('class' => 'btn w100')).BR.BR;
    $lRet.= '</td>'.LF;

    $lRet.= '<td  class="w300 p8">'.LF;
    $lRet.= $this -> getDstPanel();
    $lRet.= '</td>'.LF;

    $lRet.= '</tr>'.LF;
    $lRet.= '</table>';
    $lRet.= '</div>';
    return $lRet;
  }

protected function getSrcPanel() {
    $lRet = '';
    $lRet.= '<b>'.lan('lib.allUser').'</b>'.BR.LF;
    $lRet.= '<select name="src[]" id="src" size="20" class="inp w300" multiple="multiple">'.LF;

    if (!empty($this -> mAplSrc)) {
    asort($this->mAplSrc);
    foreach ($this -> mAplSrc as $lKey => $lVal) {

      // Remove User,Group if they already invited to APL.
      // if $this -> mAddUser = TRUE
      if (!empty($this->mOpenLoopInvitedUser) AND in_array($lKey,$this->mOpenLoopInvitedUser)){
        continue;
      }
      // Remove Destination User, they default invited.
      if (in_array($lKey, $this -> mDst)) {
              if (!$this-> mAddUser) {
          // By Add User/Group destination shouldn't be sorted out.
          // Only invited User can be sorted.
          continue;
        }
      }
      $lRet.= '<option value="'.htm($lKey).'">';
      $lRet.= htm($lVal);
      $lRet.= '</option>'.LF;
    }
    }
    $lRet.= '</select>'.LF;
    return $lRet;
  }

  protected function getDstPanel() {
    $lRet = '';
    $lRet.= '<b>'.htm(lan('job-apl.menu')).'</b>'.BR.LF;
    $lRet.= '<select name="dst[]" id="dst" size="20" class="inp w300" multiple="multiple">'.LF;

    // iF $this -> mAddUser = TRUE, It is no new APL but add User to APL.
    if ($this -> mAddUser) {
      $this -> mDst= '';
    }
    if (!empty($this -> mDst))
    foreach ($this -> mDst as $lKey) {
      if (isset($this -> mAplSrc[$lKey])) {
        $lVal = $this -> mAplSrc[$lKey];
        $lRet.= '<option value="'.htm($lKey).'">';
        $lRet.= htm($lVal);
        $lRet.= '</option>'.LF;
      }
    }
    $lRet.= '</select>'.LF;
    return $lRet;
  }

  protected function getImg($aId, $aImg) {
    $lRet = '<td class="bar1">';
    $lDiv = getNum('b');
    $lAtt = array();
    $lAtt['onmouseover'] = 'Flow.crpTip(this,'.$aId.')';
    $lAtt['onmouseout']  = 'Flow.hideTip();';
    $lAtt['id'] = $lDiv;
    $lRet.= img($aImg, $lAtt);
    $lRet.= '</td>';
    return $lRet;
  }

  protected function getTitle() {
    return '';
  }

  protected function preTitle() {
    $lCrp = CCor_Res::get('crp');
    $lFid = $this -> mStpDef['from_id'];
    $lTid = $this -> mStpDef['to_id'];

    $lRet = '<div class="frm ar" style="padding:16px;">'.LF;
    $lRet.= '<table cellpadding="4" cellspacing="0" border="0" class="tbl" align="right"><tr>'.LF;
    if (isset($lCrp[$lFid])) {
      $lImg = $lCrp[$lFid]['img'];
	  $lPath = CApp_Crpimage::getSrcPath($this->mSrc, 'img/crp/big/'.$lImg.'.gif');
      $lRet.= $this -> getImg($lFid, $lPath);
    }
    $lRet.= '<td class="bar1">';
    $lRet.= img('img/ico/32/change.gif');
    $lRet.= '</td>';
    if (isset($lCrp[$lTid])) {
      $lImg = $lCrp[$lTid]['img'];
	  $lPath = CApp_Crpimage::getSrcPath($this->mSrc, 'img/crp/big/'.$lImg.'.gif');
      $lRet.= $this -> getImg($lTid, $lPath);
    }
    $lRet.= '</tr></table>'.BR.BR.BR.BR;
    $lRet.= '</div>';

    $lPnl = new CHtm_Panel($this -> mCap, $lRet, 'crp.dlg.ico');
    $lPnl -> setDivAtt('class', 'w800');
    $lRet = $lPnl -> getContent();

    return $lRet;
  }

  protected function getAPL_Notifications() {
    $lRet = '';
    $this -> mNotWithAplNotifications = false;

    $lRet.= '<div class="frm" style="padding:16px;">'.LF;
    // Warning!!
    if (0 < $this -> mNoChoice) {
      $lRet.= '<div class="cr b ac" style="padding:6px;">'.LF;
      $lRet.= htm(lan('apl.warn')).LF;
      $lRet.= '</div>'.LF;
    }
    $lRet.= '<table cellpadding="2" cellspacing="0" class="tbl">'.LF;

    #if (!empty($this -> mNot)) {
    #  $lRet.= $this -> getAllNotifications();
    #}

    #echo '<pre>---getAPL_Notifications--$this -> mInvite-$this -> mNot';var_dump($this -> mInvite,$this -> mNot,$this -> mGru2AplRole,'#############');echo '</pre>';
    if(!empty($this -> mInvite)) {
      $lGruArr = CCor_Res::extract('id', 'name', 'gru');
      $lGid    = array();
      foreach ($this -> mInvite as $lUid) {
        $lDefArr = array();
        $lActArr = array();
        $lMemberOfGroup = array();

        if (0 === strpos($lUid, 'G')) {
          $lGid[] = str_replace('G', '', $lUid);
          #echo '<pre>---getAPL_Notifications---'.get_class().'---';var_dump($lUid,$lGid,'#############');echo '</pre>';
          continue;
        } else {
          $lNam = $this -> mArrUsr[$lUid]['fullname'];
          $lNam .= ' <'.$this -> mArrUsr[$lUid]['email'].'>';
        }
        $lCur = 0; // -> Selected im DropDown

        if (!empty($this -> mApl_Rol2GruId)) {
          $lSql = 'SELECT COUNT(*) FROM `al_usr_mem` WHERE `uid`='.$lUid.' AND `gid` IN ('.implode(',',$this -> mApl_Rol2GruId).')';
          $lAmount = CCor_Qry::getInt($lSql);
        } else {
          $lAmount = 0;// -> email_apl
        }
        if(0 < $lAmount) {
          $lSql = 'SELECT `gid` FROM `al_usr_mem` WHERE `uid`='.$lUid.' AND `gid` IN ('.implode(',',$this -> mApl_Rol2GruId).')';
          #echo '<pre>---getAPL_Notifications---getAPL_Notifications $lAmount';var_dump($lAmount,$lSql,'#############');echo '</pre>';
          $lQry = new CCor_Qry($lSql);
          foreach ($lQry as $lRow) {
            #$lGid[]    = $lRow['gid'];
            $lDefArr[] = $this -> mGru2AplRole[ $lRow['gid'] ]['check'];
            $lgidact = $this -> mGru2AplRole[ $lRow['gid'] ]['act'];
            $lActArr[] = $lgidact;
            if (isset($this -> mGru2AplRole[ $lRow['gid'] ]['sid'])) {
              $lMemberOfGroup[$lgidact] = $this -> mGru2AplRole[ $lRow['gid'] ]['sid'];
            }
          }
          if (1 < count($lMemberOfGroup)) {
            $lCur = 0; // -> Selected im DropDown
            foreach ($lMemberOfGroup as $lk => $lv) {
              #echo '<pre>---dialog.php---'.get_class().'---';var_dump($lActArr,$lk, $lv,$lUid, $this -> mJob[$lv],'#############');echo '</pre>';
              if ($lUid == $this -> mJob[$lv]) {
                $lCur = $lk; // -> Selected im DropDown
              }
            }
            $lDef = 'y'; // Yes checked
            $lAct = 0;
          } else {
            $lCur = $lActArr[0]; // -> Selected im DropDown
            $lDef = $lDefArr[0];
            $lAct = $lActArr[0];
          }
        } else {
          $lCur = $this -> mEmailInfo_Apl;#-1; // -> Selected im DropDown
          $lDef = 'y'; // Yes checked
          $lAct = -1;
        }
       #echo '<pre>---getAPL_Notifications---$lUid - $lGid';var_dump($lUid,$lGid,$lCur,$lDef,$lAct,'#############');echo '</pre>';

        $lRet.= '<tr>';
        $lRet.= '<td class="td2 w16 ac">';

        if ('n' == $lDef) {
          $lRet.= '<input type="hidden" name="act_old1['.$lUid.']" value="1" />';
          $lRet.= '<input type="checkbox" name="act_new1['.$lUid.']" value="1" />';
        } else if ('f' == $lDef) { // In der Regel als nicht abwaehlbar vordefiniert.
          $lRet.= '<input type="hidden" name="act_old1['.$lUid.']" value="1" />';
          $lRet.= '<input type="hidden" name="act_new1['.$lUid.']" value="1" />';
          $lRet.= '<input type="checkbox" checked="checked" disabled="disabled" />';
        } else {
          $lName = CCor_Qry::getArr('SELECT firstname, lastname FROM al_usr WHERE id='.$lUid);
          //$lRet.= '<input type="hidden" id="cbhidden'.$lUid.'" value="'.$lName.'" />';
          $lRet.= '<input type="hidden" name="act_old1['.$lUid.']" value="1" />';
          $lRet.= '<input type="checkbox" id="'.$lUid.'" name="act_new1['.$lUid.']" checked="checked" value="'.$lName.'" />';
        }
        $lRet.= '</td>';

        $lRet.= '<td class="td2 nw w100p b"> ';
        $lRet.= htm($lNam);
        $lRet.= '</td>';

        $lRet.= '<td class="td2 nw">';
        $lArr = $this -> mEmailInfo;
        $lRet.= '<input type="hidden" name="act_old2['.$lUid.']" value="'.$lCur.'" />'.LF;
        $lRet.= '<select id="sel'.$lUid.'" name="act_new2['.$lUid.']" class="w200">';
        foreach ($lArr as $lKey => $lVal) {
          if (isset($this -> mGru2AplRole[$lKey]['act'])) {
            $l_Key = $this -> mGru2AplRole[$lKey]['act'];
          } else {
            $l_Key = $lKey;
          }
          $lSel = '';
          if(0 != $l_Key) {
            $lSel = ($lCur == $l_Key) ? ' selected="selected"' : '';
          }
          if (isset($this -> mGru2AplRole[$lKey]['tpl'])) { // brauche in app/sender das Template
            $l_Key = $l_Key.'.'.$this -> mGru2AplRole[$lKey]['tpl'];
            $l_Key .= '.'.$this -> mGru2AplRole[$lKey]['pos'];
            $l_Key .= '.'.$this -> mGru2AplRole[$lKey]['dur'];
            $lDdl4User = $this -> getDdl4User( $this -> mGru2AplRole[$lKey]['pos'] , $this -> mGru2AplRole[$lKey]['dur'] );
            $l_Key .= '.'.$lDdl4User;
            $l_Key .= '.'.$this -> mGru2AplRole[$lKey]['inv'];
          }
          if (isset($this -> mGru2AplRole[$lKey]['pos'])) {
            $lPosition = $this -> mGru2AplRole[$lKey]['pos'] + 1 .' ';
          } elseif (0 == $lKey) {
            $lPosition = '';
          } else {
            $lPosition = $this -> mEmailInfo_AplPos + 1 .' ';
          }
          if (!isset($this -> mGru2AplRole[$lKey]['inv']) OR (isset($this -> mGru2AplRole[$lKey]['inv']) AND 'y' == $this -> mGru2AplRole[$lKey]['inv'])) {
            $lRet.= '<option value="'.$l_Key.'"'.$lSel.'>'.$lPosition.htm($lVal).'</option>'.LF;
          }
        }
        $lRet.= '</select>'.LF;
        $lRet.= '</td>';

        $lRet.= '</tr>'.LF;

      }
    }

    #echo '<pre>---getAPL_Notifications---'.get_class().'---';var_dump($lGid,'#############');echo '</pre>';
    if (!(empty($lGid))) {
      $lGruArr = CCor_Res::extract('id', 'name', 'gru');
      #$this -> mEmailInfo_Apl = $lRow['id'].'.'.$lPar['tpl'].'.'.$lRow['pos'];
      #$lEmailInfo = explode('.', $this -> mEmailInfo_Apl);
      foreach ($lGid as $lGruId) {
        if (empty($lGruArr[$lGruId])) return;
        $lGru = $lGruArr[$lGruId];
        if (!isset($this -> mNot[0]['Z  '.$lGru]) AND  // eMailToGroup
            !isset($this -> mNot[0]['ZZZ '.$lGru])) {  // eMailToGroupAsRole

          $lDat = new CCor_Dat();
          $lDat['name']    = $lGru;
          $lDat['email']   = '...';
          $lDat['role']    = 'Group';
          $lDat['check']   = 'y';
          $lDat['confirm'] = 'one';
          $lDat['act']     = 'G.'.$lGruId;
          $lDat['pos']     = $this -> mEmailInfo_AplPos;
          $lDat['tpl_pos'] = 'G.'.$lGruId.'.'.$this -> mEmailInfo_Apl.'.one';

          $this -> mNot[0]['Z  '.$lGru] = $lDat;
        }
      }
    }

    if (!empty($this -> mNot[0])) {
      $lRet.= $this -> getAllNotifications();
    }

    $lRet.= '</table>'.LF;
    $lRet.= '</div>'.LF;

    $lPnl = new CHtm_Panel(lan('email.notif'), $lRet, 'crp.dlg.not');
    $lPnl -> setAtt('class', 'th2');
    $lPnl -> setDivAtt('class', '');

    $lRet = $lPnl -> getContent();
    return $lRet;
  }

  public static function cmpLines($a, $b) {
    $lA = $a['pos'];
    $lB = $b['pos'];
    return ($lA > $lB) ? +1 : -1;
  }

  protected function getAllNotifications($aFlagId = 0) {
    $lRet = '';

    if ($this -> mNotWithAplNotifications) {
      $lRet.= '<div class="frm" style="padding:16px;">'.LF;
      $lRet.= '<table cellpadding="2" cellspacing="0" class="tbl">'.LF;
    }
    #echo '<pre>---AllNotifications---';var_dump($this -> mNot,'#############');echo '</pre>';
    uasort($this->mNot[$aFlagId], array(__CLASS__, 'cmpLines'));
    foreach ($this -> mNot[$aFlagId] as $lRow) {
      if ($this ->mAddUser){
        // If Add User an existing APL
        //dont invite the user doppel,if his Flag 'inv'== 'n'
        if ($lRow['inv'] == 'n'){
          continue;
        }
      }
      if('Versand der Gpm-Benachrichtigung' != $lRow['name']){
        $lRet.= '<tr>';
        $lRet.= '<td class="td2 w16 ac">';

        $lNam = $lRow['name'];
        $lNam .= ' <'.$lRow['email'].'>';
        $lDef = $lRow['check'];
        $lAct = $lRow['act'];
        $lPos = $lRow['tpl_pos'];
        if ('n' == $lDef) {
          $lRet.= '<input type="hidden" name="act_old['.$aFlagId.']['.$lAct.']" value="1" />';
          #$lRet.= '<input type="hidden" name="act_pos['.$lAct.']" value="'.$lPos.'" />';
          $lRet.= '<input type="checkbox" name="act_new['.$aFlagId.']['.$lAct.']" value="'.$lPos.'" />';
        } else if ('f' == $lDef) {
          $lRet.= '<input type="checkbox" checked="checked" disabled="disabled" />';
          $lRet.= '<input type="hidden" name="act_old['.$aFlagId.']['.$lAct.']" value="1" />';
          $lRet.= '<input type="hidden" name="act_new['.$aFlagId.']['.$lAct.']" value="'.$lPos.'" />';
        } else {
          $lRet.= '<input type="hidden" name="act_old['.$aFlagId.']['.$lAct.']" value="1" />';
          #$lRet.= '<input type="hidden" name="act_pos['.$lAct.']" value="'.$lPos.'" />';
          $lRet.= '<input type="checkbox" name="act_new['.$aFlagId.']['.$lAct.']" checked="checked" value="'.$lPos.'" />';
        }
        $lRet.= '</td>';

        $lRet.= '<td class="td2 nw b"> ';
        $lRet.= htm($lRow['role']);
        $lRet.= '</td>';

        $lRet.= '<td class="td1 nw w100p">&nbsp;';
        if ($this -> mIsApl) {
          $lRet.= $lRow['pos'] + 1 .' ';
        }
        $lRet.= htm($lNam);
        $lRet.= '</td>';

        $lRet.= '</tr>'.LF;
      }
    }

    return $lRet;
  }

  protected function getNotifications() {
    if (empty($this -> mNot[0])) return '';

    $lRet = '<div class="frm" style="padding:16px;">'.LF;
    $lRet= '<table cellpadding="2" cellspacing="0" class="tbl">'.LF;
    $lRet = $this -> getAllNotifications();
    $lRet.= '</table>'.LF;
    $lRet.= '</div>'.LF;

    $lPnl = new CHtm_Panel(lan('email.notif'), $lRet, 'crp.dlg.not');
    $lPnl -> setAtt('class', 'th2');
    $lPnl -> setDivAtt('class', '');
    $lRet = $lPnl -> getContent();
    return $lRet;
  }

  protected function getFlagNotifications($aFlagId) {
    if (empty($this -> mNot[$aFlagId])) return '';

    $lRet = '<div class="frm" style="padding:16px;">'.LF;
    $lRet.= '<table cellpadding="2" cellspacing="0" class="tbl">'.LF;
    $this -> mNotWithAplNotifications = false; //sonst doppelte Anzeige von div&table!
    $lRet.= $this -> getAllNotifications($aFlagId);
    $lRet.= '</table>'.LF;
    $lRet.= '</div>'.LF;

    $lPnl = new CHtm_Panel(lan('email.notif'), $lRet, 'crp.dlg.not');
    $lPnl -> setAtt('class', 'th2');
    $lPnl -> setDivAtt('class', '');
    $lRet = $lPnl -> getContent();
    return $lRet;
  }

  protected function getJs() {
    $lRet = '';
    return $lRet;
  }

  public function loadUpload() {
    if (!bitset($this -> mFla, sfUploadFile)) return '';

    if (empty($this -> mJob)) {
      $lApl = new CApp_Apl_Loop($this -> mSrc, $this -> mJobId, 'apl');
    } else {
      $lApl = new CApp_Apl_Loop($this -> mSrc, $this -> mJobId, 'apl', MID, $this -> mJob['webstatus']);
    }
    $lFiles = $lApl -> getCurrentUserFiles(CCor_Usr::getAuthId());

    $lAtt = array('class' => 'btn w200');
    $lDiv = getNum('upload');
    $lSub = "doc";

    $lJs = 'Flow.Std.ajxImg("'.$lDiv.'","'.lan('lib.file.from').'"); new Ajax.Updater("'.$lDiv.'","index.php",{parameters:';
    $lJs.= '{act:"job-apl-page-fil.upload",src:"'.$this -> mSrc.'",jid:"'.$this -> mJobId.'",sub:"'.$lSub.
           '",div:"'.$lDiv.
           '",fid:"'.$this -> mFrmId.
           '",uid:"'.CCor_Usr::getAuthId().'" } ';
    $lJs.= '});';

    $lRet = '';
    $lTid = 'FilIdX';
    $lRet.= '<div class="tbl w800">';
    $lRet.= '<div class="th1" onclick="Flow.Std.togTr(\''.$lTid.'\')">'.htm(lan('job-fil.menu')).': '.lan('job-fil.doc').'</div>';

    $lRet.= '<table cellpadding="0" cellspacing="0" class="frm" width="100%">'.LF;

    $lRet.= '<tr id="'.$lTid.'">';
    $lRet.= '<td colspan="4">';

    $lRet.= '<table cellpadding="4" cellspacing="0" class="frm" width="100%" style="border:0">'.LF;

    $lArr = explode(LF, $lFiles);
    $lFiles = '';
    $ic = 0;
    foreach ($lArr as $lFile) {
      if (!(trim($lFile) == '')) {
        $ic++;
        if ($ic == 1) {
          $lFiles.= $lFile;
        } else {
          $lFiles.= LF.$lFile;
        }
      }
    }
    if ($ic == 0) $ic = 1;

    $this -> mUserFiles = $lFiles;
    $lRet.= '<tr>';
    $lRet.= '<td width="5%"></td>';
    $lRet.= '<td>';
    $lRet.= '<td>';
    $lAli = 'userfiles';
    $lRet.= '<textarea class="frm" name="'.$lAli.'" cols="50" rows="'.$ic.'" style="border-top:0px;border-left:0px;border-bottom:0px;border-right:0px;"'.
            ' onchange="javascript:ajxAplFiles(\''.$lAli.'\',\''.$this -> mFrmId.'\')" '.
            '>'.$lFiles.'</textarea>';
    $lRet.= '</td>';

    $lRet.= '<td>';
    $lRet.= '<div id="'.$lDiv.'" style="text-align:right;">';
    $lRet.= '<form id="'.getNum($lDiv).'">';
    $lRet.= btn(lan('lib.upload'), $lJs, 'img/ico/16/new-hi.gif', 'button', $lAtt).NB.BR.BR;
    $lRet.= '</form>';
    $lRet.= '</div>';
    $lRet.= '</td>';

    $lRet.= '</tr>';
    $lRet.= '</table>';

    $lRet.= '</td>';
    $lRet.= '</tr>';
    $lRet.= '</table>';
    $lRet.= '</div>';

    // $this -> setFormTag('<form id="'.$this -> mFrmId.'" action="index.php" method="post" enctype="multipart/form-data">');
    // $lRet.= '<form id="'.$this -> mFrmId.'" action="index.php" method="post" enctype="multipart/form-data" target="'.$this -> mTarget.'">';
    $lPnl = new CHtm_Panel(lan('job-fil.menu'), $lRet, 'crp.dlg.upl');
    $lPnl -> setAtt('class', 'th2');
    $lPnl -> setDivAtt('class', '');
    $lRet = $lPnl -> getContent();

    $lRet = '<div class="tbl w800">';
    $lRet.= $lPnl -> getContent();
    $lRet.= '</div>';

    #$lDivX = getNum($lDiv);
    #$lTaxX = getNum($lDivX);
    #$lRet = '<form id="'.$lDivX.'" action="index.php" method="post" enctype="multipart/form-data" target="'.$lTaxX.'">'.$lRet.'</form>';

    $this -> mUpload = $lRet;
  }

  protected function getFormTag() {
    $lRet = '';
    $lRet = '<div class="tbl w800">';
    $lRet.= '<!-- '.get_class($this).'getFormTag preTitle -->'.LF;
    $lRet.= $this -> preTitle();
    $lRet.= '</div>';

    $lAplf = ($this -> mApl_Notify AND empty($this -> mDialogStep));
    if (!$lAplf) {
      $lRet.= '<!-- '.get_class($this).'getFormTag mUpLoad -->'.LF;
      $lRet.= $this -> mUpload;
    }

    $lRet.= parent::getFormTag();
    return $lRet;
  }

  protected function getButtons() {
    $lRet = '';

    if (bitset($this -> mFla, sfUploadFile)) {
      $lRet.= '<input type="hidden" name="listuserfiles" value="'.htm($this -> mUserFiles).'" />';
    }
    $lRet.= '<div class="btnPnl">'.LF;

    if ($this -> mApl_Notify AND !empty($this -> mDialogStep) ) {
      $lRet.= btn(lan('lib.ok'), 'return Flow.Std.ListCheck("'.LAN.'");', 'img/ico/16/ok.gif', 'submit').NB;
    } elseif (bitset($this -> mFla, sfStartApl)) {
      $lRet.= btn(lan('lib.ok'), 'Flow.Std.fprAll(\'dst\')', 'img/ico/16/ok.gif', 'submit').NB;
    } else {
      $lRet.= btn(lan('lib.ok'), '', 'img/ico/16/ok.gif', 'submit').NB;
    }
    if (!empty($this -> mCancel)) {
      $lRet.= btn(lan('lib.cancel'), "go('index.php?act=".$this -> mCancel."')", 'img/ico/16/cancel.gif');
    }
    $lRet.= '</div>'.LF;
    return $lRet;
  }

  /**
   * Get Last Open Loop Id
   * @param $this->mSrc string Jobtyp
   * @param $this->mJobId string JobId
   * @return $lRet string Last Open LoopId
   */
  protected function getLastOpenLoop(){
    $lRet = '';
    $lApl = new CApp_Apl_Loop($this -> mSrc,$this -> mJobId);
    $lRet = $lApl-> getLastOpenLoop();
    return $lRet;
  }

  /**
   * Invited User and GroupList
   * @param $aLoopId int Last Open LoopId
   * @return array
   */
  protected function getOpenLoopInvitedUser($aLoopId){
    $lSql = 'SELECT * FROM al_job_apl_states WHERE 1';
    $lSql.= ' AND inv="Y"';
    $lSql.= ' AND del="N"'; // Not Deleted User
    $lSql.= ' AND loop_id='.$aLoopId;
    $lSql.= ' ORDER BY pos';

    $lQry = new CCor_Qry($lSql);

    $lAplUserArr = array();
    foreach ($lQry as $lRow) {
      if (0 == $lRow['gru_id'] AND !isset($lAplUserArr[ $lRow['user_id'] ])) {
        $lAplUserArr[] = $lRow['user_id'];
      } else {
        if (!empty($lRow['comment']) OR !isset($lAplUserArr["G".$lRow['gru_id']])) {
          $lAplUserArr[] = "G".$lRow['gru_id'];
        }
      }
    }
    return $lAplUserArr;
 }

 public function setCountryEventIds($aArr) {
   $this->mCtrEveIds = $aArr;
 }

 public function addCountryAplSelections($aType = 'apl') {
   $this->prependCountryApls($aType);
   $this->prependForm('<div id="apl_add_dlg"></div>');
   $this->mIsCountryApl = TRUE;
 }

 protected function getFinder() {
   if (!isset($this->mFinder)) {
     $this->mFinder = new CJob_Apl_Finder();
   }
   return $this->mFinder;
 }

 protected function prependCountryApls($aType = 'apl') {
   $lCountries = $this->mJob[$this->mCountryAplField];
   $lArr = explode(',', $lCountries);
   if (empty($lArr)) return;

   foreach ($lArr as $lCountry) {
     $this->prependForm($this->getCountryApl($lCountry, $aType));
   }
 }

 public function setCanDeselectCountry($aFlag = true) {
   $this->mCanDeselectCountry = $aFlag;
 }

 protected function encKey($aCtr) {
   #return htm("'".$aCtr."'");
   if (empty($aCtr)) $aCtr = ' ';
   return base64_encode($aCtr);
 }

 protected function getCountryApl($aCtr = '', $aType = 'apl') {
   $lFinder = $this->getFinder();

   $lFinder->setEventType($aType);
   if ($this->mStepId == 1052) {
     $lFinder->setEventType('apl');
   }
   if (($this->mStepId == 1057) or ($this->mStepId == 1146)) {
     $lFinder->setEventType('apl-ord');
   }
   if ($this->mStepId == 1171) {
     $lFinder->setEventType('apl-rep');
   }

   if ($this->mStepId == 1238) {
     $lFinder->setEventType('apl-nau');
   }

   if ($this->mStepId == 1270) {
     $lFinder->setEventType('apl-icn');
   }

   $lJob = $this->mJob->toArray();
   $lJob[$this->mCountryAplField] = $aCtr;
   $lJid = $lJob['jobid'];
   $lFinder->setJob($lJob)->setCountry($aCtr);

   $lEvents = $lFinder->getMatchingEvents();

   $lNum = getNum('i');
   $lRet = '<div class="frm p16">';
   #$lRet.= $this->getSelect('eve['.$aCtr.']', $lEvents);

   $lRet.= '<div style="float:left; margin-right:1em;">';
   #$lRet.= '<div style="margin-bottom:1em;">';
   $lEveId = getNum('i');
   $lRet.= '<div class="th2">Template</div>';
   $lRet.= '<select id="'.$lEveId.'" name="eve['.$this->encKey($aCtr).']" size="10" class="w300 apl-tpl" onchange="Flow.apl.getOverview(this.value,\''.$lJid.'\',\''.$aCtr.'\',\''.$lNum.'\')">';

   #$lRet.= '<select name="eve['.$aCtr.']" size="1" onchange="Flow.getAplOverview(this.value,\''.$lNum.'\')" style="width:100%">';
   $lSelectedId = 0;
   if (!empty($this->mCtrEveIds)) {
     if (!empty($this->mCtrEveIds[$aCtr])) {
       $lSelectedId = $this->mCtrEveIds[$aCtr];
     }
   }
   $lOnlyOne = (count($lEvents) == 1);
   foreach ($lEvents as $lId => $lName) {
     $lSelected = (($lSelectedId == $lId) || $lOnlyOne) ? ' selected="selected"' : '';
     $lRet.= '<option value="'.$lId.'"'.$lSelected.'>'.htm($lName).'</option>';
     if ($lOnlyOne) {
       $lSelectedId = $lId;
     }
   }
   $lRet.= '</select>';

   $lRet.= '</div>';

   $lObj = new CJob_Apl_Preview($lJid, $lNum);
   $lObj->setJob($this->mJob);
   if ($this->mCanDeselectCountry) {
     $lObj->setCheck(true);
   }
   if (!empty($lSelectedId)) {
     $lObj->setEventId($lSelectedId)->loadActions();
     $lObj->saveToSession($aCtr);
     $lObj->loadPreviousAplStates($aType, $aCtr);
   }
   $lRet.= '<div style="float:left; margin-right:1em;">';
   $lRet.= '<div id="'.$lNum.'" class="bc-apl">';
   $lRet.= $lObj->getContent();
   $lRet.= '</div>'.BR;

   $lRet.= $this -> getAddRevisorButtons($lJid, $aCtr, $lNum, $lEveId, $this->mCanDeselectCountry);

   $lRet.= '</div>';

   $lRet.= '<div style="clear:both"></div>';
   $lRet.= '</div>';
   $lRet.= $this -> getWarningMsg();
   //$lRet = var_export($lFinder->getMatchingEvents(), TRUE);

   $lCtrHtb = CCor_Res::get('htb', 'ctr');

   $lPnl = new CHtm_Panel('Approval Workflow', $lRet);
   #$lPnl = new CHtm_Panel('Approval Loop: '.$aCtr.' - '.$lCtrHtb[$aCtr], $lRet);
   $lPnl -> setAtt('class', 'th2');
   $lPnl -> setDivAtt('class', '');
   return $lPnl -> getContent();
 }
 
 /**
  * Empty method coud be rewritten at the cust or mand level
  * needed to display a special warning message for BC, request id=771
  * @return string
  */
 protected function getWarningMsg() {
   return '';
 }

 protected function getAddRevisorButtons($aJid, $aCtr, $aNum, $aEveId, $aCanDeselect = FALSE) {
   $lRet = '<a href="javascript:Flow.apl.showAddRevisorDlg(\'job-'.$this->mSrc.'\',\''.$aJid.'\',\''.$aCtr.'\',\''.$aNum.'\', \'\', \''.$aCanDeselect.'\')" class="nav fl" style="margin-right:16px">Add Revisor</a>';
   #$lRet.= '<a href="javascript:Flow.apl.showAddReaderDlg(\'job-rep\',\''.$lNum.'\')" class="nav fl" style="margin-right:16px">Add Reader</a>';
   #$lRet.= '<a href="javascript:Flow.apl.updateOverview(\''.$lJid.'\',\''.$aCtr.'\',\''.$lNum.'\')" class="nav fl" style="margin-right:16px">Update</a>';
   $lRet.= '<a href="javascript:Flow.apl.getOverview($F(\''.$aEveId.'\'),\''.$aJid.'\',\''.$aCtr.'\',\''.$aNum.'\')" class="nav fl" style="margin-right:16px">Reset</a>';
   return $lRet;
 }

}
