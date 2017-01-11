<?php
class CInc_Sys_Log_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('sys-log.menu');
    $this -> mMmKey = 'opt';

    // Ask If user has right for this page
    $lpn = 'log';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdNone);
    }
  }

  protected function actStd() {
    $lVie = new CSys_Log_List();
    $this -> render($lVie);
  }

  protected function actDel() {
    $lId = $this -> getReqInt('id');
    CCor_Qry::exec('DELETE FROM al_sys_log WHERE id='.$lId);
    $this -> redirect();
  }

  protected function actTruncate() {
    CCor_Qry::exec('TRUNCATE al_sys_log');
    $this -> actClser();
    $this -> redirect();
  }

}