<?php

class CInc_Usr_Crp_Step_Mod extends CCor_Obj
{

  public function getPost (ICor_Req $aReq)
  {
    $lUsr = CCor_Usr::getInstance();
    $lUid = $lUsr -> getId();
    $lSaveUsrHis = new CUsr_Rig_Mod();
    
    $this -> mUid = $aReq -> getInt('id');
    $lCrp = $aReq -> getInt('crp');
    
    $lCrps = CCor_Res::extract('id', 'name_en', 'crpmaster');
    $lCrpName = (isset($lCrps[$lCrp])) ? $lCrps[$lCrp] : $lCrp;
  
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
              var_dump($lK);
              $lDel[] = $lKey;
            } else {
              $lFlagsDel[] = array(
                  $lKey => $lK
              );
            }
          }
        } else {
          if (0 != $lNew) {
            if (0 == $lK) {
              $lIns[] = $lKey;
            } else {
              $lFlagsIns[] = array(
                  $lKey => $lK
              );
            }
          }
        }
      }
    }
    // echo
    // '<pre>---mod.php---'.get_class().'---';var_dump($lDel,$lFlagsDel,$lIns,$lFlagsIns,'#############');echo
    // '</pre>';
    
    $lQry = new CCor_Qry();
    $lAllSteps = CCor_Res::extract('id', 'name_en', 'crpstep');
    
    if ( ! empty($lDel)) {
      $lDelStep = array();
      $lSql = 'DELETE FROM al_usr_rig_stp WHERE fla_id=0 AND usr_id=' .$this -> mUid . ' AND stp_id=';
      foreach ($lDel as $lStp) {
        $lQry -> query($lSql . $lStp);
        $lStepName = (isset($lAllSteps[$lStp])) ? $lAllSteps[$lStp] : $lStp;
        $lDelStep[] = $lStepName;
      }
      $aMsg = implode(",", $lDelStep) ." (Critical Path: " . $lCrpName . ' )';
      $lSaveUsrHis -> saveUsrHis($this -> mUid, $lUid, $aDate=date("Y-m-d"), $aTyp=14, $aSubject='Deleted CRP step/s', $aMsg);
    }
    
    if ( ! empty($lIns)) {
      $lAddStep = array();
      $lSql = 'INSERT INTO al_usr_rig_stp SET fla_id=0,crp_id=' . $lCrp .',usr_id=' . $this -> mUid . ',stp_id=';
      foreach ($lIns as $lStp) {
        $lQry -> query($lSql . $lStp);
        $lStepName = (isset($lAllSteps[$lStp])) ? $lAllSteps[$lStp] : $lStp;
        $lAddStep[] = $lStepName;
      }
      
      $aMsg = implode(",", $lAddStep) ." (Critical Path: " . $lCrpName . ' )';
      $lSaveUsrHis -> saveUsrHis($this -> mUid, $lUid, $aDate=date("Y-m-d"), $aTyp=14, $aSubject='Added CRP step/s', $aMsg);
     }
    
    if ( ! empty($lFlagsDel)) {
      $lSql = '';
      foreach ($lFlagsDel as $lVal) {
        foreach ($lVal as $lStp => $lFla) {
          $lSql = 'DELETE FROM al_usr_rig_stp WHERE fla_id=' . $lFla .' AND crp_id=' . $lCrp . ' AND usr_id=' . $this -> mUid .
               ' AND stp_id=' . $lStp;
          $lQry -> query($lSql);
        }
      }
    }
    
    if ( ! empty($lFlagsIns)) {
      $lSql = '';
      foreach ($lFlagsIns as $lVal) {
        foreach ($lVal as $lStp => $lFla) {
          $lSql = 'INSERT INTO al_usr_rig_stp SET fla_id=' . $lFla . ',crp_id=' .$lCrp . ',usr_id=' . $this -> mUid . ',stp_id=' . $lStp;
          $lQry -> query($lSql);
        }
      }
    }
  }
 
}