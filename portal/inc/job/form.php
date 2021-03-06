<?php
/**
 * Jobs: Formular
 *
 *  Description
 *
 * @package    JOB
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 14548 $
 * @date $Date: 2016-06-15 13:48:01 +0200 (Wed, 15 Jun 2016) $
 * @author $Author: ahajali $
 */
class CInc_Job_Form extends CCor_Tpl {

  protected $mAllFlags = array();
  protected $mAct;    // Controller action to set if ok is pressed
  protected $mAssignedProId;
  protected $mCancel; // URL for cancel button. Leave empty if no cancel button should be available
  protected $mExistingStatus = array();
  protected $mFac;
  protected $mFie = array();
  protected $mFla = 0;
  protected $mFlagConfirmed = array();
  protected $mJobId;
  protected $mMandatoryFieldsByJob = array();
  protected $mMandatoryFieldsByStatus = array();
  protected $mPar = array();
  protected $mPag = array();
  protected $mShowCopyPanel = TRUE;
  #protected $mShowFlags = array();
  protected $mSrc;    // Jobtype
  public    $mSrcArr = array(); // Available JobTyp
  protected $mTemplates  = array();
  protected $mVal = array();
  protected $mFieldStates = array();

  public function __construct($aSrc, $aAct, $aPage = 'job', $aJobId = '') {
    $this -> mHiddenUpload = FALSE;
    $this -> mSrc = $aSrc;
    $this -> mAct = $aAct;
    $this -> mJobId = $aJobId;
    $this -> mTab = (empty($aPage)) ? 'job' : $aPage;
    $this -> setParam('act', $aAct);
    $this -> mJob = array();
    $this -> mUsr = CCor_Usr::getInstance();
    $this -> mCanEdit = $this -> mUsr -> canEdit('job-'.$aSrc);
    $this -> mAllFlags = CCor_Res::get('fla');
    $this -> mAllActions = CCor_Res::get('action');
    #$this -> mCancel = substr($aAct, 0 , $lPos);

    $this -> getFac();
    $this -> mPnl = new CJob_Btnpanel();
    $this -> addPanel('act', 'Actions');

    $this -> openProjectFile('job/main.htm');
    $this -> mRole = array();
    $this -> mFie = CCor_Res::getByKey('alias', 'fie');
    #$this -> isMandatory();
    $this -> mState = fsStandard;
    $this -> mSrcArr = CCor_Cfg::get('all-jobs'); //array('art', 'rep', 'sec', 'mis', 'adm', 'com', 'tra');
    $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');
    if (in_array($this -> mSrc, $this -> mSrcArr) OR 'sku' == $this -> mSrc) $lSrc = $this -> mSrc;
    else $lSrc = 'pro';
    $this -> mCrpId = $lCrp[$lSrc];
    $this -> checkCopyPanel();
    #$this -> mShowFlags = CApp_Apl_Loop::showFlagButtons($this -> mSrc, $this -> mJobId, $this -> mAllFlags);
    #$this -> mFlagConfirmed = $this -> isFlagConfirmed();
    $this -> loadTabs();
  }

  protected function checkCopyPanel() {
    // Add Button 'Copy to', Show Copy Panel
    if (($this -> mSrc != 'pro') && !empty($this -> mJobId)) {
      $lSql = 'SELECT id,pro_id FROM al_job_sub_'.MID.' WHERE jobid_'.$this -> mSrc.'='.esc($this -> mJobId);
      $lQry = new CCor_Qry($lSql);
      $lRow = $lQry -> getDat();
      if ($lRow) {
        $this -> mProjectId = $lRow['pro_id'];
        $this -> mProItemId = $lRow['id'];
      }

      $this -> mCopyJob = $this -> mUsr -> canCopyJob($this -> mSrcArr);
      if ($this -> mShowCopyPanel) {
        if (!empty($this -> mCopyJob)) {
          $this -> addCopyPanel();
        }
      }
    }
  }

  protected function loadTabs() {
    // get default tabs
    $lCfg = CCor_Cfg::getInstance();
    $this -> lDefaultTabs = $lCfg -> get('job.mask.tabs');

    // Angabe der Jobmasken in mand/mand_Nr/mand/inc/job/formtpl.php - OHNE Beschr�nkung, OHNE Funktionen!
    $FormTpl = new CJob_Formtpl();
    $this -> mTemplates = $FormTpl -> mTemplates;

    // Webcenter ProjektId nur f�r Jobs "art, rep" und mit dem Recht "job-wec-id" verkn�pft.
    // Falls es in der Job-Maske keine Reiter "Details (det)" gibt, soll es unter "Identifikation (job)" angezeigt werden.
    $lUsr = CCor_Usr::getInstance();
    if(in_array($this -> mSrc, array('rep','art')) AND $lUsr -> canEdit('job-wec-id')) {
      if (in_array('det',$this -> lDefaultTabs)) {
        $this -> mTemplates['rep']['det']['wec'] = 'rep';
      } else {
        $this -> mTemplates['rep']['job']['wec'] = 'rep';
      }
    } else {
      unset($this -> mTemplates['rep']['job']['wec']);
    }

    $lAdditionalTab = CCor_Qry::getArr('SELECT link FROM al_tab_slave WHERE mand='.MID.' AND code="'.$this -> mTab.'" AND type="job" AND subtype="'.$this -> mSrc.'";');
    if (!empty($lAdditionalTab)) {
      $lResult = array($this -> mTab => array($lAdditionalTab => $this -> mSrc));
      $this -> mTemplates[$this -> mSrc] = array_merge($this -> mTemplates[$this -> mSrc], $lResult);
    }
  }

  protected function preSet() {
  }

  protected function getFac() {
    if (isset($this -> mFac)) {
      return;
    }
    if (!empty($this -> mJobId)) {
      $this -> mFac = new CHtm_Fie_Fac($this -> mSrc, $this -> mJobId);
    } else {
      $this -> mFac = new CHtm_Fie_Fac();
    }
  }

  public function setFieldState($aAlias, $aState) {
    $this -> mFieldStates[$aAlias] = $aState;
  }

  protected function addPage($aKey) {
    $lPag['key'] = $aKey;
    $lPag['par'] = array();
    $this -> mPag[$aKey] = $lPag;
  }

  protected function addPart($aPage, $aKey, $aRc = NULL) {
    if(isset($aRc)) {
      $lPar['src'] = $aRc;
    }
    $lPar['pag'] = $aPage;
    $lPar['key'] = $aKey;
    $this -> mPag[$aPage]['par'][$aKey] = $lPar;
  }

  public function addDef($aDef) {
    $lAlias = $aDef['alias'];
    $this -> mFie[$lAlias] = $aDef;
  }

  protected function & getDef($aAlias) {
    if (isset($this -> mFie[$aAlias])) {
      return $this -> mFie[$aAlias];
    } else {
      return NULL;
    }
  }

  public function setParam($aKey, $aValue) {
    $this -> mPar[$aKey] = $aValue;
  }

  public function getParam($aKey) {
    if (isset($this -> mPar[$aKey])) {
      return $this -> mPar[$aKey];
    } else {
      return NULL;
    }
  }

  protected function getHiddenFields() {
    if (empty($this -> mPar)) {
      return;
    }
    $lRet = '';
    foreach ($this -> mPar as $lKey => $lVal) {
      $lRet.= '<input type="hidden" name="'.$lKey.'" value="'.htm($lVal).'" />'.LF;
    }
    return $lRet;
  }

  public function getVal($aKey, $aDefault = '') {
    if (isset($this -> mJob[$aKey])) {
      return $this -> mJob[$aKey];
    } else {
      return $aDefault;
    }
  }

  public function setVal($aKey, $aValue) {
    $this -> mVal[$aKey] = $aValue;
  }

  public function assignVal($aArr) {
    if (empty($aArr)) {
      return;
    }
    foreach ($aArr as $lKey => $lVal) {
      $this -> setVal($lKey, $lVal);
    }
  }

  public function addPanel($aKey, $aCaption) {
    $this -> mPnl -> addPanel($aKey, $aCaption, '', 'job_'.$aKey);
  }

  public function addBtn($aKey, $aCaption, $aAction = '', $aImg = '', $aType = 'button', $aAttr = array()) {
    if ($aAction == '') {
      $aAction = 'return checkMandatoryFieldsByJob();';
    }

    $this -> mPnl -> addBtn($aKey, $aCaption, $aAction, $aImg, $aType, $aAttr);
  }

  public function addButton($aKey, $aBtn) {
    $this -> mPnl -> addButton($aKey, $aBtn);
  }

  protected function getContTabs() {
    if (isset($this -> mTabs)) {
      return $this -> mTabs -> getContent();
    }
    return '';
  }

  protected function getContHeader() {
    if (isset($this -> mHeader)) {
      return $this -> mHeader -> getContent();
    }
    return '';
  }

  protected function getBlockFilter($aPar) {
    return FALSE;
  }

  protected function getPartForms($aPar) {
    // SonderFunktion unter CUST 340
    $lFilteredBlocks = $this -> getBlockFilter($aPar);
    $lPar = ($lFilteredBlocks) ? $lFilteredBlocks : $aPar;
    $lRet = '';
    foreach ($lPar as $lKey => $lVal) {
      if(!isset($lVal['src'])) {
        $lPar = new CJob_Part($this -> mSrc, $lKey, $this -> mFac, $this -> mJob);
      } else {
        $lPar = new CJob_Part($lVal['src'], $lKey, $this -> mFac, $this -> mJob);
      }

      if ($this -> mHiddenUpload) {
        $lPar -> setHidden($this -> mHiddenUpload);
      }

      if (!$this -> mCanEdit) {
        $lPar -> setDisabled(TRUE);
      } else {
        $lPar -> setState($this -> mState);
      }
      $lRet.= $lPar -> getContent();
    }
    return $lRet;
  }


  protected function getPages() {
    $lRet = '';
    foreach ($this -> mPag as $lKey => $lPag) {
      $lDis = ($this -> mTab == $lKey) ? 'block' : 'none';
      $lRet.= '<div style="display:'.$lDis.'" id="pag'.$lKey.'">'.LF;
      $lRet.= $this -> getPartForms($lPag['par']);
      $lRet.= '</div>';
    }
    $lRet.= $this -> getMandatoryFieldsCheckJs();
    return $lRet;
  }

  protected function getContButtons() {
    if (isset($this -> mPnl)) {
      return $this -> mPnl -> getContent();
    }
    return '';
  }

  public function setLang($aLang = NULL) {
    $lLoc = (NULL == $aLang) ? LAN : $aLang;
    parent::setLang($lLoc);
    $lArr = CCor_Res::get('fie');
    $lUsr = CCor_Usr::getInstance();

    foreach ($lArr as $lFie) {
      $lAlias = $lFie['alias'];
      $lCaption = htm($lFie['name_'.$lLoc]);
      if (($lFie['flags'] & ffMandatory) == ffMandatory) {
        $lCaption.='*';
      }

      // If Jobfield has Read Flag active, ask for User READ-RIGHT (combination of 'fie_' and Alias).
      // If User has no READ-RIGHT, dont show the Jobfield.
      $lFieRight = 'fie_'.$lAlias;
      if (bitset($lFie['flags'],ffRead) && !$this -> mUsr -> canRead($lFieRight)) { // If Edit Flag active
        $this -> setPat('bez.'.$lAlias, '');
        continue;
      }

 	  $lAlias = 'bez.'.$lAlias;
/* 	  if ($lUsr -> getPref('job.feldtips', 'Y') == 'Y')
 	  {
 	  	$lDes = htm($lFie['desc_'.$lLoc]);
 	  	if (!empty($lDes))
 	  	{
	 	  	$lDes = preg_replace("/[\n\r\'|&#0*39;]/"," ",$lDes);
	      	$lCaption = toolTip(nl2br($lDes), $lCaption).$lCaption.'</span>';
 	  	}

      }
*/
      $this -> setPat($lAlias, $lCaption);
    }
  }

  protected function addRoles() {
  	$lUid = $this -> mUsr -> getId();
  	$lUsr = CCor_Usr::getInstance();
  	$lUsrGroups = $lUsr -> getMemArray();

  	$lGrpBackups = array();
  	$lUsrBackups = $lUsr -> getAllAbsentUsersIBackup();
  	foreach($lUsrBackups as $lBackupUsr) {
  	  $lBackUsr = new CCor_Anyusr($lBackupUsr);
  	  $lBackupGrps = $lBackUsr -> getMemArray();
  	  $lGrpBackups = array_merge($lGrpBackups, $lBackupGrps);
  	}

    foreach ($this -> mFie as $lAli => $lDef) {
      if (!in_array($lDef['typ'], array('uselect','gselect'))) continue;
      $lVal = $this -> getVal($lAli);
      if ($lDef['typ'] == 'uselect' && $lVal == $lUid OR in_array($lVal, $lUsrBackups)) {
        $this -> addRole($lAli);
      } if($lDef['typ'] == 'gselect' && in_array($lVal, $lUsrGroups) OR in_array($lVal, $lGrpBackups)) {
        $this -> addRole($lAli);
      }
    }
  }

  protected function addRole($aAlias) {
    $this -> dbg('ADDING ROLE '.$aAlias);
    $this -> mRole[$aAlias] = $aAlias;
  }

  protected function hasRole($aAlias = NULL) {
    if (NULL == $aAlias) {
      return !empty($this -> mRole);
    }
    if ($this -> mRole) {
      if ($aAlias) {
        if(isset($this -> mRole[$aAlias])) {
          return ($this -> mRole[$aAlias]);
        } else {
          return FALSE;
        }
      }
    }
  }

  protected function loadFlagRoleRights() {
    $this -> mRolFlag = array();
    if (empty($this -> mRole)) {
      return;
    }
    $lSql = 'SELECT id FROM al_rol WHERE mand='.MID.' AND alias IN (';
    foreach ($this -> mRole as $lAli) {
      $lSql.= '"'.addslashes($lAli).'",';
    }
    $lSql = strip($lSql).')';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lArr[] = $lRow['id'];
    }
    if (empty($lArr)) return;
    $lSql = 'SELECT fla_id,crp_id FROM al_rol_rig_stp WHERE fla_id!= 0 AND role_id IN ('.implode(',', $lArr).')';
    $lQry -> query($lSql);
    foreach ($lQry as $lRow) {
      $this -> mRolFlag[$lRow['fla_id']][$lRow['crp_id']] = TRUE;
    }
  }

  protected function canConfirmFlagAsRole($aFlagId, $aCrpId) {
    if (!isset($this -> mRolFlag)) {
      $this -> loadFlagRoleRights();
    }
    return isset($this -> mRolFlag[$aFlagId][$aCrpId]);
  }

  protected function canConfirmFlag($aFlagId, $aCrpId) {
    $lFlag = intval($aFlagId);
    $lCrpId = intval($aCrpId);
    if ($this -> mUsr -> canConfirmFlag($lFlag, $lCrpId)) {
      return TRUE;
    }
    if ($this -> hasRole()) {
      return $this -> canConfirmFlagAsRole($lFlag, $lCrpId);
    }
    return FALSE;
  }

  protected function loadRoleRights() {
    $this -> mRolStp = array();
    if (empty($this -> mRole)) {
      return;
    }
    $lSql = 'SELECT id FROM al_rol WHERE mand='.MID.' AND alias IN (';
    foreach ($this -> mRole as $lAli) {
      $lSql.= '"'.addslashes($lAli).'",';
    }
    $lSql = strip($lSql).')';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lArr[] = $lRow['id'];
    }
    if (empty($lArr)) return;
    $lSql = 'SELECT stp_id FROM al_rol_rig_stp WHERE role_id IN ('.implode(',', $lArr).')';
    $lQry -> query($lSql);
    foreach ($lQry as $lRow) {
      $this -> mRolStp[$lRow['stp_id']] = TRUE;
    }
  }

  protected function canStepRole($aStepId) {
    if (!isset($this -> mRolStp)) {
      $this -> loadRoleRights();
    }
    return isset($this -> mRolStp[$aStepId]);
  }

  protected function canStep($aStepId) {
    $lStp = intval($aStepId);
    // darf es NICHT geben, da die step mandantenabhaengig unterschiedlich sind!
    //$lFnc = 'canStep'.$lStp;
    //if ($this -> hasMethod($lFnc)) {
    //  return $this -> $lFnc();
    //}
    return $this -> hasRight($aStepId);
  }

  protected function hasRight($aStepId) {
    $lStp = intval($aStepId);
    if ($this -> mUsr -> canStep($lStp)) {
      return TRUE;
    }
    if ($this -> hasRole()) {
      return $this -> canStepRole($lStp);
    }
    return FALSE;
  }

  // Can User the Job Edit by this CRP Status ?
  protected function canStatus($aStatusId) {
    $lStp = intval($aStatusId);
    if ($this -> mUsr -> canStatus($lStp)) {
      return TRUE;
    } else return FALSE;
  }

  protected function canStatusEdit() {
    $lSta = intval($this -> mJob['webstatus']);
    if (empty($lSta)) return; // new job
    $lCrp = CCor_Res::extract('status', 'id', 'crp', $this -> mCrpId);
    if (!empty($lCrp) AND isset($lCrp[$lSta])) {
      $lSid = $lCrp[$lSta];

      // ASk first if CRP Status requires Rights for edit.
      // Feld "Flags" ist converted to BINARY to find out if the 4.Bit(008 -> Edit Rights) ist equal to 1.
      // Substring (Flags,5,1) get 4.Bit because Edit Rights = 8
      // There must be "008 - Editing privileges necessary " in the Helptabel.

      $lSql = " SELECT id FROM al_crp_status WHERE SUBSTRING(LPAD(BIN(flags) , 8, '0') , 5, 1) = '1'  AND mand='".MID."' AND id='".$lSid."'";
      $lQry = new CCor_Qry($lSql);
      if ($lRow = $lQry -> getAssoc()) {
        $this -> dbg('CRP Status = '.$lSid.' requires Rights for Job-Edit');
        if (!$this -> canStatus($lSid)) {
          $this -> msg('User can not Edit by this Critical Path Status ', mtUser, mlWarn);
          $this -> mCanEdit = FALSE;
        }
      }
    }
  }

  protected function canAssign() {
    $lRet = FALSE;
    if ('pro' == $this -> mSrc) return FALSE;
    if (empty($this -> mJobId)) return FALSE;
    if (!$this -> mCanEdit OR !$this -> mUsr -> canRead('job-pro')) return FALSE;
    $lSql = 'SELECT pro_id FROM al_job_sub_'.intval(MID).' WHERE jobid_'.$this -> mSrc.'='.esc($this -> mJobId);
    $lRet = CCor_Qry::getInt($lSql);
    if ($lRet) {
      $this -> mAssignedProId = $lRet;
    }
    return !$lRet;
  }

  /**
   * assign sku to project
   *
   * the button is visible only when there are projects
   * and the current sku is not yet assigned to any of these
   */
  protected function canAssignSkuSur() {
    if (empty($this -> mJobId)) return FALSE;
    if (!$this -> mUsr -> canEdit('job-sku')) return FALSE;
    $lSql = 'SELECT id FROM al_job_pro_'.intval(MID);
    if (0 < CCor_Qry::getInt($lSql)) {
      $lSql = 'SELECT pro_id FROM al_job_sku_sur_'.intval(MID).' WHERE sku_id='.esc($this -> mJobId);
      return !CCor_Qry::getInt($lSql);
    } else {
      return FALSE;
    }
  }

  /**
   * assign job to sku
   *
   * the button is visible only when there are skus
   */
  protected function canAssignSkuSub() {
    if (empty($this -> mJobId)) return FALSE;
    if (!$this -> mUsr -> canEdit('job-sku')) return FALSE;
    $lSql = 'SELECT COUNT(id) FROM al_job_sku_'.intval(MID);
    return CCor_Qry::getInt($lSql);
  }

  public function getSetStatusButtonMenu() {
    $lMen = new CHtm_Menu('Button');
    #	$lMen -> addTh2(lan('crp-stp.change'));

    // Ab welchem Status ist der Job in Produktion (existiert die JobId != A000...)
    #$lSql = 'SELECT `to_id` FROM `al_crp_step` WHERE `mand`='.MID.' AND `trans` LIKE '.esc("anf2job").' AND `crp_id`='.$this -> mCrpId;
    #$lSta2Prod = CCor_Qry::getInt($lSql);
    #$lSql = 'SELECT `status` FROM `al_crp_status` WHERE `id`='.$lSta2Prod;
    #$this -> mWebSta2Prod = CCor_Qry::getInt($lSql);

    $lArr = CCor_Res::getByKey('status', 'crp', $this -> mCrpId);
    foreach ($lArr as $lSta => $lRow) {
      if ($this -> mWebSta2Prod <= $lSta AND  $lSta < $this -> mWebSta2Arc) {
        $lIco = CApp_Crpimage::getCrpIco($this->mSrc, $lRow['display'].'b');
        $lMen -> addItem('index.php?act=job-'.$this -> mSrc.'.setcomment&src='.$this -> mSrc.'&sta='.$lSta.'&jobid='.$this -> mJobId, $lRow['name_'.LAN], $lIco);
      }
    }
    $lLnk = "javascript:gIgn=1;Flow.Std.popMen('".$lMen -> mDivId."')";
    $lBtn = btn(lan('crp-stp.change'), $lLnk, 'img/ico/16/ml-2.gif', 'button', array('class' => 'btn w200','id' => $lMen -> mLnkId));
    $lBtn .= $lMen -> getMenuDiv();
    return $lBtn;
  }

  public function getCopyButtonMenu($aAttr = array()) {
    $lMen = new CHtm_Menu('Button');
    foreach ($this -> mCopyJob as $lSta) {
      $lImg = (THEME == 'default' ? 'job-'.$lSta : CApp_Crpimage::getColourForSrc($lSta)) . '.gif';
      $lMen -> addItem('index.php?act=job-'.$lSta.'.cpy&amp;jobid='.$this -> mJobId.'&amp;src='.$this -> mSrc.'&amp;target='.$lSta, lan('lib.copy_to').' '.lan('job-'.$lSta.'.menu'), 'ico/16/'.$lImg);
    }
  	$lLnk = (THEME === 'default' ? "Flow.Std.popMain('".$lMen -> mDivId."')" : "javascript:gIgn=1;Flow.Std.popMen('".$lMen -> mDivId."')");
    $lRet = btn(lan('lib.copy_to'), $lLnk, 'img/ico/16/copy.gif', 'button', array('class' => 'btn w200','id' => $lMen -> mLnkId));
    $lRet.= $lMen -> getMenuDiv();

    if (!empty($this -> mProjectId)) {
      $lRet.= BR.BR;
      $lMen = new CHtm_Menu('Button');
      foreach ($this -> mCopyJob as $lSta) {
        $lImg = (THEME == 'default' ? 'job-'.$lSta : CApp_Crpimage::getColourForSrc($lSta)) . '.gif';
        $lMen -> addItem('index.php?act=job-'.$lSta.'.cpy&amp;jobid='.$this -> mJobId.'&amp;src='.$this -> mSrc.'&amp;target='.$lSta.'&amp;pid='.$this -> mProjectId.'&amp;itmid='.$this -> mProItemId.'&amp;proid='.$this -> mProjectId, lan('lib.copy_to').' '.lan('job-'.$lSta.'.menu'), 'ico/16/'.$lImg);
      }
  	  $lLnk = (THEME === 'default' ? "Flow.Std.popMain('".$lMen -> mDivId."')" : "javascript:gIgn=1;Flow.Std.popMen('".$lMen -> mDivId."')");
      $lRet.= btn(lan('lib.copy_in_project'), $lLnk, 'img/ico/16/copy.gif', 'button', array('class' => 'btn w200','id' => $lMen -> mLnkId));
      $lRet.= $lMen -> getMenuDiv();
    }
    return $lRet;
  }

  protected function addReassignButton() {
    if (empty($this -> mJobId)) return;
    if ('pro' == $this -> mSrc) return FALSE;
    if (!$this -> mCanEdit OR !$this -> mUsr -> canRead('job-pro')) return FALSE;
    $lSql = 'SELECT pro_id FROM al_job_sub_'.intval(MID).' WHERE jobid_'.$this -> mSrc.'='.esc($this -> mJobId);
    $lRet = CCor_Qry::getInt($lSql);
    if ($lRet) {
      $this -> addBtn('act', lan('Reassign to other project'), 'go("index.php?act=job-'.$this -> mSrc.'.assignprj&jobid='.$this -> mJobId.'&prjid='.$lRet.'&pid='.$lRet.'")', 'img/ico/16/next-hi.gif', 'button', array('class' => 'btn w200'));
    }
  }

  /**
   * Job Flag Buttons (Cancel,On Hold....)
   * Depending on Right ..... and Role 'per_prj_verantwortlich'
   * Flags 'Color' and 'PDF Approval' are be activated in the CRP.
   * @Return Job Flag Buttons
   */

  protected function addFlagButtons() {
    $lSta = intval($this -> mJob['webstatus']);
    $lJflSet = Array(); // set_de or set_en from al_jfl
    $lJflReset = Array(); //reset_de or reset_en from al_jfl

    if ($this -> hasRole('per_prj_verantwortlich') OR $this -> mUsr -> canRead('jfl')) {
      $lJflSet = CCor_Res::extract('val','set_'.LAN,'jfl');
      $lJflReset = CCor_Res::extract('val','reset_'.LAN,'jfl');

      $lAtt = array();
      $lAtt['class'] = 'btn w200';
      if (bitset($this -> mFla, jfOnhold)) {
        $this -> addBtn('jfl', $lJflReset[jfOnhold], 'go("index.php?act=job-'.$this -> mSrc.'.jfl&flag=-'.jfOnhold.'&jobid='.$this -> mJobId.'")', 'img/ico/16/ml-2.gif', 'button', $lAtt);
      } else {
        $this -> addBtn('jfl', $lJflSet[jfOnhold], 'go("index.php?act=job-'.$this -> mSrc.'.jfl&flag='.jfOnhold.'&jobid='.$this -> mJobId.'")', 'img/ico/16/ml-2.gif', 'button', $lAtt);
      }
      if (bitset($this -> mFla, jfCancelled)) {
        $this -> addBtn('jfl', $lJflReset[jfCancelled], 'go("index.php?act=job-'.$this -> mSrc.'.jfl&flag=-'.jfCancelled.'&jobid='.$this -> mJobId.'")', 'img/ico/16/ml-4.gif', 'button', $lAtt);
#        $this -> addArchiveButtons();
      } else {
        $this -> addBtn('jfl', $lJflSet[jfCancelled], 'go("index.php?act=job-'.$this -> mSrc.'.jfl&flag='.jfCancelled.'&jobid='.$this -> mJobId.'")', 'img/ico/16/ml-4.gif', 'button', $lAtt);
      }
      #if (bitset($this -> mFla, jfPrinter)) {
      #  $this -> addBtn('jfl', 'Reset Printer Request', 'go("index.php?act=job-'.$this -> mSrc.'.jfl&flag=-'.jfPrinter.'&jobid='.$this -> mJobId.'")', 'img/jfl/'.jfPrinter.'.gif', 'button', $lAtt);
      #} else {
      #  $this -> addBtn('jfl', 'Printer Request', 'go("index.php?act=job-'.$this -> mSrc.'.jfl&flag='.jfPrinter.'&jobid='.$this -> mJobId.'")', 'img/jfl/'.jfPrinter.'.gif', 'button', $lAtt);
      #}

      // Colour and PDF Approved Buttons are only active,if in the crp for aktuell Status the flags are activated.
      $lCrpArr = Array();
      $lColorApp = Array(); // on Which Status is Proof Freigabe
      $lPdfApp = Array();
      $lCrpArr = CCor_Res::get('crp',$this -> mCrpId);

      foreach ($lCrpArr as $lRow) {
        if ($lRow['status'] == $lSta) {
          if (bitset($lRow['flags'],staflaColorApproval)) {
            $lColorApp[] = $lSta;
          }
          if (bitset($lRow['flags'],staflaPdfApproval)) {
            $lPdfApp[] = $lSta;
          }
        }
      }

      if (!empty($lColorApp) AND in_array($lSta, $lColorApp)) {
        if (bitset($this -> mFla, jfColorApproved)) {
          $this -> addBtn('jfl', $lJflReset[jfColorApproved], 'go("index.php?act=job-'.$this -> mSrc.'.jfl&flag=-'.jfColorApproved.'&jobid='.$this -> mJobId.'")', 'img/jfl/'.jfColorApproved.'.gif', 'button', $lAtt);
        } else {
          $this -> addBtn('jfl', $lJflSet[jfColorApproved], 'go("index.php?act=job-'.$this -> mSrc.'.jfl&flag='.jfColorApproved.'&jobid='.$this -> mJobId.'")', 'img/jfl/'.jfColorApproved.'.gif', 'button', $lAtt);
        }
      }

      if (!empty($lPdfApp) AND in_array($lSta, $lPdfApp)) {
        if (bitset($this -> mFla, jfPdfApproved)) {
          $this -> addBtn('jfl', $lJflReset[jfPdfApproved], 'go("index.php?act=job-'.$this -> mSrc.'.jfl&flag=-'.jfPdfApproved.'&jobid='.$this -> mJobId.'")', 'img/jfl/'.jfPdfApproved.'.gif', 'button', $lAtt);
        } else {
          $this -> addBtn('jfl', $lJflSet[jfPdfApproved], 'go("index.php?act=job-'.$this -> mSrc.'.jfl&flag='.jfPdfApproved.'&jobid='.$this -> mJobId.'")', 'img/jfl/'.jfPdfApproved.'.gif', 'button', $lAtt);
        }
      }
    }

    if (bitset($this -> mFla, jfOnhold)) return;
    if (bitset($this -> mFla, jfCancelled)) return;

    $this -> getFlagButtons();

    $this -> addPanel('stpind', lan('crp.independent'), '', 'job.jfl');
    $this -> addStatusIndependentButtons();
  }

  protected function getFlagButtons() {
    $lUid = $this -> mUsr -> getId();
    $lShowFlags = CApp_Apl_Loop::showFlagButtons($this -> mSrc, $this -> mJob['jobid'], $this -> mAllFlags);
    foreach ($lShowFlags as $lFlagTyp => $lShow) {
      if (isset($this -> mFlagConfirmed[$lFlagTyp])) {
        if (($lShow !== '-' AND $lShow) OR   //[TYP => $lShow mu� im Vgl. zuerst stehen] Eingeladen und an der Reihe ODER
            ($lShow === '-' AND $this -> canConfirmFlag($lFlagTyp, $this -> mCrpId)) // kein Eintrag in DB und habe das Recht
          ) {
          $lMen = new CHtm_Menu('Button');
          $lFlagEve = $this -> mAllFlags[$lFlagTyp];
          $lName = $lFlagEve['name_'.LAN];

          $lImg = 'img/flag/';
          $Indx = $lFlagTyp.'add';
          if (isset($lShowFlags[$Indx])) {//wenn user bereits confirmed hat und dieses aendern darf.
            switch($lShowFlags[$Indx]) {
              case FLAG_STATE_AMENDMENT :
                $lImg.= $lFlagEve['amend_ico'];
                BREAK;
              case FLAG_STATE_CONDITIONAL :
                $lImg.= $lFlagEve['condit_ico'];
                BREAK;
              case FLAG_STATE_APPROVED :
                $lImg.= $lFlagEve['approv_ico'];
                BREAK;
            }

          } elseif ($this -> mFlagConfirmed[$lFlagTyp]) {
            //wird in function addStatusButtons definiert!
            $lImg.= $lFlagEve['eve_'.flEve_conf.'_ico'];
          } else {
            $lImg.= $lFlagEve['eve_'.flEve_act.'_ico'];
          }
          $lImg.= '.gif';
          $lArr = array();
          if (bitset($lFlagEve['flags_conf'], flagBtnAmend)) {
            $lArr[] = array('id' => FLAG_STATE_AMENDMENT, 'name' => $lFlagEve['amend_'.LAN], 'ico' => $lFlagEve['amend_ico']);
          }
          if (bitset($lFlagEve['flags_conf'], flagBtnCondit)) {
            $lArr[] = array('id' => FLAG_STATE_CONDITIONAL, 'name' => $lFlagEve['condit_'.LAN], 'ico' => $lFlagEve['condit_ico']);
          }
          if (bitset($lFlagEve['flags_conf'], flagBtnApprov)) {
            $lArr[] = array('id' => FLAG_STATE_APPROVED, 'name' => $lFlagEve['approv_'.LAN], 'ico' => $lFlagEve['approv_ico']);
          }

          $lJsFunktionConfirm = array();
          if (isset($this -> mAllActions[$lFlagEve['eve_conf']]) AND bitset($lFlagEve['flags_conf'], flagJsAnyConf)) {
            $lActionArr = $this -> mAllActions[$lFlagEve['eve_conf']];
            foreach ($lActionArr as $lRowEve) {
              if ('add_js2btn' == $lRowEve['typ']) {
                $lPar = unserialize($lRowEve['param']);
                if ($lPar['js'] != '') {
                  if ($lPar['value'] != '') {
                    $lValue = dehtm($lPar['value']);
                    $lJsFunktionConfirm[] = $lPar['js'].'(&quot;'.$lValue.'&quot;)';
                  } else {
                    $lJsFunktionConfirm[] = $lPar['js'].'()';
                  }
                }
              }
            }
          }
          if (!empty($lJsFunktionConfirm)) {
            $lTempJs = implode(";", $lJsFunktionConfirm);
            $lJs = $lTempJs.';';
          } else {
            $lJs = '';
          }
          foreach ($lArr as $lRow) {
            $lUrl = '';
            if (!empty($lJs)) {
              $lUrl.= $lJs.'go(&quot;';
            }
            $lUrl.= 'index.php?act=job-'.$this -> mSrc;
            if (bitset($lFlagEve['flags_conf'], flagComment)) {
              $lUrl.= '.flag';
            } else {
              $lUrl.= '.sflag';
            }
            $lUrl.= '&vote='.$lRow['id'].'&flag='.$lFlagTyp;
            $lUrl.= ($this -> mSrc == 'pro') ? '&id='.$this -> mJobId.'&jobid='.$this -> mJob['jobid'] : '&jobid='.$this -> mJobId;
            if (!empty($lJs)) {
              $lUrl.= '&quot;)';
              $lMen -> addJsItem($lUrl, $lRow['name'], 'flag/'.$lRow['ico'].'.gif');
            } else {
              $lMen -> addItem($lUrl, $lRow['name'], 'flag/'.$lRow['ico'].'.gif');
            }
          }

          $lLnk = (THEME === 'default' ? "Flow.Std.popMain('".$lMen -> mDivId."')" : "javascript:gIgn=1;Flow.Std.popMen('".$lMen -> mDivId."')");
          $lBtn = btn($lName, $lLnk, $lImg, 'button', array('class' => 'btn w200','id' => $lMen -> mLnkId));
          $lBtn .= $lMen -> getMenuDiv();
          $this -> addButton('jfl', $lBtn);

        } elseif (('-' != $lShow AND !$lShow)) {
          $this -> dbg(MID.','.$this -> mSrc.','.$this -> mJobId.': User('.$lUid.') isn\'t in the series for Flag '.$lFlagTyp.' or has still confirmed.', mlInfo);
        } else {
          $this -> dbg(MID.','.$this -> mSrc.','.$this -> mJobId.': User('.$lUid.') has no Rights for Flag '.$lFlagTyp.'!', mlWarn);
        }//end_if (!$lShow)
      }
    }
  }

  protected function addAplButtons($aAplstatus) {
    $lAplstatusArr = explode(',', $aAplstatus);
    $lSta = intval($this -> mJob['webstatus']);
    if (!in_array($lSta,$lAplstatusArr)) return;

    $lUid = CCor_Usr::getAuthId();

    $lApl = new CApp_Apl_Loop($this -> mSrc, $this -> mJobId);
    if (!$lApl -> isUserActiveNow($lUid)) {
      return;
    }

    $lAtt = array();
    $lAtt['class'] = 'btn w200';

    $lAplButtons = CCor_Cfg::get('buttons.apl', array());
    if (!empty($lAplButtons)) {
      $this -> addPanel('apl', lan('job-apl.menu'), '', 'job.apl');
      foreach ($lAplButtons as $lAplKey => $lAplBtn) {
        $this -> addBtn('apl', lan('apl.'.$lAplBtn), 'go("index.php?act=job-'.$this -> mSrc.'.apl&flag='.$lAplKey.'&jobid='.$this -> mJobId.'")', 'img/ico/16/flag-0'.$lAplKey.'.gif', 'button', $lAtt);
      }
    }
  }

  protected function addStatusButtons() {
    $this -> addReassignButton();

    $lUsr = CCor_Usr::getInstance();
    $lTip = ($lUsr -> getPref('job.btntips', 'Y') == 'Y');
    $this -> mFlagConfirmed = CJob_Cnt::isFlagConfirmed($this -> mJob); // kann nicht im job/form/constructor aufgerufen werden, da mJob erst ab job/SRC/form verfuegbar.

    if (bitset($this -> mFla, jfOnhold) OR bitset($this -> mFla, jfCancelled)) {
      $this -> addArchiveButtonsAfterFlag($lTip);
      return;
    }

    $lSta = intval($this -> mJob['webstatus']);
    $lCrp = CCor_Res::extract('status', 'id', 'crp', $this -> mCrpId);
    if (!empty($lCrp) AND isset($lCrp[$lSta])) {
      // fuer 22877 Project CRP functionality extension: work with status, which has no steps
      $lCrpStaDis = CCor_Res::extract('status', 'display', 'crp', $this -> mCrpId);
      $lCrpDisSta = CCor_Res::extract('display', 'id', 'crp', $this -> mCrpId);
      $lDisplay = $lCrpStaDis[$lSta];
      if (isset($this -> mAutoProStatus) AND $lDisplay >= $this -> m1StatusNoFromStep AND isset($lCrpDisSta[$this -> mAutoProStatus]) AND $lDisplay < $this -> mAutoProStatus) { // definiert in job/pro/form
        $lSid = $lCrpDisSta[$this -> mAutoProStatus];
      } else {
        $lSid = $lCrp[$lSta];
      }

      $lSql = 'SELECT s.id,s.name_'.LAN.' AS name,c.display,s.event,s.flag_act,s.flag_stp,s.cond,c.status AS newwebstatus FROM al_crp_step s, al_crp_status c';
      $lSql.= ' WHERE c.mand='.MID.' AND s.from_id='.$lSid;
      $lSql.= ' AND s.to_id = c.id ORDER BY c.display,s.id' ;
      $lQry = new CCor_Qry($lSql);
      foreach ($lQry as $lRow) {
        if (!empty($lRow['cond'])) {
          $lReg = new CApp_Condition_Registry();
          $lCnd = $lReg -> loadFromDb($lRow['cond']);
          $lCnd -> setContext('data', $this -> mJob);
          if (!$lCnd -> isMet()) {
            $this -> dbg('Cond '.$lRow['cond'].' is not met');
            continue;
          } else {
            $this -> dbg('Cond '.$lRow['cond'].' is met');
          }
        }

        $lFlagMandatory = FALSE;
        // If there is a Javascript Action (typ == add_js2btn),
        // the Javascript Function will be called before function 'setStep'
        $lJsFunktion = array();
        if (isset($this -> mAllActions[$lRow['event']])) {
          $lActionArr = $this -> mAllActions[$lRow['event']];
          foreach ($lActionArr as $lRowEve) {
            if ('add_js2btn' == $lRowEve['typ']) {
              $lPar = unserialize($lRowEve['param']);
              if ($lPar['js'] != '') {
                if ($lPar['value'] != '') {
                  $lValue = dehtm($lPar['value']);
                  $lJsFunktion[] = $lPar['js'].'("'.$lValue.'")';
                } else {
                  $lJsFunktion[] = $lPar['js'].'()';
                }
              }
            }
          }
        }
        if (!empty($lRow['flag_act'])) {
          $lFlag = explode(',', $lRow['flag_act']);
          foreach ($lFlag as $lF) {
            $lFlagEveAct = $this -> mAllFlags[$lF];
            if (isset($this -> mAllActions[$lFlagEveAct['eve_act']])) {
              $lActionArr = $this -> mAllActions[$lFlagEveAct['eve_act']];
              foreach ($lActionArr as $lRowEve) {
                if ('add_js2btn' == $lRowEve['typ']) {
                  $lPar = unserialize($lRowEve['param']);
                  if ($lPar['js'] != '') {
                    if ($lPar['value'] != '') {
                      $lValue = dehtm($lPar['value']);
                      $lJsFunktion[] = $lPar['js']."('".$lValue."')";#.'("'.$lValue.'")';
                    } else {
                      $lJsFunktion[] = $lPar['js'].'()';
                    }
                  }
                }
              }
            }
          }
        }
        $lJsFunktionStp = array();
        if (!empty($lRow['flag_stp'])) {

          $lFlag = explode(',', $lRow['flag_stp']);
          foreach ($lFlag as $lF) {
            $lFlagEveStp = $this -> mAllFlags[$lF];
            if (bitset($lFlagEveStp['flags_conf'], flagMandatory)) { // stoppe den CRP-Step!
              if (isset($this -> mFlagConfirmed[$lF]) AND !$this -> mFlagConfirmed[$lF]) {
                $lFlagMandatory = TRUE;

                if (isset($this -> mAllActions[$lFlagEveStp['eve_mand']])) {
                  $lActionArr = $this -> mAllActions[$lFlagEveStp['eve_mand']];
                  foreach ($lActionArr as $lRowEve) {
                    if ('add_js2btn' == $lRowEve['typ']) {
                      $lPar = unserialize($lRowEve['param']);
                      if ($lPar['js'] != '') {
                        if ($lPar['value'] != '') {
                          $lValue = dehtm($lPar['value']);
                          $lJsFunktionStp[] = $lPar['js']."('".$lValue."')";#.'("'.$lValue.'")';
                        } else {
                          $lJsFunktionStp[] = $lPar['js'].'()';
                        }
                      }
                    }
                  }
                }
              }
            }
          }
        }

        if (!empty($lJsFunktionStp)) {
          $lJsFunktion = $lJsFunktionStp;
        }

        if ($this -> canStep($lRow['id'])) {
          $lDiv = getNum('b');
          $lAtt = array();
          if ($lTip) {
            $lAtt['onmouseover'] = 'Flow.stpTip(this,'.$lRow['id'].');';
            $lAtt['onmouseout']  = 'Flow.hideTip();';
          }
          $lAtt['id'] = $lDiv;
          $lAtt['class'] = 'btn w200';

          if ($lSta <= $lRow['newwebstatus']) {
            $lCheckMandatoryFieldsByStatus = 'true';
            $lCheck = TRUE;
          } else {
            $lCheckMandatoryFieldsByStatus = 'false';
            $lCheck = FALSE;
          }

          if (!empty($lJsFunktion)) {
            $lTempJs = implode(";", $lJsFunktion);
          } else {
            $lTempJs = '';
          }

          if ($lFlagMandatory) {
            $this -> msg($lFlagEveStp['name_'.LAN].' is mandatory!',mtUser,mlWarn);
            $lJs = $lTempJs.';go("index.php?act=job-rep.stopflag&sid='.$lRow['id'].'&jobid='.$this -> mJobId.'");';
          } else {
            $lJs = $lTempJs.';setStep(this,'.$lRow['id'].','.$lCheckMandatoryFieldsByStatus.','.$lRow['newwebstatus'].');';
          }
          $lIco = CApp_Crpimage::getCrpIco($this->mSrc, $lRow['display'].'b');
          $this -> addBtn('stp', $lRow['name'], $lJs, $lIco, 'button', $lAtt);
        }
      }
    }
  }

  protected function addStatusIndependentButtons() {
    $lUsr = CCor_Usr::getInstance();

    $lSql = 'SELECT id,name_'.LAN.' AS name,event,cond FROM al_crp_step ';
    $lSql.= 'WHERE mand='.MID.' AND from_id=0 AND crp_id='.$this->mCrpId.' ';
    $lSql.= 'ORDER BY name_'.LAN;

    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      if (!empty($lRow['cond'])) {
        $lReg = new CApp_Condition_Registry();
        $lCnd = $lReg -> loadFromDb($lRow['cond']);
        $lCnd -> setContext('data', $this -> mJob);
        if (!$lCnd -> isMet()) {
          $this -> dbg('Cond '.$lRow['cond'].' is not met');
          continue;
        } else {
          $this -> dbg('Cond '.$lRow['cond'].' is met');
        }
      }
      if ($this -> canStep($lRow['id'])) {
        $lDiv = getNum('b');
        $lAtt = array();
        $lAtt['id'] = $lDiv;
        $lAtt['class'] = 'btn w200';

        $lPath = 'ico/16/next-hi.gif';
        $lJs = 'Flow.JobForm.setActionStep(this,"'.$this->mSrc.'",'.$lRow['id'].')';
        $this -> addBtn('stpind', $lRow['name'], $lJs, $lPath, 'button', $lAtt);
      }
    }
  }

  protected function addArchiveButtonsAfterFlag($aTip) {
  	$lSql = 'SELECT s.id,s.name_'.LAN.' AS name,c.display,s.event,s.flag_act,s.flag_stp,s.cond,c.status AS newwebstatus FROM al_crp_step s, al_crp_status c';
  	$lSql.= ' WHERE c.mand='.MID;
  	$lSql.= ' AND (s.trans = "job2arc" OR s.trans = "pro2arc") AND c.status = 200 AND s.event = 0 AND c.crp_id = '.$this -> mCrpId.' AND s.to_id = c.id LIMIT 1;' ;

  	$lQry = new CCor_Qry($lSql);
  	foreach ($lQry as $lRow) {
  		if ($this -> canStep($lRow['id'])) {
  			$lDiv = getNum('b');
  			$lAtt = array();
  			if ($aTip) {
  				$lAtt['onmouseover'] = 'Flow.stpTip(this,'.$lRow['id'].');';
  				$lAtt['onmouseout']  = 'Flow.hideTip();';
  			}
  			$lAtt['id'] = $lDiv;
  			$lAtt['class'] = 'btn w200';
  			$lJs = 'setStep(this,'.$lRow['id'].',false,'.$lRow['newwebstatus'].');';
			$lIco = CApp_Crpimage::getCrpIco($this->mSrc, $lRow['display'].'b');
  			$this -> addBtn('stp', $lRow['name'], $lJs, $lIco, 'button', $lAtt);
  		}
  	}
  }

  protected function onBeforeContent() {
    $canUpload = new CJob_Uploadchecker($this -> mSrc, $this -> mAct, $this -> mJobId, $this -> mJob);
    $this -> mHiddenUpload = $canUpload -> disableUpload();

    $this -> preSet();
    $lRet = $this -> getContHeader();
    $lRet.= $this -> getContTabs();
    $this -> setParam(sec_token, $_SESSION[sec_token]);
    $this -> setParam('webstatus', $this -> mJob['webstatus']);
    $this -> isMandatory();

    $this -> setPat('frm.btn',      $this -> getContButtons());
    $this -> setPat('frm.hidden',   $this -> getHiddenFields());
    $this -> setPat('frm.parts',    $this -> getPages());
    $this -> setPat('frm.act',      $this -> mAct);
    $this -> setPat('frm.page',     $this -> mTab);
    $this -> setPat('frm.clientid', MID);
    $this -> setPat('frm.src',      $this -> mSrc);

    $this -> precompile();
    $this -> setLang();
  }

  protected function isMandatory() {
    $lCurrentWebstatus = intval($this -> mJob['webstatus']);

    // mandatory fields available in all status
    foreach ($this -> mFie as $lAli => $lDef) {
      if (($lDef['flags'] & ffMandatory) == ffMandatory) {
        $this -> mMandatoryFieldsByJob[] = $lDef;
      }
    }

    // mandatory fields available in selected status
    if (!empty($lCurrentWebstatus)) {
      $lSql = 'SELECT status, mandbystat FROM al_crp_status WHERE mand='.intval(MID).' AND crp_id='.esc($this -> mCrpId).' ORDER BY status;';
      $lQry = new CCor_Qry($lSql);
      foreach ($lQry as $lRow) {
        array_push($this -> mExistingStatus, $lRow['status']);
        $lMandatoryFields = explode(',', $lRow['mandbystat']);
        if (!empty($lMandatoryFields)) {
          foreach ($lMandatoryFields as $lAlias) {
            if (isset($this -> mFie[$lAlias])) {
              $this -> mMandatoryFieldsByStatus[$lRow['status']][$lAlias] = $this -> mFie[$lAlias];
            }
          }
        }
      }
    }
  }

  protected function specialCaseMandatoryFields() {
    return '';
  }

  protected function getMandatoryFieldsCheckJs() {
      /*
       * function checkMandatoryFields() wird erstellt {
       *  ueber PHP-Array $this -> mandatoryFields iterieren
       *  vorher pruefen, ob array_count > 0
       *  dann iterieren, sonst direkt form.submit
       *  ansosten die pruefung
       *
       * Caption ueber
       * $cap = "caption_".$usr -> lang;
       * und dann
       * $mandatoryFields[aktuellesElement][$cap]
       *
       * js-array[alias] = caption
       *}
       * erste Javascript Funktion: getDocumentFormElement gibt die Objekt zurueck
       *
       * zweite Javascript checkMandatoryFields() prueft ob der object.value leer ist oder
       * nicht. Wenn es leer ist, wird im Array eingefuegt fuer spaetere Fehlermeldung.
       *
       * Bei Leer werden border-color:rot eingestellt.
       *
       *
       */

    $_ret = "<script type='text/javascript'>\n";
    $_ret.= "function getDocumentFormElement(aName) {\n";
    $_ret.= "  obj = document.jobFrm.elements[aName];\n";
    if (!empty($this -> srcFieldSuffix)) {
      $_ret.= "  if (obj) {\n";
    }
    $_ret.= "    return obj;\n";
    if (!empty($this -> srcFieldSuffix)) {
      $_ret.= "  } else {\n";
      $_ret.= "    return document.jobFrm.elements[aName.slice(0,-1) + '".$this -> srcFieldSuffix."' + aName.slice(-1)];\n";
      $_ret.= "  }\n";
    }
    $_ret.= "}\n";

    $_ret.= 'function hiError(aObj) { jQuery(aObj).css({"border" : "1px solid red"});}'.LF;
    $_ret.= 'function resetError() { jQuery(".inp").css({"border" : "1px solid #ccc"});}'.LF;
    $_ret.= 'function hasValue(aObj, aVal) { var el = jQuery(aObj); if (!el) { return false; } if (jQuery(el).is(":checkbox")) {  lVal = (jQuery(el).is(":checked")) ? "on" : ""; } else { lVal = jQuery(el).val(); } return (lVal == aVal); }'.LF;

    if (CCor_Cfg::get('validate.available')) {
      $_ret .= 'function checkMandatoryFieldsByJob(aStep) {'.LF;
      $_ret .= "  resetError();\n";
      $_ret .= '  if (doCheckMandatoryFieldsByJob()) {'.LF;
      $_ret .= '    var frm = document.jobFrm;'.LF;
      $_ret .= '    Flow.job.validate(frm, function(){if(aStep) {frm.step.value=aStep;} frm.submit()});'.LF;
      $_ret .= '  }'.LF;
      $_ret .= '  return false;'.LF;
      $_ret .= '}' . LF;
      $_ret.= 'function doCheckMandatoryFieldsByJob() {'.LF;
    } else {
      $_ret.= 'function checkMandatoryFieldsByJob() {'.LF;
      $_ret.= 'resetError();'.LF;
    }
    if (empty($this -> mMandatoryFieldsByJob)) {
      $_ret.= "  // keine Pflichtfelder vorhanden!\n";
      $_ret.= "  return checkIntegerFields();\n";
      $_ret.= "}\n";
    } else {
      $_ret.= "  var obj;\n";
      $_ret.= "  var leerfelder = new Array();\n";
      $_ret.= "  var fehlermeldung = '".lan('lib.mandatorymessage').": ';\n\n";
      $_ret.= $this -> specialCaseMandatoryFields();
      foreach ($this -> mMandatoryFieldsByJob as $lAlias => $lWert) {
        $_alias = $lWert['alias'];
        $_caption = $lWert['name_'.LAN];
        $_ret.= "  obj = getDocumentFormElement('val[$_alias]');\n";
        // If Jobfields has Feature 'NoChoice', has default Value although it isn't be selected.
        if (isset($lWert['NoChoice'])) {
          $lNoChoiceVal = $lWert['NoChoice'];
          $_ret.= "  if (hasValue(obj, '".$lNoChoiceVal."')) {\n";
        }else{
          $_ret.= "  if (hasValue(obj, '')) {\n";
        }
        $_ret.= "    hiError(obj);\n";
        $_ret.= "    leerfelder = leerfelder.concat('".$_caption."');\n";
        $_ret.= "  }\n";
      }
      $_ret.= "\n  if (leerfelder.length != 0) { \n";
      $_ret.= "    var arraytostring = leerfelder.valueOf(); \n";
      $_ret.= "    alert (fehlermeldung + arraytostring); \n";
      $_ret.= "    return false; \n";
      $_ret.= "  } else {\n";
      $_ret.= "    return checkIntegerFields(); \n";
      $_ret.= "  }\n";
      $_ret.= "}\n";
    }

    foreach ($this -> mExistingStatus as $lKeyOuter => $lValueOuter) {
      $_ret.= "function checkMandatoryFieldsByStatus".$this -> mExistingStatus[$lKeyOuter]."() {\n";

      $_retInner ='';
      $lMandatoryFieldsFound = FALSE;
      if (isset($this -> mMandatoryFieldsByStatus[$this -> mExistingStatus[$lKeyOuter]])) {
        foreach ($this -> mMandatoryFieldsByStatus[$this -> mExistingStatus[$lKeyOuter]] as $lAlias => $lWert) { // $lCounter changed to $lKeyOuter
          $_alias = $lWert['alias'];
          $_caption = $lWert['name_'.LAN];
          $_retInner.= "  obj = getDocumentFormElement('val[$_alias]');\n";
          $_retInner.= "  if (hasValue(obj, '')) {\n";
          $_retInner.= "    hiError(obj);\n";
          $_retInner.= "    leerfelder = leerfelder.concat('".$_caption."');\n";
          $_retInner.= "  }\n";
          $lMandatoryFieldsFound = TRUE;
        }
      }

      if ($lMandatoryFieldsFound == FALSE) {
        $_ret.= "  // keine Pflichtfelder vorhanden!\n";
        $_ret.= "  return true;\n";
        $_ret.= "}\n";
      } else {
        $_ret.= "  var obj;\n";
        $_ret.= "  var leerfelder = new Array();\n";
        $_ret.= "  var fehlermeldung = '".lan('lib.mandatorymessage').": ';\n\n";
        $_ret.= $_retInner;
        $_ret.= "\n  if (leerfelder.length != 0) { \n";
        $_ret.= "    var arraytostring = leerfelder.valueOf(); \n";
        $_ret.= "    alert (fehlermeldung + arraytostring); \n";
        $_ret.= "    return false; \n";
        $_ret.= "  } else {\n";
        $_ret.= "    return true; \n";
        $_ret.= "  }\n";
        $_ret.= "}\n";
      }
    }

    //check for any integer fields to be mandatory checked on submission of form
    $lIgnore = array('apl', 'webstatus', 'flags');
    $_ret.= "function checkIntegerFields() {\n";
    $_ret.= "  var obj;\n";
    $_ret.= "  var leerfelder = new Array();\n";
    $_ret.= "  var fehlermeldung = '".lan('lib.integermessage').": ';\n";
	$_ret.= "  var int_regex = /^-?\d*\.?\d*$/;\n\n";

	$_retInner = '';
    foreach ($this -> mFie as $lAli => $lDef) {
      if($lDef['typ'] == 'int' && !in_array($lDef['alias'], $lIgnore)) {
        if(array_key_exists($lDef['alias'], $this -> mFac -> mIds)) {
          $_alias = $lDef['alias'];
          $_caption = $lDef['name_'.LAN];
          $_id = $this -> mFac -> mIds[$_alias];

          $_retInner.= "  obj = getDocumentFormElement('val[$_alias]');\n";
          $_retInner.= "  if (hasValue(obj, '') && !int_regex.test(obj.value)) {\n";
          $_retInner.= "    hiError(obj);\n";
          $_retInner.= "    leerfelder = leerfelder.concat('".$_caption."');\n";
          $_retInner.= "  }\n\n";
        }
      }
    }
    $_ret.= $_retInner;

    $_ret.= "  if (leerfelder.length != 0) { \n";
    $_ret.= "    var arraytostring = leerfelder.valueOf(); \n";
    $_ret.= "    alert (fehlermeldung + arraytostring); \n";
    $_ret.= "    return false; \n";
    $_ret.= "  } else {\n";
    $_ret.= "    return true; \n";
    $_ret.= "  }\n";
    $_ret.= "}\n";

    $_ret.= "</script>";
    return $_ret;
  }

  public function getTemplates() {
    $lSub = explode('-',$this -> mSrc);
    if (2 == count($lSub)) {
      $lSrc = $lSub[1];
    } else {
      $lSrc = $this -> mSrc;
    }
    if (isset($this -> mTemplates[$lSrc])) {
      return $this -> mTemplates[$lSrc];
    } else {
      return array();
    }
  }

  public function getPrintTemplates() {
    $lPrint = array();
    $lSub = explode('-',$this -> mSrc);
    if (2 == count($lSub)) {
      $lSrc = $lSub[1];
    } else {
      $lSrc = $this -> mSrc;
    }
    if (isset($this -> mTemplates[$lSrc])) {
      foreach($this -> mTemplates[$lSrc] as $lSite => $lTempl) {
        foreach($lTempl as $lTpl => $l_Src) {
          $lPrint['job'][$lTpl] = $l_Src;
        }
      }
    }
    return $lPrint;
  }

  protected function addCopyPanel() {
    $this -> addPanel('cpy', lan('lib.copy'), '', 'job.cpy');
    $this -> addButton('cpy', $this -> getCopyButtonMenu());
  }
}
