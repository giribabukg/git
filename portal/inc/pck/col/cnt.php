<?php
class CInc_Pck_Col_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('pck-col.menu');
    $this -> mDom = $this -> getReq('dom');
    $lpn = 'pck';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdNone);
    }
  }

  protected function actStd() {
    $lMen = new CPck_Menu($this -> mDom, 'col');
    $lVie = new CPck_Col_List($this -> mDom);
    $this -> render(CHtm_Wrap::wrap($lMen, $lVie));
  }

  protected function actEdt() {
    $lId  = $this -> getReq('id');
    $lVie = new CPck_Col_Form_Edit($lId, $this -> mDom);
    $lMen = new CPck_Menu($this -> mDom , 'col');
    $this -> render(CHtm_Wrap::wrap($lMen, $lVie));
}

  protected function actSedt() {
    $lMod = new CPck_Col_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> update();
    $this -> redirect('index.php?act=pck-col&dom='.$this -> mDom);
  }

  protected function actNew() {
    $lId = $this -> getReqInt('id');
    $lMen = new CPck_Menu($this -> mDom, 'col');
    $lVie = new CPck_Col_Form_Base('pck-col.snew', $this -> mDom, lan('pck-col.new'), 'pck-col&dom='.$this -> mDom);
    $lVie -> setDom($this -> mDom);
    $this -> render(CHtm_Wrap::wrap($lMen, $lVie));
  }

  protected function actSnew() {
    $lMod = new CPck_Col_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> insert();
    $this -> redirect('index.php?act=pck-col&dom='.$this -> mDom);
   }

  protected function actDel() {
    $lId = $this -> getReqInt('id');
    $lSql = 'DELETE FROM al_pck_columns WHERE id='.$lId.' AND domain='.esc($this -> mDom).' AND mand='.MID;
    CCor_Qry::exec($lSql);
    $this -> redirect('index.php?act=pck-col&dom='.$this -> mDom);
  }

}