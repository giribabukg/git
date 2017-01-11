<?php
class CInc_Cms_Categories_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);

    $this -> mTitle = lan('cms-categories.menu');
    $this -> mMmKey = 'opt';
    
    $lPriv = 'cms-categories';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lPriv)) {
      $this -> setProtection('*', $lPriv, rdNone);
    }
  }

  protected function actStd() {
    $lVie = new CCms_Categories_List();
    $this -> render($lVie);
  }

  protected function actNew() {
    $lVie = new CCms_Categories_Form_Base('cms-categories.snew', lan('cms-categories.new'));
    $this -> render($lVie);
  }

  protected function actSnew() {
    $lMod = new CCms_Categories_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> insert();
    $this -> redirect();
  }

  protected function actEdt() {
    $lId = $this -> getReqInt('id');
    $lVie = new CCms_Categories_Form_Edit($lId);
    $this -> render($lVie);
  }

  protected function actSedt() {
    $lMod = new CCms_Categories_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> update();
    $this -> redirect();
  }

  protected function actDel() {
    $lValue = $this -> getVal('value');
    if (empty($lValue)) {
      $this -> redirect();
    }

    $lSql = 'DELETE FROM `al_cms_categories` WHERE `mand`='.intval(MID).' AND `value`='.esc($lValue);
    CCor_Qry::exec($lSql);
    
    $lSql = 'DELETE FROM `al_cms_categorytasks` WHERE `mand`='.intval(MID).' AND `category`='.esc($lValue);
    CCor_Qry::exec($lSql);

    CCms_Categories_Mod::clearCache();
    $this -> redirect();
  }
  
  protected function actAct() {
    $lSid = $this -> getInt('id');
    $lSql = 'UPDATE `al_cms_categories` SET `active`=1 WHERE `mand`='.intval(MID).' AND `id`='.esc($lSid);
    CCor_Qry::exec($lSql);
    $this -> redirect();
  }

  protected function actDeact() {
    $lSid = $this -> getInt('id');
    $lSql = 'UPDATE `al_cms_categories` SET `active`=0 WHERE `mand`='.intval(MID).' AND `id`='.esc($lSid);
    CCor_Qry::exec($lSql);
    $this -> redirect();
  }
}