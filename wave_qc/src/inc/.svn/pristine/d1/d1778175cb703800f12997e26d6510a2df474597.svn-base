<?php
class CInc_Usr_Fil_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('job-filt');
    $this -> mReq -> expect('id');
    
    // Ask If user has right for this page
    $lpn = 'usr-fil';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdRead);
    }
  }

  protected function actStd() {
    $lUid = $this -> mReq -> getInt('id');

    $lMen = new CUsr_Menu($lUid, 'fil');
    $lVie = new CUsr_Fil_Form($lUid);

    $this -> render(CHtm_Wrap::wrap($lMen, $lVie));
  }

  protected function actSedt() {
    $lUid = $this -> getInt('id');
    $lVal = $this -> getReq('val');
    $lVal = serialize($lVal);
    $lSql = 'UPDATE al_usr SET fil='.esc($lVal).' WHERE id='.$lUid;
    CCor_Qry::exec($lSql);
    $this -> redirect('index.php?act=usr-fil&id='.$lUid);
  }

}