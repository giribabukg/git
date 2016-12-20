<?php
class CInc_Ldt_Itm_Cnt extends CCor_Cnt {
  
  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = 'Leadtime Items';
    $lpn = 'lts';
    $lUsr = CCor_Usr::getInstance();    
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdNone);
    }    
  }
  
  protected function getStdUrl() {
    $lLid = $this -> getReqInt('lid');
    return 'index.php?act=ldt-itm&lid='.$lLid;
  }
  
  protected function actStd() {
    $lId = $this -> getReqInt('lid');
    $lVie = new CLdt_Itm_List($lId);
    $this -> render($lVie);
  }
  
  protected function actEdt() {
    $lId  = $this -> mReq -> getInt('id');
    $lLid = $this -> mReq -> getInt('lid');
    $lVie = new CLdt_Itm_Form($lLid, 'ldt-itm.sedt', 'Edit Leadtime Item');
    $lVie -> load($lId);
    $this -> render($lVie);
  }
  
  protected function actSedt() {
    $lMod = new CLdt_Itm_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> update();
    $this -> redirect();
  }
  
  protected function actNew() {
    $lLid = $this -> mReq -> getInt('lid');
    $lVie = new CLdt_Itm_Form($lLid, 'ldt-itm.snew', 'New Leadtime Item');
    $this -> render($lVie);
  }
  
  protected function actSnew() {
    $lMod = new CLdt_Itm_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> insert();
    $this -> redirect();
  }
  
  protected function actDel() {
    $lId = $this -> getReqInt('id');
    $lLid = $this -> mReq -> getInt('lid');
    $lSql = 'DELETE FROM al_ldt_itm WHERE id='.$lId;
    CCor_Qry::exec($lSql);
    $this -> redirect();
  }
  
}