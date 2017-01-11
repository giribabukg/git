<?php
class CInc_Gru_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('gru.menu');
    $this -> mMmKey = 'usr';
    $lpn = 'gru';
    
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdNone);
    }
  }

  protected function actStd() {
    if(CCor_Cfg::get("ajxGroupList")) {
      $lAjxList = 0;
    }
    else {
      $lAjxList = -1;
    }
    $lVie = new CGru_List($lAjxList);
    $this -> render($lVie);
  }

  protected function actEdt() {
    $lGid = $this -> getInt('id');
    //darf nur die Gruppen aus aktuellem Mandant und "Mandant=0" editiert werden.
    $lNam = CCor_Qry::getInt('SELECT id FROM al_gru WHERE id='.$lGid.' AND mand IN (0,'.MID.')');
    if (!$lNam){
      $this -> redirect();
    }
    $lMen = new CGru_Menu($lGid, 'dat');
    $lVie = new CGru_Form_Edit($lGid);
    $lVie -> setParam('id', $lGid);
    $this -> render(CHtm_Wrap::wrap($lMen,$lVie));
}

  protected function actSedt() {
    $lGid = $this -> getInt('id');
    $lMod = new CGru_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> update();
    $lMod -> writeHis();
    CCor_Cache::clearStatic('cor_res_gru_'.MID);
    $this -> redirect('index.php?act=gru.edt&id='.$lGid);
  }

  protected function actNew() {
    $lPid = $this -> getReqInt('pid');
    $lVie = new CGru_Form_Base('gru.snew', lan('gru.new'), NULL, $lPid);
    $this -> render($lVie);
  }

  protected function actNewExt() {
    $lPid = $this -> getReqInt('pid');
    $lVie = new CGru_Form_Base('gru.snew', lan('gru.newExt'), NULL, $lPid);
    $lVie->setExternal();
    $this -> render($lVie);
  }

  protected function actInvExt() {
    $lUid = $this->getReqInt('id');
    $lGruId = $this->getReqInt('gru');
    $lMen = new CGru_Menu($lUid, 'dat');
    $lVie = new CGru_Invite_Form('gru.sendInv', lan('inv.usr'), $lGruId);
    $this->render(CHtm_Wrap::wrap($lMen, $lVie));
  }

  protected function actSendInv() {
    $lVal = $this -> mReq -> getVal('val');
    $lGruId = $lVal['gruID'];
    $lGru = CCor_Res::get('gru', array('id'=>$lGruId));
 
    $lUsr = CCor_Usr::getInstance();
    $lAllClients = CCor_Res::extract('id', 'name_' . LAN, 'mand');
    $lCurrentClient = (isset($lAllClients[MID])) ? $lAllClients[MID] : CUSTOMER_NAME;
    $lMails = preg_split("/[;,:]+/", trim($lVal['to_emails']));
    //Prevent inserting empty row
    if (in_array(null, $lMails)) {
       $this -> redirect('index.php?act=gru.invExt&gru='.$lGruId);
    } 
    foreach ($lMails as $lRow) {
      //$lEmails= CApp_Pwd::EnDecryptor('encrypt','uemail='.$lRow.'&invemail='.$lUsr->getVal('email'));
      $lData = http_build_query (array('uemail'=> $lRow, 'invemail'=> $lUsr -> getVal('email')));
      $lEmails= CApp_Pwd::EnDecryptor('encrypt', urldecode($lData));
      
      //Send User to Tmp Table
      $lDate = date('Y-m-d');
      $lSQl = "INSERT INTO `al_usr_tmp_external` (`email`, `inviteremail`, `gru`, `invitedate`, `mand`) VALUES ('".$lRow."', '".$lUsr->getVal('email')."', '".$lGruId."', '".$lDate."', '".MID."');";
      CCor_Qry::exec($lSQl);

      //Send Invitation Email
      $lTpl = new CApp_Tpl();
      $lTpl -> loadTemplate(CCor_Cfg::get('invitation-tpl', 0));
      $lTpl -> setPat('portal_name', $lCurrentClient);
      $lTpl -> setPat('from.firstname', $lUsr -> getVal('firstname'));
      $lTpl -> setPat('from.lastname', $lUsr -> getVal('lastname'));
      $lTpl -> setPat('from.anrede', $lUsr -> getVal('anrede'));
      $lTpl -> setPat('from.email', $lUsr -> getVal('email'));
      $lTpl -> setPat('from.company', $lUsr -> getVal('company'));
      $lTpl -> setPat('from.phone', $lUsr -> getVal('phone'));
      $lTpl -> setPat('link', CCor_Cfg::get("base.url", "") . 'index.php?act=log.externaluser&data='.$lEmails);

      $lSubject = $lTpl -> getSubject();
      $lBody = $lTpl -> getBody();

      $lMail = new CApi_Mail_Item($lUsr -> getVal('email'), $lUsr -> getFullName(), $lRow, $lRow);
      $lMail -> setSubject($lSubject);
      $lMail -> setText($lBody);
      $lMail -> insert();
    }

    $this -> redirect();
  }

  protected function actSnew() {
    $lUsr = CCor_Usr::getInstance();
    $lUid = $lUsr -> getId();
    $lTyp = $this -> mReq -> val['typ'];
    $lGroupName = $this -> mReq -> val['name'];
    
    $lMod = new CGru_Mod();
    $lMod -> getPost($this -> mReq);

    if ($lMod -> insert()) {
      $lId = $lMod -> getInsertId();
      $lMod -> saveGruHis($lUid, $lId, date("Y-m-d"), 14, "Added group ($lGroupName)",'');
      CCor_Cache::clearStatic('cor_res_gru_'.MID);

      //Create Condition for External Groups
      if($lTyp === 'ext') {
        //Get Parent Group
        $lParent = $lMod->getVal('parent_id');
        $lName = $lMod->getVal('name');
        $lPrefix = CCor_Cfg::get('Ext.Group.Prefix');
        $lQry = new CCor_Qry();

        //Get User Selection Field from this group
        $lRes = new CCor_Res_Fie();
        $lFie = $lRes->get(array('param' => 'a:1:{s:3:"gid";s:2:"'.$lParent.'";}'));
        $lFie = $lFie[key($lFie)];

        //Create Condition
        //1. Update al_cnd_master
        $lCnd = '(('.$lFie['alias'].' = "'.$lId.'"))';
        $lSql = 'INSERT INTO `al_cnd_master` (`mand`, `name`, `flags`, `aliased`, `natived`) VALUES ('.MID.', '.esc($lPrefix.$lName).', 7, '.esc($lCnd).', '.esc($lCnd).');';
        $lQry->exec($lSql);
        //2. Update al_cnd_items
        $lMasterId = $lQry->getInsertId();
        $lSql = 'INSERT INTO `al_cnd_items` (`cnd_id`, `field`, `operator`, `value`, `conjunction`) VALUES ('.$lMasterId.', '.esc($lFie['alias']).', \'op_equals\', '.$lId.', \'\');';
        $lQry->exec($lSql);
        //3. Update al_cnd
        $lSql = 'INSERT INTO `al_cnd` (`mand`, `grp_id`, `cnd_id`) VALUES (\''.MID.'\', \''.$lId.'\', \''.$lMasterId.'\');';
        $lQry->exec($lSql);
        //4. Update al_gru
        $lCndId = $lQry->getInsertId();
        $lSql = 'UPDATE `al_gru` SET `cnd`='.$lCndId.', `procnd`='.$lMasterId.' WHERE  `id`='.$lId.';';
        $lQry->exec($lSql);

        $lMod -> saveGruHis($lUid, $lId, date("Y-m-d"), 14, 'Condition '. esc($lPrefix.$lName) . ' was automaticly created for this group.','');
      }
      $this -> redirect('index.php?act=gru.edt&id='.$lId);
    }
    $this -> redirect();
  }

  protected function actDel() {
    $lUsr = CCor_Usr::getInstance();
    $lGid = $this -> getReqInt('id');
    //Group deactivate
    CCor_Qry::exec('UPDATE al_gru SET del="Y" WHERE mand IN(0,'.MID.') AND id='.$lGid);
    //Added to history
    $lGruHisSave = CGru_Mod::saveGruHis($lUsr -> getId(), $lGid, date("Y-m-d"), $aTyp=14, $aSubject="Group deactivated", $aMsg='');
    CCor_Cache::clearStatic('cor_res_gru_'.MID);
    $this -> redirect();
  }
  
  protected function actReact() {
    $lUsr = CCor_Usr::getInstance();
    $lGid = $this -> getReqInt('id');
    //Group activate
    CCor_Qry::exec('UPDATE al_gru SET del="N" WHERE mand IN(0,'.MID.') AND id='.$lGid);
    //Added to history
    $lGruHisSave = CGru_Mod::saveGruHis($lUsr -> getId(), $lGid, date("Y-m-d"), $aTyp=14, $aSubject="Group reactivated", $aMsg='');
    CCor_Cache::clearStatic('cor_res_gru_'.MID);
    $this -> redirect();
  }
  
  protected function actcheckUsrAvail () {
    $lVal = $this -> getReq('val');
    if (in_array(null, $lVal)) {
      echo lan('usr.error.email');
      exit();
    }
    $lUsrList = preg_split("/[;,:]+/", trim($lVal['usr']));
    foreach ($lUsrList as $lRow) {
      $lEmailValidation = isValidEmail($lRow);
      if ($lEmailValidation == false) {
        $lEmailError[] = $lRow;
      }
      $lSql = 'SELECT email FROM al_usr WHERE email='.esc($lRow);
      $lSql .= ' union ';
      $lSql .= 'select email from al_usr_tmp_external WHERE email="' . $lRow . '"';
      $lQry = CCor_Qry::getStr($lSql);
      if ($lQry) {
        $lNotAvailUsersEmail[] = $lRow;
      }
    }
    if ($lEmailError) {
      echo lan('ext.gru.email.invalid').":\n".implode("\n", $lEmailError). "\n";
    }
    if ($lNotAvailUsersEmail) {
      echo lan('ext.gru.email.used').":\n".implode("\n", $lNotAvailUsersEmail);
    }
  }
}