<?php
class CInc_Wiz_Itm_Cnt extends CCor_Cnt {
  
  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('wiz-itm.menu');
    $this -> mWiz = $this -> getReq('id');
  }
  
  protected function getStdUrl() {
    return 'index.php?act='.$this -> mMod.'&id='.$this -> mWiz;
  }
      
  protected function actStd() {
    $lMen = new CWiz_Menu($this -> mWiz, 'itm');
    $lVie = new CWiz_Itm_List($this -> mWiz);
    $this -> render(CHtm_Wrap::wrap($lMen, $lVie));
  }
  
  protected function actNew() {
    $lMen = new CWiz_Menu($this -> mWiz, 'itm');
    $lVie = new CWiz_Itm_Form('wiz-itm.snew', 'New Wizard Step', $this -> mWiz);
    $this -> render(CHtm_Wrap::wrap($lMen, $lVie));
  }
  
  protected function actSnew() {
    $this -> dump($this -> mReq, 'REQ');
    $lMod = new CWiz_Itm_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> insert();
    $this -> redirect();
  }
  
  protected function actEdt() {
    $lId = $this -> getInt('id');
    $lSid = $this -> getInt('sid');
    
    $lFrm = new CWiz_Itm_Form('wiz-itm.sedt', 'Edit Wizard', $lId);
    $lFrm -> load($lSid);
    
    $this -> render($lFrm);
  }
  
  protected function actSedt() {
    $lMod = new CWiz_Itm_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> update();
    $this -> redirect();
  }
  
  protected function actDel() {
    $lId = $this -> getInt('id');
    $lSid = $this -> getInt('sid');
    $lMod = new CWiz_Itm_Mod();
    $lMod -> delete($lSid, $lId);
    $this -> redirect();
  }
  
}