<?php
class CInc_Tpl_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('tpl.menu');
    $this -> mMmKey = 'opt';

    // Ask If user has right for this page
    $lpn = 'tpl';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdRead);
    }
  }

  public function actStd() {
    $lVie = new CTpl_List();
    $this -> render($lVie);
  }

  public function actEdt() {
    $lId = $this -> getInt('id');
    $lFrm = new CTpl_Form('tpl.sedt', lan('tpl.edt'));
    $lFrm -> load($lId);
    $this -> render($lFrm);
  }

  public function actSedt() {
    $lMod = new CTpl_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> update();
    $this -> redirect('index.php?act=tpl');
  }

  public function actNew() {
    $lFrm = new CTpl_Form('tpl.snew', lan('lib.neutpl'));
    $this -> render($lFrm);
  }

  public function actSnew() {
    $lMod = new CTpl_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> insert();
    $this -> redirect('index.php?act=tpl');
  }

  public function actDel() {
    $lId = $this -> getReqInt('id');
    $lMod = new CTpl_Mod();
    $lMod -> delete($lId);
    $this -> redirect();
  }

}