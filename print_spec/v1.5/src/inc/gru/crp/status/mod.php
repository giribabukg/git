<?php
class CInc_Gru_Crp_Status_Mod extends CCor_Obj {
  
  public function getPost(ICor_Req $aReq) {
    $lUsr = CCor_Usr::getInstance();
    $lUid = $lUsr -> getId();
    $lSaveGruHis = new CGru_Mod();
    
    $this -> mGid = $aReq -> getInt('id');
    $this -> mNames = CCor_Res::extract('id', 'name', 'gru');
    $lGroupName = $this -> mNames[ $this -> mGid];
    
    $lCrp = $aReq -> getInt('crp');
    $lCrps = CCor_Res::extract('id', 'name_en', 'crpmaster');
    $lCrpName = (isset($lCrps[$lCrp])) ? $lCrps[$lCrp] : '';
    
    $this -> mReqVal = $aReq -> getVal('val');
    $this -> mReqOld = $aReq -> getVal('old');
    
    $lIns = array();
    $lDel = array();
    foreach ($this -> mReqOld as $lKey => $lVal) {
      $lNew = (isset($this -> mReqVal[$lKey])) ? 1 : 0;
      if (1 == $lVal) {
        if (1 != $lNew) {
          $lDel[] = $lKey;
        }
      } else {
        if (0 != $lNew) {
          $lIns[] = $lKey;
        }
      }
    }
    $lQry = new CCor_Qry();
    $lAllSteps = CCor_Res::extract('id', 'name_en', 'crp');
    
    if (!empty($lDel)) {
      $lDelStatus = array();
      $lSql = 'DELETE FROM al_gru_rig_status WHERE gru_id='.$this -> mGid.' AND sta_id IN ('.implode(',',$lDel).')';
       foreach ($lDel as $lStp) {
          $lQry -> query($lSql);
          $lStepName = (isset($lAllSteps[$lStp])) ? $lAllSteps[$lStp] : '';
          $lDelStatus[] = $lStepName;
        }
        $lSaveGruHis -> saveGruHis($lUid, $this -> mGid, $aDate=date("Y-m-d"), $aTyp=14, $aSubject="Deleted CRP status ($lGroupName)", implode(",", $lDelStatus) ." (Critical Path: " . $lCrpName . ' )');
    }

  if (!empty($lIns)) {
    $lInsStatus = array();
    $lSql = 'INSERT INTO al_gru_rig_status SET gru_id='.$this -> mGid.',sta_id=';
    foreach ($lIns as $lStp) {
      $lQry -> query($lSql.$lStp);
      $lStepName = (isset($lAllSteps[$lStp])) ? $lAllSteps[$lStp] : '';
      $lInsStatus[] = $lStepName;
    }
    $lSaveGruHis -> saveGruHis($lUid, $this -> mGid, $aDate=date("Y-m-d"), $aTyp=14, $aSubject="Added CRP status ($lGroupName)", implode(",", $lInsStatus) ." (Critical Path: " . $lCrpName . ' )');
  }
  }
}