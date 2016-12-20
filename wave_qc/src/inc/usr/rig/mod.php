<?php

class CInc_Usr_Rig_Mod extends CCor_Obj
{

 public function __construct() {
   
 }

  protected function loadRights ()
  {
    $this -> mRig = array();
    $lSql = 'SELECT code,level FROM al_usr_rig WHERE user_id=' . $this -> mUid .
         ' ';
    if ( ! empty($this -> mRight))
      $lSql .= 'AND `right`="' . $this -> mRight . '" ';
    $lSql .= 'AND mand=' . $this -> mMid;
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      
      $this -> mRig[$lRow['code']] = intval($lRow['level']);
    }
  }

  public function getPost (ICor_Req $aReq, $aOld = TRUE)
  {
    $lUsr = CCor_Usr::getInstance();
    $lUid = $lUsr -> getId();
    
    $this -> mUid = $aReq -> getInt('id');
    $this -> mMid = $aReq -> getInt('mid');
    
    $this -> mRight = $aReq -> getVal('rig');
    $this -> loadRights();
   
    
    $this -> mReqVal = $aReq -> getVal('val');
    $this -> mReqOld = $aReq -> getVal('old');
    
    $lAddRights = array();
    $lDelRights = array();
    
    foreach ($this -> mReqOld as $lKey => $lVal) {
      
      $lSet = array();
      $lRig = (isset($this -> mRig[$lKey])) ? $this -> mRig[$lKey] : 0;
      $lNew = (isset($this -> mReqVal[$lKey])) ? array_sum($this -> mReqVal[$lKey]) : 0;
      $lOld = array_sum($lVal);
      
      $lQry = new CCor_Qry();
      if ($lNew != $lOld) {
        
        #$lFieldname = $lKey;
        $lFieldname = str_replace('fie_', '', $lKey);
        
        $lUpd = $lRig - $lOld + $lNew;
        
        $lAllChangRig = $this -> getRights($this -> mReqVal[$lKey], $lVal, $lFieldname, $this -> mMid, $lOld,$this -> mRight);
        
        if ($lAllChangRig[0] != "")
          $lAddRights[] = $lAllChangRig[0];
        if ($lAllChangRig[1] != "")
          $lDelRights[] = $lAllChangRig[1];
        
        if ($lUpd == 0) {
          $lSql = 'DELETE FROM al_usr_rig WHERE user_id=' . $this -> mUid . ' ';
          if ( ! empty($this -> mRight))
            $lSql .= 'AND `right`="' . $this -> mRight . '" ';
          $lSql .= 'AND mand=' . $this -> mMid . ' ';
          $lSql .= 'AND code="' . addslashes($lKey) . '"';
        } else {
          $lSql = 'REPLACE INTO al_usr_rig SET user_id=' . $this -> mUid . ', ';
          if ( ! empty($this -> mRight))
            $lSql .= '`right`="' . $this -> mRight . '", ';
          $lSql .= 'mand=' . $this -> mMid . ', ';
          $lSql .= 'code="' . addslashes($lKey) . '",';
          $lSql .= 'level=' . $lUpd;
        }
        $lQry -> query($lSql);
      }
    }
    if (count($lAddRights) > 0) {
      $this -> saveUsrHis ($this -> mUid, $lUid, $aDate=date("Y-m-d"), $aTyp=14, $aSubject='Added/s right', implode(",", $lAddRights));
    }
    if (count($lDelRights) > 0) {
      $this -> saveUsrHis ($this -> mUid, $lUid, $aDate=date("Y-m-d"), $aTyp=14, $aSubject='Deleted/s right', implode(",", $lDelRights));
    }
  }

  public function getDataArray ($aDataArray)
  {
    $lValArray = array();
    foreach ($aDataArray as $key => $value) {
      switch ($value) {
        case 1:
          $lValArray[] = lang('lib.read', 'en');
          break;
        case 2:
          $lValArray[] = lang('lib.edit', 'en');
          break;
        case 4:
          $lValArray[] = lang('lib.delete', 'en');
          break;
        case 8:
          $lValArray[] = lang('lib.insert', 'en');
          break;
      }
    }
    
    return $lValArray;
  }

  public function getRightType ($aType)
  {
    switch ($aType) :
      case 'htg':
        return CCor_Res::extract('domain', 'description', 'htbmaster');
        break;
      case 'fie':
        return CCor_Res::extract('alias', 'name_en', 'fie');
        break;
      default:
        return CCor_Res::extract('code', 'name_en', 'rights');
    endswitch;
  }
  
  public function getRights ($aNewRig, $aOldRig, $aFieldname, $aMid, $aOld,$mRight)
  {

   $lRights = (isset($mRight)) ? $mRight : '';//ex. fie
   $lArrUserRights = $this->getRightType($lRights);
  
   $lNewValArray = $this -> getDataArray($aNewRig); //$aNewRig =  1/2/4/8 
   $lOldValArray = $this -> getDataArray($aOldRig);
    
    $lFieldnameFull = (isset($lArrUserRights[$aFieldname])) ? $lArrUserRights[$aFieldname] : $aFieldname;
    $lAddMessage = count(array_diff($lNewValArray, $lOldValArray)) > 0 ? $lFieldnameFull ." (" . implode(",", array_diff($lNewValArray, $lOldValArray)) . ",Mand:" .
         $aMid . ",Old level:" . $aOld . ") " : "";
    
    $lDeleteMessage = count(array_diff($lOldValArray, $lNewValArray)) > 0 ? $lFieldnameFull ." (" . implode(",", array_diff($lOldValArray, $lNewValArray)) . ",Mand:" .
         $aMid . ",Old level:" . $aOld . ") " : "";
    
    return array($lAddMessage, $lDeleteMessage);
  }
  
  public function saveUsrHis ($aUid, $aUsrId, $aDate, $aTyp, $aSubject, $aMsg)
  {
    $lQry = new CCor_Qry();
    $lUsrHisSql = "INSERT INTO al_usr_his (uid,user_id,datum,typ,subject,msg) VALUES ('" .$aUid . "','" . $aUsrId . "','" . $aDate . "','" . $aTyp . "','" .
        $aSubject . "','" . $aMsg . "')";
    $lQry -> query($lUsrHisSql);
  }
}