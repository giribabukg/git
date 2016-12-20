<?php
class CInc_Usg_Mem_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);

    $this -> mClass = 'usg';
    $this -> mSubclass = 'mem';
    $this -> mTitle = lan('usg-mem.menu');
    $this -> mReq -> expect('id');

    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($this -> mClass)) {
      $this -> setProtection('*', $this -> mClass, rdRead);
    }
  }

  protected function actStd() {
    $lUserId = $this -> getInt('id');

    $lMenu = new CUsg_Menu($lUserId, $this -> mSubclass, $this -> mClass);
    $lForm = new CUsg_Mem_Form($lUserId);

    $this -> render(CHtm_Wrap::wrap($lMenu, $lForm));
  }

  protected function actSedt() {
    $lUserId = $this -> getInt('id');
    $lUser = new CCor_Anyusr($lUserId);
    
    #Get old groups
    $lOld = $lUser->getMemArray();
    
    #Delete all groups
    $lSql = 'DELETE FROM al_usr_mem WHERE uid='.$lUserId;
    $lSql.= ' AND mand IN (0,'.MID.')';
    $lSql.= ' AND gid != 0;';
    $lQry = new CCor_Qry($lSql);
    $lVal = $this -> mReq -> val;

    #Add all groups
    $lQry -> query('SELECT id, mand, parent_id FROM al_gru;');
    foreach ($lQry as $lRow) {
      $lGroupId = $lRow['id'];
      $lGroupMandator = $lRow['mand'];
      $lGroupParentId = $lRow['parent_id'];
      if (isset($lVal[$lGroupId])) {
        CCor_Qry::exec('INSERT INTO al_usr_mem (uid,gid,mand) VALUES ('.$lUserId.','.$lGroupId.','.$lGroupMandator.');');
        if ($lGroupParentId > 0) {
          CCor_Qry::exec('INSERT INTO al_usr_mem (uid,gid,mand) VALUES ('.$lUserId.','.$lGroupParentId.','.$lGroupMandator.');');
        }
      }
    }

    CCor_Cache::clearStatic('cor_res_mem_'.MID);
    CCor_Cache::clearStatic('cor_res_mem_'.MID.'_uid');
    CCor_Cache::clearStatic('cor_res_mem_'.MID.'_gid');
    
    #Get new groups
    $lUser = new CCor_Anyusr($lUserId);
    $lNew = $lUser->getMemArray();
    
    #Clean Arrays
    $lOld = array_unique($lOld);
    $lNew = array_unique($lNew);
    
    #Get logged in User
    $lUsr = CCor_Usr::getInstance();
    
    #Save user activity history
    $lSaveHis = new CUsr_Mem_Mod();
    $lSaveHis -> saveHis($lOld, $lNew, $lUserId, $lUsr->getId());
    $this -> redirect('index.php?act='.$this -> mClass.'-'.$this -> mSubclass.'&id='.$lUserId);
  }
}