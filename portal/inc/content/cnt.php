<?php
class CInc_Content_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('content.text.menu');
    $this -> mMmKey = 'content';

    // Ask If user has right for this page
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead('content')) {
      $this -> setProtection('*', $lpn, rdRead);
    }
  }

  protected function actStd() {
    $lVie = new CContent_List();
    $this -> render($lVie);
  }

  protected function actEdt() {
    $lId = $this -> getInt('id');

    $lFrm = new CContent_Form('content.sedt', lan('content.sedt'));
    $lFrm -> load($lId);
    $this->render($lFrm);
  }
    
  protected function actSedt() {
    $lMod = new CContent_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> update();
    $this -> redirect();
  }

  protected function actNew() {
    $lFrm = new CContent_Form('content.snew', lan('content.new'));
    $this -> render($lFrm);
  }

  protected function actSnew() {
    $lMod = new CContent_Mod();
    $lMod -> getPost($this -> mReq);
    $lReqVal = $this->mReq->getVal(val);
    $lMod -> duplicateCheck($lReqVal['alias']);
    $lMod -> insert();
    $this -> redirect();
  }
    
  protected function actCopy() {
    $lId = $this -> getInt('id');
    $lFrm = new CContent_Form('content.scopy', lan('content.copy'));
    $lFrm -> load($lId);

    $this -> render($lFrm);
  }

  protected function actScopy() {
    $lMod = new CContent_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> insert();
    $this -> redirect();
  }

  protected function actDel() {
    $lId = $this -> mReq -> getInt('id');
    $lSql = 'DELETE FROM al_text_content WHERE mand='.MID.' AND id="'.addslashes($lId).'"';
    CCor_Qry::exec($lSql);
    $this -> redirect();
  }

}