<?php
class CInc_Usg_Opt_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);

    $this -> mTitle = lan('usr.menu');
    $this -> m2Act = 'usg';

    // Ask If user has right for this page
    $lpn = 'usg';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdRead);
    }

    //Get Mail TPL for sending a new Password
    $this -> mEmailTemplate = CCor_Cfg::get('tpl.email');
    if(!isset($this -> mEmailTemplate['pwd'])) {
      $this -> mEmailTemplate['pwd'] = "27";
    }
  }

  protected function actStd() {

    $lUid = $this -> getInt('id');
    $lMen = new CUsg_Menu($lUid, 'opt', $this -> m2Act);
    $lVie = new CUsg_Opt_Menu($lUid);
    $this -> render(CHtm_Wrap::wrap($lMen, $lVie));
  }

  protected function actUsr() {
    $lNewUsr = $this -> getVal('usr');
    $lUid = $this -> getInt('id');

    $lQry = new CCor_Qry('SELECT id FROM al_usr WHERE user="'.$lNewUsr.'"');
    if ($lRow = $lQry -> getAssoc()) {
      if ($lUid != $lRow['id']) {
        CCor_Msg::add(lan('hom.usr.error.inuse'), mtUser, mlError);
        $this -> redirect();
      }
    }

    if ($lNewUsr != 'null') {
      $lQry = new CCor_Qry('SELECT * FROM al_usr WHERE id='.$lUid);
      if (!$lRow = $lQry -> getDat()) {
        $this -> dbg('Record not found', mlError);
        $this -> redirect('index.php?act=usg');
      }

      $lAllClients = CCor_Res::extract('id', 'name_'.LAN, 'mand');
      $lCurrentClient = (isset($lAllClients[MID])) ? $lAllClients[MID] : CUSTOMER_NAME;
      $lDate = date(lan('lib.date.xxl'), strtotime("+2 week"));

      $lQry -> query('SELECT * FROM al_eve_tpl WHERE id=26');
      $lDat = $lQry -> getDat();

      $lUsr = CCor_Usr::getInstance();

      $lTpl = new CCor_Tpl();
      $lTpl -> setPat('to.user', $lNewUsr);
      $lTpl -> setPat('portal_name', $lCurrentClient);
      $lTpl -> setPat('date', $lDate);
      foreach ($lRow as $lKey => $lVal) {
        if ($lKey != 'user') {
          $lTpl -> setPat('to.'.$lKey, $lVal);
        }
        $lTpl -> setPat('from.'.$lKey, $lUsr -> getVal($lKey));
        if ('anrede' == $lKey) {
          if (in_array($lVal, array('Herr', 'Mr.'))) {
            $lTpl -> setPat('geehrte', 'geehrter');
          } elseif (in_array($lVal, array('Frau', 'Mrs.', 'Miss'))) {
            $lTpl -> setPat('geehrte', 'geehrte');
          } else {
            $lTpl -> setPat('geehrte', 'geehrte/r');
          }
        }
      }

      $lTpl -> setDoc($lDat['subject']);
      $lSub = $lTpl -> getContent();

      $lTpl -> setDoc($lDat['msg']);
      $lTxt = $lTpl -> getContent();
      $lUsr = CCor_Usr::getInstance();
      $lFrmAdr = $lUsr -> getVal('email');
      $lFrmNam = $lUsr -> getVal('first_lastname');
      $lRecAdr = $lRow['email'];
      $lRecNam = $lRow['firstname'].' '.$lRow['lastname'];

      $lMai = new CApi_Mail_Item($lFrmAdr, $lFrmNam, $lRecAdr, $lRecNam, $lSub, $lTxt);
      $lMai -> setSenderID($lUsr -> getVal('id'));
      $lMai -> setReciverId($lRow['id']);
      $lMai -> setMailType(mailSys);
      $lMai -> insert();

      $lQry = new CCor_Qry();
      $lQry -> query('UPDATE al_usr SET user="'.$lNewUsr.'" WHERE id='.$lUid);

      $this -> msg('A new username has been sent', mtUser, mlInfo);

      $lHis = new CUsr_His_Mod();
      $lHis -> setVal('subject', 'New username sent');
      $lHis -> insert();

      $this -> redirect('index.php?act=usg-opt&id='.$lUid);
    } else {
      $this -> redirect('index.php?act=usg-opt&id='.$lUid);
    }
  }

  protected function actPwd() {
    $lUid = $this -> getInt('id');

    $lQry = new CCor_Qry('SELECT * FROM al_usr WHERE id='.$lUid);
    if (!$lRow = $lQry -> getDat()) {
      $this -> dbg('Record not found', mlError);
      $this -> redirect('index.php?act=usg');
    }

    $lPwd = CApp_Pwd::createPassword(8);

    $lArr = CCor_Res::extract('id', 'name_'.LAN, 'mand');
    $lPortalName = (isset($lArr[MID])) ? $lArr[MID] : CUSTOMER_NAME;
    $lDate = date(lan('lib.date.xxl'), strtotime("+2 week"));



    $lQry -> query('SELECT * FROM al_eve_tpl WHERE id='.esc($this -> mEmailTemplate['pwd']));
    $lDat = $lQry -> getDat();

    $lUsr = CCor_Usr::getInstance();

    $lTpl = new CCor_Tpl();
    $lTpl -> setPat('new.pwd', $lPwd);
    $lTpl -> setPat('portal_name', $lPortalName);
    $lTpl -> setPat('date', $lDate);
    foreach ($lRow as $lKey => $lVal) {
      $lTpl -> setPat('to.'.$lKey, $lVal);
      $lTpl -> setPat('from.'.$lKey, $lUsr -> getVal($lKey));
      if ('anrede' == $lKey) {
        if ( in_array($lVal, array('Herr', 'Mr.')) )
          $lTpl -> setPat('geehrte', 'geehrter');
        elseif ( in_array($lVal, array('Frau', 'Mrs.', 'Miss')) )
          $lTpl -> setPat('geehrte', 'geehrte');
        else
          $lTpl -> setPat('geehrte', 'geehrte/r');
      }
    }

    $lTpl -> setDoc($lDat['subject']);
    $lSub = $lTpl -> getContent();
    $lTpl -> setDoc($lDat['msg']);
    $lTxt = $lTpl -> getContent();
    $lUsr = CCor_Usr::getInstance();
    $lFrmAdr = $lUsr -> getVal('email');
    $lFrmNam = $lUsr -> getVal('first_lastname');
    $lRecAdr = $lRow['email'];
    $lRecNam = $lRow['firstname'].' '.$lRow['lastname'];

    $lMai = new CApi_Mail_Item($lFrmAdr, $lFrmNam, $lRecAdr, $lRecNam, $lSub, $lTxt);
    $lMai -> setSenderID($lUsr -> getVal('id'));
    $lMai -> setReciverId($lRow['id']);
    $lMai -> setMailType(mailSys);
    $lMai -> insert();

    $lPwd = CApp_Pwd::encryptPassword($lPwd);

    $lQry = new CCor_Qry();
    $lQry -> query('UPDATE al_usr SET pass="'.$lPwd.'" WHERE id='.$lUid);

    $this -> msg('A new Password has been sent', mtUser, mlInfo);

    $lHis = new CUsg_His_Mod();
    $lHis -> setVal('subject', lan('usg.new-pwd-send'));
    $lHis -> insert();

    $this -> redirect('index.php?act=usg-opt&id='.$lUid);
  }

  protected function actUsrchg() {
    $lUid = $this -> getInt('id');
    $this -> msg('Username change requested', mtUser, mlInfo);
    $lHis = new CUsr_His_Mod();
    $lHis -> setVal('subject', 'Username change requested');
    $lHis -> insert();

    // START
    $lUid = $this -> getInt('id');

    $lQry = new CCor_Qry('SELECT * FROM al_usr WHERE id='.$lUid);
    if (!$lRow = $lQry -> getDat()) {
      $this -> dbg('Record not found', mlError);
      $this -> redirect('index.php?act=usr');
    }

    $lAllClients = CCor_Res::extract('id', 'name_'.LAN, 'mand');
    $lCurrentClient = (isset($lAllClients[MID])) ? $lAllClients[MID] : CUSTOMER_NAME;
    $lDate = date(lan('lib.date.xxl'), strtotime("+2 week"));

    $lQry -> query('SELECT * FROM al_eve_tpl WHERE id=24');
    $lDat = $lQry -> getDat();

    $lUsr = CCor_Usr::getInstance();

    $lTpl = new CCor_Tpl();
    $lTpl -> setPat('portal_name', $lCurrentClient);
    foreach ($lRow as $lKey => $lVal) {
      if ($lKey != 'user') {
        $lTpl -> setPat('to.'.$lKey, $lVal);
      }
      $lTpl -> setPat('from.'.$lKey, $lUsr -> getVal($lKey));
      if ('anrede' == $lKey) {
        if (in_array($lVal, array('Herr', 'Mr.'))) {
          $lTpl -> setPat('geehrte', 'geehrter');
        } elseif (in_array($lVal, array('Frau', 'Mrs.', 'Miss'))) {
          $lTpl -> setPat('geehrte', 'geehrte');
        } else {
          $lTpl -> setPat('geehrte', 'geehrte/r');
        }
      }
    }

    $lTpl -> setDoc($lDat['subject']);
    $lSub = $lTpl -> getContent();

    $lTpl -> setDoc($lDat['msg']);
    $lTxt = $lTpl -> getContent();
    $lUsr = CCor_Usr::getInstance();
    $lFrmAdr = $lUsr -> getVal('email');
    $lFrmNam = $lUsr -> getVal('first_lastname');
    $lRecAdr = $lRow['email'];
    $lRecNam = $lRow['firstname'].' '.$lRow['lastname'];

    $lMai = new CApi_Mail_Item($lFrmAdr, $lFrmNam, $lRecAdr, $lRecNam, $lSub, $lTxt);
    $lMai -> setSenderID($lUsr -> getVal('id'));
    $lMai -> setReciverId($lRow['id']);
    $lMai -> setMailType(mailSys);
    $lMai -> insert();
    // END

    $this -> redirect('index.php?act=usg-opt&id='.$lUid);
  }

  protected function actPwdchg() {
    $lUid = $this -> getInt('id');

    $lQry = new CCor_Qry('SELECT * FROM al_usr WHERE id='.$lUid);
    if (!$lRow = $lQry -> getDat()) {
      $this -> dbg('Record not found', mlError);
      $this -> redirect('index.php?act=usg');
    }

    $lArr = CCor_Res::extract('id', 'name_'.LAN, 'mand');
    $lPortalName = (isset($lArr[MID])) ? $lArr[MID] : CUSTOMER_NAME;

    $lQry -> query('SELECT * FROM al_eve_tpl WHERE id=25');
    $lDat = $lQry -> getDat();

    $lUsr = CCor_Usr::getInstance();

    $lTpl = new CCor_Tpl();
    $lTpl -> setPat('portal_name', $lPortalName);
    foreach ($lRow as $lKey => $lVal) {
      if ($lKey != 'user') {
        $lTpl -> setPat('to.'.$lKey, $lVal);
      }
      $lTpl -> setPat('from.'.$lKey, $lUsr -> getVal($lKey));
      if ('anrede' == $lKey) {
        if ( in_array($lVal, array('Herr', 'Mr.')) )
          $lTpl -> setPat('geehrte', 'geehrter');
        elseif ( in_array($lVal, array('Frau', 'Mrs.', 'Miss')) )
          $lTpl -> setPat('geehrte', 'geehrte');
        else
          $lTpl -> setPat('geehrte', 'geehrte/r');
      }
    }

    $lTpl -> setDoc($lDat['subject']);
    $lSub = $lTpl -> getContent();
    $lTpl -> setDoc($lDat['msg']);
    $lTxt = $lTpl -> getContent();
    $lUsr = CCor_Usr::getInstance();
    $lFrmAdr = $lUsr -> getVal('email');
    $lFrmNam = $lUsr -> getVal('first_lastname');
    $lRecAdr = $lRow['email'];
    $lRecNam = $lRow['firstname'].' '.$lRow['lastname'];

    $lMai = new CApi_Mail_Item($lFrmAdr, $lFrmNam, $lRecAdr, $lRecNam, $lSub, $lTxt);
    $lMai -> setSenderID($lUsr -> getVal('id'));
    $lMai -> setReciverId($lRow['id']);
    $lMai -> setMailType(mailSys);
    $lMai -> insert();

    $this -> msg('Password change requested', mtUser, mlInfo);

    $lHis = new CUsg_His_Mod();
    $lHis -> setVal('subject', lan('usg.pwd_change_req'));
    $lHis -> insert();

    $this -> redirect('index.php?act=usg-opt&id='.$lUid);
  }

}