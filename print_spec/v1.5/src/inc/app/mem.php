<?php
class CInc_App_Mem extends CCor_Obj {

  protected $mMem;
  protected $mUpd;

  public function __construct() {
    $this->mGroups = CCor_Res::extract('id', 'parent_id', 'gru');
    $this->mNames  = CCor_Res::extract('id', 'name', 'gru');
    $this->mMand   = CCor_Res::extract('id', 'mand', 'gru');
  }

  protected function getMembership($aUid) {
    $lRet = array();
    $lSql = 'SELECT gid FROM al_usr_mem WHERE uid='.intval($aUid);
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lRet[$lRow['gid']] = $lRow['gid'];
    }
    return $lRet;
  }

  public function dbg($aText, $aLvl = mlInfo) {
    parent::dbg($aText, $aLvl);
    #echo $aText.BR;
  }

  public function addToGroups($aUid, $aGroups, $aMod = NULL) {
    if (is_string($aGroups)) {
      $lGroups = explode(',', $aGroups);
    } else if (is_array($aGroups)) {
      $lGroups = $aGroups;
    } else {
      $lGroups = array($aGroups);
    }
    if (empty($lGroups)) return;

    $lUid = intval($aUid);
    if (empty($lUid)) return;

    $this->mMem = $this->getMembership($lUid);
    foreach ($lGroups as $lGid) {
      $this->addGroup($lGid);
    }
    if (empty($this->mUpd)) return;

    $lQry = new CCor_Qry();
	$lAdminlevel = array();
	$lQry -> query('SELECT * FROM al_gru WHERE admin_level <> 0');
    if ($lRow = $lQry -> getAssoc()) {
      $lGroupId = $lRow['id'];
      $lAdminlevel[] = $lGroupId;
    }
    #copying membership except Admin level
    $lExceptGruMem = array_diff($this -> mUpd, $lAdminlevel);
    #All membership
    $lAllGroup = $this -> mUpd;
    $lMem = (empty($aMod)) ? $lExceptGruMem : $this -> mUpd;
    foreach ($lMem as $lGid) {
      $this->dbg('Adding to Group '.$lGid.' '.$this->mNames[$lGid]);
      $lArr = array();
      $lArr['uid'] = $lUid;
      $lArr['gid'] = $lGid;
      if (isset($this->mMand[$lGid])) {
        $lArr['mand'] = $this->mMand[$lGid];
      }
      $lSql = 'REPLACE INTO al_usr_mem SET ';
      foreach ($lArr as $lKey => $lVal) {
        $lSql.= $lKey.'='.intval($lVal).',';
      }
      $lSql = strip($lSql).';';
      $lQry->exec($lSql);
    }
  }

  protected function addGroup($aGid) {
    $lGid = intval($aGid);
    if (empty($lGid)) return;
    if (isset($this->mMem[$lGid])) return;

    $this->mMem[$lGid] = $lGid;
    $this->mUpd[$lGid] = $lGid;

    $lParent = $this->mGroups[$lGid];
    if (!empty($lParent)) {
      $this->addGroup($lParent);
    }
  }

  public function removeFromGroups($aUid, $aGroups) {
    if (is_string($aGroups)) {
      $lGroups = explode(',', $aGroups);
    } else if (is_array($aGroups)) {
      $lGroups = $aGroups;
    } else {
      $lGroups = array($aGroups);
    }
    if (empty($lGroups)) return;

    $lUid = intval($aUid);
    if (empty($lUid)) return;

    $this->mMem = $this->getMembership($lUid);
    $this->mUpd = array();

    foreach ($lGroups as $lGid) {
      $this->removeFromGroup($lGid);
    }

    if (empty($this->mUpd)) return;
    foreach ($this->mUpd as $lGid) {
      $this->dbg('Removing from Group '.$lGid.' '.$this->mNames[$lGid]);
    }
    if (empty($this->mUpd)) return;
    $lSql = 'DELETE FROM al_usr_mem WHERE uid='.$lUid.' ';
    $lSql.= 'AND gid IN ('.implode(',', $this->mUpd).');';
    new CCor_Qry($lSql);
  }

  protected function removeFromGroup($aGid) {
    $aGid = intval($aGid);
    if (!isset($this->mMem[$aGid])) return;
    unset($this->mMem[$aGid]);
    $this->mUpd[$aGid] = $aGid;

    foreach ($this->mGroups as $lSubGid => $lParent) {
      if ($lParent == 0) continue;
      if ($lParent == $aGid) {
        $this->removeFromGroup($lSubGid);
      }
    }
  }

}