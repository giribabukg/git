<?php
class CInc_Usr_Crp_Status_Mod extends CCor_Obj {
  
  public function getPost(ICor_Req $aReq) {
    $this -> mUid = $aReq -> getInt('id');
    $this -> mReqVal = $aReq -> getVal('val');
    $this -> mReqOld = $aReq -> getVal('old');
    $lSaveUsrHis = new CUsr_Rig_Mod();
    
    $lUsr = CCor_Usr::getInstance();
    $lUid = $lUsr -> getId();
    
    $lCrp = $aReq -> getInt('crp');
    $lCrps = CCor_Res::extract('id', 'name_en', 'crpmaster');
    $lCrpName = (isset($lCrps[$lCrp])) ? $lCrps[$lCrp] : $lCrp;
    
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
    $lAllStatus = CCor_Res::extract('id', 'name_en', 'crp');
    
    if ( ! empty($lDel)) {
      $lDelStatus = array();
      $lSql = 'DELETE FROM al_usr_rig_status WHERE usr_id='.$this -> mUid.' AND sta_id=';
      foreach ($lDel as $lSta) {
        $lQry -> query($lSql . $lSta);
        $lCrpStatus = (isset($lAllStatus[$lSta])) ? $lAllStatus[$lSta] : $lSta;
        $lDelStatus [] = $lCrpStatus;
      }
      $aMsg = implode(",", $lDelStatus) ." (Critical Path: " . $lCrpName . ' )';
      $lSaveUsrHis -> saveUsrHis($this -> mUid, $lUid, $aDate=date("Y-m-d"), $aTyp=14, $aSubject='Deleted CRP status', $aMsg);
    }
    
    if ( ! empty($lIns)) {
      $lInsStatus = array();
      $lSql = 'INSERT INTO al_usr_rig_status SET usr_id='.$this -> mUid.',sta_id=';
      foreach ($lIns as $lSta) {
        $lQry -> query($lSql . $lSta);
        $lCrpStatus = (isset($lAllStatus[$lSta])) ? $lAllStatus[$lSta] : $lSta;
        $lInsStatus [] = $lCrpStatus;
      }
      $aMsg = implode(",", $lInsStatus) ." (Critical Path: " . $lCrpName . ' )';
      $lSaveUsrHis -> saveUsrHis($this -> mUid, $lUid, $aDate=date("Y-m-d"), $aTyp=14, $aSubject='Added CRP status', $aMsg);
    }
  }
    
}