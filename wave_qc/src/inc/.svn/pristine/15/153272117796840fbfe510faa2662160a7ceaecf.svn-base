<?php
class CInc_Usr_Crp_Mod extends CCor_Obj {
  
  public function getPost(ICor_Req $aReq) {
    $this -> mUid = $aReq -> getInt('id');
    
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
    if (!empty($lDel)) {
      $lSql = 'DELETE FROM al_usr_rig_stp WHERE usr_id='.$this -> mUid.' AND stp_id IN ('.implode(',',$lDel).')';
      $lQry -> query($lSql);
    }
    if (!empty($lIns)) {
      $lSql = 'INSERT INTO al_usr_rig_stp SET usr_id='.$this -> mUid.',stp_id=';
      foreach ($lIns as $lStp) {
        $lQry -> query($lSql.$lStp);
      }
    }
  }
    
}