<?php
/**
 * Jobs: Header
 *
 *  Description
 *
 * @package    JOB
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 12014 $
 * @date $Date: 2016-01-11 22:01:54 +0800 (Mon, 11 Jan 2016) $
 * @author $Author: ahajali $
 */
class CInc_Job_Header extends CCor_Tpl {

  protected $mSrc;
  protected $mJob;
  protected $mCrp;
  protected $mMenu;
  protected $mUsr;
  /*
   * @var string|int Assigned Project Id
  */
  protected $mProId = '';
  /*
   * @var string|int Assigned Project Item Id
  */
  protected $mProItemId = '';
  /*
   * @var string is_Master
  */
  protected $mIsMaster = '';
  /*
   * @var string|int MasterId
  */
  protected $mMasterId = '';
  /*
   * Master-Variant Bundle aktiv?
  */
  public $mMasterVariantBundleActiv = FALSE;

  /*
   * Master-Variant Bundle
  * Check if column 'is_master' defined in al_job_sub_X
  */
  protected $mColumnIsMasterDefined = FALSE;
  /*
   * Related Jobs from Project Item
  * @var array
  */
  public $mRelatedJobs = Array();

  public function __construct($aSrc, $aJob, $aCrp) {
    $this -> mSrc = $aSrc;
    $this -> mJob = $aJob;
    $this -> mUsr = CCor_Usr::getInstance();

    $this -> mJid = 0;
    if (($this -> mJob instanceof CJob_Dat) OR ($this -> mJob instanceof CArc_Dat)) { // AVAILABLE FOR 5.0 AND BEYOND
      // if (is_a($this -> mJob, "CJob_Dat") OR is_a($this -> mJob, "CArc_Dat")) { // DEPRECATED FOR 5.3 AND BENEATH
      $this -> mJid = $this -> mJob -> getId();
    }
    if ('sku' != $this -> mSrc AND empty($this -> mJid)) {
      $this -> msg(MID. ' Header: The '.$this -> mSrc.'-job is a sku OR the JobId is empty.', mtUser, mlWarn);
      CCor_Cnt::redirect('index.php?act=job-'.$this -> mSrc);
    }

    $this -> mCrp = $aCrp;
    $this -> mTitle = lan('job-'.$this -> mSrc.'.menu');
    $this -> mImg = 'img/ico/40/'.LAN.'/job-'.$this -> mSrc.'.gif';

    $this -> openProjectFile('job/'.$this -> mSrc.'/header.htm');
    $lFieFlags = Array();
    $lFieFlags = CCor_Res::extract('alias', 'flags', 'fie');
    $lPat = $this -> findPatterns('val.');
    $this -> setLang();
    $this -> setImg();
    $this -> setImage();
    if (!empty($lPat)) {
      foreach ($lPat as $lAli) {
        // If Jobfield has Read Flag active, ask for User READ-RIGHT (combination of 'fie_' and Alias).
        // If User has no READ-RIGHT, dont show the Jobfield.
        $lFieRight = 'fie_'.$lAli;
        if (isset($lFieFlags[$lAli]) && bitset($lFieFlags[$lAli],ffRead)){
          if (!$this-> mUsr -> canRead($lFieRight)){
            $this -> setPat('val.'.$lAli, '');
            continue;
          }
        }
        $this -> setPat('val.'.$lAli, htm($aJob[$lAli]));
      }
    }
    $lMapper = CCor_Res::extract('alias', 'name_'.LAN, 'fie');
    $lPat = $this -> findPatterns('bez.');
    if (!empty($lPat)) {
      foreach ($lPat as $lAli) {
        if (isset($lMapper[$lAli])) {
          // If Jobfield has Read Flag active, ask for User READ-RIGHT (combination of 'fie_' and Alias).
          // If User has no READ-RIGHT, dont show the Jobfield.
          $lFieRight = 'fie_'.$lAli;
          if (isset($lFieFlags[$lAli]) && bitset($lFieFlags[$lAli],ffRead)){
            $lFieRight = 'fie_'.$lAli;
            if (!$this-> mUsr -> canRead($lFieRight)){
              $this -> setPat('bez.'.$lAli, '');
              continue;
            }
          }
          $this -> setPat('bez.'.$lAli, htm($lMapper[$lAli]));
        }
      }
    }
    if (!empty($this -> mJid)) {
      $this -> setPat('val.jobid', htm(jid($this -> mJid)));
    }
    $this -> mMenu = TRUE;
    $this -> mFlags = 0;
    $this -> mPnl = array();
    $this -> mMasterVariantBundleActiv = CCor_Cfg::get('master.varaiant.bundle', FALSE);
    if($this -> mMasterVariantBundleActiv){
      $this -> mColumnIsMasterDefined = $this-> isFieldIsMasterDefined();
    }


  }

  public function getSrc() {
    return $this -> mSrc;
  }

  public function hideMenu($aFlag = true) {
    $this -> mMenu = !(bool)$aFlag;
  }

  /**
   * Get the Job Status Bar Object
   * Descendants can overwrite this function to set Deadlines in the bar
   *
   * @return CJob_Bar
   */
  protected function getBar() {
    if ($this -> mSrc !== 'pro'){
      $lRet = new CJob_Bar($this -> mSrc, $this -> mJob, $this -> mCrp);
    }else{
      $lRet = new CJob_Pro_Bar($this -> mSrc, $this -> mJob, $this -> mCrp);
    }
    return $lRet;
  }

  /**
   * Get the Job Image Object
   * @todo Currently there are only two sizes: 100x100 and 300x300 which are also hard-coded. This can be beautified and generalised
   *
   * @return CJob_Bar
   */
  protected function getImage($aMagnify = FALSE) {
    $lRet = new CJob_Image($this -> mJob, $aMagnify);
    return $lRet;
  }

  public function addPanel($aContent) {
    $this -> mPnl[] = $aContent;
  }

  protected function onBeforeContent() {
    if(THEME !== 'default'){
      $this -> setPat('theme.class', CApp_Crpimage::getColourForSrc($this -> mSrc));
    }
    if ($this -> mMenu) {
      $this -> setPat('menu.related', $this -> getRelatedMenu());
      $this -> setPat('menu.bookmark', $this -> getBookmarkMenu());
      $this -> setPat('menu.notfications', $this -> getNotificationsMenu());
      // If Master-Variant Bundle is active
      if ($this -> mColumnIsMasterDefined){
        // If not Assigned to any Project, dont show
        if ($this ->mProId != '' AND ($this->mIsMaster != '' OR $this->mMasterId !='')){
          $this -> setPat('menu.masterbundle', $this -> getMasterBundleMenu());
        }else {
          $this -> setPat('menu.masterbundle', '');
        }
      }

    } else {
      $this -> setPat('menu.related', '');
      $this -> setPat('menu.bookmark', '');
      if (CCor_Cfg::get('master.varaiant.bundle', FALSE)){
        $this -> setPat('menu.masterbundle', '');
      }
    }
    if (empty($this -> mPnl)) {
      $this -> setPat('menu.panel', '');
    } else {
      $lRet = '<table cellpadding="2" cellspacing="0" border="0"><tr>'.LF;
      foreach ($this -> mPnl as $lContent) {
        if('&nbsp;' == $lContent){
          $lRet.= '<td valign="top" width="98%">';
        }else{
          $lRet.= '<td class="nw" valign="top">';
        }
        $lRet.= $lContent;
        $lRet.= '</td>';
      }
      $lRet.= '</tr></table>'.LF;
      $this -> setPat('menu.panel', $lRet);
    }
    $lBar = $this -> getBar();
    $this -> setPat('hdr.statusbar', $lBar -> getContent());

    $lImage = $this -> getImage();
    $this -> setPat('wec_pi', $lImage -> getContent());

    $lImage = $this -> getImage(TRUE);
    $this -> setPat('wec_pi_large', $lImage -> getContent());
  }

  /**
   * Get Related Jobs menu
   *
   * @return string
   */
  protected function getRelatedMenu() {
    $lJid = $this -> mJid;
    $lSrc = $this -> mSrc;

    $lArr = array();
    $lArrProject = Array(); // Projekt Infos
    $lUsr = CCor_Usr::getInstance();
    if(THEME === 'default'){
      $lMen = new CHtm_Menu(lan('job-rel.menu'));
    } else {
      $lImg = img('img/ico/16/rel.gif');
      $lMen = new CHtm_Menu($lImg, 'rel', false);
    }

    //add projekt to related menu
    $lArrProject = $this -> getRelatedProject();
    if (!empty($lArrProject)){
      // Add Projekt Infos to Menu
      $lProId =  $lArrProject['id'];
      // Set Projekt
      $this -> setProjektId($lProId);
      if ($lShowProject = CCor_Cfg::get('job-rel.projects', TRUE)){
        $lSubLink = CCor_Cfg::get('job-rel.projects.link','sub');
        $lProLink  = 'index.php?act=job-pro-'.$lSubLink.'&amp;jobid='.$lProId;
        $lContentToShow = lan('job-pro.menu').' : '.implode(', ',$lArrProject);
        $lMen -> addItem($lProLink, $lContentToShow, 'ico/16/'.LAN.'/job-pro.gif');

      }
    }

    //add skus to related menu
    if ($lShowSkus = CCor_Cfg::get('job-rel.skus', FALSE)){
      $this -> addSku($lMen);
    }
    //add jobs to related menu
    if ($lShowJobs = CCor_Cfg::get('job-rel.jobs', TRUE)){
      $this -> addJobs($lMen);
    }
    return $lMen -> getContent();
  }

  protected function getBookmarkMenu() {
    $lMen = new CJob_Bookmarks($this -> mSrc, $_REQUEST['jobid'], $this -> mJob['stichw']);
    return $lMen -> getContent();
  }
  
  protected function getNotificationsMenu() {
    if (!$this -> mUsr -> canRead('notification-center')) return '';
    $lJobid = $this -> mJid;
    $lRet = CApp_Notfications::getJobNotfications($lJobid);
    return $lRet;
  }

  /*
   * Get Master-Variant Bundle Menu
  */
  protected function getMasterBundleMenu() {
    $lJid = $this -> mJid;
    $lSrc = $this -> mSrc;
    $lIsMaster = $this -> mIsMaster;
    $lMasterId = $this -> mMasterId;
    $lProItemId = $this -> mProItemId;
    $lRelatedJobs = $this -> mRelatedJobs;

    $lArr = array();
    $lMen = new CHtm_Menu(lan('menu.masterbundle'));

    if ($lIsMaster == 'X'){
      // Frist load Master Jobs
      foreach ($lRelatedJobs as $lKey => $lVal){
        foreach ($lVal as $Id => $lVal2){
          $lJid = $Id;
        }
        $lMen -> addItem('index.php?act=job-'.$lKey.'.edt&amp;jobid='.$lJid, jid($lJid, TRUE).', '.lan('job-'.$lKey.'.item').': '.$lJid, 'ico/16/master.gif');
      }

      // Load Variant Jobs
      $lSql = 'Select jobid_rep,jobid_art,jobid_sec,jobid_mis,jobid_adm,jobid_com,jobid_tra FROM al_job_sub_'.MID ;
      $lSql.= ' WHERE pro_id='.$this->mProId.' AND master_id='.$lProItemId;
      $lQry = new CCor_Qry($lSql);
      foreach ($lQry as $lRow){
        foreach($lRow as $lKey => $lVal){
          if ($lVal == '') continue;
          $lTempSrc = substr($lKey,-3);
          $lMen -> addItem('index.php?act=job-'.$lTempSrc.'.edt&amp;jobid='.$lVal, jid($lVal, TRUE).', '.lan('job-'.$lTempSrc.'.item').': '.$lVal, 'ico/16/variant.gif');
        }
      }

    }elseif ($this -> mMasterId != ''){
      // Variant Job, find Master and Variants
      // Find Master Jobs
      // Load Variant Jobs
      $lSql = 'Select jobid_rep,jobid_art,jobid_sec,jobid_mis,jobid_adm,jobid_com,jobid_tra FROM al_job_sub_'.MID ;
      $lSql.= ' WHERE pro_id='.$this->mProId.' AND id='.$this -> mMasterId;
      $lQry = new CCor_Qry($lSql);
      foreach ($lQry as $lRow){
        foreach($lRow as $lKey => $lVal){
          if ($lVal == '') continue;
          $lTempSrc = substr($lKey,-3);
          $lMen -> addItem('index.php?act=job-'.$lTempSrc.'.edt&amp;jobid='.$lVal, jid($lVal, TRUE).', '.lan('job-'.$lTempSrc.'.item').': '.$lVal, 'ico/16/master.gif');
        }
      }
      // Find Variant Jobs
      $lSql = 'Select jobid_rep,jobid_art,jobid_sec,jobid_mis,jobid_adm,jobid_com,jobid_tra FROM al_job_sub_'.MID ;
      $lSql.= ' WHERE pro_id='.$this->mProId.' AND master_id='.$this -> mMasterId;
      $lQry = new CCor_Qry($lSql);
      foreach ($lQry as $lRow){
        foreach($lRow as $lKey => $lVal){
          if ($lVal == '') continue;
          if ($lVal == $lJid) continue;
          $lTempSrc = substr($lKey,-3);
          $lMen -> addItem('index.php?act=job-'.$lTempSrc.'.edt&amp;jobid='.$lVal, jid($lVal, TRUE).', '.lan('job-'.$lTempSrc.'.item').': '.$lVal, 'ico/16/variant.gif');
        }
      }

    }
    return $lMen -> getContent();
  }

  /*
   * Check Master VAariant Fields defined
  * @param boolean $lRet
  */
  protected function isFieldIsMasterDefined(){
    $lRet = FALSE;
    $lTabelColumns = new CCor_Qry('SHOW COLUMNS FROM al_job_sub_'.MID);
    $lColumns = $lTabelColumns->getAssocs('Field');
    if(array_key_exists('is_master',$lColumns) AND array_key_exists('master_id',$lColumns)){
      $lRet = TRUE;
    }else {
      $this ->dbg('For Master-Variant Bundle missing jobfields is_master and master_id in Tabelle al_job_sub_'.MID,mlWarn);
    }
    return $lRet;
  }


  public function getRelatedJobs(){
    $lRet = Array();
    $lSqlArr = array();
    $lSrc = $this -> mSrc;
    $lJid = $this -> mJid;

    $lSqlArr['art'] = 'SELECT id,jobid_rep,jobid_sec,jobid_mis,jobid_adm,jobid_com,jobid_tra';
    $lSqlArr['rep'] = 'SELECT id,jobid_art';
    $lSqlArr['sec'] = $lSqlArr['rep'];
    $lSqlArr['mis'] = $lSqlArr['rep'];
    $lSqlArr['adm'] = $lSqlArr['rep'];
    $lSqlArr['com'] = $lSqlArr['rep'];
    $lSqlArr['tra'] = $lSqlArr['rep'];

    if ($this -> mColumnIsMasterDefined){
      $lSqlArr[$lSrc].= ',is_master,master_id';
    }

    if (isset($lSqlArr[$lSrc])){
      $lSrcSql = $lSqlArr[$lSrc];
      $lSql = $lSrcSql.' FROM al_job_sub_'.intval(MID).' WHERE jobid_'.$lSrc.'="'.addslashes($lJid).'"';
      $lQry = new CCor_Qry($lSql);
      foreach ($lQry as $lRow) {
        foreach ($lRow as $lKey => $lVal) {
          if (!empty($lVal)) {
            // Get is_Master
            if ($lKey == 'id'){
              $this -> mProItemId = $lVal;
              continue;
            }
            // Get is_Master
            if ($lKey == 'is_master'){
              $this -> mIsMaster = $lVal;
              continue;
            }
            // Get Master_Id
            if ($lKey == 'master_id'){
              $this -> mMasterId = $lVal;
              continue;
            }
            $lSrc = substr($lKey,-3);

            // Load JobInfos from Shadow
            $lRow = Array();
            $lArrFieldsToShow = CCor_Cfg::get('job-rel.jobs.fields',Array());
            // Load fields from Shadow.
            if (!empty($lArrFieldsToShow)){
              $lStrFieldsToShow = implode(',',$lArrFieldsToShow);
              $lSql = 'Select '.$lStrFieldsToShow. ' FROM al_job_shadow_'.MID;
              $lSql.= ' Where src="'.$lSrc.'" AND jobid ="'.$lVal.'"';
              $lQry = new CCor_Qry($lSql);
              $lRow = $lQry -> getAssoc();
              // If no Result, check if the fields(job-rel.jobs.fields) are defined in the table 'shadow'
              if (!$lRow){
                // No Result. Check feilds in the shadow table.
                $this ->dbg('Check Fields(job-rel.jobs.fields) in the table shadow, if they are defined.',mlWarn);
              }
            }
            $lRet[$lSrc][$lVal] = $lRow;
          }
        }
      }
      if (!empty($lRet)){
        $this -> setRelatedJobs($lRet);
      }
    }
    return $lRet;
  }

  /*
   * Set Related Jobs
  * @param array Array of Related Jobs
  */
  public function setRelatedJobs($aRet){
    $this -> mRelatedJobs = $aRet;
  }

  /*
   * Get Related Project Infos
  * @retun $lRet Array Project Infos
  */
  public function getRelatedProject(){
    $lRet = Array();
    $lJid = $this -> mJid;
    $lSrc = $this -> mSrc;
    $lArrShowFields = CCor_Cfg::get('job-rel.projects.fields',Array());
    // Id is mandatory, for link
    if (!in_array('id',$lArrShowFields)){
      array_unshift($lArrShowFields,'id');
    }
    $lStrSqlShowFields = ''; // Fields to show

    // preparation for SQL
    foreach ($lArrShowFields as $lKey){
      $lStrSqlShowFields.= 'p.'.$lKey.',';
    }
    $lStrSqlShowFields = substr($lStrSqlShowFields,0,-1);
    $lSql = 'SELECT '.$lStrSqlShowFields.' FROM al_job_pro_'.MID.' as p';
    $lSql.= ' LEFT JOIN al_job_sub_'.MID.' as s on p.id= s.pro_id WHERE jobid_'.$lSrc.'="'.addslashes($lJid).'"';
    $lQry = new CCor_Qry($lSql);

    if ($lRow = $lQry -> getAssoc()){
      $lRet = $lRow;
    }
    return $lRet;
  }

  /*
   * Get Related Sku Infos
  * @retun $lRet Array Sku Infos
  */
  public function getRelatedSkus(){
    $lRet = Array();
    $lJid = $this -> mJid;
    $lSrc = $this -> mSrc;
    $lArrShowFields = CCor_Cfg::get('job-rel.skus.fields',Array('id','stichw'));
    // Id is mandatory, for link
    if (!in_array('id',$lArrShowFields)){
      array_unshift($lArrShowFields,'id');
    }
    $lStrSqlShowFields = ''; // Fields to show

    // preparation for SQL
    foreach ($lArrShowFields as $lKey){
      $lStrSqlShowFields.= 'p.'.$lKey.',';
    }
    $lStrSqlShowFields = substr($lStrSqlShowFields,0,-1);
    $lSql = 'SELECT '.$lStrSqlShowFields.' FROM al_job_sku_'.MID.' as p';
    $lSql.= ' LEFT JOIN al_job_sku_sub_'.MID.' as s on p.id= s.sku_id WHERE s.job_id="'.addslashes($lJid).'"';
    $lQry = new CCor_Qry($lSql);

    if ($lRow = $lQry -> getAssoc()){
      $lRet = $lRow;
    }
    return $lRet;
  }

  /*
   * Set ProjektId
  *
  */
  public function setProjektId($aProId){
    $this -> mProId = $aProId;
  }

  /*
   * Add Skus to elated Menu
  */
  public function addSku(& $aMen){
    $lMen = $aMen;
    $lArrSkus = $this -> getRelatedSkus();
    if (!empty($lArrSkus)){
      // Add Projekt Infos to Menu
      $lSubLink = CCor_Cfg::get('job-rel.skus.link','sub');
      $lSkuId =  $lArrSkus['id'];
      $lSkuLink  = 'index.php?act=job-sku-'.$lSubLink.'&amp;jobid='.$lSkuId;
      $lContentToShow = lan('job-sku.menu').' : '.implode(', ',$lArrSkus);
      $lMen -> addItem($lSkuLink, $lContentToShow, 'ico/16/'.LAN.'/job-sku.gif');
    }
  }

  /*
   * Add Jobs to Related Menu
  */
  public function addJobs(& $aMen){
    $lMen = $aMen;
    $lArrRelatedJobs = $this -> getRelatedJobs();
    $lSubLink = CCor_Cfg::get('job-rel.jobs.link','job');
    foreach ($lArrRelatedJobs as $lKey => $lVal){
      foreach ($lVal as $lId => $lVal2) {
        $lJobLink  = 'index.php?act=job-'.$lKey.'.edt&amp;jobid='.$lId.'&amp;page='.$lSubLink;
        $lContentToShow = '';
        if (!empty($lVal2)) $lContentToShow = implode(',',$lVal2);
        // If No Content , show only JobType and JobId.
        if ($lContentToShow == ''){
          // Show conventional form: [JobId] + Jobtype + JobId
          $this ->dbg('Conventional view ([JobId] + JobType + JobId) because no content to show.',mlInfo);
          $lContentToShow = lan('job-'.$lKey.'.menu').' '.$lId;
        }
        $lMen -> addItem($lJobLink, jid($lId, TRUE).' : '.$lContentToShow, 'ico/16/'.LAN.'/job-'.$lKey.'.gif');
      }
    }
  }
}