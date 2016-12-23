<?php
class CInc_Cor_Anyusr extends CCor_Obj {
  
  private $mId;
  private $mPref;
  private $mPriv;
  private $mMem;
  private $mCnd; // TODO: new
  private $mVal;
  private $mCrp; // TODO: new
  private $mFlags; // TODO: new
  private $mFie; // TODO: this is different from CCor_Usr. Why can't CCor_Anyusr be a subset of CCor_Usr without Singleton?
  
  public function __construct($aId) {
    $this -> mId = intval($aId);
    $this -> loadVals();
  }

  protected function loadVals() {
    if (empty($this -> mId)) {
      return;
    }
    $lQry = new CCor_Qry('SELECT * FROM al_usr WHERE del="N" AND id='.$this -> mId);
    $this -> mVal = $lQry -> getAssoc();
    if (FALSE !== $this -> mVal) {
      $this -> mVal['fullname'] = cat($this -> mVal['firstname'], $this -> mVal['lastname']);
    } else {
      $this -> mVal = array();
    }
  }

  public function getVal($aKey) {
    return (isset($this -> mVal[$aKey])) ? $this -> mVal[$aKey] : '';
  }

  public function getKeyVals() {
    return $this -> mVal;
  }

  public function getId() {
    return $this -> mId;
  }

  public function getFullName() {
    return $this -> mVal['fullname'];
  }

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
    $this -> mPref[$aKey] = $aValue;
  }
  
  private function createPref() {
    if (NULL === $this -> mPref) {
      $this -> mPref = new CCor_Usr_Pref($this -> mId);
    }
  }

  public function getPrefObject() {
    $this -> createPref();
    return $this -> mPref;
  }

  public function loadPrefsFromDb() {
    $this -> createPref();
    $this -> mPref -> loadPrefsFromDb();
  }
  
  // Priviledges
  
  public function canDo($aKey, $aLvl) {
    if (!isset($this -> mPriv)) {
      $this -> mPriv = new CCor_Usr_Priv($this -> mId);
    }
    return $this -> mPriv -> canDo($aKey, $aLvl);
  }
  
  public function canRead($aKey) {
    return $this -> canDo($aKey, rdRead);
  }
  
  public function canEdit($aKey) {
    return $this -> canDo($aKey, rdEdit);
  }
  
  public function canInsert($aKey) {
    return $this -> canDo($aKey, rdIns);
  }
  
  public function canDelete($aKey) {
    return $this -> canDo($aKey, rdDel);
  }
  
  // Membership

  protected function getMembership() {
    if (!isset($this -> mMem)) {
      $this -> mMem = new CCor_Usr_Mem($this -> mId);
    }
  }

  public function isMemberOf($aGid) {
    $this -> getMemberShip();
    return $this -> mMem -> isMemberOf($aGid);
  }

  public function getMemArray() {
    $this -> getMemberShip();
    return $this -> mMem -> getArray();
  }

  // Critical path

  protected function getCrp() {
    if (!isset($this -> mCrp)) {
      $this -> getMembership();
      $this -> mCrp = new CCor_Usr_Crp($this -> mId, $this -> mMem);
    }
  }

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

  // Job conditions
  
  protected function getCnd() {
    if (!isset($this -> mCnd)) {
      $this -> getMemberShip();
      $this -> mCnd = new CCor_Usr_Cond($this -> mId, $this -> mMem);
    }
  }

  // CCor_Anyusr specialties

  public function Exist() {
    if (!empty($this -> mVal)) {
      return TRUE;
    } else {
      return FALSE;
    }
  }

  public function canCopyJob($aSrcArr) {
    $lRet = array();
    foreach ($aSrcArr as $lKey) {
      if ($this -> canInsert('job-'.$lKey)){
        $lRet[] = $lKey;
      }
    }
    return $lRet;
  }

  // CCor_Anyusr specialties: field block rights
  
  public function canDoBlock($aSrc, $aKey, $aLvl) {
    if (!isset($this -> mFie[$aSrc])) {
      $this -> mFie[$aSrc] = new CCor_Usr_Fie($this -> mId, $aSrc);
    }
    return $this -> mFie[$aSrc] -> canDo($aKey, $aLvl);
  }
  
  public function canReadBlock($aSrc, $aKey) {
    return $this -> canDoBlock($aSrc, $aKey, rdRead);
  }
  
  public function canEditBlock($aSrc, $aKey) {
    return $this -> canDoBlock($aSrc, $aKey, rdEdit);
  }
  
  /**
   * Get the min level of the amdin level for this user.
   * @return Ambigous <number, mixed>
   */
  public function getAdminLevel () {
    $lGrpMem = $this -> getMemArray();
    $lGroups = CCor_Res::extract('id', 'admin_level', 'gru');
    $lTmp = array_flip($lGrpMem);
    $lGrpMem = array_intersect_key($lGroups, $lTmp);
    $lGrpMemFilter = array_filter($lGrpMem);
    $lMin = (empty($lGrpMemFilter)) ? 0 :  min($lGrpMemFilter);
    return $lMin;
  }
}