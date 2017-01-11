<?php
/**
 * Core: Main User Object
 *
 * A singleton object (there can be only one ;-) providing
 * access to user access rights and preferences of the
 * current logged in user
 *
 * @package    cor
 * @subpackage usr
 * @copyright  5Flow GmbH (http://www.5flow.eu)
 * @version    $Rev: 14379 $
 * @date       $Date: 2016-06-06 16:28:05 +0200 (Mon, 06 Jun 2016) $
 * @author     $Author: psarker $
 */
class CCor_Usr extends CCor_Obj {

  /**
   * User ID from user database table
   *
   * @var int
   */
  private $mId;


  /**
   * Preference object for getPref/setPref
   *
   * @var CCor_Usr_Pref
   */
  private $mPref;

  /**
   * Priviledge object for canRead, canEdit etc.
   *
   * @var CCor_Usr_Priv
   */
  private $mPriv;

  /**
   * Membership object for isMemberOf and getMemArray
   *
   * @var CCor_Usr_Mem
   */
  private $mMem;
  private $mCnd;
  private $mVal;
  private $mCrp;
  private $mFlags;

  /**
   * Singleton Instance Variable
   *
   * @var CCor_Usr Singleton Instance
   */

  private static $mInstance = NULL;

  private function __construct() {
    $this -> mId = self::getAuthId();
    $this -> loadVals();
  }

  private function __clone() {}

  /**
   * Singleton getInstance method
   *
   * Only way to get a reference to the user object
   * @return CCor_Usr
   */
  public static function getInstance(){
    if (NULL === self::$mInstance) {
      self::$mInstance = new self();
    }
    return self::$mInstance;
  }

  /**
   * Get the ID of the current user. If user is not logged in, return false.
   * @return boolean|integer
   */
  public static function getAuthId() {
    $lSys = CCor_Sys::getInstance();
    $lUid = intval($lSys['usr.id']);
    if (0 === $lUid) {
      return FALSE;
    }
    return $lUid;
  }

  public static function insertJobFile($lSrc, $lJobID, $lSub, $lFileName, $lCategory = '', $lText = '', $lPathName = '') {
    $lSql = 'INSERT INTO al_job_files SET ';
    $lSql.= 'uid='.CCor_Usr::getAuthId().',';
    $lSql.= 'src='.esc($lSrc).',';
    $lSql.= 'jobid='.esc($lJobID).',';
    $lSql.= 'sub='.esc($lSub).',';
    $lSql.= 'filename='.esc($lFileName).',';
    if (isset($lCategory))
      $lSql.= 'category='.esc($lCategory).',';
    if (isset($lText))
      $lSql.= 'txt='.esc($lText).',';
    if (isset($lPathName))
      $lSql.= 'pathname='.esc($lPathName).',';
    $lSql.= 'mand='.MID;
    CCor_Qry::exec($lSql);
    return TRUE;
  }

  public static function deleteJobFile($lSrc, $lJid, $lSub, $lFil) {
    $lSql = 'delete from al_job_files WHERE jobid='.esc($lJid).' AND src='.esc($lSrc).' AND sub='.esc($lSub).' AND uid='.CCor_Usr::getAuthId().' AND filename='.esc($lFil);
    CCor_Qry::exec($lSql);
    return True;
  }

  public static function uploadedJobFile($lSrc, $lJid, $lSub, $lFil) {
    $lSql = 'update al_job_files set ToWec="Y" WHERE jobid='.esc($lJid).' AND src='.esc($lSrc).' AND sub='.esc($lSub).' AND uid='.CCor_Usr::getAuthId().' AND filename='.esc($lFil);
    CCor_Qry::exec($lSql);
    return True;
  }
 
  public static function uploadedJobFileToGv($lSrc, $lJid, $lSub, $lFil) {
    $lSql = 'update al_job_files set ToGv="Y" WHERE jobid='.esc($lJid).' AND src='.esc($lSrc).' AND sub='.esc($lSub).' AND uid='.CCor_Usr::getAuthId().' AND filename='.esc($lFil);
    CCor_Qry::exec($lSql);
    return True;
  }
  
  public static function updateTxt($lSrc, $lJid, $lSub, $lFil, $lTxt = '') {
      $lSql = 'UPDATE al_job_files';
      $lSql.= ' SET txt='.esc($lTxt);
      $lSql.= ' WHERE';
      $lSql.= ' mand='.MID.' AND';
      $lSql.= ' uid='.CCor_Usr::getAuthId().' AND';
      $lSql.= ' src='.esc($lSrc).' AND';
      $lSql.= ' jobid='.esc($lJid).' AND';
      $lSql.= ' sub='.esc($lSub).' AND';
      $lSql.= ' filename='.esc($lFil).';';

      CCor_Qry::exec($lSql);
      return TRUE;
  }

  /**
   * Return the ID of the mandator the user is logged in to.
   * Is also available via global constant MID.
   * @return integer|NULL
   */
  public function getMandId() {
    return $this -> getPref('sys.mid');
  }

  protected function loadVals() {
    if (empty($this -> mId)) {
      return;
    }
    $lQry = new CCor_Qry('SELECT * FROM al_usr WHERE id='.$this -> mId);
    $this -> mVal = $lQry -> getAssoc();
    $this -> mVal['fullname'] = cat($this -> mVal['firstname'], $this -> mVal['lastname']);
    $this -> mVal['first_lastname'] = cat($this -> mVal['firstname'], $this -> mVal['lastname']);
  }

  /**
   * Get a value from the user's table row, e.g. the firstname or the email address.
   * @param string $aKey Field of the user db row or special 'fullname'/'first_lastname'
   * @return string
   */
  public function getVal($aKey) {
    return (isset($this -> mVal[$aKey])) ? $this -> mVal[$aKey] : '';
  }

  /**
   * Get all fields from the user's table row.
   * @return array
   */
  public function getKeyVals() {
    return  $this -> mVal;//array_keys($this -> mVal);
  }

  /**
   * Reload the ID from session
   * Only used during login because instance is already set before user id is available
   */
  public function reloadId() {
    $this -> mId = self::getAuthId();
  }

  /**
   * Get the ID of the current user.
   * @return integer
   */
  public function getId() {
    return $this -> mId;
  }

  public function getFullName() {
    $lSys = CCor_Sys::getInstance();
    $lRet = cat($lSys['usr.firstname'], $lSys['usr.lastname']);
    return $lRet;
  }

  /**
   * Get a user preference value, e.g. the sort order in a job list.
   *
   * Persistent preferences are loaded into the session during login.
   * @param string $aKey Unique identifier of the preference, e.g. 'job-art.ord'
   * @param string $aStd Default value if the preference is not set for this user
   * @return mixed Value of the preference, e.g. 'webstatus'
   */
  public function getPref($aKey, $aStd = NULL) {
    $this -> createPref();
    if (isset($this -> mPref[$aKey])) {
      $lRet = $this -> mPref[$aKey];
    } else {
      $lRet = $aStd;
    }
    return $lRet;
  }

  public function setPref($aKey, $aValue) {
    $this -> createPref();
    $this -> mPref -> doSet($aKey, $aValue);
    $this -> mPref[$aKey] = $aValue;
  }

  private function createPref() {
    if (NULL === $this -> mPref) {
      $this -> mPref = new CCor_Usr_Pref($this -> mId);
    }
  }
  
  public function getPrefObject() {
    $this->createPref();
    return $this->mPref;
  }

  public function loadPrefsFromDb() {
    $this -> createPref();
    $this -> mPref -> loadPrefsFromDb();
  }

  // Priviledges

  /**
   * Has the user the right to read/edit/insert/delete the specified ressource?
   * @see CCor_Usr::canRead
   * @see CCor_Usr::canEdit
   * @see CCor_Usr::canInsert
   * @see CCor_Usr::canDelete
   * @param string $aKey Unique identifier of the right, e.g. 'job-pro'
   * @param integer $aLvL One of rdRead, rdEdit, rdIns or rdDel
   * @return boolean
   */
  public function canDo($aKey, $aLvl) {
    if (!isset($this -> mPriv)) {
      $lMid = $this -> getMandId();
      $this -> mPriv = new CCor_Usr_Priv($this -> mId, $lMid);
    }
    return $this -> mPriv -> canDo($aKey, $aLvl);
  }

  /**
   * Can the user read the specified ressource?
   * @param string $aKey The ressouce, e.g. 'usr' for users
   * @return boolean
   */
  public function canRead($aKey) {
    return $this -> canDo($aKey, rdRead);
  }

  /**
   * Can the user edit the specified ressource?
   * @param string $aKey The ressouce, e.g. 'usr' for users
   * @return boolean
   */
  public function canEdit($aKey) {
    return $this -> canDo($aKey, rdEdit);
  }

  /**
   * Can the user insert a new row of the specified ressource type?
   * @param string $aKey The ressouce, e.g. 'usr' for users
   * @return boolean
   */
  public function canInsert($aKey) {
    return $this -> canDo($aKey, rdIns);
  }

  /**
   * Can the user delete a row of the specified ressource type?
   * @param string $aKey The ressouce, e.g. 'usr' for users
   * @return boolean
   */
  public function canDelete($aKey) {
    return $this -> canDo($aKey, rdDel);
  }


  // membership


  protected function getMembership() {
    if (!isset($this -> mMem)) {
      $this -> mMem = new CCor_Usr_Mem($this -> mId);
    }
  }

  /**
   * Is the current user a member of the specified group?
   *
   * @param integer $aGid ID of group to check, primary key in group table
   * @return boolean TRUE if user is Member of given group
   */
  public function isMemberOf($aGid) {
    $this -> getMemberShip();
    return $this -> mMem -> isMemberOf($aGid);
  }

  /**
   * Get all groups the user is a member of as an array
   * @return array Array of the groups' IDs
   */
  public function getMemArray() {
    $this -> getMemberShip();
    return $this -> mMem -> getArray();
  }

  /**
   * Get all groups (except the root/ Deleted groups) the user is a member of
   * @return string Comma separated list of group IDs
   */
  public static function getMembershipImplode() {
    $lUid = self::getAuthId();
    $lSql = 'SELECT p.id FROM al_gru p, al_usr_mem q WHERE p.id=q.gid AND p.mand='.MID.' AND p.parent_id<>0 AND p.del="N" AND q.uid='.$lUid;
    $lQry = new CCor_Qry($lSql);
    $lRes = $lQry -> getImplode('id');
    return $lRes;
  }

  /**
   * Get all Mandatories the user is a member of
   * @return Array with mand code as array key, and mand name as array value.
   */
  public static function getMandMembership() {
    $lUid = self::getAuthId();
    $lSql = 'SELECT m.id, m.code, m.name_'.LAN.' FROM al_usr_mand u, al_sys_mand m WHERE u.mand = m.id AND u.uid = '.$lUid;
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lRes[$lRow['code']] =  $lRow['name_'.LAN];
    }
    return $lRes;
  }

  // critical path

  protected function getCrp() {
    if (!isset($this -> mCrp)) {
      $this -> getMembership();
      $this -> mCrp = new CCor_Usr_Crp($this -> mId, $this -> mMem);
    }
  }

  /**
   * Can the user execute the specified step (i.e. job status change)?
   * @param integer $aStepId ID of the step
   * @return boolean
   */
  public function canStep($aStepId) {
    $this -> getCrp();
    return $this -> mCrp -> canDo($aStepId);
  }

  protected function getCrpFlags($aJob = array()) {
    if (!isset($this -> mFlags)) {
      $this -> getMembership();
      $this -> mFlags = new CCor_Usr_Flag($this -> mId, $this -> mMem, $aJob);
    }
  }

  /**
   * Can the user execute the specified flag step (i.e. flag status change)?
   * @param integer $aFlagId
   * @param integer $aCrpId ID of the workflow/Critical Path master
   * @param unknown_type $aJob
   */
  public function canConfirmFlag($aFlagId, $aCrpId, $aJob = array()) {
    $this -> getCrpFlags($aJob);
    return $this -> mFlags -> canDo($aFlagId, $aCrpId);
  }

  // Get CRP Status Edit Rights

  public function canStatus($aStatusId) {
    $this -> getMembership();
    $this -> mCrpStatus = new CCor_Usr_Sta($this -> mId, $this -> mMem);
    return $this -> mCrpStatus -> canDo($aStatusId);
  }

  // job conditions

  protected function getCnd() {
    if (!isset($this -> mCnd)) {
      $this -> getMemberShip();
      $this -> mCnd = new CCor_Usr_Cond($this -> mId, $this -> mMem);
    }
  }

  public function getJobCond() {
    $this -> getCnd();
    $lRet = array();
    $lVal = trim($this -> mCnd -> getCondSql());
    if (!empty($lVal)) {
      $lRet[] = '('.$lVal.')';
    }
    $lVal = trim($this -> getVal('cnd_sql'));
    if (!empty($lVal)) {
      $lRet[] = '('.$lVal.')';
    }
    $lRet = implode(' AND ', $lRet);
    return $lRet;
  }

  public function canReadJob($aSrc, $aJobId) {
    $lJid = intval($aJobId);
    $lCnd = $this -> getJobCond();
    $lSql = 'SELECT COUNT(*) FROM al_job_'.$aSrc.' WHERE 1 ';
    $lSql.= 'AND id='.$lJid.' ';
    if (!empty($lCnd)) {
      $lSql.= 'AND ('.$lCnd.') ';
    }
    $lCnt = CCor_Qry::getInt($lSql);
    return ($lCnt > 0);
  }

  public function addRecentJob($aSrc, $aJobId, $aKeyword = '') {
    $lSql = 'REPLACE INTO al_usr_recent SET ';
    $lSql.= 'usr_id='.$this -> getId().', ';
    $lSql.= 'mand='.esc(MID).', ';
    $lSql.= 'src="'.addslashes($aSrc).'", ';
    $lSql.= 'jobid="'.addslashes($aJobId).'", ';
    $lSql.= 'keyword="'.addslashes($aKeyword).'" ';
    CCor_Qry::exec($lSql);
  }

  public function getRecentJobs($aMax = 10) {
    $lSql = 'SELECT * FROM al_usr_recent WHERE ';
    $lSql.= 'usr_id='.$this -> getId().' ';
    $lSql.= 'AND mand='.esc(MID).' ';
    $lSql.= 'GROUP BY jobid ';
    $lSql.= 'ORDER BY stamp DESC ';
    $lMax = intval($aMax);
    if (!empty($lMax)) {
      $lSql.= 'LIMIT '.$lMax;
    }
    $lQry = new CCor_Qry($lSql);
    return $lQry;
  }

  public function addBookmark($aSrc, $aJobId, $aKeyword = '') {
    $lSql = 'REPLACE INTO al_usr_bookmark SET ';
    $lSql.= 'usr_id='.$this -> getId().', ';
    $lSql.= 'src="'.addslashes($aSrc).'", ';
    $lSql.= 'jobid="'.addslashes($aJobId).'", ';
    $lSql.= 'keyword="'.addslashes($aKeyword).'" ';
    CCor_Qry::exec($lSql);
  }

  public function removeBookmark($aSrc, $aJobId, $aKeyword = '') {
    $lSql = 'DELETE FROM al_usr_bookmark WHERE ';
    $lSql.= 'usr_id='.$this -> getId().' AND ';
    $lSql.= 'src="'.addslashes($aSrc).'" AND ';
    $lSql.= 'jobid="'.addslashes($aJobId).'";';
    CCor_Qry::exec($lSql);
  }

  public function getBookmarks($aMax = 5) {
    $lSql = 'SELECT * FROM al_usr_bookmark WHERE ';
    $lSql.= 'usr_id='.$this -> getId().' ';
    $lSql.= 'ORDER BY stamp DESC ';
    $lSql.= 'LIMIT '.intval($aMax);
    $lQry = new CCor_Qry($lSql);
    return $lQry;
  }

  protected function getInfoObj() {
    if (!isset($this -> mInf)) {
      $this -> mInf = new CCor_Usr_Info($this -> mId);
    }
  }

  public function getInfo($aKey, $aStd = NULL) {
    $this -> getInfoObj();
    return $this -> mInf -> get($aKey, $aStd);
  }

  public function setInfo($aKey, $aVal) {
    $this -> getInfoObj();
    return $this -> mInf -> set($aKey, $aVal);
  }

  //
  /**
   * Return an array
   * @param array $aSrcArr available Jobarts
   * @return $lRet Jobarts which can be inserted from current user.
   * */
  public function canCopyJob($aSrcArr) {
    $lRet = array();
    foreach ($aSrcArr as $lKey) {
      if ($this -> canInsert('job-'.$lKey)){
        $lRet[] = $lKey;
      }
    }
    return $lRet;
  }

  public function stopCopyJob() {
    $lQry = "DELETE FROM `al_usr_pref` WHERE  `uid`=".$this->mId." AND `code`='usr.copymem' AND `mand`=".MID.";";
    CCor_Qry::exec($lQry);
    $this->loadPrefsFromDb(); //Reload Prefs from DB to reconfigure Session Data
  }

  /*
   * Arbeitet anders als die gleichnamige Fkt in api/alink/query/getjob...!
   *
   * used for prefilling job/pro-mask, get the preselect in getTypeTselect
   * provide only conditions with '=' == 'Operator'
   * $aPrfx used for prefilling in new job/project
   * first used in mand_1003 with special alias defined in mand/cfg
   * WICHTIG: muss dafuer $lRet[$lProjectSupplier] = $lValue zurueckliefern!
   */
  public static function getArrUserConditions($aSrc = 'pro', $aPrfx = '') {
    $lReturn = array();
    $lRet = $lReturn;

    $lSupplierArr = CCor_Cfg::get('cond.supplier', array());
    $lProjectSupplier = '';
    $lJobSupplier = '';
    if (!empty($lSupplierArr)) {
      if(isset($lSupplierArr['pro']) AND !empty($lSupplierArr['pro'])) {
        $lProjectSupplier = $lSupplierArr['pro'];
        if(isset($lSupplierArr['job']) AND !empty($lSupplierArr['job'])) {
          $lJobSupplier = $lSupplierArr['job'];
        }
      }
    }
    $lUid = CCor_Usr::getAuthId();
    $lSql = 'SELECT * FROM al_cnd WHERE usr_id='.$lUid.' AND mand='.MID;
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      // wenn Feld 'Cond' empty, No Condition
      //if ($lRow['cond'] !== '') {
      if ($lRow['name_en'] !== '') {
        $lArr = explode(';', $lRow['name_en']); // entspricht der AND - Verknuepfung, arbeite mit name_en, da cond evtl. geloescht werden koennte
        foreach ($lArr as $lVal) {
          list($lField, $lOp, $lValue) = explode(' ', $lVal, 3);
            // it's not the same alias for projects and jobs for supplier!
            //liefert ENTWEDER 'project_supplier' ODER 'supplier'
            if (!empty($lJobSupplier) AND $lJobSupplier == $lField AND 'pro' == $aSrc AND '=' == $lOp) { // 'supplier' == $lField
              $lRet[$lProjectSupplier] = $lValue;    // $lRet['project_supplier']
            } else {
              $lTmp = array();
              $lTmp['field'] = $lField;
              $lTmp['op']    = strtolower($lOp);
              $lTmp['value'] = $lValue;
            $lRet[] = $lTmp;
        }

      }
    }
    }

    if (!empty($aPrfx)) {
      $lReturn[$aPrfx] = array();
      $lReturn[$aPrfx] = $lRet;
      $lRet = $lReturn;
    }
    return $lRet;
  }

  protected function getMandObj() {
    if (!isset($this -> mMidObj)) {
      $this -> mMidObj = new CCor_Usr_Mand($this -> mId, $this->getVal('mand'));
    }
    return $this->mMidObj;
  }

  public function canAccessMid($aMid) {
    return $this -> getMandObj() -> canAccess($aMid);
  }

  /**
   * Check if i have to backup any user and if this user is on Holiday.
   * If i have to backup someone RETURN the user ID of this person, else return false
   * @return number|boolean
   */
  public function shallIBackupAnyUsr() {
  	$lUid = self::getAuthId();
  	$lSql = 'SELECT id FROM al_usr WHERE backup='.$lUid;

  	$lSql = 'SELECT p.uid FROM al_usr_pref p ';
  	$lSql.= 'WHERE p.uid IN (SELECT id FROM al_usr WHERE backup='.$lUid.') ';
  	if(defined(MID))
  	$lSql.= 'AND p.mand='.MID.' '; // @todo: only use mand 0. I am on holiday or I am not
  	$lSql.= 'AND p.code="usr.onholiday" ';
  	$lSql.= 'AND p.val="Y" ';
  	$lSql.= 'LIMIT 1 ';

  	$lRet = CCor_Qry::getInt($lSql);
  	if (empty($lRet)) return false;

  	return $lRet;
  }

  /**
   * Get a list of user ids of all absent people I backup.
   * @return array
   */
  public function getAllAbsentUsersIBackup() {
    $lUid = self::getAuthId();
    $lSql = 'SELECT id FROM al_usr WHERE backup='.$lUid;

    $lSql = 'SELECT p.uid FROM al_usr_pref p ';
    $lSql.= 'WHERE p.uid IN (SELECT id FROM al_usr WHERE backup='.$lUid.') ';
    if(defined(MID))
    $lSql.= 'AND p.mand='.MID.' '; // @todo: only use mand 0. I am on holiday or I am not
    $lSql.= 'AND p.code="usr.onholiday" ';
    $lSql.= 'AND p.val="Y" ';

    $lRet = array();
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lRet[] = $lRow['uid'];
    }
    return $lRet;
  }

}