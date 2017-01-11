<?php
class CInc_Gru_Mem_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('usr-mem.menu');
    $this -> mReq -> expect('id');
    $this -> mVmKey = 'mem';

    // Ask If user has right for this page
    $lpn = 'gru';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdRead);
    }
  }

  protected function actStd() {
  	
    $lGid = $this -> getReqInt('id');

    $lMen = new CGru_Menu($lGid, 'mem');
    $lVie = new CGru_Mem_Form($lGid, CCor_Qry::getArr("SELECT max_usr from al_gru WHERE id = ". $lGid));

    $this -> render(CHtm_Wrap::wrap($lMen, $lVie));
  }

  protected function actSedt() {
    $lUsr = CCor_Usr::getInstance();
    $this -> mUid = $lUsr -> getId();
    
    $lOld = explode(',', $this -> getVal('old'));
    $lNew = $this -> getVal('dst');
    $lGid = $this -> getInt('gid');
    $lQry = new CCor_Qry();
    $lMem = new CInc_App_Mem();
    $lGroupName = $lMem -> mNames[$lGid];
    $lMaxUser = CCor_Qry::getArr("SELECT max_usr from al_gru WHERE id = ". $lGid);

    $this -> mUsr = CCor_Res::extract('id', 'fullname', 'usr');
    $lUser = array();
    $lSaveGruHis = new CGru_Mod();
    $lSaveUsrHis = new CUsr_Rig_Mod();
    # if old user list is greater than new usr list means remove user 
    if ((count($lOld) > count($lNew)) && ! empty($lOld) && (count($lNew) <= $lMaxUser || $lMaxUser == 0)) {
      foreach ($lOld as $lUid) {
        if (empty($lNew) || ! in_array($lUid, $lNew)) {
          
          $lMem -> removeFromGroups($lUid, $lGid);
          $lUser [] = $this -> mUsr[$lUid];
          # Send information to users history
          $lSaveUsrHis -> saveUsrHis($lUid, $this -> mUid, $aDate=date("Y-m-d"), $aTyp=14, $aSubject="Remove from group ($lGroupName)", $aMsg='');
        }
      }
      $lSelectedUser = implode("; ", $lUser);
      $lSaveGruHis -> saveGruHis($this -> mUid, $lGid, $aDate=date("Y-m-d"), $aTyp=14, $aSubject="Remove user from group ($lGroupName)", $lSelectedUser);
    }
    if ((count($lOld) <= count($lNew)) && ! empty($lNew) && (count($lNew) <= $lMaxUser || $lMaxUser == 0)) {
   
      foreach ($lNew as $lUid) {
        if (empty($lOld) || ! in_array($lUid, $lOld)) {
          
          $lMem -> addToGroups($lUid, $lGid, $aMod='mem');
          $lUser[] = $this -> mUsr[$lUid];
          # Send information to users history 
          $lSaveUsrHis -> saveUsrHis($lUid, $this -> mUid, $aDate=date("Y-m-d"), $aTyp=14, $aSubject="Added to group ($lGroupName)", $aMsg='');
        }
      }
     $lSelectedUser = implode("; ", array_filter($lUser));
     $lSaveGruHis -> saveGruHis($this -> mUid, $lGid, $aDate=date("Y-m-d"), $aTyp=14, $aSubject="Added user to group ($lGroupName)", $lSelectedUser);
    }
    if(count($lNew) > $lMaxUser && $lMaxUser != 0) {
      CCor_Msg::add("[CInc_Gru_Mem_Cnt] More Users than allowed for this group!", mtDebug, mlError);
    }
    $this -> redirect('index.php?act=gru-mem&id=' . $lGid);
  }

}