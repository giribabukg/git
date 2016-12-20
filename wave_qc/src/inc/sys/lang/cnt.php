<?php
class CInc_Sys_Lang_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('sys-lang.menu');
    $this -> mMmKey = 'opt';

    // Ask If user has right for this page
    $lpn = 'sys-lang';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdNone);
    }
    $this -> mAvailLang = CCor_Res::get('languages');
  }

  protected function actStd() {
    $lVie = new CSys_Lang_List();
    $this -> render($lVie);
  }

  protected function actNew() {
    $lVie = new CSys_Lang_Form('sys-lang.snew', lan('lib.new_item'));
    $this -> render($lVie);
  }

  protected function actSnew() {
    $lMod = new CSys_Lang_Mod();
    $lMod -> getPost($this -> mReq);
    if ($lMod -> insert()) {
      foreach ($this -> mAvailLang as $lLang => $lName) {
        CCor_Cache::clearStatic('cor_res_lang_'.$lLang);
      }
    }
    $this -> redirect();
  }

  protected function actEdt() {
    $lCod = $this -> getReq('id');
    $lMid = $this -> getReq('mid');
    $lVie = new CSys_Lang_Form('sys-lang.sedt', lan('lib.edit_item'));
    $lVie -> load($lCod, $lMid);
    $this -> render($lVie);
  }

  protected function actSedt() {
    $lMod = new CSys_Lang_Mod();
    $lMod -> getPost($this -> mReq);
    if ($lMod -> hasChanged()) {
      $lMod -> update();
      foreach ($this -> mAvailLang as $lLang => $lName) {
        CCor_Cache::clearStatic('cor_res_lang_'.$lLang);
      }
    }
    $this -> redirect();
  }

  protected function actDel() {
    $lCod = $this -> getReq('id');
    $lMid = $this -> getReq('mid');
    CCor_Qry::exec('DELETE FROM al_sys_lang WHERE code="'.addslashes($lCod).'" AND mand='.intval($lMid));
    foreach ($this -> mAvailLang as $lLang => $lName) {
      CCor_Cache::clearStatic('cor_res_lang_'.$lLang);
    }
    $this -> redirect();
  }

}