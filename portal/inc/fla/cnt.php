<?php
class CInc_Fla_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('lib.flags');
   $this -> mMmKey = 'opt';

    $lpn = 'fla';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdNone);
    }
  }

  protected function actStd() {
    $lVie = new CFla_List();
    $this -> render($lVie);
  }

  protected function actEdt() {
    $lId  = $this -> getReqInt('id');
    
    $lMen = new CFla_Menu($lId, 'dat');
    $lVie = new CFla_Form_Edit($lId);
    $this -> render(CHtm_Wrap::wrap($lMen, $lVie));
  }

  protected function actSedt() {
    $lMod = new CFla_Mod();
    $lMod -> getPost($this -> mReq);
    if ($lMod -> update()) {
      CCor_Cache::clearStatic('cor_res_fla_'.MID);
    }
    $this -> redirect();
  }

  protected function actNew() {
    $lVie = new CFla_Form_Base('fla.snew', lan('flag.new'));

    $lPreFill = flagComment;
    $lVie -> setVal('flags_act', $lPreFill);

    $lPreFill+= flagCommentSkip;
    $lPreFill+= flagBtnDisplay1;
    $lPreFill+= flagJsAnyConf;
    #$lPreFill+= flagBtnAmend + flagBtnApprov);
    $lVie -> setVal('flags_conf', $lPreFill);
    $this -> render($lVie);
  }

  protected function actSnew() {
    $lMod = new CFla_Mod();
    $lMod -> getPost($this -> mReq);
    if ($lMod -> insert()) {
      CCor_Cache::clearStatic('cor_res_fla_'.MID);
    }
    $this -> redirect();
  }

  protected function actDel() {
    $lId  = $this -> getReqInt('id');
    $lMod = new CFla_Mod();
    if ($lMod -> delete($lId)) {
      CCor_Cache::clearStatic('cor_res_fla_'.MID);
    }
    $this -> redirect();
  }

}