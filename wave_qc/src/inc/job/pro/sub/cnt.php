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

    #$lQry = new CCor_Qry('SELECT * FROM al_job_pro_'.intval(MID).' WHERE id='.$lJobId);
    #$lJob = $lQry -> getDat();
    $lJob = new CJob_Pro_Dat();
    if ($lJob -> load($lJobId)) {
      $lJob -> addRecentJob();
    }
    #echo '<pre>---cnt.php---'.get_class().'---';var_dump($lJobId,$lJob,'#############');echo '</pre>';

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

    if ($lSubsUpdate != 0){
      // Save und Update Sub Jobs
      if (!empty($lMod -> mChangedFields)){
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
    if (CCor_Qry::exec($lSql)){
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
      if (!empty($lAssignedVariants)){
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
        foreach ($lAssignedVariants as $lKey => $lVal ){
          $lAssignedVariantJobs[]= $this -> getAssignedJobs($lVal);
        }
        //Define Update
        $lUpd = array('master_id' => '');

        foreach ($lAssignedVariantJobs as $lKey => $lVal){
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
    if (empty($lCols)){
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

  //22651 Project Critical Path Functionality - Project CRP depends on job CRP:
  protected function actStataktual() {
    $lProId = $this -> getInt('jobid');

    $lUsr = CCor_Usr::getInstance();
    $lRig = $lUsr -> canRead('job-pro-status.actualize');
    if (rdRead == $lRig) {
      $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');
      foreach ($lCrp as $lSrc => $lCrpId) {
        $lCrpStaDis[$lSrc] = CCor_Res::extract('status', 'display', 'crp', $lCrpId);
      }

      $lSrcStaPro = CCrp_Cnt::getJob2ProjectAssignment();

      // get all job types there are
      $lTabelColumns = new CCor_Qry('SHOW COLUMNS FROM `al_job_sub_'.MID.'`');
      foreach ($lTabelColumns as $lRow) {
        $lField = $lRow -> Field;
        if (strpos($lField, 'jobid_') === 0) {
          $lSrc = str_replace('jobid_', '', $lField);
          $lExistingColumns[$lSrc] = $lField;
        }
      }
      $lSqlSrc = array_map("backtick", $lExistingColumns);
      $lSqlSrc = implode(',', $lSqlSrc); // returns something like `jobid_art`,`jobid_rep`,`jobid_sec`,`jobid_adm`,`jobid_mis`,`jobid_com`,`jobid_tra`

      // get all jobs assigned to this project
      $lSql = 'SELECT `id` as sub_id, '.$lSqlSrc;
      $lSql.= ' FROM `al_job_sub_'.MID.'` WHERE `pro_id`='.$lProId;
      $lQry = new CCor_Qry($lSql);
      $lJobs = array();
      foreach ($lQry as $lRow) {
        foreach ($lExistingColumns as $lTyp => $lSrc) {
          if (!empty($lRow[$lSrc])) {
            $lJob = esc($lRow[$lSrc]);
            $lJobs[ $lJob ] = array('src' => $lTyp, 'sub_id' => $lRow['sub_id']);
          }
        }
      }

      $lAllJobIds = implode(',', array_keys($lJobs));

      if (!empty($lAllJobIds) AND !empty($lSrcStaPro)) {
        // is the job already archived?
        $lSql = 'SELECT `jobid` FROM `al_job_arc_'.MID.'` WHERE `jobid` IN ('.$lAllJobIds.')';
        $lQry -> query($lSql);
        $lArcJobs = array();
        foreach ($lQry as $lRow) {
          $lJob = esc($lRow['jobid']);
          $lArcJobs[] = $lJob;
          // remove archived jobs
          #unset($lJobs[ $lJob ]);
        }
        #$lAllJobIds = implode(',', array_keys($lJobs));

        if (!empty($lArcJobs)) {
          $lAllArcJobIds = implode(',', $lArcJobs);
          // Aktualisiere die Shadow mit dem Archivstatus. Wenn nicht mit STATUS_ARCHIV gearbeitet wird, musspro Jobtyp & per al_crp_step: job2arc,mand,crp_id die to_id geholt werden und mit al_crp_status erhaelt man den status ODER direkt mit getJobList.
          $lSql = 'UPDATE `al_job_shadow_'.MID.'` SET webstatus='.STATUS_ARCHIV.' WHERE `jobid` IN ('.$lAllArcJobIds.')';
          // delete archived jobs from table
          #$lSql = 'DELETE FROM `al_job_pro_crp` WHERE `jobid` IN ('.$lAllArcJobIds.')';
          $lQry -> query($lSql);
        }

        if (!empty($lAllJobIds)) {
          // Hole die Statuswechseltermine aus der Shadow. ACHTUNG, wenn Status > 11 verwendet werden (Intouch)!
          $lSql = 'SELECT `jobid`,`webstatus`,`fti_1`,`fti_2`,`fti_3`,`fti_4`,`fti_5`,`fti_6`,`fti_7`,`fti_8`,`fti_9`,`fti_10`,`fti_11`,`lti_1`,`lti_2`,`lti_3`,`lti_4`,`lti_5`,`lti_6`,`lti_7`,`lti_8`,`lti_9`,`lti_10`,`lti_11`';
          $lSql.= ' FROM `al_job_shadow_'.MID.'` WHERE jobid IN ('.$lAllJobIds.')';
          $lQry -> query($lSql);
          $lJobStatus = array();
          foreach ($lQry as $lRow) {
            $lJobStatus[ esc($lRow['jobid']) ] = $lRow;
          }

          /*
          // Sind die JobIds bereits in der Tabelle enthalten?
          $lSql = 'SELECT `jobid` FROM `al_job_pro_crp` WHERE `mand`='.MID.' AND `jobid` IN ('.$lAllJobIds.')';
          $lQry -> query($lSql);
          foreach ($lQry as $lRow) {
            // bereits enthaltene Jobs werden nicht beruecksichtigt
            unset($lJobs[ esc($lRow['jobid']) ]);
          }
          */

          if (!empty($lJobs)) {
            foreach ($lJobs as $lJobId => $lInfo) {
              if (isset($lJobStatus[$lJobId])) {
                $lJobStatusInfo = $lJobStatus[$lJobId];
                $lSrc = $lInfo['src'];
                if (isset($lSrcStaPro[$lSrc])) {
                  $lStaPro = $lSrcStaPro[$lSrc];
                  $lJobstatus = $lJobStatusInfo['webstatus'];

                  if (isset($lCrpStaDis[$lSrc][$lJobstatus])) {
                    $lFiTime = array();
                    $lLaTime = array();
                    foreach ($lStaPro as $lK => $lDisplay) {
                      if (0 < $lDisplay['pro_con']) {
                        $lTimeIndx = $lDisplay['pro_con'];
                      } else {
                        $lTimeIndx = $lDisplay['display'];
                      }

                      if (!isset($lFiTime[$lDisplay['pro_con']])) {
                        $lFiTime[$lTimeIndx] = '000-00-00 00:00:00';
                      }

                      if (!isset($lLaTime[$lTimeIndx])) {
                        $lLaTime[$lTimeIndx] = '000-00-00 00:00:00';
                      }

                      $lFirst = 'fti_'.$lDisplay['display'];
                      $lLast  = 'lti_'.$lDisplay['display'];

                      if (!empty($lJobStatusInfo[$lFirst]) AND $lFiTime[$lTimeIndx] < $lJobStatusInfo[$lFirst]) {
                        $lFiTime[$lTimeIndx] = $lJobStatusInfo[$lFirst];
                      }

                      if (!empty($lJobStatusInfo[$lLast]) AND $lLaTime[$lTimeIndx] < $lJobStatusInfo[$lLast]) {
                        $lLaTime[$lTimeIndx] = $lJobStatusInfo[$lLast];
                      }
                    } // end_foreach ($lJobstatus as $lDisplay)

                    $lSql = 'REPLACE INTO `al_job_pro_crp` SET';
                    $lSql.= ' mand='.MID.',';
                    $lSql.= ' pro_id='.esc($lProId).',';
                    $lSql.= ' sub_id='.esc($lInfo['sub_id']).',';
                    $lSql.= ' src='.esc($lSrc).',';
                    $lSql.= ' jobid='.$lJobId.','; // jobid is already escaped
                    $lSql.= ' job_status='.esc($lCrpStaDis[$lSrc][$lJobstatus]);
                    if (isset($lStaPro[$lJobstatus])) {
                      $lProStatus = $lStaPro[$lJobstatus]['pro_con']; // $lSrcStaPro[$lSrc][ $lRow['status'] ] = array($lRow['display'], $lRow['pro_con']);
                      $lSql.= ', pro_status='.esc($lProStatus);
                    }
                    foreach ($lFiTime as $lDis => $lTim) {
                      if ('000-00-00 00:00:00' < $lTim) {
                        $lSql.= ',fti_'.$lDis.'='.esc($lTim);
                      }
                    }
                    foreach ($lLaTime as $lDis => $lTim) {
                      if ('000-00-00 00:00:00' < $lTim) {
                        $lSql.= ',lti_'.$lDis.'='.esc($lTim);
                      }
                    }
                    $lQry -> query($lSql);
                  } // end_if (isset($lCrpStaDis[$lSrc][$lJobstatus]))
                } // end_if (isset($lSrcStaPro[$lSrc]))
              } // end_if (isset($lJobStatus[$lJobId]))
            } // end_foreach ($lJobs as $lJobId => $lInfo)
          } else {
            $this -> dbg('1.Jobstatus aktualisieren: Nothing todo!');
          } // end_if/else (!empty($lJobs))
        } else {
          $this -> dbg('2.Jobstatus aktualisieren: Nothing todo!');
        } // end_if/else (!empty($lAllJobIds))
      } else {
        $this -> dbg('3.Jobstatus aktualisieren: Nothing todo!');
      } // endif/else (!empty($lAllJobIds) AND !empty($lSrcStaPro))
    } else { // end_if (1 == $lRig)
      $this -> dbg('User '.$lUsr -> getId().' has no right for updating project crp.', mlWarn);
    }

    $this -> redirect('index.php?act=job-pro-sub&jobid='.$lProId);
  } //end_function actStataktual()

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
    #echo '<pre>---cnt.php---'.get_class().'---';var_dump($lJobId,$lJob,'#############');echo '</pre>';

    $lVie = new CJob_Pro_Header($lJob);
    $lRet = $lVie -> getContent();

    //http://php5/mpp5/src/index.php?act=job-rep.edt&jobid=010002437

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
   if (!empty($lArrVariants)){
     $lSql = 'UPDATE al_job_sub_'.MID;
     $lSql.= ' SET master_id='.$lMasterId;
     $lSql.= ' WHERE pro_id='.$lJobId;
     $lSql.= ' AND id IN('.$lVariants.')';
     CCor_Qry::exec($lSql);
     $this ->dbg('Variants '.$lVariants .' assigned to Master Id#'.$lMasterId);

     foreach ($lArrVariants as $lVariantId){
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
  public function getAssignedVariants($aProId, $aMasterId){
    $lRet = Array();
    $lProId = $aProId;
    $lMasterId = $aMasterId;

    $lSql = 'SELECT id FROM al_job_sub_'.MID;
    $lSql.= ' WHERE pro_id='.$lProId;
    $lSql.= ' AND master_id='.$aMasterId;

    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow){
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
    $lTabelColumns = new CCor_Qry('SHOW COLUMNS FROM al_job_sub_'.MID);
    foreach ($lTabelColumns as $lRow) {
      $lExistingColumns[] = $lRow -> Field;
    }

    // Get jobid_src ie jobid_rep,jobid_art....
    foreach($lSubSrc as $lKey){
      $lSrc = substr($lKey ,strrpos($lKey,'_')+1);
      $lColum = 'jobid_'.$lSrc;
      if (isset($lExistingColumns,$lColum)){
        $lArrSrc[]= $lSrc;
        $lSqlStr.= $lColum.',';
      }
    }
    $lSqlStr = substr($lSqlStr,0,-1);

    $lSql = 'SELECT '.$lSqlStr.' FROM al_job_sub_'.MID;
    $lSql.= ' WHERE id='.$lPrjItemId;
    $lQry = new CCor_Qry($lSql);
    $lRow = $lQry->getDat();
    foreach ($lRow as $lKey=>$lVal){
      // If Source Column has JobId, take it to return array()
      if ($lVal !=''){
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
  protected function updateAssignedJobs($aAssignedJobs, $aUpd, $aUpdateArchiveJobs = TRUE){
    $lAssignedJobs= Array();
    $lUpd = Array();
    $lAssignedJobs = $aAssignedJobs;
    $lUpdateArchiveJobs = $aUpdateArchiveJobs;
    $lUpd = $aUpd;

    // Check if JobKey defined
    $lFie = CCor_Res::extract('alias', 'id', 'fie');

    foreach($lUpd as $lKey => $lVal){
      if (!array_key_exists($lKey,$lFie)){
        $this ->dbg('Project Item Jobs can not be updated.',mlError);
        $this ->dbg($lKey.' is not defined Jobfields',mlError);
        unset($lUpd[$lKey]);
      }
    }
    foreach($lAssignedJobs as $lSrc => $lId){
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
  public function checkColumnIsMasterDefined(){
    $lRet = FALSE;

    $lTabelColumns = new CCor_Qry('SHOW COLUMNS FROM al_job_sub_'.MID);
    foreach ($lTabelColumns as $lRow) {
      if ($lRow -> Field == 'is_master'){
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
    if (empty ($lUpd)){
      return $lRet;
    }
    $lSql = 'SELECT COUNT(*) FROM al_job_arc_'.MID.' WHERE jobid='.esc($aJobId);
    $lCnt = CCor_Qry::getInt($lSql);
    if (0 < $lCnt) {
      if ($lUpdateArchiveJobs){
        // Update archive Jobs
        $lSql = 'Update al_job_arc_'.MID.' SET ';
        $lSqlUpdt ='';
        foreach ($lUpd as $lKey => $lVal) {
          $lSqlUpdt.= $lKey.' = "'.$lVal.'",';
        }
        $lSql.= substr($lSqlUpdt,0,-1);
        $lSql.= ' WHERE jobid='.esc($aJobId);
        if (CCor_Qry::exec($lSql)){
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
    if (empty($lAssignedJobs)){
      $this ->dbg('There is a no Sub Jobs to update from Project Item Id:# '.$lItemId);
      return '';
    }
    $lUpdateArchiveJobs = FALSE;
    $this -> updateAssignedJobs ($lAssignedJobs, $lUpdates, $lUpdateArchiveJobs);
  }
}