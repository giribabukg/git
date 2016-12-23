<?php

class CInc_Usr_Mem_Mod extends CCor_Mod_Table
{

  public function __construct ()
  {}

  public function saveHis ($oldArr, $newArr, $uid, $userId)
  {
    #Search New Grp
    $lNewGrp = self::getGroupDiffs($oldArr, $newArr);
    #Search Old Grp
    $lOldGrp = self::getGroupDiffs($newArr, $oldArr);
    
    $lSaveUsrHis = new CUsr_Rig_Mod ();
    #Added new group
    if ($lNewGrp != "") {
      $lSaveUsrHis -> saveUsrHis($uid, $userId,$aDate = date("Y-m-d"), $aTyp = 14, $aSubject = 'Group/s Added',$lNewGrp);
    }
    #Deleted group
    if ($lOldGrp != "") {
      $lSaveUsrHis -> saveUsrHis($uid, $userId,$aDate = date("Y-m-d"), $aTyp = 14, $aSubject = 'Group/s Deleted',$lOldGrp);
    }
  }

  protected function getGroupDiffs ($arr1, $arr2)
  {
    $lRet = "";
    foreach ($arr2 as $row) {
      if ( ! in_array($row, $arr1)) {
        $lGroupName = CCor_Res::extract('id', 'name', 'gru',array('id' => $row));
        $lGroupMand = CCor_Res::extract('id', 'mand', 'gru',array('id' => $row));
        $lRet .= $lGroupName[$row] . " (ID:" . $row . ",Mand:" .$lGroupMand[$row] . "), ";
      }
    }
    return substr($lRet, 0,  - 2);
  }
}