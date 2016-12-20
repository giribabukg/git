<?php
class CInc_Gru_His_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('gru.menu').' '.lan('lib.history');
    $this -> mMmKey = 'usr';
    
    $lpn = 'gru';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdNone);
    }
  }
  
  protected function actStd() {
    $lUid = $this -> getInt('id');
    $lMen = new CGru_Menu($lUid, 'his');
    $lVie = new CGru_His_List($lUid);
  
    $this -> render(CHtm_Wrap::wrap($lMen, $lVie));
  }
  
  protected function actNew() {
    $lUid = $this -> mReq -> getInt('id');
    $lVie = new CGru_His_Form_Base('gru-his.snew', lan('lib.msg.new'), 'gru-his&id='.$lUid);
    $lVie -> setParam('id', $lUid);
  
    $lMen = new CGru_Menu($lUid, 'his');
    $this -> render(CHtm_Wrap::wrap($lMen, $lVie));
  }
  
  protected function actSnew() {
    $lUid = $this -> mReq -> getInt('id');
    $lMod = new CGru_His_Mod();
    $lMod -> getPost($this -> mReq, FALSE);
    $lMod -> insert();
    $this -> redirect('index.php?act=gru-his&id='.$lUid);
  }

  
}