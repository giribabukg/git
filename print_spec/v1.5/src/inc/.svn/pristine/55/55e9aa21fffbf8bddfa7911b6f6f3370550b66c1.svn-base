<?php
class CInc_Mnd_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);

    $this -> mTitle = lan('mnd.menu');

    // Ask If user has right for this page
    $lpn = 'mnd';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdRead);
    }
  }

  protected function actStd() {
    $lVie = new CMnd_List();
    $this -> render($lVie);
  }

  protected function actEdt() {
    $lId = $this -> getInt('id');
    $lFrm = new CMnd_Form('mnd.sedt', lan('mnd.edt'));
    $lFrm -> load($lId);
    $this -> render($lFrm);
  }

  protected function actSedt() {
    $lMod = new CMnd_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> update();
    $this -> redirect('index.php?act=mnd');
  }

  protected function actNew() {
    $lFrm = new CMnd_Form('mnd.snew', lan('lib.neumnd'));
    $this -> render($lFrm);
  }

  protected function actSnew() {
    $lMod = new CMnd_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> insert();
    $this -> redirect('index.php?act=mnd');
  }

  protected function actLogin() {
    $lId = $this -> getVal('id');
    if (MID == $lId) {
      $lQry = new CApi_Alink_Query('login');
      $lQry -> addParam('sid', MAND);
      $lQry -> addParam('user', MANDATOR_NAME);
      $lQry -> addParam('pass', MAND);
      $lRes = $lQry -> query();
      $lErrNo = $lRes -> getVal('errno');
      if (0 == $lErrNo) {
        $this -> msg('Login okay');
      } else {
        $this -> msg('Login error: '.$lRes -> getVal('errmsg'), mtUser, mlError);
      }
    }

    $this -> redirect();
  }
  /**
   * @method disable
   * @author pdohmen
   * @description Function for disabling the login for specific Mand
   */
  protected function actDisable() {
    $lUsr = CCor_Usr::getInstance();
    if($lUsr->canEdit("mnd-dis")) {
      $lId = $this -> getInt('id');
      //Set Disable Option in DB
      $qry = "UPDATE `al_sys_mand` SET `disabled`='Y' WHERE  `id`=".$lId.";";
      CCor_Qry::exec($qry);
      $qry = "UPDATE `al_usr` SET `disabled`='Y' WHERE `mand` = ".$lId.";";
      CCor_Qry::exec($qry);
      //Kill session from all users logged on this mand
      $lSql = "SELECT uid FROM al_usr_pref WHERE code = 'sys.mid' AND val ='".$lId."';";
      $lQry = new CCor_Qry($lSql);

      foreach($lQry as $lRow) {
        //Kill current logged in sessions
        CCor_Ses::killSession($lRow["uid"]);
      }

      $log = new CCor_Log();
      $log->log("Login was disabled for Mand ".$lId,1,1);
    }
    $this->redirect();
  }
    /**
   * @method enable
   * @author pdohmen
   * @description Function for enabling the login for specific Mand
   */
  protected function actEnable() {
    $lUsr = CCor_Usr::getInstance();
    if($lUsr->canEdit("mnd-dis")) {
      $lId = $this -> getInt('id');
      $qry = "UPDATE `al_sys_mand` SET `disabled`='N' WHERE  `id`=".$lId.";";
      CCor_Qry::exec($qry);
      $qry = "UPDATE `al_usr` SET `disabled`='N' WHERE `mand` = ".$lId.";";
      CCor_Qry::exec($qry);

      $log = new CCor_Log();
      $log->log("Login was enabled for Mand ".$lId,1,1);
    }
    $this->redirect();
  }
  /**
   * @method EnableAll
   * @author pdohmen
   * @description Enables all Mandants
   */
  protected function actEnableAll() {
    $lUsr = CCor_Usr::getInstance();
    if($lUsr->canEdit("mnd-dis")) {
      $lId = $this -> getInt('id');
      $qry = "UPDATE `al_sys_mand` SET `disabled`='N';";
      CCor_Qry::exec($qry);
      $qry = "UPDATE `al_usr` SET `disabled`='N';";
      CCor_Qry::exec($qry);

      $log = new CCor_Log();
      $log->log("Login was enabled for all Mandants",1,1);
    }
    $this->redirect();
  }
    /**
   * @method disableAll
   * @author pdohmen
   * @description Function for disabling the login for all mands
   */
  protected function actDisableAll() {
    $lUsr = CCor_Usr::getInstance();
    if($lUsr->canEdit("mnd-dis")) {
      $lId = $this -> getInt('id');
      //Set Disable Option in DB
      $qry = "UPDATE `al_sys_mand` SET `disabled`='Y';";
      CCor_Qry::exec($qry);
      $qry = "UPDATE `al_usr` SET `disabled`='Y';";
      CCor_Qry::exec($qry);
      //Kill session from all users logged on this mand
      $lSql = "SELECT uid FROM al_usr_pref WHERE code = 'sys.mid';";
      $lQry = new CCor_Qry($lSql);

      foreach($lQry as $lRow) {
        //Kill current logged in sessions
        CCor_Ses::killSession($lRow["uid"]);
      }

      $log = new CCor_Log();
      $log->log("Login was disabled for all Mandants",1,1);
    }
    $this->redirect();
  }
}