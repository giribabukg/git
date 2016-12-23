<?php
class CInc_Chk_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);

    $this -> mTitle = lan('chk.menu');

    $lPriv = 'chk';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lPriv)) {
      $this -> setProtection('*', $lPriv, rdNone);
    }
  }

  protected function actStd() {
    $lVie = new CChk_List();
    $this -> render($lVie);
  }

  protected function actNew() {
    $lVie = new CChk_Form_Base('chk.snew', lan('chk.new'));
    $this -> render($lVie);
  }

  protected function actSnew() {
    $lMod = new CChk_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> insert();
    $this -> redirect();
  }

  protected function actEdt() {
    $lId = $this -> getReqInt('id');
    $lVie = new CChk_Form_Edit($lId);
    $this -> render($lVie);
  }

  protected function actSedt() {
    $lMod = new CChk_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> update();
    $this -> redirect();
  }

  protected function actDel() {
    $lDomain = $this -> getVal('domain');
    if (empty($lDomain)) {
      $this -> redirect();
    }

    $lSql = 'DELETE FROM al_chk_master WHERE mand='.addslashes(MID).' AND domain="'.addslashes($lDomain).'"';
    CCor_Qry::exec($lSql);

    $lSql = 'DELETE FROM al_chk_items WHERE mand='.addslashes(MID).' AND domain="'.addslashes($lDomain).'"';
    CCor_Qry::exec($lSql);

    CChk_Mod::clearCache();
    $this -> redirect();
  }
}