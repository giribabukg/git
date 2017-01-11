<?php
class CInc_Conditions_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);

    $this -> mTitle = lan($this -> mMod.'.menu');
    $this -> mMmKey = 'opt';

    $lUser = CCor_Usr::getInstance();
    if (!$lUser -> canRead($this -> mMod)) {
      $this -> setProtection('*', $this -> mMod, rdRead);
    }
  }

  protected function actStd() {
    $lList = new CConditions_List();
    $this -> render($lList);
  }

  protected function actNew() {
    $lType = $this -> getVal('type');

    $lForm = new CConditions_Form($this -> mMod.'.snew', lan($this -> mMod.'.new'));
    $lForm -> setVal('type', $lType);

    $this -> render($lForm);
  }

  protected function actSnew() {
    $lParams = $this -> getVal('par');

    $lMod = new CConditions_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> setParams($lParams);

    if ($lMod -> insert()) {
      $lId = $lMod -> getInsertId();

      $this -> redirect('index.php?act='.$this -> mMod.'.edt&id='.$lId);
    } else {
      $this -> redirect();
    }
  }

  protected function actEdt() {
    $lId = $this -> getInt('id');

    $lForm = new CConditions_Form($this -> mMod.'.sedt', lan($this -> mMod.'.edt'));
    $lForm -> load($lId);

    $this -> render($lForm);
  }

  protected function actSedt() {
    $lParams = $this -> getVal('par');

    $lMod = new CConditions_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> setParams($lParams);
    $lMod -> update();

    $this -> redirect();
  }

  protected function actCpy() {
    $lId = $this -> getInt('id');

    $lForm = new CConditions_Form($this -> mMod.'.scpy', lan($this -> mMod.'.cpy'));
    $lForm -> load($lId);

    $this -> render($lForm);
  }

  protected function actScpy() {
    $lParams = $this -> getVal('par');

    $lMod = new CConditions_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> setParams($lParams);

    if ($lMod -> insert()) {
      $lId = $lMod -> getInsertId();

      $this -> redirect('index.php?act='.$this -> mMod.'.edt&id='.$lId);
    } else {
      $this -> redirect();
    }
  }

  protected function actDel() {
    $lId = $this -> getInt('id');
    $lSql = 'DELETE FROM al_cond WHERE id="'.addslashes($lId).'";';
    CCor_Qry::exec($lSql);

    CEve_Mod::clearCache();

    $this -> redirect();
  }
}