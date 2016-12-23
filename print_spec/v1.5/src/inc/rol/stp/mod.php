<?php
class CInc_Rol_Stp_Mod extends CCor_Obj {

  public function getPost(ICor_Req $aReq) {
    $this -> mRid = $aReq -> getInt('id');
    $lCrp = $aReq -> getInt('crp');
    
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
    $lSql='';
    if (!empty($lDel)) {
      $lSql = 'Update al_rol_rig_stp SET show_mytask = "N"  WHERE role_id='.$this -> mRid.' AND stp_id IN ('.implode(',',$lDel).')';
      $lQry -> query($lSql);
      // After Changes, clear Cache.
      CCor_Cache::clearStatic('cor_res_rolmytask_'.MID);
    }
    if (!empty($lIns)) {
      $lSql = 'Update al_rol_rig_stp SET show_mytask = "Y"  WHERE role_id='.$this -> mRid.' AND stp_id IN ('.implode(',',$lIns).')';
      $lQry -> query($lSql);
      // After Changes, clear Cache.
      CCor_Cache::clearStatic('cor_res_rolmytask_'.MID);
    }
    
  }

}