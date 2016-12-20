<?php
class CInc_App_Chk_Check extends CCor_Obj {

  public function __construct($aSrc, $aJobId, $aJob = '') {
    $this -> mSrc = $aSrc;
    $this -> mJobId = $aJobId;
    if (empty($aJob)) {
      $lFac = new CJob_Fac($this -> mSrc, $this -> mJobId);
      $this -> mJob = $lFac -> getDat();
    } else $this -> mJob = $aJob;
    $lUser = CCor_Usr::getInstance();
    $this -> mUserId = $lUser -> getId();
  }

  /**
   * Get the check lists Ids of the lists that I should test, as a member of this group
   * return False: if nothing to check for this user
   * return array: array if the checklists that user has to check in this Job 
   * @return boolean|array
   */
  protected function getMasterCheckListsTobeCheckedAsGroup() {
    $lUser = CCor_Usr::getInstance();
    $lUserGroups = $lUser -> getMembershipImplode();
    if (empty($lUserGroups)) return false;
    $lMasterCheckLists = array();
    $lSql = 'SELECT chk_master_src FROM al_gru WHERE id IN ('.$lUserGroups.') AND chk_master_src != ""';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lMasterCheckLists[] = '"'.$lRow['chk_master_src'].'"';
    }
    $lMasterCheckLists = implode(',', $lMasterCheckLists); // all check lists Id non condition filtered yet.

    $lSql = 'SELECT * FROM al_chk_master WHERE id IN('.$lMasterCheckLists.')';
    $lMasterCheckLists = array();
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      if ($lRow['cnd_id'] != 0) {
        $lReg = new CInc_App_Condition_Registry();
        $lCnd = $lReg -> loadFromDb($lRow['cnd_id']);
        $lCnd -> setContext('data', $this -> mJob);
        if (!$lCnd -> isMet()) {
          continue;
        }
      }
      $lMasterCheckLists[] = $lRow['id'];
    }
    return $lMasterCheckLists;
  }

  /**
   * Get all check Items for the the passed check master Ids
   * @return array
   */
  protected function getCheckItems() {
    $lMasterCheckLists = implode(',', $this -> getMasterCheckListsTobeCheckedAsGroup());
    $lSql = 'SELECT * FROM al_chk_items WHERE master_id IN ('.$lMasterCheckLists.')';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      if ($lRow['cnd_id'] != 0) {
        $lReg = new CInc_App_Condition_Registry();
        $lCnd = $lReg -> loadFromDb($lRow['cnd_id']);
        $lCnd -> setContext('data', $this -> mJob);
        if (!$lCnd -> isMet()) {
          continue;
        }
      }
      $this -> mCheckItems[] = $lRow;
    }
    return $this -> mCheckItems;
  }

  /**
   * Check if this user is a member of at least one group that have checklist to check
   * return false if there is no checklists
   * return the checklists itmes of each group this user is memeber of
   * @return boolean|array
   */
  public function doUserHasCheckList() {
    $lRet = $this -> getCheckItems();
    if (empty($lRet)) return false;
    return $lRet;
  }
}