<?php
class CInc_Usr_External_Cnt extends CCor_Cnt {
  

  public function __construct(ICor_Req $aReq, $aMod, $aAct, $aMid = NULL) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('usr-external.menu');
  
    // Ask If user has right for this page
    $lpn = 'usr-external';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdRead);
    }
  }
  
  protected function actStd() {
    $lVie = new CUsr_external_List();
    $this -> render($lVie);
  }
 
  protected function actDel() {
    $lUid = CCor_Usr::getAuthId();
    $lId = $this -> getInt('id');
    $lDelUserEmail =  CCor_Qry::getStr('SELECT email FROM al_usr_tmp_external WHERE id='.$lId);
    $lMod = new CUsr_External_Mod();
    //Added to admin history if user rejected/ deleted
    $this -> extUsrHis ($lUid, $lUid, date("Y-m-d", time()), $lDelUserEmail, lan('external.user.rejected'));
    $lMod -> delete($lId);
    $this -> redirect();
  }
  
  protected function actInActiveDelSel() {
    $lIds = $this -> getVal('ids');
    $lIdsArr = explode(",",$lIds);
    if(empty($lIds)){
      $this -> redirect();
    }
    $lUid = CCor_Usr::getAuthId();
    $lQry = new CCor_Qry('SELECT email FROM al_usr_tmp_external WHERE id IN ('.$lIds.')');
    foreach ($lQry as $lRow) {
      $lDelUsersEmail[] = $lRow['email'];
     }
    //Added to admin history if user rejected/ deleted
    $this -> extUsrHis ($lUid, $lUid, date("Y-m-d", time()), implode(', ', $lDelUsersEmail), lan('external.user.rejected'));
    $lSql = 'DELETE FROM al_usr_tmp_external WHERE id IN ('.$lIds.')';
    CCor_Qry::exec($lSql);
    $this -> redirect();
  }
  
/*   public function actEdt() {
    $lId = $this -> getInt('id');
    $lFrm = new CUsr_External_Form('usr-external.sedt', lan('usr-external.edt'));
    $lFrm -> load($lId);
    $this -> render($lFrm);
  }
  
  public function actSedt() {
    $lMod = new CUsr_External_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> update();
    $this -> redirect('index.php?act=usr-external');
  } */
  
  protected function actConExtUsrSelected(){
    $lIds = $this -> getVal('ids');
    $lIdsArr = explode(",",$lIds);
    
    if(empty($lIds)){
      $this -> redirect();
    }
   
    //Password creation
    list($lLength) =  array_values(CCor_Cfg::get('hom-pwd.conditions'));
    $lPwd = CApp_Pwd::createPassword($lLength);
    $lEnc = CApp_Pwd::encryptPassword($lPwd);
    
    $lAllClients = CCor_Res::extract('id', 'name_'.LAN, 'mand');
    $lCurrentClient = (isset($lAllClients[MID])) ? $lAllClients[MID] : CUSTOMER_NAME;

    $lUsr = CCor_Usr::getInstance();
    $lCurrentTime = date("Y-m-d", time());
    
    foreach ($lIdsArr as $lId){
     $lQry = new CCor_Qry('SELECT * FROM al_usr_tmp_external WHERE id='.$lId);
     $lRow = $lQry -> getAssoc();
     $lCompany = $this -> getCompanyName($lRow['email'], $lRow['gru']);

     $lSql = 'INSERT INTO al_usr SET ';
     $lSql .= '`anrede`=' . esc($lRow['anrede']) . ',';
     $lSql .= '`firstname`=' . esc($lRow['firstname']) . ',';
     $lSql .= '`lastname`=' . esc($lRow['lastname']) . ',';
     $lSql .= '`user`=' . esc($lRow['user']) . ',';
     $lSql .= '`email`=' . esc($lRow['email']) . ',';
     $lSql .= '`inviteremail`=' . esc($lRow['inviteremail']) . ',';
     $lSql .= '`phone`=' . esc($lRow['phone']) . ',';
     $lSql .= '`pass`=' . esc($lEnc) . ',';
     $lSql .= '`password_disable`=' . '"Y"' . ',';
     $lSql .= '`lastreset_password`=' . esc($lCurrentTime) . ',';
     $lSql .= '`mand`=' . esc($lRow['mand']) . ',';
     $lSql .= 'created=NOW();';
     CCor_Qry::exec($lSql);
     CCor_Cache::clearStatic('cor_res_usr');
     
     $lNam = cat($lRow['firstname'], $lRow['lastname']);
     $aLink = CCor_Cfg::get('base.url');
     //Mail to external user with user name/ password
     $lSendEmail = $this -> EmailSend('ext.usr.pwd', $aFrom = 'admin@5flow.eu', $aFromName = 'Administrator', $aTo = $lRow['email'], $aToName = $lNam, $aLink = $aLink, $aAnrede = $lRow['anrede'],$aUser=$lRow['user'],$aPass=$lPwd,$aClient= $lCurrentClient);      
     
     //Mand table update
     $lUserEmail = $lRow['email'];
     $lQry = new CCor_Qry('SELECT id,mand FROM al_usr WHERE email="'.$lUserEmail.'"');
     $lRow1 = $lQry -> getAssoc();
     CCor_Qry::exec('INSERT INTO al_usr_mand SET uid='.$lRow1['id'].', mand='.$lRow1['mand']);
     
     //Membership update
     $this -> grantMem($aUserId= $lRow1['id'], $aGrpId = $lRow['gru']);
     //Delete cache
     CCor_Cache::clearStatic('cor_res_mem_' . MID);
     CCor_Cache::clearStatic('cor_res_mem_' . MID . '_uid');
     CCor_Cache::clearStatic('cor_res_mem_' . MID . '_gid');
     
     //History update invite/ applied/ approve time
     $lInviterName = CCor_Res::extract('firstname', 'id', 'usr', array('email' => $lRow['inviteremail']));
     $this -> extUsrHis ($lRow1['id'], implode(" ",$lInviterName), $lRow['invitedate'], lan('ext.usr.inviter'));
     $this -> extUsrHis ($lRow1['id'], $lRow1['id'], $lRow['created'], lan('ext.usr.his.applied'));
     $this -> extUsrHis ($lRow1['id'], $lUsr -> getId(), $lCurrentTime, lan('ext.usr.his.approve'));
     
     //Added to password history
     CApp_Pwd::addedToPassHis($lRow1['id'], $lEnc);
    }
    //After successfull transfer to al_usr then delete user from external temporary table
    $lSql = 'DELETE FROM al_usr_tmp_external WHERE id IN ('.$lIds.')';
    CCor_Qry::exec($lSql);
    $this -> redirect();
  }
   
  protected function EmailSend($aEmailTpl, $aFrom, $aFromName, $aTo, $aToName, $aLink, $aAnrede, $aUser, $aPass, $aClient){
  
    $lEmailTpl = CCor_Cfg::get('tpl.email', array());
    if ( ! empty($lEmailTpl) and isset($lEmailTpl[$aEmailTpl])) {
      $lUsrTpl = $lEmailTpl[$aEmailTpl];
    }
    if ( ! empty($lUsrTpl)) {
      // email send
      $lTpl = new CApp_Tpl();
      if (is_int($lUsrTpl)) {
        $lTpl -> loadTemplate($lUsrTpl);
      } else {
        $lTpl -> loadTemplate(0, $lUsrTpl, LAN);
      }
      $lTpl -> setPat('link', $aLink);
      $lTpl -> setPat('to.anrede', $aAnrede);
      if (in_array($aAnrede, array('Herr','Mr.')))
        $lTpl -> setPat('geehrte', 'geehrter');
      elseif (in_array($aAnrede, array('Frau', 'Mrs.', 'Miss')))
      $lTpl -> setPat('geehrte', 'geehrte');
      else
        $lTpl -> setPat('geehrte', 'geehrte/r');
      
      $lTpl -> setPat('to.name', $aToName);
      $lTpl -> setPat('to.user', $aUser);
      $lTpl -> setPat('new.pwd', $aPass);
      $lTpl -> setPat('portal_name', $aClient);
      
      $lTpl -> setPat('from.anrede', lan('lib.salutation.value'));
      $lTpl -> setPat('from.firstname', 'Administrator');
      $lTpl -> setPat('from.lastname', 'Team');
      $lTpl -> setPat('from.email', 'info@5flow.eu');
      $lTpl -> setPat('from.phone', '');
      
      $lSub = $lTpl -> getSubject();
      $lBod = $lTpl -> getBody();
      $lTxt = $lTpl -> getContent();
  
      $lMail = new CApi_Mail_Item($aFrom, $aFromName, $aTo, $aToName);
      $lMail -> setSubject($lSub);
      $lMail -> setText($lBod);
      $lMail -> insert(false);
    } else {
      $this -> msg('No/Kein Emailtemplate', mtUser, mlWarn);
    }
  }

  protected function grantMem ($aUserId, $aGroupId) {
    if ($aGroupId==0){
      return TRUE;
    }
    $lGrpIds = array();
    $i = 0;
    while ($aGroupId != 0) {
      $lGrpIds[$i] = $aGroupId;
      $lQry = new CCor_Qry('SELECT parent_id FROM al_gru WHERE id = ' . $aGroupId .' ');
      foreach ($lQry as $lRow) {
        $aGroupId = $lRow['parent_id'];
      }
      $i++ ;
    }
    $lGrpIdFMem = implode(',', array_values($lGrpIds));
    $lQry -> query('SELECT id, mand, parent_id FROM al_gru WHERE id IN(' . $lGrpIdFMem . ');');
    foreach ($lQry as $lRow) {
      $lGroupId = $lRow['id'];
      $lGroupMandator = $lRow['mand'];
      $lGroupParentId = $lRow['parent_id'];
      CCor_Qry::exec('INSERT INTO al_usr_mem (uid,gid,mand) VALUES (' . $aUserId . ',' .$lGroupId . ',' . $lGroupMandator . ');');
    }
  }

  protected function getCompanyName ($aUserEmail, $aUserGroupId) {
    $lDomain = array_pop(explode('@', $aUserEmail));
    $lQry = new CCor_Qry('SELECT * FROM al_gru WHERE comp_dom like "%'.$lDomain.'%" AND id='.$aUserGroupId);
    $lRow = $lQry -> getAssoc();
    $lCompany = $lRow ? $lRow['comp_name']: '';
    return $lCompany;
  }

  public function extUsrHis ($aUid, $aUser_id, $aDatum, $aSubject, $aMsg=NULL) {
    $lSql = 'INSERT INTO al_usr_his SET ';
    $lSql .= 'uid=' . $aUid . ',';
    $lSql .= 'user_id="' . $aUser_id . '",';
    $lSql .= 'datum="' . $aDatum . '",';
    $lSql .= 'typ=1,';
    $lSql .= 'subject="' . $aSubject . '",';
    $lSql .= 'msg="' . $aMsg . '"';
    CCor_Qry::exec($lSql);
  }
}