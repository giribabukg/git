<?php
class CInc_Rol_Crp_Mod extends CCor_Obj {
  
  public function getPost(ICor_Req $aReq) {
    $this -> mRid = $aReq -> getInt('id');
    $this -> mNames = CCor_Res::extract('id', 'name', 'rol');
    $lRoleName = $this -> mNames[$this -> mRid];
    
    $lCrp = $aReq -> getInt('crp');
    $lArr = CCor_Res::extract('id', 'name_'.LAN, 'crpmaster');
    $lCrpName = $lArr[$lCrp];
    
    $lUsr = CCor_Usr::getInstance();
    $lUid = $lUsr -> getId();
    
    $this -> mReqVal = $aReq -> getVal('val');
    $this -> mReqOld = $aReq -> getVal('old');

    $lIns = array();
    $lDel = array();
    $lFlagsIns = array();
    $lFlagsDel = array();
    foreach ($this -> mReqOld as $lKey => $lValue) {
      foreach ($lValue as $lK => $lOld) {
      $lNew = (isset($this -> mReqVal[$lKey][$lK])) ? 1 : 0;
        if (1 == $lOld) {
          if (1 != $lNew) {
            if (0 == $lK) {
              $lDel[] = $lKey;
            } else {
              $lFlagsDel[] = array($lKey => $lK);
            }
          }
        } else {
          if (0 != $lNew) {
            if (0 == $lK) {
              $lIns[] = $lKey;
            } else {
              $lFlagsIns[] = array($lKey => $lK);
            }
          }
        }
      }
    }
    #echo '<pre>---mod.php---'.get_class().'---';var_dump($lDel,$lFlagsDel,$lIns,$lFlagsIns,'#############');echo '</pre>';
    $lQry = new CCor_Qry();
    $lAllCrpStep = CCor_Res::extract('id', 'name_en', 'crpstep');
    
    if (!empty($lDel)) {
      $lSql = 'DELETE FROM al_rol_rig_stp WHERE fla_id=0 AND role_id='.$this -> mRid.' AND stp_id IN ('.implode(',',$lDel).')';
      foreach ($lDel as $lStp){
          $lQry -> query($lSql);
          $lDelSteps[] = $lAllCrpStep[$lStp];
      }
      //Added to Role History
      CRol_Mod::saveRoleHis($lUid, $this -> mRid, $aDate=date("Y-m-d"), $aTyp=14, $aSubject="Deleted from $lRoleName($lCrpName)", 'CRP:'.implode(", ", $lDelSteps));
    }
    
    if (!empty($lIns)) {
      $lSql = 'INSERT INTO al_rol_rig_stp SET fla_id=0,crp_id='.$lCrp.',role_id='.$this -> mRid.',stp_id=';
      foreach ($lIns as $lStp) {
        $lQry -> query($lSql.$lStp);
        $lInsSteps[] = $lAllCrpStep[$lStp];
      }
      CRol_Mod::saveRoleHis($lUid, $this -> mRid, $aDate=date("Y-m-d"), $aTyp=14, $aSubject="Added to $lRoleName($lCrpName)", 'CRP:'.implode(", ", $lInsSteps));
    }

    if (!empty($lFlagsDel)) {
      $lSql = '';
      foreach ($lFlagsDel as $lVal) {
        foreach ($lVal as $lStp => $lFla) {
          $lSql = 'DELETE FROM al_rol_rig_stp WHERE fla_id='.$lFla.' AND crp_id='.$lCrp.' AND role_id='.$this -> mRid.' AND stp_id='.$lStp;
          $lQry -> query($lSql);
        }
      }
    }
    if (!empty($lFlagsIns)) {
      $lSql = '';
      foreach ($lFlagsIns as $lVal) {
        foreach ($lVal as $lStp => $lFla) {
          $lSql = 'INSERT INTO al_rol_rig_stp SET fla_id='.$lFla.',crp_id='.$lCrp.',role_id='.$this -> mRid.',stp_id='.$lStp;
          $lQry -> query($lSql);
        }
      }
    }

  }
    
}