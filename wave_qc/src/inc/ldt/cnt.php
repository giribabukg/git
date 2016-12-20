<?php
class CInc_Ldt_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('ldts'); // Leadtimes
    $lpn = 'ldt';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdNone);
    }
  }

  protected function actStd() {
    $lVie = new CLdt_List();
    $this -> render($lVie);
  }

  protected function actEdt() {
    $lId = $this -> mReq -> getInt('id');
    $lVie = new CLdt_Form('ldt.sedt', lan('ldt.edt.lst'));
    $lVie -> load($lId);
    $this -> render($lVie);
  }

  protected function actSedt() {
    $lMod = new CLdt_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> update();
    $this -> redirect();
  }

  protected function actNew() {
    $lVie = new CLdt_Form('ldt.snew', lan('ldt.new.lst'));
    $this -> render($lVie);
  }

  protected function actSnew() {
    $lMod = new CLdt_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> insert();
    $this -> redirect();
  }

  protected function actFac() {
    $lId = $this -> mReq -> getInt('id');
    $lVie = new CLdt_Facform('ldt.sfac', lan('fac.edt.lst')); // Edit Factor List
    $lVie -> load($lId);
    $this -> render($lVie);
  }

  protected function actSfac() {
    $lId = $this -> getReqInt('id');
    $lVal = $this -> getReq('val');
    $lArr = array();
    for ($i = 1; $i < 6; $i++) {
      $lKey = 'fac'.$i;
      $lFid = (isset($lVal[$lKey])) ? $lVal[$lKey] : 0;
      if (!empty($lFid)) {
        $lArr[] = $lFid;
      }
    }
    $lUpd = implode(',', $lArr);
    $lSql = 'UPDATE al_ldt_master SET fac_cols="'.$lUpd.'" WHERE id='.$lId;
    CCor_Qry::exec($lSql);
    $this -> redirect();
  }

  protected function actDel() {
    $lId = $this -> mReq -> getInt('id');
    $lSql = 'DELETE FROM al_eve WHERE mand='.MID.' AND id="'.addslashes($lId).'"';
    CCor_Qry::exec($lSql);
    $this -> redirect();
  }

}