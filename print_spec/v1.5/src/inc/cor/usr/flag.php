<?php
class CInc_Cor_Usr_Flag extends CCor_Obj {

  protected $mId;
  protected $mMem;
  protected $mFlags;

  public function __construct($aUid, & $aMem, $aJob = array()) {
    $this -> mId = intval($aUid);
    $this -> mMem = $aMem -> getStr();
    $this -> mFlags = array();
    $this -> mUsrRole = array();
    $this -> mJob = $aJob;
    $this -> mFie = CCor_Res::getByKey('alias', 'fie');
    $this -> loadRights();
    if (!empty($this -> mJob)) {
      $this -> addRoles();
      #echo '<pre>---flag.php---'.get_class().'---';var_dump($this -> mUsrRole,'#############');echo '</pre>';
    }
  }

  protected function loadRights() {
    $lSql = 'SELECT fla_id,crp_id FROM al_usr_rig_stp WHERE fla_id!=0 AND usr_id='.$this -> mId;
    $lQry = new CCor_Qry($lSql);
    foreach($lQry as $lRow) {
      $lFlagId = $lRow['fla_id'];
      $lCrpId = $lRow['crp_id'];
      $this -> mFlags[$lFlagId][$lCrpId] = true;
    }
    if (empty($this -> mMem)) return;
    $lSql = 'SELECT fla_id,crp_id FROM al_gru_rig_stp WHERE fla_id!=0 AND gru_id IN ('.$this -> mMem.')';
    #echo '<pre>---flag.php---'.get_class().'---';var_dump($lSql,'#############');echo '</pre>';
    $lQry = new CCor_Qry($lSql);
    foreach($lQry as $lRow) {
      $lFlagId = $lRow['fla_id'];
      $lCrpId = $lRow['crp_id'];
      $this -> mFlags[$lFlagId][$lCrpId] = true;
    }
    #echo '<pre>---flag.php---'.get_class().'---';var_dump($this -> mFlags,'#############');echo '</pre>';
  }

  #---- ROLES ----

  public function getVal($aKey, $aDefault = '') {
    if (isset($this -> mJob[$aKey])) {
      return $this -> mJob[$aKey];
    } else {
      return $aDefault;
    }
  }

  protected function addRoles() {
    $lUid = $this -> mId;
    foreach ($this -> mFie as $lAli => $lDef) {
      if ('uselect' != $lDef['typ']) continue;
      $lVal = $this -> getVal($lAli);
      if ($lVal == $lUid) {
        $this -> addRole($lAli);
      }
    }
  }

  protected function addRole($aAlias) {
    $this -> dbg('ADDING ROLE '.$aAlias);
    $this -> mUsrRole[$aAlias] = $aAlias;
  }

  protected function hasRole($aAlias = NULL) {
    if (NULL == $aAlias) {
      return !empty($this -> mUsrRole);
    }
    if ($this -> mUsrRole) {
      if ($aAlias) {
        if(isset($this -> mUsrRole[$aAlias])) {
          return ($this -> mUsrRole[$aAlias]);
        } else {
          return False;
        }
      }
    }
  }

  protected function loadFlagRoleRights() {
    $this -> mRolFlag = array();
    if (empty($this -> mUsrRole)) {
      return;
    }
    $lSql = 'SELECT id FROM al_rol WHERE mand='.MID.' AND alias IN (';
    foreach ($this -> mUsrRole as $lAli) {
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
      $this -> mRolFlag[$lRow['fla_id']][$lRow['crp_id']] = true;
    }
  }

  protected function canConfirmFlagAsRole($aFlagId, $aCrpId) {
    if (!isset($this -> mRolFlag)) {
      $this -> loadFlagRoleRights();
    }
    return isset($this -> mRolFlag[$aFlagId][$aCrpId]);
  }

  #-----

  public function canDo($aFlag, $aCrpId) {
    $lFlag = intval($aFlag);
    $lCrpId = intval($aCrpId);
    if (isset($this -> mFlags[$lFlag][$aCrpId])) {
      return true;
    }
    if ($this -> hasRole()) {
      return $this -> canConfirmFlagAsRole($lFlag, $lCrpId);
    }
    return false;
  }
/* // Vorlage aus job/form.php
  protected function canConfirmFlag($aFlagId, $aCrpId) {
    $lFlag = intval($aFlagId);
    $lCrpId = intval($aCrpId);
    if ($this -> mUsr -> canConfirmFlag($lFlag, $lCrpId)) {
      return true;
    }
    if ($this -> hasRole()) {
      return $this -> canConfirmFlagAsRole($lFlag, $lCrpId);
    }
    return false;
  }
*/
}