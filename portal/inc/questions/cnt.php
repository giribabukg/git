<?php
class CInc_Questions_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);

    $this -> mTitle = lan('questions-list.menu');
    $this -> mMmKey = 'opt';
    
    $lPriv = 'questions';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lPriv)) {
      $this -> setProtection('*', $lPriv, rdNone);
    }
  }

  protected function actStd() {
    $lVie = new CQuestions_List();
    $this -> render($lVie);
  }

  protected function actNew() {
    $lVie = new CQuestions_Form_Base('questions.snew', lan('questions.new'));
    $this -> render($lVie);
  }

  protected function actSnew() {
    $lMod = new CQuestions_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> insert();
    $this -> redirect();
  }

  protected function actEdt() {
    $lId = $this -> getReqInt('id');
    $lVie = new CQuestions_Form_Edit($lId);
    $this -> render($lVie);
  }

  protected function actSedt() {
    $lMod = new CQuestions_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> update();
    $this -> redirect();
  }

  protected function actDel() {
    $lId = $this -> getVal('id');
    if (empty($lId)) {
      $this -> redirect();
    }

    $lSql = 'DELETE FROM al_questions_master WHERE mand='.addslashes(MID).' AND id="'.addslashes($lId).'"';
    CCor_Qry::exec($lSql);

    $lSql = 'DELETE FROM al_questions_items WHERE mand='.addslashes(MID).' AND master_id="'.addslashes($lId).'"';
    CCor_Qry::exec($lSql);

    CQuestions_Mod::clearCache();
    $this -> redirect();
  }
  protected function actAct() {
    $lSid = $this -> getInt('id');
    $lSql = 'UPDATE al_questions_master SET active=1 WHERE mand='.MID.' AND id='.$lSid;
    CCor_Qry::exec($lSql);
    $this -> redirect();
  }

  protected function actDeact() {
    $lSid = $this -> getInt('id');
    $lSql = 'UPDATE al_questions_master SET active=0 WHERE mand='.MID.' AND id='.$lSid;
    CCor_Qry::exec($lSql);
    $this -> redirect();
  }
}