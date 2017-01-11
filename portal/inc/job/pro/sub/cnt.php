<?php
class CInc_Job_Pro_Sub_Cnt extends CCor_Cnt {

  public $mViewJoblist = TRUE;
  /*
   * Array for Save Changes in History
   */
  public $mArrHistory = Array();

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mAva = fsPro;
    $this -> mTitle = lan('job-sub.menu');
    $this -> mMmKey = 'job-pro';
    $this -> mViewJoblist = CCor_Cfg::get('view.projekt.joblist', TRUE);
  }

  protected function getStdUrl() {
    $lJobId = $this -> mReq -> jobid;
    return 'index.php?act='.$this -> mMod.'&jobid='.$lJobId;
  }

  protected function actStd() {
    $lJobId = $this -> getInt('jobid');

    $lJob = new CJob_Pro_Dat();
    if ($lJob -> load($lJobId)) {
      $lJob -> addRecentJob();
    }

    $lVie = new CJob_Pro_Header($lJob);
    $lRet = $lVie -> getContent();

    $lVie = new CJob_Pro_Tabs($lJobId, 'sub');
    $lRet.= $lVie -> getContent();

    if ($this -> mViewJoblist) {
      $lVie = new CJob_Pro_Sub_Job_List($lJobId);
    } else {
      $lVie = new CJob_Pro_Sub_List($lJobId);
    }
    $lRet.= $lVie -> getContent();

    $this -> render($lRet);
  }

  protected function actFpr() {
    $lJobAva = fsArt; // analog zu job-all,hier geht es um die Jobliste, keine Projektliste!
    $lJobId = $this -> getInt('jobid');

    $lVie = new CHtm_Fpr($this -> mMod.'.sfpr', $this -> mMod.'&jobid='.$lJobId);
    $lVie -> setParam('jobid', $lJobId);
    $lDef = CCor_Res::get('fie');

    $lArr = array();
    foreach ($lDef as $lFie) {
      $lFla = intval($lFie['flags']);
      $lAva = intval($lFie['avail']);
      if (bitSet($lAva, $lJobAva)) {
        if (bitSet($lFla, ffList)) {
          $lArr[$lFie['id']] = $lFie['name_'.LAN];
        }
      }
    }
    $lVie -> setSrc($lArr);
    $lUsr = CCor_Usr::getInstance();
    $lVie -> setSel($lUsr -> getPref($this -> mPrf.'.cols'));
    $this -> render($lVie);
  }

  protected function actSfpr() {
    $lDst = $this -> mReq -> getVal('dst');
    $lUsr = CCor_Usr::getInstance();
    $lUsr -> setPref($this -> mPrf.'.cols', implode(',', $lDst));
    $this -> redirect();
  }

  protected function actSpr() {
    $lJobId = $this -> getInt('jobid');

    $lVie = new CHtm_Fpr($this -> mMod.'.sspr');
    $lVie -> setTitle(lan('lib.opt.spr'));
    $lVie -> setParam('jobid', $lJobId);
    $lDef = CCor_Res::get('fie');

    $lArr = array();
    foreach ($lDef as $lFie) {
      $lFla = intval($lFie['flags']);
      $lAva = intval($lFie['avail']);
      if (bitSet($lAva, $this -> mAva)) {
        if (bitSet($lFla, ffSearch)) {
          $lArr[$lFie['id']] = $lFie['name_'.LAN];
        }
      }
    }

    $lVie -> setSrc($lArr);
    $lUsr = CCor_Usr::getInstance();
    $lVie -> setSel($lUsr -> getPref($this -> mPrf.'.sfie'));
    $this -> render($lVie);
  }

  protected function actSspr() {
    $lDst = $this -> mReq -> getVal('dst');
    $lUsr = CCor_Usr::getInstance();
    $lUsr -> setPref($this -> mPrf.'.sfie', implode(',', $lDst));
    $this -> redirect();
  }

  protected function actAllview() {
    $lUsr = CCor_Usr::getInstance();

    $lPrf = array();
    $lPrf['cols'] = $lUsr -> getPref($this -> mMod.'.cols');
    $lPrf['lpp']  = $lUsr -> getPref($this -> mMod.'.lpp');
    $lPrf['ord']  = $lUsr -> getPref($this -> mMod.'.ord');
    $lPrf['sfie'] = $lUsr -> getPref($this -> mMod.'.sfie');

    $lQry = new CCor_Qry();
    foreach ($lPrf as $lKey => $lVal) {
      $lSql = 'UPDATE al_sys_pref SET val="'.addslashes($lVal).'" ';
      $lSql.= 'WHERE code="'.$this -> mMod.'.'.$lKey.'" AND mand='.MID;
      $lQry -> query($lSql);
    }
    $this -> redirect();
  }

  protected function actEdt() {
    $lProId = $this -> getInt('jobid');
    $lItmId  = $this -> getInt('id');

    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canEdit('job-pro-sub')) {
      $this -> redirect('index.php?act=job-pro-sub&jobid='.$lProId);
    }

    $lJob = new CJob_Pro_Sub_Item_Dat();
    $lJob -> load($lItmId);

    $lVie = new CJob_Pro_Header($lJob);
    $lRet = $lVie -> getContent();

    $lRet.= '<table cellpadding="1" cellspacing="0" border="0" class="tabTbl"><tr>
		<td class="tabAct nw" id="tabjob">Identifikation</td></tr></table>';

    $lFrm = new CJob_Pro_Sub_Form('job-pro-sub.sedt', $lProId, $lJob, '', $lItmId);
    $lFrm -> setParam('old[id]', $lItmId);
    $lFrm -> setParam('itemId', $lItmId);
    $lFrm -> setParam('subsupdate', 0);

    $lRet.= $lFrm -> getContent();
    $this -> render($lRet);
  }

  protected function actSedt() {
    $lJobId = $this -> getInt('jobid');
    $lItemId = $this -> getInt('itemId');
    //$lPag = $this -> getReq('page');
    $lSubsUpdate = $this -> getReq('subsupdate');
    $lMod = new CJob_Pro_Sub_Mod();
    $lMod -> getPost($this -> mReq);

    $lMod -> update();

    if ($lSubsUpdate != 0) {
      // Save und Update Sub Jobs
      if (!empty($lMod -> mChangedFields)) {
        $this -> mArrHistory = $lMod -> getHistoryUpdate();
        $this -> updSubJobs($lItemId, $lMod -> mChangedFields);
      }
    }

    $this -> redirect('index.php?act=job-pro-sub.edt&jobid='.$lJobId.'&id='.$lItemId);
  }

  protected function actWizedt() {
    $lRet = '';
    $lJobId = $this -> getInt('jobid');
    $lId  = $this -> getInt('id');

    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canEdit('job-pro-sub')) {
      $this -> redirect('index.php?act=job-pro-sub&jobid='.$lJobId);
    }

    $lJob = new CJob_Pro_Dat();
    $lJob -> load($lJobId);

    $lQry = new CCor_Qry('SELECT * FROM al_job_sub_'.intval(MID).' WHERE id='.$lId);
    $lSub = $lQry -> getDat();

    $lVie = new CJob_Pro_Header($lJob);
    $lRet.= $lVie -> getContent();

    $lVie = new CJob_Pro_Sub_Wizform('job-pro-sub.sedt', 'Edit Subjob', 'job-pro-sub&jobid='.$lJobId);
    $lVie -> setParam('jobid', $lJobId);
    $lVie -> setParam('sid', $lId);
    $lVie -> setParam('old[id]', $lId);
    $lVie -> setParam('val[id]', $lId);
    $lVie -> getWizard($lSub['wiz_id']);
    $lVie -> assignVal($lSub);
    $lRet.= $lVie -> getContent();

    $this -> render($lRet);
  }

  protected function actWiznew() {
    $lRet = '';
    $lJobId = $this -> getInt('jobid');
    $lWiz = $this -> getInt('wiz');

    $lJob = new CJob_Pro_Dat();
    $lJob -> load($lJobId);

    $lVie = new CJob_Pro_Header($lJob);
    $lRet.= $lVie -> getContent();

    $lVie = new CJob_Pro_Sub_Wizform('job-pro-sub.snew', 'New Subjob', 'job-pro-sub&jobid='.$lJobId);
    $lVie -> setParam('jobid', $lJobId);
    $lVie -> setParam('wiz', $lWiz);
    $lVie -> assignVal($lJob); // default: use project values
    $lVie -> getWizard($lWiz);
    $lRet.= $lVie -> getContent();

    $this -> render($lRet);
  }

  public function actNew() {
    $lRet = '';
    $lJobId = $this -> getInt('jobid');

    $lJob = new CJob_Pro_Dat();
    $lJob -> load($lJobId);

    $lVie = new CJob_Pro_Header($lJob);
    $lRet = $lVie -> getContent();

    $lFrm = new CJob_Pro_Sub_Form('job-pro-sub.snew', $lJobId, $lJob);
    $lRet.= $lFrm -> getContent();

    $this -> render($lRet);
  }

  protected function actSnew() {
    $lJobId = $this -> getInt('jobid');
    $lWiz = $this -> getInt('wiz', 1);
    $lPag = $this -> getReq('page');

    $lMod = new CJob_Pro_Sub_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> setVal('wiz_id', $lWiz);
    $lMod -> setVal('pro_id', $lJobId);
    $lMod -> insert();

    $this -> redirect('index.php?act=job-pro-sub&jobid='.$lJobId);
  }

  protected function actCpy() {
    $lProId = $this -> getInt('pid'); // Project ID, not yet used, for further improvement
    $lItmId = $this -> getInt('itmid'); // Item ID, not yet used, for further improvement
    $lJobId = $this -> getReq('jobid'); // Job ID

    if ($this -> mViewJoblist) {

      $lSrc = $this -> getReq('src'); // Source

      $lObj = 'CJob_'.$lSrc.'_Dat';
      $lJob = new $lObj();
      $lJob -> load($lJobId);

      $lCpySetEmpty = CCor_Cfg::get('job.cpy.set-empty', array());
      foreach ($lCpySetEmpty as $lAli) {
        $lJob[$lAli] = '';
      }

      $lDefFie = CCor_Res::get('fie');
      foreach ($lDefFie as $lFie) {
        $lFla = intval($lFie['flags']);
        $lAva = intval($lFie['avail']);
        $lAli = $lFie['alias'];
        if (!bitSet($lAva, $this -> mAva) OR !bitSet($lFla, ffCopy)) {
          $lJob[$lAli] = '';
        }
      }

      $lObj = 'CJob_'.$lSrc.'_Header';
      $lVie = new $lObj($lJob);
      $lRet = $lVie -> getContent();

      $lObj = 'CJob_'.$lSrc.'_Tabs';
      $lVie = new $lObj();
      $lRet.= $lVie -> getContent();

      $lObj = 'CJob_'.$lSrc.'_Form';
      $lFrm = new $lObj('job-'.$lSrc.'.snew', 0, $lJob);
      $lRet.= $lFrm -> getContent();

    } else {

      $lSubId  = $this -> getInt('id'); //SubId = ItmId

      $lUsr = CCor_Usr::getInstance();
      if (!$lUsr -> canEdit('job-pro-sub')) {
        $this -> redirect('index.php?act=job-pro-sub&jobid='.$lJobId);
      }

      $lJob = new CJob_Pro_Sub_Item_Dat();
      $lJob -> load($lSubId);

      $lVie = new CJob_Pro_Header($lJob);
      $lRet = $lVie -> getContent();

      $lFrm = new CJob_Pro_Sub_Form('job-pro-sub.snew', $lJobId, $lJob);
      $lRet.= $lFrm -> getContent();

    }
    $this -> render($lRet);
  }

  protected function actWizcpy() {
    $lJobId = $this -> getInt('jobid');
    $lId  = $this -> getInt('id');

    $lQry = new CCor_Qry('SELECT * FROM al_job_pro_'.intval(MID).' WHERE id='.$lJobId);
    $lJob = $lQry -> getDat();

    $lQry = new CCor_Qry('SELECT * FROM al_job_sub_'.intval(MID).' WHERE id='.$lId);
    $lSub = $lQry -> getDat();

    $lVie = new CJob_Pro_Header($lJob);
    $lRet = $lVie -> getContent();

    $lVie = new CJob_Pro_Sub_Wizform('job-pro-sub.snew', 'Copy Subjob', 'job-pro-sub&jobid='.$lJobId);
    $lVie -> setParam('jobid', $lJobId);
    $lVie -> setParam('sid', $lId);
    $lVie -> setParam('wiz', $lSub['wiz_id']);
    $lVie -> getWizard($lSub['wiz_id']);
    $lVie -> assignVal($lSub);
    $lRet.= $lVie -> getContent();

    $this -> render($lRet);
  }

  protected function actDel() {
    $lJobId = $this -> getInt('jobid');
    $lId  = $this -> getInt('id');

    $lSql = 'DELETE FROM al_job_sub_'.MID.' WHERE id="'.$lId.'" ';
    CCor_Qry::exec($lSql);
    
    //22651 Project Critical Path Functionality
    $lMod = new CJob_Pro_Mod();
    $lMod -> deleteFromProjectStatusInfo('', $lJobId, $lId);
  
    $this -> redirect('index.php?act=job-pro-sub&jobid='.$lJobId);
  }

  protected function actUnassign() {
    $lJobId = $this -> getReq('jobid');
    $lId  = $this -> getReq('id');
    $lSrc = $this -> getVal('src');

    $lSql = 'DELETE FROM al_job_sub_'.MID.' WHERE pro_id="'.$lJobId.'" AND jobid_'.$lSrc.'="'.$lId.'";';
    CCor_Qry::exec($lSql);

    //22651 Project Critical Path Functionality
    $lMod = new CJob_Pro_Mod();
    $lMod -> deleteFromProjectStatusInfo('', $lJobId, $lId);
    
    $lFac = new CJob_Fac($lSrc, $lId);
    $lJobMod = $lFac->getMod($lId);
    $lJobMod->forceVal('pro_id', '');
    $lJobMod->update();
  
    $this -> redirect('index.php?act=job-pro-sub&jobid='.$lJobId);
  }
  
  /* Wizard */

  protected function actWiz() {
    $lJobId = $this -> getInt('jobid');
    $lWid = $this -> getInt('wiz');

    $lDat = array();
    $lSes = CCor_Ses::getInstance();
    $lSes['job-pro-sub.wiz.dat'] = $lDat;

    $this -> redirect('index.php?act=job-pro-sub.wizfrm&jobid='.$lJobId.'&wiz='.$lWid.'&step=0');
  }

  protected function actWizfrm() {
    $lJobId = $this -> getInt('jobid');
    $lWid = $this -> getInt('wiz');
    $lStp = $this -> getInt('step');

    $lSes = CCor_Ses::getInstance();
    $lDat = $lSes['job-pro-sub.wiz.dat'];

    $lVie = new CJob_Pro_Sub_Wiz($lJobId, $lStp, $lWid, $lDat);
    $this -> render($lVie);
  }

  protected function getStepFromPost() {
    $lStp = $this -> getInt('step');
    $lReq = $this -> getReq('val');

    $lSes = CCor_Ses::getInstance();
    $lDat = $lSes['job-pro-sub.wiz.dat'];
    $this -> dump($lReq, 'REQUEST');
    foreach ($lReq as $lAli => $lArr) {
      if (!empty($lArr)) {
        foreach ($lArr as $lKey => $lVal) {
          if ('' != $lVal) {
            $lDat[$lStp][$lKey][$lAli] = $lVal;
          }
        }
      }
    }
    $lSes['job-pro-sub.wiz.dat'] = $lDat;
  }

  protected function actWizprev() {
    $lJobId = $this -> getInt('jobid');
    $lStp = $this -> getInt('step');
    $lWid = $this -> getInt('wiz');

    $this -> getStepFromPost();

    $this -> redirect('index.php?act=job-pro-sub.wizfrm&jobid='.$lJobId.'&wiz='.$lWid.'&step='.($lStp-1));
  }

  protected function actWiznext() {
    $lJobId = $this -> getInt('jobid');
    $lStp = $this -> getInt('step');
    $lWid = $this -> getInt('wiz');

    $this -> getStepFromPost();

    $this -> redirect('index.php?act=job-pro-sub.wizfrm&jobid='.$lJobId.'&wiz='.$lWid.'&step='.($lStp+1));
  }

  protected function mult($aSrc, $aArr) {
    $lRet = array();
    if (empty($aSrc)) {
      foreach ($aArr as $lKey => $lVal) {
        $lLin = array();
        foreach($lVal as $lAli => $lValue) {
          $lLin[$lAli] = $lValue;
        }
        $lRet[] = $lLin;
      }
    } else {
      foreach ($aSrc as $lRow) {
        foreach ($aArr as $lKey => $lVal) {
          $lLin = $lRow;
          foreach($lVal as $lAli => $lValue) {
            $lLin[$lAli] = $lValue;
          }
          $lRet[] = $lLin;
        }

      }
    }
    return $lRet;
  }

  protected function actWizfinish() {
    $lJobId = $this -> getInt('jobid');
    $lStp = $this -> getInt('step');
    $lWid = $this -> getInt('wiz');

    $lQry = new CCor_Qry('SELECT * FROM al_job_pro_'.intval(MID).' WHERE id='.$lJobId);
    $lPro = $lQry -> getDat();
    $this -> getStepFromPost();

    $lSes = CCor_Ses::getInstance();
    $lDat = $lSes['job-pro-sub.wiz.dat'];

    $lJobs = array();
    foreach ($lDat as $lStp => $lArr) {
      $lJobs = $this -> mult($lJobs, $lArr);
    }

    foreach ($lJobs as $lJob) {
      $lMod = new CJob_Pro_Sub_Mod();
      $lMod -> setVal('pro_id', $lJobId);
      $lMod -> setVal('wiz_id', $lWid);
      foreach ($lPro as $lKey => $lVal) {
        $lMod -> setVal($lKey, $lVal);
      }
      foreach ($lJob as $lKey => $lVal) {
        $lMod -> setVal($lKey, $lVal);
      }
      $lMod -> insert();
    }
    $this -> redirect('index.php?act=job-pro-sub&jobid='.$lJobId);
  }

  protected function actMaster_set() {
    $lJobId = $this -> getInt('jobid');
    $lMasterId = $this -> getInt('sid');

    $lColumnIsMasterDefined = $this -> checkColumnIsMasterDefined();
    if(!$lColumnIsMasterDefined) {
      $this -> dbg('column is_master must be defined for Master-Variant Bundle. Type : enum("","X")  NOT NULL default ""',mlError);
      $this -> redirect('index.php?act=job-pro-sub&jobid='.$lJobId);
    }

    $lSql = 'UPDATE al_job_sub_'.intval(MID).' SET is_master="X" WHERE id='.$lMasterId;
    CCor_Qry::exec($lSql);
    $this ->dbg('Project ItemId #'.$lMasterId.' setted as master');

    // Get Src and JobIds from ProjectItem.
    $lAssignedJobs = $this -> getAssignedJobs($lMasterId);

    $lUpd = array(
      'is_master' => 'X'
    );
    $this -> updateAssignedJobs($lAssignedJobs, $lUpd);
    $this -> redirect('index.php?act=job-pro-sub&jobid='.$lJobId);
  }

  protected function actMaster_unset() {
    $lJobId = $this -> getInt('jobid');
    $lMasterId = $this -> getInt('sid');
    $lAssignedVariantJobs = Array();
    $lSql = 'UPDATE al_job_sub_'.intval(MID).' SET is_master="" WHERE id='.$lMasterId;
    if (CCor_Qry::exec($lSql)) {
      // Master Propertie is deleted.

      // Find Assigned Jobs and update
      // Unset Master Jobfiled from assigned Jobs
      // Get Src and JobIds from ProjectItem.
      $lAssignedJobs = $this -> getAssignedJobs($lMasterId);
      // Define Update
      $lUpd = array('is_master' => '');
	  // Update
      $this -> updateAssignedJobs($lAssignedJobs, $lUpd);

      // Find Assigned Variants
      $lAssignedVariants = $this -> getAssignedVariants($lJobId,$lMasterId);
      if (!empty($lAssignedVariants)) {
        // Item has Variant Items

        $lTempArrVariants = array_map("esc", $lAssignedVariants);//jedes Element wird ".mysql_escaped."
        $lVariants = implode(',', $lTempArrVariants);

        // Sign out the assigned Variant Project Items
        $lSql = 'UPDATE al_job_sub_'.MID;
        $lSql.= ' SET master_id=""';
        $lSql.= ' WHERE pro_id='.$lJobId;
        $lSql.= ' AND id IN('.$lVariants.')';
        CCor_Qry::exec($lSql);
        $this ->dbg('Variants '.$lVariants .' signed out from Master Id#'.$lMasterId);

        // Get Asiigned variant joblist
        foreach ($lAssignedVariants as $lKey => $lVal ) {
          $lAssignedVariantJobs[]= $this -> getAssignedJobs($lVal);
        }
        //Define Update
        $lUpd = array('master_id' => '');

        foreach ($lAssignedVariantJobs as $lKey => $lVal) {
          $this -> updateAssignedJobs($lVal, $lUpd);
        }
      }
    }else{
      // Master can not be deletd
      $this ->dbg('Can not Updated',mlWarn);
    }
    $this -> redirect('index.php?act=job-pro-sub&jobid='.$lJobId);
  }

  protected function actVariant_unset() {
    $lJobId = $this -> getInt('jobid');
    $lVariantId = $this -> getInt('sid');

    $lSql = 'UPDATE al_job_sub_'.intval(MID).' SET master_id="" WHERE id='.$lVariantId;
    CCor_Qry::exec($lSql);
    $this ->dbg('VariantId #'.$lVariantId .' signed out from Master-Variant Bundle');


    $lAssignedJobs = $this -> getAssignedJobs($lVariantId);
    $lUpd = array('master_id' => '');
    $this -> updateAssignedJobs($lAssignedJobs, $lUpd);
    $this -> redirect('index.php?act=job-pro-sub&jobid='.$lJobId);
  }

  protected function actSlide() {
    $lJobId  = $this -> getReq('jobid', '');
    $lSrc  = $this -> getVal('src');
    $lFrom = $this -> getReqInt('from', 0);
    $lTo   = $this -> getReqInt('to', 0);

    // Get master, Variant infos from reference project item
    $lUpd = Array();
    
    /*
     * If Reference Projekt-Item is Master or Variant,
    * then Update Master-Variant Infos in Job
    */
    $lSql = 'SELECT * FROM al_job_sub_'.intval(MID).' ';
    
    $lSqlFrom = $lSql.' WHERE id ='.$lFrom;
    $lQry = new CCor_Qry($lSqlFrom);
    if ($lRowFrom = $lQry -> getAssoc()) {
      $lProId = $lRowFrom['pro_id'];
      if ($lRowFrom['is_master'] == 'X') {
        $lUpd['is_master'] = '';
      }
      if ($lRowFrom['master_id'] != '') {
        $lUpd['master_id'] = '';
      }
    }

    $lSqlTo = $lSql.' WHERE id='.$lTo;
    // Get master, Variant infos from target project item
    $lQry = new CCor_Qry($lSqlTo);
    if ($lRowTo = $lQry -> getAssoc()) {
      if (array_key_exists('is_master', $lRowTo)) {
        $lUpd['is_master'] = $lRowTo['is_master'];
      }
      if (array_key_exists('master_id', $lRowTo)) {
        $lUpd['master_id'] = $lRowTo['master_id'];
      }
    }
    
    // Any fields configured to write to the job from the item?
    $lTransfer = CCor_Cfg::get('job-pro.fields.onslide'); // default: none
    if (!empty($lTransfer)) {
      foreach ($lTransfer as $lField) {
        if (array_key_exists($lField, $lRowTo)) {
          $lUpd[$lField] = $lRowTo[$lField];
        }
      }
    }
    
    $lSqlTo = 'UPDATE al_job_sub_'.intval(MID).' SET jobid_'.$lSrc.'="'.addslashes($lJobId).'" WHERE id='.$lTo;
    if (CCor_Qry::exec($lSqlTo)) {
      // Job is moved to target
      $lSqlFrom = 'UPDATE al_job_sub_'.intval(MID).' SET jobid_'.$lSrc.'="" WHERE id='.$lFrom;
       // Del Membership from reference ProjektItem
      if (CCor_Qry::exec($lSqlFrom)) {
        // All SQLs succesfully
        // Update in Job the  Master Variant Infos
        if (!empty($lUpd)) {
          $lFac = new CJob_Fac($lSrc);
          $lMod = $lFac -> getMod($lJobId);
          $lMod -> forceUpdate($lUpd);
        }

      }

    }
    //22651 Project Critical Path Functionality
    $lMod = new CJob_Pro_Mod();
    $lMod -> updateProjectStatusInfo($lJobId, $lProId, $lTo, $lProId, $lFrom);
    $this -> redirect('index.php?act=job-pro-sub&jobid='.$lProId);
  }

  protected function actCsvexp() {
    $lJobId = $this -> getInt('jobid');
    $lUsr = CCor_Usr::getInstance();

    $lMandArr = CCor_Res::extract('code', 'name_'.LAN, 'mand');
    $lCols = $lUsr -> getPref('job-pro-sub.cols');

    // If No columns specified
    if (empty($lCols)) {
      CCor_Msg::add('No columns to show specified', mtUser, mlError);
      $this -> redirect('index.php?act=job-pro-sub&jobid='.$lJobId);
    }
    header('Content-type: text/csv');

    $lMandantName = str_replace(' ','_',$lMandArr[MAND]);
    $lFileName = lan('job-pro-sub.menu');
    $lFileName.= '_';
    $lFileName.= 'PrjId:'.$lJobId;
    $lFileName.= '_';
    $lFileName.= $lMandantName;
    $lFileName.= '_';
    $lFileName.= date('Ymd_H-i-s');
    $lFileName.= '.csv';
    header('Content-Disposition: attachment; filename="'.$lFileName.'"');
    flush();

    $lWithoutLimit = TRUE;
    $lJobList = new CJob_Pro_Sub_Job_List($lJobId, $lWithoutLimit);
    $lJobList -> mIte = $lJobList -> mIte -> getArray();
    $lJobList -> loadFlags();
    $lRet = $lJobList ->getCsvContent();
  }

  protected function actStataktual() {
    $lProjectID = $this -> getInt('projectid');

    //
    // 1.1 preparation: all job types
    //

    $lAllJobTypes = CCor_Res::extract('id', 'code', 'crpmaster'); // returns array; gets all job types
    $lAllJobTypesImploded = '\''.implode('\',\'', $lAllJobTypes).'\''; // returns string; gets all job types

    $this -> dbg('1.1 preparation: $lAllJobTypes: '.print_r($lAllJobTypes, TRUE));
    $this -> dbg('1.1 preparation: $lAllJobTypesImploded: '.print_r($lAllJobTypesImploded, TRUE));

    //
    // 1.2 preparation: relevant job types
    //

    $lRelevantJobTypes = array(); // returns array; gets relevant job types

    $lShowColumns = new CCor_Qry('SHOW COLUMNS FROM al_job_sub_'.MID.' LIKE "jobid_%";');
    foreach ($lShowColumns as $lRow) {
      $lFieldWithoutPrefix = substr($lRow -> Field, 6); // removes >jobid_<
      $lKeyExists = in_array($lFieldWithoutPrefix, $lAllJobTypes) ? $lRelevantJobTypes[$lFieldWithoutPrefix] = $lRow -> Field : NULL;
    }

    $lRelevantJobTypesImploded = '`'.implode('`,`', $lRelevantJobTypes).'`'; // returns string; gets relevant job types

    $this -> dbg('1.2 preparation: $lRelevantJobTypes: '.print_r($lRelevantJobTypes, TRUE));
    $this -> dbg('1.2 preparation: $lRelevantJobTypesImploded: '.print_r($lRelevantJobTypesImploded, TRUE));

    //
    // 1.3 preparation: all job IDs from al_job_sub_X and from al_job_pro_crp
    //

    $lAllItems = array();
    $lAllJobIDs = array();
    $lAllJobIDsByJobSub = array();
    $lAllJobIDsByJobPro = array();

    $lSQL = 'SELECT id,'.$lRelevantJobTypesImploded;
    $lSQL.= ' FROM al_job_sub_'.MID.' AS sub';
    $lSQL.= ' WHERE sub.pro_id='.$lProjectID;
    $lSQL.= ' AND del="N";';  

    $this -> dbg('1.3 preparation: all job IDs from al_job_sub_X: $lSQL: '.print_r($lSQL, TRUE));

    $lQry = new CCor_Qry($lSQL);
    foreach ($lQry as $lOuterKey => $lOuterValue) {
      $lID = $lOuterValue -> __get('id');

      foreach ($lRelevantJobTypes as $lInnerKey => $lInnerValue) {
        if (!empty($lOuterValue[$lInnerValue])) {
          $lAllItems[$lID][] = $lOuterValue[$lInnerValue];
          $lAllJobIDsByJobSub[$lOuterValue[$lInnerValue]] = $lInnerKey;
        }
      }
    }

    ksort($lAllJobIDsByJobSub); // unnecessary, for maintenance only, can be removed when error-free

    unset($lID); // to ensure that $lID will be re-initialised next time

    $lSQL = 'SELECT jobid,src';
    $lSQL.= ' FROM al_job_pro_crp';
    $lSQL.= ' WHERE mand='.MID;
    $lSQL.= ' AND pro_id='.$lProjectID.';';

    $this -> dbg('1.3 preparation: all job IDs from al_job_pro_crp: $lSQL: '.print_r($lSQL, TRUE));

    $lQry = new CCor_Qry($lSQL);
    foreach ($lQry as $lKey => $lValue) {
      $lAllJobIDsByJobPro[$lValue['jobid']] = $lValue['src'];
    }

    ksort($lAllJobIDsByJobPro); // unnecessary, for maintenance only, can be removed when error-free

    $lAllJobIDs = array_merge($lAllJobIDsByJobSub, $lAllJobIDsByJobPro);
    $lJobIDsInSubOnly = array_diff_key($lAllJobIDsByJobSub, $lAllJobIDsByJobPro);
    $lJobIDsInProOnly = array_diff_key($lAllJobIDsByJobPro, $lAllJobIDsByJobSub);

    ksort($lAllJobIDs); // unnecessary, for maintenance only, can be removed when error-free
    ksort($lJobIDsInSubOnly); // unnecessary, for maintenance only, can be removed when error-free
    ksort($lJobIDsInProOnly); // unnecessary, for maintenance only, can be removed when error-free

    $this -> dbg('1.3 preparation: $lAllJobIDsByJobSub: '.print_r($lAllJobIDsByJobSub, TRUE));
    $this -> dbg('1.3 preparation: $lAllJobIDsByJobPro: '.print_r($lAllJobIDsByJobPro, TRUE));
    $this -> dbg('1.3 preparation: $lAllJobIDs: '.print_r($lAllJobIDs, TRUE));
    $this -> dbg('1.3 preparation: $lJobIDsInSubOnly: '.print_r($lJobIDsInSubOnly, TRUE));
    $this -> dbg('1.3 preparation: $lJobIDsInProOnly: '.print_r($lJobIDsInProOnly, TRUE));

    //
    // 1.4 preparation: critical paths
    //

    $lAllCriticalPaths = array();

    $lSQL = 'SELECT `master`.code AS jobtype, `status`.`status` AS webstatus_of_job, `status`.display AS display_of_job, `status`.pro_con AS display_of_project';
    $lSQL.= ' FROM al_crp_master AS `master`,';
    $lSQL.= ' al_crp_status AS `status`';
    $lSQL.= ' WHERE `master`.mand='.MID;
    $lSQL.= ' AND `status`.mand='.MID;
    $lSQL.= ' AND `master`.id=`status`.crp_id';
    $lSQL.= ' AND `master`.code IN ('.$lAllJobTypesImploded.');';

    $this -> dbg('1.4 preparation: $lSQL: '.print_r($lSQL, TRUE));

    $lQry = new CCor_Qry($lSQL);
    foreach ($lQry as $lKey => $lValue) {
      $lAllCriticalPaths[$lValue['jobtype']][$lValue['webstatus_of_job']] = array(
        'display_of_job' => $lValue['display_of_job'],
        'webstatus_of_job' => $lValue['webstatus_of_job'],
        'display_of_project' => $lValue['display_of_project'],
        'webstatus_of_project' => NULL);
    }

    $this -> dbg('1.4 preparation: $lAllCriticalPaths: '.print_r($lAllCriticalPaths, TRUE));

    //
    // 2. we have to update job webstatus for archived jobs
    //

    $lArchivedJobIDs = array();
    $lAllJobIDsImploded = '\''.implode('\',\'', array_keys($lAllJobIDs)).'\'';
    $lSQL = 'SELECT jobid,src FROM al_job_arc_'.MID.' WHERE jobid IN ('.$lAllJobIDsImploded.');';

    $this -> dbg('2. we have to update job webstatus for archived jobs: $lAllJobIDs: '.print_r($lAllJobIDs, TRUE));
    $this -> dbg('2. we have to update job webstatus for archived jobs: $lAllJobIDsImploded: '.print_r($lAllJobIDsImploded, TRUE));
    $this -> dbg('2. we have to update job webstatus for archived jobs: $lSQL: '.print_r($lSQL, TRUE));

    $lQry -> query($lSQL);
    foreach ($lQry as $lKey => $lValue) {
      $lArchivedJobIDs[$lValue['jobid']] = $lValue['src'];

      $lTemp = max($lAllCriticalPaths[$lValue['src']]);

      $lSQL = 'UPDATE al_job_pro_crp';
      $lSQL.= ' SET job_status='.esc($lTemp['webstatus_of_job']);
      $lSQL.= ' WHERE src='.esc($lValue['src']);
      $lSQL.= ' AND jobid='.esc($lValue['jobid']).';';
      $lResult = CCor_Qry::exec($lSQL);

      $this -> dbg('2. we have to update job webstatus for archived jobs: $lSQL: '.print_r($lSQL, TRUE));
    }

    $this -> dbg('2. we have to update job webstatus for archived jobs: $lArchivedJobIDs: '.print_r($lArchivedJobIDs, TRUE));

    unset($lTemp); // to ensure that $lTemp will be re-initialised next time

    //
    // 3. we have to update job webstatus: active
    //

    $lActiveJobIDs = array_diff(array_keys($lAllJobIDs), array_keys($lArchivedJobIDs));

    $this -> dbg('3. we have to update job webstatus: active: $lAllJobIDs: '.print_r($lAllJobIDs, TRUE));
    $this -> dbg('3. we have to update job webstatus: active: $lArchivedJobIDs: '.print_r($lArchivedJobIDs, TRUE));
    $this -> dbg('3. we have to update job webstatus: active: $lActiveJobIDs: '.print_r($lActiveJobIDs, TRUE));

    foreach ($lActiveJobIDs as $lKey => $lValue) {
      $lFac = new CJob_Dat();
      $lFac -> load($lValue);
      $lJob = $lFac -> toArray();

      $lSQL = 'UPDATE al_job_pro_crp';
      $lSQL.= ' SET job_status='.esc($lAllCriticalPaths[$lJob['src']][$lJob['webstatus']]['webstatus_of_job']);
      $lSQL.= ' WHERE pro_id='.esc($lProjectID);
      $lSQL.= ' AND jobid='.esc($lJob['jobid']).';';
      $lResult = CCor_Qry::exec($lSQL);

      $this -> dbg('3. we have to update job webstatus: active: $lSQL: '.print_r($lSQL, TRUE));
    }

    //
    // 4. we have to remove items from al_job_sub_X that have no jobs assigned
    //

    $lSQL = 'SELECT id';
    $lSQL.= ' FROM al_job_sub_'.MID.' AS sub';
    $lSQL.= ' WHERE sub.pro_id='.$lProjectID;
    $lSQL.= ' AND del="N"';
    foreach ($lRelevantJobTypes as $lKey => $lValue) {
      $lSQL.= ' AND !(`'.$lValue.'` > \'\')';
    }
    $lResult = CCor_Qry::getImp($lSQL);

    $this -> dbg('4. we have to remove items from al_job_sub_X that have no jobs assigned (get jobIDs): $lSQL: '.print_r($lSQL, TRUE));

    if (!empty($lResult)) {
      $lSQL = 'UPDATE al_job_sub_'.MID;
      $lSQL.= ' SET del="Y"';
      $lSQL.= ' WHERE id IN ('.$lResult.');';
      $lResult = CCor_Qry::exec($lSQL);

      $this -> dbg('4. we have to remove items from al_job_sub_X that have no jobs assigned (set del="Y"): $lSQL: '.print_r($lSQL, TRUE));
    }

    //
    // 5. we have to remove items from al_job_pro_crp that have no jobs assigned
    //

    $lSQL = 'SELECT id';
    $lSQL.= ' FROM al_job_pro_crp';
    $lSQL.= ' WHERE pro_id='.$lProjectID;
    $lSQL.= ' AND jobid="";';
    $lResult = CCor_Qry::getImp($lSQL);

    $this -> dbg('5. we have to remove items from al_job_pro_crp that have no jobs assigned: $lSQL: '.print_r($lSQL, TRUE));

    if (!empty($lResult)) {
      $lSQL = 'DELETE FROM al_job_pro_crp';
      $lSQL.= ' WHERE pro_id='.$lProjectID;
      $lSQL.= ' AND id IN ('.$lResult.');';
      $lResult = CCor_Qry::exec($lSQL);

      $this -> dbg('5. we have to remove items from al_job_pro_crp that have no jobs assigned: $lSQL: '.print_r($lSQL, TRUE));
    }

    //
    // 6. jobIDs that are in al_job_sub_X but not in al_job_pro_crp
    //

    foreach ($lJobIDsInSubOnly as $lJobID => $lSrc) {
      $lSubID = CCor_Qry::getInt('SELECT id FROM al_job_sub_'.MID.' WHERE pro_id='.$lProjectID.' AND jobid_'.$lSrc.'="'.$lJobID.'";');

      $lIsActiveJob = array_key_exists($lJobID, $lActiveJobIDs);

      if ($lIsActiveJob) {
        $lFac = new CJob_Dat();
        $lFac -> load($lJobID);
        $lJob = $lFac -> toArray();
        $lWebStatus = $lJob['webstatus'];
      } else {
        $lFac = new CArc_Dat();
        $lFac -> load($lJobID);
        $lJob = $lFac -> toArray();
        $lWebStatus = $lJob['webstatus'];
      }
      $DisplayOfJob = $lAllCriticalPaths[$lSrc][$lWebStatus]['display_of_job'];

      $DisplayOfProject = CCor_Qry::getInt('SELECT `status`.pro_con FROM al_crp_status AS `status`, al_crp_master AS `master` WHERE `status`.crp_id=`master`.id AND `status`.mand='.MID.' AND `master`.mand='.MID.' AND `status`.status=\''.$lWebStatus.'\' AND `master`.code=\''.$lSrc.'\';');

      $lSQL = 'INSERT INTO al_job_pro_crp (';
      $lSQL.= ' `mand`,';
      $lSQL.= ' `pro_id`,';
      $lSQL.= ' `sub_id`,';
      $lSQL.= ' `src`,';
      $lSQL.= ' `jobid`,';
      $lSQL.= ' `job_status`,';
      $lSQL.= ' `pro_status`';
      $lSQL.= ' ) VALUES (';
      $lSQL.= MID.',';
      $lSQL.= $lProjectID.',';
      $lSQL.= $lSubID.',';
      $lSQL.= '\''.$lSrc.'\',';
      $lSQL.= '\''.$lJobID.'\',';
      $lSQL.= $DisplayOfJob.',';
      $lSQL.= $DisplayOfProject;
      $lSQL.= ' );';
      $lResult = CCor_Qry::exec($lSQL);

      $this -> dbg('6. jobIDs that are in al_job_sub_X but not in al_job_pro_crp: $lSQL: '.print_r($lSQL, TRUE));
    }

    //
    // 7. jobIDs that are in al_job_pro_crp but not in al_job_sub_X
    //
    $lSQL = 'SHOW COLUMNS FROM al_job_sub_'.MID;
    $lResult = CCor_Qry::getImp($lSQL, 'Field');
    $lExplode = explode(',', $lResult);
    $lColumnNames = array_flip($lExplode);

    $this -> dbg('7. jobIDs that are in al_job_pro_crp but not in al_job_sub_X: $lColumnNames: '.print_r($lColumnNames, TRUE));

    foreach ($lJobIDsInProOnly as $lJobID => $lSrc) {
      $lIsActiveJob = array_key_exists($lJobID, $lActiveJobIDs);

      if ($lIsActiveJob) {
        $lFac = new CJob_Dat();
        $lFac -> load($lJobID);
        $lJob = $lFac -> toArray();
      } else {
        $lFac = new CArc_Dat();
        $lFac -> load($lJobID);
        $lJob = $lFac -> toArray();
      }

      $lDiff = array_diff_key($lColumnNames, $lJob);
      foreach ($lDiff as $lKey => $lValue) {
        if (!array_key_exists($lKey, $lJob)) {
          unset($lJob[$lKey]);
        }
      }

      $lJob['jobid_'.$lSrc] = $lJob['jobid'];
      unset($lJob['jobid']);
      unset($lJob['src']);

      $lSQL = 'INSERT INTO al_job_sub_'.MID.' (';
      $lSQL.= ' `pro_id`,';
      $lSQL.= ' `jobid_'.$lSrc.'`';
      $lSQL.= ' ) VALUES (';
      $lSQL.= $lProjectID.',';
      $lSQL.= '\''.$lJobID.'\'';
      $lSQL.= ' );';
      $lResult = CCor_Qry::exec($lSQL);

      $this -> dbg('7. jobIDs that are in al_job_pro_crp but not in al_job_sub_X: $lSQL: '.print_r($lSQL, TRUE));

      $lSQL = 'UPDATE al_job_pro_crp';
      $lSQL.= ' SET `sub_id`='.mysql_insert_id();
      $lSQL.= ' WHERE `pro_id`='.$lProjectID;
      $lSQL.= ' AND `jobid`='.$lJobID;
      $lResult = CCor_Qry::exec($lSQL);

      $this -> dbg('7. jobIDs that are in al_job_pro_crp but not in al_job_sub_X: $lSQL: '.print_r($lSQL, TRUE));

      CJob_Utl_Sub::reflectUpdate($lJobID, $lJob);
    }

    $this -> redirect('index.php?act=job-pro-sub&jobid='.$lProjectID);
  }

  /*
   * Show Master ProjektItem List to assig the variant Jobs
   */
  protected function actAssignmaster() {
    $lJobId = $this -> getReq('jobid');
    $lVariants = $this -> getReq('sid');

    //if the job already assigned to Projekt, get ProjektId.
    //$lPrjId = $this -> getReq('prjid');

    $lJob = new CJob_Pro_Dat();
    if ($lJob -> load($lJobId)) {
      $lJob -> addRecentJob();
    }

    $lVie = new CJob_Pro_Header($lJob);
    $lRet = $lVie -> getContent();

    $lVie = new CJob_Pro_Sub_Assign($lJobId,$lVariants);
    $lRet.= $lVie -> getContent();
    $this -> render($lRet);
  }

  /*
   * Assign Variant Items to Master Item
   */
  protected function actSassignmaster() {
    $lJobId = $this -> getReq('jobid');
    $lMasterId = $this -> getReq('id');
    $lArrVariants = explode(',',$this -> getReq('sid'));
    // Prepare variant projekt item list for SQL.
    $lTempArrVariants = array_map("esc", $lArrVariants);//jedes Element wird ".mysql_escaped."
    $lVariants = implode(',', $lTempArrVariants);

    $lSql = '';
    // If no variant , return back
   if (!empty($lArrVariants)) {
     $lSql = 'UPDATE al_job_sub_'.MID;
     $lSql.= ' SET master_id='.$lMasterId;
     $lSql.= ' WHERE pro_id='.$lJobId;
     $lSql.= ' AND id IN('.$lVariants.')';
     CCor_Qry::exec($lSql);
     $this ->dbg('Variants '.$lVariants .' assigned to Master Id#'.$lMasterId);

     foreach ($lArrVariants as $lVariantId) {
       $lAssignedJobs = $this -> getAssignedJobs($lVariantId);
       $lUpd = array('master_id' => $lMasterId);
       $this -> updateAssignedJobs ($lAssignedJobs, $lUpd);
     }

   }else {
     // No Variant Projekt Items
     $this ->dbg('No Variants are selected.');
   }
   $this ->redirect('index.php?act=job-pro-sub&jobid='.$lJobId);

  }

  /*
   * Get Assigned Variant Projekt Items
   * @param string|int $aProId Project Id
   * @param string|int $aMasterId Master Id
   * return array $lRet Variant Projektitem List
   *
   */
  public function getAssignedVariants($aProId, $aMasterId) {
    $lRet = Array();
    $lProId = $aProId;
    $lMasterId = $aMasterId;

    $lSql = 'SELECT id FROM al_job_sub_'.MID;
    $lSql.= ' WHERE pro_id='.$lProId;
    $lSql.= ' AND master_id='.$aMasterId;

    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lRet[] = $lRow['id'];
    }
    return $lRet;
  }

  /*
   * Get Src and JobIds from Project Items
   * @param string|int $lPrjItemId Project Item Id
   * return Array Job sources ans IDs
   */
  protected function getAssignedJobs($aPrjItemId) {
    $lRet = Array();
    $lPrjItemId = $aPrjItemId;
    $lSubSrc = CCor_Cfg::get('menu-projektitems');
    //@var Array Existing Columns in al_job_sub_X
    $lExistingColumns = Array();
    $lSqlStr = '';

    // Get existing columns from archive table
    $lTableColumns = new CCor_Qry('SHOW COLUMNS FROM al_job_sub_'.MID);
    foreach ($lTableColumns as $lRow) {
      $lExistingColumns[] = $lRow -> Field;
    }

    // Get jobid_src ie jobid_rep,jobid_art....
    foreach($lSubSrc as $lKey) {
      $lSrc = substr($lKey ,strrpos($lKey,'_')+1);
      $lColum = 'jobid_'.$lSrc;
      if (isset($lExistingColumns,$lColum)) {
        $lArrSrc[]= $lSrc;
        $lSqlStr.= $lColum.',';
      }
    }
    $lSqlStr = substr($lSqlStr,0,-1);

    $lSql = 'SELECT '.$lSqlStr.' FROM al_job_sub_'.MID;
    $lSql.= ' WHERE id='.$lPrjItemId;
    $lQry = new CCor_Qry($lSql);
    $lRow = $lQry->getDat();
    foreach ($lRow as $lKey=>$lVal) {
      // If Source Column has JobId, take it to return array()
      if ($lVal !='') {
        $lSrc = substr($lKey ,strrpos($lKey,'_')+1);
        $lRet[$lSrc] = $lVal;
      }

    }
    return $lRet;
  }

  /*
   * Update Assigned Jobs
   * @param Array $aAssignedJobs Joblist of assigned jobs
   * @param Array $aUpd Job Updates
   *
   */
  protected function updateAssignedJobs($aAssignedJobs, $aUpd, $aUpdateArchiveJobs = TRUE) {
    $lAssignedJobs= Array();
    $lUpd = Array();
    $lAssignedJobs = $aAssignedJobs;
    $lUpdateArchiveJobs = $aUpdateArchiveJobs;
    $lUpd = $aUpd;

    // Check if JobKey defined
    $lFie = CCor_Res::extract('alias', 'id', 'fie');

    foreach($lUpd as $lKey => $lVal) {
      if (!array_key_exists($lKey,$lFie)) {
        $this ->dbg('Project Item Jobs can not be updated.',mlError);
        $this ->dbg($lKey.' is not defined Jobfields',mlError);
        unset($lUpd[$lKey]);
      }
    }
    foreach($lAssignedJobs as $lSrc => $lId) {
      if ($this -> updArc($lId, $lUpd, $lUpdateArchiveJobs)) continue;
      $lFac = new CJob_Fac($lSrc);
      $lMod = $lFac -> getMod($lId);
      $lRet = $lMod -> forceUpdate($lUpd);
      if ($lRet) {
        // Update Shadow
        CJob_Utl_Shadow::reflectUpdate($lSrc, $lId, $lUpd);
      }
      // Update History
      $lMod -> addHistory(htEdit, lan('job.changes'). ' Project Items','',array('upd' => $this -> mArrHistory));
    }
  }

  /*
   * Check If Column 'is_master'is defined in al_job_sub_[MID
   * @return boolean $lRet is defined retun TRUE, if not return FLASE
   */
  public function checkColumnIsMasterDefined() {
    $lRet = FALSE;

    $lTableColumns = new CCor_Qry('SHOW COLUMNS FROM al_job_sub_'.MID);
    foreach ($lTableColumns as $lRow) {
      if ($lRow -> Field == 'is_master') {
        $lRet = TRUE;
        break 1;
      }
    }

    return $lRet;
  }

  /*
   * Update Archive Jobs
   * @param string $aJobId JobId
   * @param array $aUpd Updates
   *
   * @return boolean Archive? Update Succes?
   */
  protected function updArc($aJobId, $aUpd = Array(), $aUpdateArchiveJobs = TRUE) {
    $lRet = FALSE;
    $lUpd = $aUpd;
    $lUpdateArchiveJobs = $aUpdateArchiveJobs;
    if (empty ($lUpd)) {
      return $lRet;
    }
    $lSql = 'SELECT COUNT(*) FROM al_job_arc_'.MID.' WHERE jobid='.esc($aJobId);
    $lCnt = CCor_Qry::getInt($lSql);
    if (0 < $lCnt) {
      if ($lUpdateArchiveJobs) {
        // Update archive Jobs
        $lSql = 'Update al_job_arc_'.MID.' SET ';
        $lSqlUpdt ='';
        foreach ($lUpd as $lKey => $lVal) {
          $lSqlUpdt.= $lKey.' = "'.$lVal.'",';
        }
        $lSql.= substr($lSqlUpdt,0,-1);
        $lSql.= ' WHERE jobid='.esc($aJobId);
        if (CCor_Qry::exec($lSql)) {
          $lRet = TRUE;
        }
      }else {
        // It is archive Job but no Update
        $this -> dbg ('JobId #'.$aJobId.' is in archive and not be updated.',mlWarn);
        $lRet = TRUE;
      }
    }
    return $lRet;
  }

  /*
   * Update assigned jobs
   * @param string $aItemId project item id
   * @param array $aUpdates update array
   */
  public function updSubJobs($aItemId, $aUpdates) {
    $lItemId = $aItemId;
    $lUpdates = $aUpdates;
    $lAssignedJobs = Array();
    $lAssignedJobs = $this -> getAssignedJobs($lItemId);
    if (empty($lAssignedJobs)) {
      $this ->dbg('There is a no Sub Jobs to update from Project Item Id:# '.$lItemId);
      return '';
    }
    $lUpdateArchiveJobs = FALSE;
    $this -> updateAssignedJobs ($lAssignedJobs, $lUpdates, $lUpdateArchiveJobs);
  }

  protected function actSelview() {
    $lId = $this -> getInt('id');

    $lQry = new CCor_Qry('SELECT * FROM al_usr_view WHERE id='.$lId);
    if ($lRow = $lQry -> getDat()) {
      $lUsr = CCor_Usr::getInstance();
      $lUsr -> setPref($this -> mMod.'.cols', $lRow['cols']);
      $lUsr -> setPref($this -> mMod.'.lpp', $lRow['lpp']);
      $lUsr -> setPref($this -> mMod.'.ord', $lRow['ord']);
      $lUsr -> setPref($this -> mMod.'.sfie', $lRow['sfie']);
      $lUsr -> setPref($this -> mMod.'.page', 0);
    }
    $this -> redirect();
  }
}