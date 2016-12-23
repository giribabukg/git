<?php
class CInc_Rol_Cnt extends CCor_Cnt {
  
  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('rol.menu');
    $this -> mMmKey = 'usr';
    // Ask If user has right for this page
    $lpn = 'rol';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdNone);
    }
  }
  
  protected function actStd() {
    $lVie = new CRol_List();
    $this -> render($lVie);
  }
  
  protected function actEdt() {
    $lId = $this -> getReqInt('id');
    //darf nur die Rollen aus aktuellem Mandant und "Mandant=0" editiert werden.
    $lNam = CCor_Qry::getInt('SELECT id FROM al_rol WHERE id='.$lId.' AND mand IN (0,'.MID.')');
    if (!$lNam){
      $this -> redirect();
    }
    $lMen = new CRol_Menu($lId, 'dat');
    $lVie = new CRol_Form('rol.sedt', lan('rol.edt'));
    $lVie -> load($lId);
    $this -> render(CHtm_Wrap::wrap($lMen, $lVie));
}
  
  protected function actSedt() {
    $lMod = new CRol_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> update();
    $lId = $lMod -> getVal('id');
    $this -> redirect('index.php?act=rol.edt&id='.$lId);
  }
  
  protected function actNew() {
    $lVie = new CRol_Form('rol.snew', lan('rol.new'));
    $this -> render($lVie);
  }
  
  protected function actSnew() {
    $lMod = new CRol_Mod();
    $lMod -> getPost($this -> mReq);
    if ($lMod -> insert()) {
      $lId = $lMod -> getInsertId();
      $this -> redirect('index.php?act=rol.edt&id='.$lId);
    }
    $this -> redirect();
  }
  
  protected function actDel() {
    $lId = $this -> getReqInt('id');
    $lMod = new CRol_Mod();
    $lMod -> delete($lId);
    $this -> redirect();
  }
    
}