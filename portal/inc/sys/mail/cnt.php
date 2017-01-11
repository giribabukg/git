<?php
class CInc_Sys_Mail_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('sys-mail.menu');
    $this -> mMmKey = 'opt';

    // Ask If user has right for this page
    $lpn = 'log';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdNone);
    }
  }

  protected function actStd() {
    $lVie = new CSys_Mail_List();
    $this -> render($lVie);
  }

  protected function actDel() {
    $lId = $this -> getReqInt('id');
    CCor_Qry::exec('DELETE FROM al_sys_mails WHERE id='.$lId);
    $this -> redirect();
  }

  protected function actDelokay() {
    CCor_Qry::exec('DELETE FROM al_sys_mails WHERE mail_state=1');
    $this -> redirect();
  }

  protected function actDelall() {
    CCor_Qry::exec('DELETE FROM al_sys_mails where mand='.MID);
    $this -> redirect();
  }

  protected function actResend() {
    $lto = $this -> mReq -> getVal('mailto');
    $lsv = ($this -> mReq -> getInt('save') == 1);
    $lMail = CApi_Mail_Resend::getInstance($lto, $lsv);
    $lMail -> send();
    $this -> redirect();
  }

  protected function actDailyMail() {
    $lto = $this -> mReq -> getVal('mailto');
    $lusr = $this -> mReq -> getVal('usr');
    $lsv = ($this -> mReq -> getInt('save') == 1);
    $lMail = CApi_Mail_Daily::getInstance($lto, $lsv);
    $lMail -> generate($lusr);
    $this -> redirect();
  }

}