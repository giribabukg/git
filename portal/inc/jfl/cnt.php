<?php
class CInc_Jfl_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);

    $this -> mTitle = lan($this -> mMod.'.menu');
    $this -> mMmKey = 'opt';

    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($this -> mMod)) {
      $this -> setProtection('*', $this -> mMod, rdRead);
    }
  }

  protected function actStd() {
    $lList = new CJfl_List();
    $this -> render($lList);
  }

  protected function actNew() {
    $lForm = new CJfl_Form($this -> mMod.'.snew', lan($this -> mMod.'.new'));
    $this -> render($lForm);
  }

  protected function actSnew() {
    $lMod = new CJfl_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> insert();
    $this -> redirect();
  }

  protected function actEdt() {
    $lId = $this -> getInt('id');

    $lForm = new CJfl_Form($this -> mMod.'.sedt', lan($this -> mMod.'.edit'));
    $lForm -> load($lId);

    $this -> render($lForm);
  }

  protected function actSedt() {
    $lMod = new CJfl_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> update();
    $this -> redirect();
  }

  protected function actDel() {
    $lId = $this -> getInt('id');
    $lSql = 'DELETE FROM al_jfl WHERE id='.$lId.';';
    CCor_Qry::exec($lSql);

    CJfl_Mod::clearCache();
    $this -> redirect();
  }
}