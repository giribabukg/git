<?php
class CInc_Crp_Ddl_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('lib.status');
    $this -> mMmKey = 'opt'; // Highlight von Hauptmenu : Options

    $lpn = 'crp';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdNone);
    }
  }

  protected function actStd() {
    $lCrp = $this -> getReqInt('id');
    $lVal = array();
    $lVal['post_crp_id']        = $this -> getReq('post_crp_id');
    $lVal['post_ddl_value']     = $this -> getReq('post_ddl_value');
    $lVal['post_ddl_status_id'] = $this -> getReq('post_ddl_status_id');

    $lMen = new CCrp_Menu($lCrp, 'ddl');
    $lVie = new CCrp_Ddl_List($lCrp, $lVal);
    if ($lCrp == $lVal['post_crp_id'] AND 0 < $lCrp) {
      $lVie -> insertData();
    }
    $this -> render(CHtm_Wrap::wrap($lMen,$lVie));
  }

}