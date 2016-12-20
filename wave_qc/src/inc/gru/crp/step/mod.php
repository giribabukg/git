<?php
class CInc_Gru_Crp_Step_Mod extends CCor_Obj {
  
  public function getPost(ICor_Req $aReq) {
    $lUsr = CCor_Usr::getInstance();
    $lUid = $lUsr -> getId();
    $lSaveGruHis = new CGru_Mod();
    
    $this -> mGid = $aReq -> getInt('id'); //group id
    $this -> mNames = CCor_Res::extract('id', 'name', 'gru');
    $lGroupName = $this -> mNames[ $this -> mGid];
    
    $lCrp = $aReq -> getInt('crp');//crp=17
    $this -> mReqVal = $aReq -> getVal('val');
    $this -> mReqOld = $aReq -> getVal('old');

    $lCrps = CCor_Res::extract('id', 'name_en', 'crpmaster');
    $lCrpName = (isset($lCrps[$lCrp])) ? $lCrps[$lCrp] : '';
    
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
    $lAllSteps = CCor_Res::extract('id', 'name_en', 'crpstep');

    if (!empty($lDel)) {
      $lDelStep = array();
      $lSql = 'DELETE FROM al_gru_rig_stp WHERE fla_id=0 AND gru_id='.$this -> mGid.' AND stp_id IN ('.implode(',',$lDel).')';
      foreach ($lDel as $lStp) {
        $lQry -> query($lSql);
        $lStepName = (isset($lAllSteps[$lStp])) ? $lAllSteps[$lStp] : '';
        $lDelStep[] = $lStepName;
      }
      
      $lStep = implode("; ", $lDelStep);
      $lSelectedUser = implode("; ", $lUser);
      $lSaveGruHis -> saveGruHis($lUid, $this -> mGid, $aDate=date("Y-m-d"), $aTyp=14, $aSubject="Deleted CRP step/s ($lGroupName)", implode(",", $lDelStep) ." (Critical Path: " . $lCrpName . ' )');
    }
    if (!empty($lIns)) {
      $lAddStep = array();
      $lSql = 'INSERT INTO al_gru_rig_stp SET fla_id=0,crp_id='.$lCrp.',gru_id='.$this -> mGid.',stp_id=';
      foreach ($lIns as $lStp) {
        $lQry -> query($lSql.$lStp);
        $lStepName = (isset($lAllSteps[$lStp])) ? $lAllSteps[$lStp] : '';
        $lAddStep[] = $lStepName;
      }
      
      $lSaveGruHis -> saveGruHis($lUid, $this -> mGid, $aDate=date("Y-m-d"), $aTyp=14, $aSubject="Added CRP step/s ($lGroupName)", implode(",", $lAddStep) ." (Critical Path: " . $lCrpName . ' )');
    }

    if (!empty($lFlagsDel)) {
      $lSql = '';
      foreach ($lFlagsDel as $lVal) {
        foreach ($lVal as $lStp => $lFla) {
          $lSql = 'DELETE FROM al_gru_rig_stp WHERE fla_id='.$lFla.' AND crp_id='.$lCrp.' AND gru_id='.$this -> mGid.' AND stp_id='.$lStp;
          $lQry -> query($lSql);
        }
      }
    }
    if (!empty($lFlagsIns)) {
      $lSql = '';
      foreach ($lFlagsIns as $lVal) {
        foreach ($lVal as $lStp => $lFla) {
          $lSql = 'INSERT INTO al_gru_rig_stp SET fla_id='.$lFla.',crp_id='.$lCrp.',gru_id='.$this -> mGid.',stp_id='.$lStp;
          $lQry -> query($lSql);
        }
      }
    }

  }

}