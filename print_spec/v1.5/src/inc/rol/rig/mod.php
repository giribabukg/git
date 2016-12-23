<?php
class CInc_Rol_Rig_Mod extends CCor_Obj {

  public function __construct() {
  }

  protected function loadRights() {
    $this -> mRig = array();
    $lSql = 'SELECT code,level FROM al_rol_rig WHERE role_id='.$this -> mRid.' ';
    $lSql.= 'AND mand='.$this -> mMid;
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $this -> mRig[$lRow['code']] = intval($lRow['level']);
    }
  }

  public function getPost(ICor_Req $aReq, $aOld = TRUE) {
    $this -> mRid = $aReq -> getInt('id');
    $this -> mMid = $aReq -> getInt('mid');
    $this -> loadRights();

    $this -> mReqVal = $aReq -> getVal('val');
    $this -> mReqOld = $aReq -> getVal('old');
    #$this -> dump($this -> mReqOld);

    foreach ($this -> mReqOld as $lKey => $lVal) {
      $lSet = array();
      $lRig = (isset($this -> mRig[$lKey])) ? $this -> mRig[$lKey] : 0;
      $lNew = (isset($this -> mReqVal[$lKey])) ? array_sum($this -> mReqVal[$lKey]) : 0;
      $lOld = array_sum($lVal);
      $lQry = new CCor_Qry();
      if ($lNew != $lOld) {
        $lUpd = $lRig - $lOld + $lNew;
        if ($lUpd == 0) {
          $lSql = 'DELETE FROM al_rol_rig WHERE role_id='.$this -> mRid.' ';
          $lSql.= 'AND mand='.$this -> mMid.' ';
          $lSql.= 'AND code="'.addslashes($lKey).'"';
        } else {
          $lSql = 'REPLACE INTO al_rol_rig SET role_id='.$this -> mRid.', ';
          $lSql.= 'mand='.$this -> mMid.', ';
          $lSql.= 'code="'.addslashes($lKey).'",';
          $lSql.= 'level='.$lUpd;

        }
        $lQry -> query($lSql);
      }

    }
  }

}