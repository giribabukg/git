<?php
class CInc_Gru_Rig_Mod extends CCor_Obj {

  public function __construct() {
    $this->mUsrRig = new CUsr_Rig_Mod();
  }

  protected function loadRights() {
    $this -> mRig = array();
    $lSql = 'SELECT code,level FROM al_gru_rig WHERE group_id='.$this -> mGid.' ';
 #   if(!empty($this -> mRight))
 #     $lSql.= 'AND `right`="'.$this -> mRight.'" ';
    $lSql.= 'AND mand='.$this -> mMid;
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $this -> mRig[$lRow['code']] = intval($lRow['level']);
    }
  }

  public function getPost(ICor_Req $aReq, $aOld = TRUE) {
    
    $lUsr = CCor_Usr::getInstance();
    $lUid = $lUsr -> getId();
    
    $this -> mGid = $aReq -> getInt('id');
    $this -> mMid = $aReq -> getInt('mid');
    $this -> mRight = $aReq -> getVal('rig');
    $this -> loadRights();

    $this -> mReqVal = $aReq -> getVal('val');
    $this -> mReqOld = $aReq -> getVal('old');
    #$this -> dump($this -> mReqOld);

    $lAddRights = array();
    $lDelRights = array();
    foreach ($this -> mReqOld as $lKey => $lVal) {
      $lSet = array();
      $lRig = (isset($this -> mRig[$lKey])) ? $this -> mRig[$lKey] : 0;
      $lNew = (isset($this -> mReqVal[$lKey])) ? array_sum($this -> mReqVal[$lKey]) : 0;
      $lOld = array_sum($lVal);
      $lQry = new CCor_Qry();
      if ($lNew != $lOld) {
        
        $lUpd = $lRig - $lOld + $lNew;
        $lFieldname = str_replace('fie_', '', $lKey);
        
        $lAllChangRig = $this -> mUsrRig -> getRights($this -> mReqVal[$lKey], $lVal, $lFieldname, $this -> mMid, $lOld,$this -> mRight);
        
        if ($lAllChangRig[0] != "")
          $lAddRights[] = $lAllChangRig[0];
        
        if ($lAllChangRig[1] != "")
          $lDelRights[] = $lAllChangRig[1];
        
        if ($lUpd == 0) {
          $lSql = 'DELETE FROM al_gru_rig WHERE group_id='.$this -> mGid.' ';
          if(!empty($this -> mRight))
            $lSql.= 'AND `right`="'.$this -> mRight.'" ';
          $lSql.= 'AND mand='.$this -> mMid.' ';
          $lSql.= 'AND code="'.addslashes($lKey).'"';
        } else {
          $lSql = 'REPLACE INTO al_gru_rig SET group_id='.$this -> mGid.', ';
          if(!empty($this -> mRight))
            $lSql.= '`right`="'.$this -> mRight.'", ';
          $lSql.= 'mand='.$this -> mMid.', ';
          $lSql.= 'code="'.addslashes($lKey).'",';
          $lSql.= 'level='.$lUpd;

        }
        $lQry -> query($lSql);
      }
    }
    $lSaveGruHis = new CGru_Mod();
    
    if (count($lAddRights) > 0) {
      $lSaveGruHis -> saveGruHis($lUid, $this -> mGid, $aDate=date("Y-m-d"), $aTyp=14, $aSubject='Added/s group right', implode(",", $lAddRights));
    }
    if (count($lDelRights) > 0) {
      $lSaveGruHis -> saveGruHis($lUid, $this -> mGid, $aDate=date("Y-m-d"), $aTyp=14, $aSubject='Deleted/s group right', implode(",", $lDelRights));
    }
  }

}