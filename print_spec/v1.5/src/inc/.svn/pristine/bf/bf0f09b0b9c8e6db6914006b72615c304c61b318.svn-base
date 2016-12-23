<?php
class CInc_Sys_Sql_Cnt extends CCor_Cnt {
  
  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = 'SQL';
    // Ask If user has right for this page
    $lpn = 'log';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdNone);
    }
  }
  
  protected function actStd() {
    $lVie = new CSys_Sql_List();
    $this -> render($lVie);
  }

}