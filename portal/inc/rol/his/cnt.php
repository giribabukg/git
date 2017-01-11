<?php
class CInc_Rol_His_Cnt extends CCor_Cnt {
  
  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('rol.menu').' '.lan('lib.history');
  
    $lpn = 'rol';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdNone);
    }
  }
  
  protected function actStd() {
    $lRolId = $this -> getInt('id');
    $lMen = new CRol_Menu($lRolId, 'his');
    $lVie = new CRol_His_List($lRolId);
    $this -> render(CHtm_Wrap::wrap($lMen, $lVie));
  }
  
  protected function actNew() {
    $lRolId = $this -> mReq -> getInt('id');
    $lVie = new CRol_His_Form_Base('rol-his.snew', lan('lib.msg.new'), 'rol-his&id='.$lRolId);
    $lVie -> setParam('id', $lRolId);
  
    $lMen = new CRol_Menu($lRolId, 'his');
    $this -> render(CHtm_Wrap::wrap($lMen, $lVie));
  }
  
  protected function actSnew() {
    $lUid = $this -> mReq -> getInt('id');
    $lMod = new CRol_His_Mod();
    $lMod -> getPost($this -> mReq, FALSE);
    $lMod -> insert();
    $this -> redirect('index.php?act=rol-his&id='.$lUid);
  }
}