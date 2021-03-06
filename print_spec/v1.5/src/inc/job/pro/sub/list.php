<?php
/**
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 11564 $
 * @date $Date: 2015-11-26 01:47:27 +0800 (Thu, 26 Nov 2015) $
 * @author $Author: ahajali $
 */
class CInc_Job_Pro_Sub_List extends CHtm_List {

  protected $mWithoutLimit;
  protected $mIsNoArc = TRUE; //wird auch aus dem Archiv heraus aufgerufen
  public $mCrpMaster = Array();
  public $mCrpSrc = Array();
  public $mCanInsert = TRUE;

  /*
   * Master-Variant Bundle aktiv?
   */
  public $mMasterVariantBundleActiv = FALSE;

  /*
   * Master-Variant Bundle
   * Check if column 'is_master' defined in al_job_sub_X
   */
  public $mColumnIsMasterDefined = FALSE;

  public function __construct($aJobId, $aIsNoArc = TRUE) {
    $this -> mIsNoArc = $aIsNoArc;
    if ($this -> mIsNoArc) {
      $lSrc = 'job';
    } else {
      $lSrc = 'arc';
    }
    // Master-Variant Bundle Active ?
    $this -> mMasterVariantBundleActiv = CCor_Cfg::get('master.varaiant.bundle', FALSE);
    // is_master is defined in al_job_sub_X;
    $this ->mColumnIsMasterDefined = $this -> checkColumnIsMasterDefined();

    parent::__construct($lSrc.'-pro-sub');
    $this -> mJobId = intval($aJobId);
    $this -> mStdLnk = 'index.php?act='.$this -> mMod.'.edt&amp;jobid='.$this -> mJobId.'&amp;id=';
    $this -> mOrdLnk = 'index.php?act='.$this -> mMod.'.ord&amp;jobid='.$this -> mJobId.'&amp;fie=';
    $this -> mDelLnk = 'index.php?act='.$this -> mMod.'.del&amp;jobid='.$this -> mJobId.'&amp;id=';
    $this -> setAtt('width', '100%');
    $this -> mCapCls = 'th1';
    $this -> mTitle = lan('job-sub.menu');
    $this -> mWithoutLimit = TRUE; //Get Project Item List without Limit.
    $this -> mCrpMaster = CCor_Res::extract('code', 'id', 'crpmaster');
    $this -> loadCrps();

    $this -> mFie = CCor_Res::getByKey('id', 'fie');
    $lUsr = CCor_Usr::getInstance();

    $this -> mCanInsArt = $lUsr -> canInsert('job-art');
    $this -> mCanInsRep = $lUsr -> canInsert('job-rep');
    $this -> mCanInsSec = $lUsr -> canInsert('job-sec');
    $this -> mCanInsMis = $lUsr -> canInsert('job-mis');
    $this -> mCanInsAdm = $lUsr -> canInsert('job-adm');
    $this -> mCanInsCom = $lUsr -> canInsert('job-com');
    $this -> mCanInsTra = $lUsr -> canInsert('job-tra');
    $this -> mUsr = $lUsr;

    $this->setupColumns();

    $this -> getPrefs();

    // By Master-Variant Bundle,if grouping is deactived,  Master Items shoul be displayed on the.
    // Order function (click on column titel) works for grouping. To ac-deactive used '-'.
    // i.g. if system pref'job-pro-sub.ord' is 'projectname',activate grouping after 'projectname'
    // if it is '-projectname', deactivate grouping.
    $lGrp = $lUsr -> getPref($lSrc.'-pro-sub.ord');
    if (substr($lGrp, 0, 1) == '-') {
      // Grouping is deactive. Master Items should be displayed on the top.
      $this -> mOrd = 'is_master';
      $this -> mDir = 'desc';
    }

    $this -> mAli = CCor_Res::getByKey('alias', 'fie');
    $this -> mPlain = new CHtm_Fie_Plain();

    $this -> getIterator();
    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);
    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);
    if (substr($lGrp, 0, 1) != '-') {
      // Grouping is active.
      $this -> setGroup($this -> mOrd);
    }
    $this -> mGrpDef = (isset($this -> mAli[$this -> mOrd])) ? $this -> mAli[$this -> mOrd] : NULL;

    $this -> mMaxLines = $this -> mIte -> getCount();
    $this -> preLoad();

    $this->createToolBar();
    $this -> addJs();
    
    $lStartDaysFuture = CCor_Cfg::get('ddl.future', 1);
    $lEndDate = mktime(0, 0, 0, date("m"), date ("d") + $lStartDaysFuture, date("Y"));
    $this -> mEndDate = strftime("%Y-%m-%d" , $lEndDate);
    $this -> mDdl = CCor_Res::get('ddl');
    $this -> mHighlightLine = CCor_Cfg::get('job.list.highlight.line');
  }


  protected function getTrTag() {
    return '<tr>';
  }


  protected function setupColumns() {
    $this->addCtr();
    if ($this->mCanInsArt) {
      $this->addColumn('sel', '', FALSE, array('width' => '16'));
      $this->addColumn('is_master', '', FALSE, array('width' => '16'));
    }
    if ($this->mMasterVariantBundleActiv && $this->mColumnIsMasterDefined && $this->mIsNoArc) {
      $this->addColumn('masterBundle', '', FALSE, array('width' => '16'));
    }
    $this->addColumns();
    $lMnuItems = CCor_Cfg::get('menu-projektitems');
    foreach ($lMnuItems as $lRow)  {
      $lTemp = str_replace("_", "-", $lRow);
      $this->addColumn($lRow, lan($lTemp.'.menu'), FALSE, array('width' => '70'));
    }
    if ($this->mCanInsert) {
      $this->addColumn('cpy', '', FALSE, array('width' => '16'));
    }
    if ($this->mIsNoArc AND $this->mCanDelete) {
      $this->addDel();
    }
  }


  protected function createToolBar() {
    $lUsr = CCor_Usr::getInstance();
    if ($this -> mIsNoArc && $this -> mCanInsArt) {
      $this -> addBtn(lan('job-pro-sub.createbundle'), 'createArt("'.$this -> mJobId.'","'.LAN.'")', 'img/ico/16/plus.gif');
    }
    if ($this -> mMasterVariantBundleActiv && $this ->mColumnIsMasterDefined && $this -> mIsNoArc){
      $this -> addBtn(lan('job-pro-sub.createMasterBundle'), 'createMasterVariant("'.$this -> mJobId.'","'.LAN.'")' , 'img/ico/16/plus.gif');
    }
    $this -> addBtn(lan('lib.opt.fpr'), 'go(\'index.php?act='.$this -> mMod.'.fpr&jobid='.$this -> mJobId.'\')', 'img/ico/16/col.gif');
    if ($this -> mIsNoArc AND $this -> mCanInsert) {
      $this -> addBtn(lan('job-pro-sub.new'), 'go(\'index.php?act='.$this -> mMod.'.new&jobid='.$this -> mJobId.'\')', 'img/ico/16/edit.gif');
    }
    if ($this -> mIsNoArc) {
      $this -> getWizards();
      if ($this -> mCanInsert) {
        $this -> addPanel('new', $this -> getSubMenu());
        $this -> addPanel(NULL, '|');
        $this -> addPanel('wiz', $this -> getWizMenu());
      }
    }
    $lRig = $lUsr -> canRead('job-pro-status.actualize');
    if (1 == $lRig) {
      $this -> addBtn(lan('job-pro-status.actualize'), 'go(\'index.php?act='.$this -> mMod.'.stataktual&jobid='.$this -> mJobId.'\')', 'img/ico/16/mt-4.gif');
    }
    $this -> addPanel('vie', $this -> getViewMenu());
  }

  protected function & getViewMenuObject() {
    $lMen = new CHtm_Menu(lan('lib.opt'));

    $lOk = 'ico/16/ok.gif';
    $lMen -> addTh2(lan('lib.opt.view'));
    $lMen -> addItem('index.php?act='.$this -> mMod.'.fpr&amp;jobid='.$this -> mJobId, lan('lib.opt.fpr'), 'ico/16/col.gif');

    $lMen -> addTh2(lan('lib.opt.savedviews'));
    #$lSql = 'SELECT id,name FROM al_usr_view WHERE ref="'.$this -> mMod.'" ';
    $lSql = 'SELECT id,name FROM al_usr_view WHERE 1 ';
    $lSql.= 'AND src="usr" AND src_id=0 AND ref="'.$this -> mMod.'" AND mand='.MID.' ';
    $lSql.= 'ORDER BY name';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lMen -> addItem('index.php?act='.$this -> mMod.'.selview&amp;id='.$lRow['id'].'&jobid='.$this -> mJobId, '[Global] '.$lRow['name'], 'ico/16/global.gif');
    }

    $lSql = 'SELECT id,name FROM al_usr_view WHERE 1 ';
    $lSql.= 'AND src="usr" AND src_id ='.$this -> mUsr -> getId().' AND ref="'.$this -> mMod.'" AND mand='.MID.' ';
    $lSql.= 'ORDER BY name';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lMen -> addItem('index.php?act='.$this -> mMod.'.selview&amp;id='.$lRow['id'].'&jobid='.$this -> mJobId, $lRow['name'], 'ico/16/col.gif');
    }
    $lMen -> addItem('index.php?act=job-view&amp;src='.$this -> mMod.'&amp;jobid='.$this -> mJobId, lan('lib.view.save'));
    if ($this -> mUsr -> canInsert('view-std')) {
      $lMen -> addItem('index.php?act='.$this -> mMod.'.allview&amp;jobid='.$this -> mJobId, lan('lib.view.save_as_std'), 'ico/16/save.gif');
    }
    return $lMen;
  }

  protected function addJs() {
    $lJs = 'function subAll(aEl){'.LF;
    $lJs.= 'var lFla=$(aEl).checked;'.LF;
    $lJs.= 'var lTr=$(aEl).up("tr");'.LF;
    $lJs.= 'while(lTr=lTr.next()){'.LF;
    $lJs.= 'if (lTr.firstDescendant().hasClassName("tg1")) break;'.LF;
    $lJs.= 'if (lTr.down("input")) lTr.down("input").checked=lFla;'.LF;
    $lJs.= '}}';
    $lPag = CHtm_Page::getInstance();
    $lPag -> addJs($lJs);
  }

  protected function getWizards() {
    $this -> mWiz = array();
    $lSql = 'SELECT * FROM al_wiz_master ORDER BY name_'.LAN;
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $this -> mWiz[$lRow['id']] = $lRow['name_'.LAN];
    }
  }

  protected function getSubMenu() {
    $lMen = new CHtm_Menu(lan('lib.new_item'));
    $lMen -> addTh2(lan('lib.tpl'));
    foreach ($this -> mWiz as $lKey => $lVal) {
      $lMen -> addItem('index.php?act=job-pro-sub.new&amp;jobid='.$this -> mJobId.'&amp;wiz='.$lKey, $lVal);
    }
    return $lMen -> getContent();
  }

  protected function getWizMenu() {
    $lMen = new CHtm_Menu(lan('wiz.menu'));
    $lMen -> addTh2(lan('lib.tpl'));
    foreach ($this -> mWiz as $lKey => $lVal) {
      $lMen -> addItem('index.php?act=job-pro-sub.wiz&amp;jobid='.$this -> mJobId.'&amp;wiz='.$lKey, $lVal);
    }
    return $lMen -> getContent();
  }

  protected function getIterator() {
    $this -> mIte = new CCor_TblIte('al_job_sub_'.intval(MID), $this -> mWithoutLimit);
    $this -> mIte -> addCnd('pro_id='.$this -> mJobId);
    $this -> mIte -> addCnd('del="N"');

  }

  protected function preLoad() {
    $lArr = array();
    $lArrNoAlink = array();
    $lArrLeer = array();
    $lPrjLeiter = array();
    $lArrLeerAmount = array();

    $this -> mIte = $this -> mIte -> getArray('id');
    /*
     * Master Variant Bundle JobId #23041
   	 * @var Boolean
  	*/
  	if ($this -> mMasterVariantBundleActiv && $this ->mColumnIsMasterDefined){
  	  $this -> mIte = $this -> sortByMaster($this -> mIte);
  	}
    $lZeile = 1;
    foreach ($this -> mIte as $lKey => $lRow) {
      if (!empty($lRow['jobid_art'])) {
        $lArr[$lRow['jobid_art']] = TRUE;
      }else{
        $lArrLeer['art'][$lZeile] = $lRow['id'];
      }
      if (!empty($lRow['jobid_rep'])) {
        $lArr[$lRow['jobid_rep']] = TRUE;
      }else{
        $lArrLeer['rep'][$lZeile] = $lRow['id'];
      }
      if (!empty($lRow['jobid_sec'])) {
        $lArr[$lRow['jobid_sec']] = TRUE;
      }else{
        $lArrLeer['sec'][$lZeile] = $lRow['id'];
      }
      if (!empty($lRow['jobid_adm'])) {
        $lArr[$lRow['jobid_adm']] = TRUE;
      }else{
        $lArrLeer['adm'][$lZeile] = $lRow['id'];
      }
      if (!empty($lRow['jobid_mis'])) {
        $lArr[$lRow['jobid_mis']] = TRUE;
      }else{
        $lArrLeer['mis'][$lZeile] = $lRow['id'];
      }
      if (!empty($lRow['jobid_com'])) {
        $lArr[$lRow['jobid_com']] = TRUE;
      }else{
        $lArrLeer['com'][$lZeile] = $lRow['id'];
      }
      if (!empty($lRow['jobid_item'])) {
        $lArr[$lRow['jobid_item']] = TRUE;
      }else{
        $lArrLeer['item'][$lZeile] = $lRow['id'];
      }

      $lJobs_PDB = CCor_Cfg::get('all-jobs_PDB');
      foreach ($lJobs_PDB as $lJob) {
        if (!empty($lRow['jobid_'.$lJob])) {
          $lArrNoAlink[] = $lRow['jobid_'.$lJob];
        }else{
          $lArrLeer[$lJob][$lZeile] = $lRow['id'];
        }
      }

      if (!empty($lRow['per_prj_verantwortlich'])) {
        $lPrjLeiter[$lZeile] = $lRow['per_prj_verantwortlich'];
      }
      $this -> mIte[$lKey]['zeile'] = $lZeile++;
    }

    foreach ($lArrLeer as $lKey => $lRow) {
      $lArrLeerAmount[$lKey] = count($lRow);
    }
    $this -> mArrLeer = $lArrLeer;
    $this -> mArrLeerAmount = $lArrLeerAmount;

    // Winter: "Der Projektverantwortliche, sowie der Admin, darf alle Jobs verschieben."
    $lUsr = CCor_Usr::getInstance();
    $lUid = $lUsr -> getId();
    $lCanSlide = false;
    foreach ($lPrjLeiter as $lPrjNr) {// user =?= PrjLeiter ?
      if ($lPrjNr == $lUid) {
        $lCanSlide = true;
        break; // Durchlauf beenden, sobald die Frage beantwortet ist.
      }
    }

    if (!$lCanSlide) {
      $lSql = 'SELECT m.uid FROM al_usr_mem m, al_gru g WHERE g.code="adm" AND g.id=m.gid AND m.uid='.$lUid.' LIMIT 0,1';
      $lAdmQry = new CCor_Qry($lSql);
      $lAdmQryResult = $lAdmQry -> getAssocs('');
      if (!empty($lAdmQryResult)) {
        $lCanSlide = TRUE;
      }
    }
    $this -> mCanSlide = $lCanSlide;

    $this -> mSub = array();
    $this -> mJfl = array();

    if (empty($lArr) AND empty($lArrNoAlink)) return;

    $lJid = '';
    if (!empty($lArr)) {
      foreach ($lArr as $lKey => $lDum) {
        $lJid.= '"'.$lKey.'",';
      }
      $lJid = strip($lJid);
      $this -> dump($lJid);
      $lAllJobids = $lJid;
      $lWriter = CCor_Cfg::get('job.writer.default', 'alink');
      if ('portal' == $lWriter) {
        $lIte = new CCor_TblIte('all', $this->mWithoutLimit);
        $lIte -> addField('jobid');
        $lIte -> addField('webstatus');
        $lIte -> addCnd('jobid IN ('.$lJid.')');
      } else {
        $lIte = new CApi_Alink_Query_Getjoblist();
        $lIte -> addField('webstatus', 'webstatus');
        $lIte -> addCondition('jobid', 'in', $lJid);
      }
      
      foreach ($lIte as $lRow) {
        $this -> mSub[$lRow['jobid']] = $lRow;
        unset($lArr[$lRow['jobid']]);
      }
    }

    if (!empty($lArr)) { // sind die nicht gefundenen Jobs bereits archiviert?
      $lArcJid = '"",';
      foreach ($lArr as $lKey => $lDum) {
        $lArcJid.= '"'.$lKey.'",';
      }
      $lArcJid = strip($lArcJid);
      $lSql = 'SELECT jobid,webstatus FROM al_job_arc_'.intval(MID).' WHERE jobid IN ('.$lArcJid.')';
      $lQry = new CCor_Qry($lSql);
      foreach ($lQry as $lRow) {
        $this -> mSub[$lRow['jobid']] = $lRow;
      }
      $lJid.= $lArcJid;
    }

    $lIds = '';
    if (!empty($lArrNoAlink)) {
      $lArrNoAl = array_map("esc", $lArrNoAlink);//jedes Element wird ".mysql_escaped."
      $lIds = implode(',', $lArrNoAl);
      $lSql = 'SELECT jobid,webstatus FROM al_job_pdb_'.intval(MID).' WHERE jobid IN ('.$lIds.')';
      $lQry = new CCor_Qry($lSql);

      foreach ($lQry as $lRow) {
        $this -> mSub[$lRow['jobid']] = $lRow;
      }
    }
    $lJid.= $lIds;

    $lSql = 'SELECT jobid,flags FROM al_job_shadow_'.intval(MID).' WHERE jobid IN ('.$lJid.')';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $this -> mJfl[$lRow['jobid']] = $lRow['flags'];
    }

    //22651 Project Critical Path Functionality
    $lSql = 'SELECT sub_id,pro_status FROM al_job_pro_crp WHERE mand='.MID.' AND jobid IN ('.$lAllJobids.')';
    $lQry = new CCor_Qry($lSql);
    $lSubCrp = array();
    foreach ($lQry as $lRow) {
      $lSubCrp[$lRow['sub_id']][$lRow['pro_status']] = true;
    }
    $this -> mSubCrp = $lSubCrp;
  }

  protected function addColumns() {
    $lUsr = CCor_Usr::getInstance();
    $lCol = $lUsr -> getPref('job-pro-sub.cols');
    if (empty($lCol)) return;

    $lArr = explode(',',$lCol);
    foreach ($lArr as $lFid) {
      if (isset($this -> mFie[$lFid])) {
        $lDef = $this -> mFie[$lFid];
        $this -> addField($lDef);
      }
    }
  }

  protected function getGroupHeader() {
    $lRet = '';
    if (!empty($this -> mGrp)) {
      $lNew = $this -> getVal($this -> mGrp);
      if ($lNew !== $this -> mOldGrp) {
        $lRet = TR;
        $lRet.= '<td class="tg1 ac">';
        $lId = getNum('c');
        $lRet.= '<input type="checkbox" class="grp" id="'.$lId.'" onclick="subAll(\''.$lId.'\')" />';
        $lRet.= '</td>';
        $lRet.= '<td class="tg1" colspan="'.($this -> mColCnt -1).'">';
        $lVal = $lNew;
        if ($this -> mGrpDef) {
          $lVal = $this -> mPlain -> getPlain($this -> mGrpDef, $lVal);
        }
        $lRet.= htm($lVal).NB;
        $lRet.= '</td>';
        $lRet.= _TR;
        $this -> mOldGrp = $lNew;
        $this -> mCls = 'td1';
      }
    }
    return $lRet;
  }

  protected function beforeRow() {
    $lMas = $this -> getVal('is_master');
    $lMasterId = $this -> getVal('master_id');

    if ('X' == $lMas) {
      $this -> mCls.= ' cy';
    }
    if (!empty($lMasterId)) {
      $this -> mCls.= ' cv';
    }
    return $this -> getGroupHeader();
  }

  protected function getTdSel() {
    $lSid = $this -> getInt('id');
    $lArt = $this -> getVal('jobid_art');
    if (empty($lArt)) {
      $lRet = '<input type="checkbox" class="art" id="c'.$lSid.'" ';
      $lRet.= ' />';
    } else {
      $lRet = NB;
    }
    return $this -> tdClass($lRet, 'ac w16');
  }

  protected function getTdMasterBundle() {
    $lSid = $this -> getInt('id');
    $lMasterId = $this -> getVal('master_id');
    $lIsMaster = $this -> getVal('is_master');
    if (empty($lMasterId) AND  $lIsMaster != 'X' ) {
      $lRet = '<input type="checkbox" class="variant" id="v'.$lSid.'" ';
      $lRet.= ' />';
    } else {
      $lRet = NB;
    }
    return $this -> tdClass($lRet, 'ac w16');
  }

  /*
   * Coloumn is_master
   */
  protected function getTdIs_master() {
    $lRet = '';
    $lMas = $this -> getVal('is_master');
    $lMaster_Id = $this -> getVal('master_id');
    $lSid = $this -> getInt('id');
    if ('X' == $lMas) {
      $lDelLink = 'index.php?act=job-pro-sub.master_unset&amp;jobid='.$this -> mJobId.'&amp;sid='.$lSid;
      $lRet.= '<a class="nav" href="javascript:Flow.Std.cnfMAssOut(\''.$lDelLink.'\', \''.LAN.'\')">';
      $lRet.= img('img/ico/16/master.gif');
      $lRet.= '</a>';
    } else {
      //Check after Variant
      if (!empty($lMaster_Id)){
        // It Is Variant
        $lDelLink = 'index.php?act=job-pro-sub.variant_unset&amp;jobid='.$this -> mJobId.'&amp;sid='.$lSid;
        $lRet.= '<a class="nav" href="javascript:Flow.Std.cnfVAssOut(\''.$lDelLink.'\', \''.LAN.'\')">';
        $lRet.= img('img/ico/16/variant.gif');
        $lRet.= '</a>';
      }else {
        // It is no Master and no Variant
         $lRet = '<a href="index.php?act=job-pro-sub.master_set&amp;jobid='.$this -> mJobId.'&amp;sid='.$lSid.'" class="nav">';
         $lRet.= img('img/ico/16/master-lo.gif');
         $lRet.= '</a>';
      }
    }
    return $this -> tdc($lRet);
  }

  protected function getTdMenu($aSrc, $aJobID, $aFrom, $aCaption) {
    $lCaptWithHtm = FALSE;

    $lMen = new CHtm_Menu($aCaption, '', $lCaptWithHtm);
    $lMen -> addItem('index.php?act=job-'.$aSrc.'.edt&amp;jobid='.$aJobID, lan('lib.open'));

    if ($this -> mIsNoArc) {
      $lMen -> addItem('index.php?act=job-'.$aSrc.'.assignprj&amp;jobid='.$aJobID.'&amp;prjid='.$this -> mJobId, lan('job.assignprj'));
    }

    $lUsr = CCor_Usr::getInstance();
    if ((isset($this -> mArrLeerAmount[$aSrc]) AND $this -> mArrLeerAmount[$aSrc] > 0 AND $this -> mCanSlide) OR ($lUsr -> canEdit('job-pro-sub.replace'))) {
      $lMen -> addTh2(lan('lib.slide'));
      foreach ($this -> mArrLeer[$aSrc] as $lKey => $lVal) {
        $lMen -> addItem('index.php?act=job-pro-sub.slide&amp;jobid='.$aJobID.'&amp;src='.$aSrc.'&amp;from='.$aFrom.'&amp;to='.$lVal, 'Zeile '.$lKey);
      }
    }

    return $lMen -> getContent();
  }

  protected function getJobTd($aSrc) {
    $lRet = '';
    $lId = $this -> getVal('jobid_'.$aSrc);
    $lSid = $this -> getInt('id');
    $lIsMaster = $this -> getVal('is_master');
    $lMasterId = $this -> getVal('master_id');

    if (!empty($lId)) {
      $lRow = (isset($this -> mSub[$lId])) ?  $this -> mSub[$lId] : new CCor_Dat();
      $lWebStatus = $lRow['webstatus'];
      $lImg = $this ->getStatusImage($aSrc, $lWebStatus);

      if (isset($this -> mJfl[$lId])) {
        $lJfl = $this -> mJfl[$lId];
        if (bitset($lJfl, jfOnhold)) {
          $lImg = 'img/jfl/'.jfOnhold.'.gif';
        }
        if (bitset($lJfl, jfCancelled)) {
          $lImg = 'img/jfl/'.jfCancelled.'.gif';
        }
      }
      $aCaption = img($lImg).NB.jid($lId, TRUE);
      $lFrom = $lSid;
      $lRet = $this -> getTdMenu($aSrc, $lId, $lFrom, $aCaption);

    } else {
      if ($this -> mIsNoArc && $this->mUsr->canInsert('job-'.$aSrc)) {
        $lLink = 'index.php?act=job-'.$aSrc.'.sub&amp;pid='.$this -> mJobId.'&amp;sid='.$lSid.'&amp;src='.$aSrc;
        if ($lIsMaster == 'X'){
          $lLink.= '&amp;ismaster=X';
        } elseif($lMasterId != ''){
          $lLink.= '&amp;masterid='.$lMasterId;
        }
        $lRet = '<a href="'.$lLink.'" class="nav">';
        $lRet.= htm(lan('lib.create'));
        $lRet.= '</a>';
      } else {
        $lRet.= NB;
      }
    }
    return $this -> td($lRet);
  }

  protected function getTdJob_Art() {
    return $this->getJobTd('art');
  }

  protected function getTdJob_Rep() {
    return $this->getJobTd('rep');
  }

  protected function getTdJob_Sec() {
    return $this->getJobTd('sec');
  }

  protected function getTdJob_Adm() {
    return $this->getJobTd('adm');
  }

  protected function getTdJob_Com() {
    return $this->getJobTd('com');
  }

  protected function getTdJob_Tra() {
    return $this->getJobTd('tra');
  }

  protected function getTdJob_Mis() {
    return $this->getJobTd('mis');
  }

  protected function getTdCpy() {
    $lSid = $this -> getInt('id');
    $lRet = '<a href="index.php?act=job-pro-sub.cpy&amp;jobid='.$this -> mJobId.'&amp;id='.$lSid.'" class="nav">';
    $lRet.= img('img/ico/16/copy.gif');
    $lRet.= '</a>';
    return $this -> td($lRet);
  }

  /* Load Critical Path
  *
  */
  protected function loadCrps(){
    if(!empty($this -> mCrpMaster)){
      foreach ($this -> mCrpMaster as $lKey => $lVal){
        $this -> mCrpSrc[$lKey] = CCor_Res::getByKey('status', 'crp', $lVal);
      }
    } else return '';
  }

  /* Get Status Image
   * @param string $aSrc Jobart
   * @param string $aWebStatus Webstatus
   * return string $lRet Status Icon
   * */
  protected function getStatusImage ($aSrc, $aWebStatus){
    $lSta = 0;
    $lSrc = $aSrc;
    $lWebStatus = $aWebStatus;
    $lRet = '';
    #echo '<pre>---list.php---'.get_class().'---';var_dump($aSrc, $aWebStatus,'#############');echo '</pre>';
    $lCrp = $this -> mCrpSrc[$lSrc];
    if (!empty($lCrp)) {
      foreach ($lCrp as $lKey => $lVal){
        if ($lWebStatus == $lVal['status']){
          $lSta = $lVal['display'];
          break;
        }
      }
    } else {
      $lSta = $lWebStatus / 10;
    }
	$lRet = CApp_Crpimage::getSrcPath($aSrc, 'img/crp/'.$lSta.'b.gif');
    return $lRet;
  }

  //22651 Project Critical Path Functionality
  protected function getTdWebstatus() {
    $lSid = $this -> getInt('id');
    $lCrp = $this -> mCrpSrc['pro'];
    $lRet = '';
    if (isset($lCrp) AND  !empty($lCrp)){
      foreach ($lCrp as $lRow) {
        if (isset($this -> mSubCrp[$lSid]) AND isset($this -> mSubCrp[$lSid][$lRow['display']])){
		  $lPath = CApp_Crpimage::getSrcPath('pro', 'img/crp/'.$lRow['display'].'b.gif');
          $lRet.= img($lPath, array('style' => 'margin-right:1px'));
        } else {
		  $lPath = CApp_Crpimage::getSrcPath('pro', 'img/crp/'.$lRow['display'].'l.gif');
          $lRet.= img($lPath, array('style' => 'margin-right:1px'));
        }
      }
    }
    return $this -> tda($lRet);
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
   * Master Variant Bundle JobId #23041
   * Sort JobArray after Master-Variant
   *
   */
  public function sortByMaster($aRet){
    $lRet = array();
    $lIte = $aRet;
    foreach ($lIte as $lKey => $lVal){
      if ($lVal['is_master'] == 'X'){
        $lRet[$lKey] = $lIte[$lKey];
        foreach ($lIte as $lKey2 => $lVal2){
          if ($lVal2['master_id'] == $lVal['id'] ){
            if (isset($lRet[$lKey2])){
              unset($lRet[$lKey2]);
            }
            $lRet[$lKey2] = $lIte[$lKey2];
            unset ($lIte[$lKey2]);
          }
        }
      }else {
        if (!empty($lIte[$lKey])){
          $lRet[$lKey] = $lIte[$lKey];
        }
      }
    }
    return $lRet;
  }

}