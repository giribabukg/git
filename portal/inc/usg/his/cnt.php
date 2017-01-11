<?php
class CInc_Usg_His_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);

    $this -> m2Act = 'usg';
    $this -> mTitle = lan('usg.menu').' '.lan('lib.history');
    $this -> mReq -> expect('id');
    
    // Ask If user has right for this page
    $lpn = 'usg';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdRead);
    }
  }

  protected function actStd() {
    $lUid = $this -> getInt('id');

    $lMen = new CUsg_Menu($lUid, 'his', $this -> m2Act);
    $lVie = new CUsg_His_List($lUid);

    $this -> render(CHtm_Wrap::wrap($lMen, $lVie));
  }

  protected function actNew() {
    $lUid = $this -> mReq -> getInt('id');
    $lVie = new CUsr_His_Form_Base($this -> m2Act.'-his.snew', lan('lib.msg.new'), $this -> m2Act.'-his&id='.$lUid);
    $lVie -> setParam('id', $lUid);

    $lMen = new CUsg_Menu($lUid, 'his', $this -> m2Act);
    $this -> render(CHtm_Wrap::wrap($lMen, $lVie));
  }

  protected function actSnew() {
    $lUid = $this -> mReq -> getInt('id');
    $lMod = new CUsr_His_Mod();
    $lMod -> getPost($this -> mReq, FALSE);
    $lMod -> insert();
    $this -> redirect('index.php?act='.$this -> m2Act.'-his&id='.$lUid);
  }

}