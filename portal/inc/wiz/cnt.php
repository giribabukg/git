<?php
class CInc_Wiz_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('wiz.menu');
    
    // Ask If user has right for this page
    $lpn = 'wiz';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdRead);
    }
  }

  protected function actStd() {
    $lVie = new CWiz_List();
    $this -> render($lVie);
  }

  protected function actNew() {
    $lFrm = new CWiz_Form('wiz.snew', lan('lib.neuwiz'));
    $this -> render($lFrm);
  }

  protected function actSnew() {
    $this -> dbg('okay');
    $lMod = new CWiz_Mod();
    $lMod -> getPost($this -> mReq);
    if ($lMod -> insert()) {
      $lId = $lMod -> getInsertId();
      $this -> redirect('index.php?act=wiz-itm&id='.$lId);
    }
    $this -> redirect();
  }

  protected function actEdt() {
    $lId = $this -> getInt('id');

    $lFrm = new CWiz_Form('wiz.sedt', lan('wiz.edt'));
    $lFrm -> load($lId);

    $lMen = new CWiz_Menu($lId, 'dat');
    $this -> render(CHtm_Wrap::wrap($lMen, $lFrm));
  }

  protected function actSedt() {
    $lMod = new CWiz_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> update();
    $this -> redirect();
  }

  protected function actDel() {
    $lId = $this -> getInt('id');
    $lMod = new CWiz_Mod();
    $lMod -> delete($lId);
    $this -> redirect();
  }

}