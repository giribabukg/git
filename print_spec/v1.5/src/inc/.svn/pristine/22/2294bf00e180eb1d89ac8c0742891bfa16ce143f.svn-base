<?php
class CInc_Usr_His_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('usr.menu').' '.lan('lib.history');
    $this -> mReq -> expect('id');
    
    // Ask If user has right for this page
    $lpn = 'usr-his';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdRead);
    }
  }

  protected function actStd() {
    $lUid = $this -> getInt('id');

    $lMen = new CUsr_Menu($lUid, 'his');
    $lVie = new CUsr_His_List($lUid);

    $this -> render(CHtm_Wrap::wrap($lMen, $lVie));
  }

  protected function actNew() {
    $lUid = $this -> mReq -> getInt('id');
    $lVie = new CUsr_His_Form_Base('usr-his.snew', lan('lib.msg.new'), 'usr-his&id='.$lUid);
    $lVie -> setParam('id', $lUid);

    $lMen = new CUsr_Menu($lUid, 'his');
    $this -> render(CHtm_Wrap::wrap($lMen, $lVie));
  }

  protected function actSnew() {
    $lUid = $this -> mReq -> getInt('id');
    $lMod = new CUsr_His_Mod();
    $lMod -> getPost($this -> mReq, FALSE);
    $lMod -> insert();
    $this -> redirect('index.php?act=usr-his&id='.$lUid);
  }
}