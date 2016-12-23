<?php
class CInc_Fie_Learn_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('fie-learn.menu');
    $this -> mMmKey = 'opt';

    $lpn = 'fie';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdNone);
    }
  }

  protected function actStd() {
    $lAli = $this -> getReq('alias');
    if (!empty($lAli)) {
      $lUsr = CCor_Usr::getInstance();
      $lUsr -> setPref('fie-learn.alias', $lAli);
    }
    $lVie = new CFie_Learn_List();
    $this -> render($lVie);
  }

  protected function actEdt() {
    $lId = $this -> getInt('id');
    $lQry = new CCor_Qry('SELECT * FROM al_fie_choice WHERE id='.$lId);
    if ($lRec = $lQry -> getAssoc()) {
      $lFrm = new CFie_Learn_Form('fie-learn.sedt', 'Edit Item');
      $lFrm -> setParam('id', $lId);
      $lFrm -> setParam('val[id]', $lId);
      $lFrm -> setParam('old[id]', $lId);
      $lFrm -> assignVal($lRec);
      $this -> render($lFrm);
    } else {
      $this -> redirect();
    }
  }

  protected function actSedt() {
    $lMod = new CFie_Learn_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> update();
    $this -> redirect();
  }

  protected function actDel() {
    $lId = $this -> getInt('id');
    $lMod = new CFie_Learn_Mod();
    $lMod -> delete($lId);
    $this -> redirect();
  }

  protected function actDelselected() {
    $lIds = $this -> getVal('ids');
    $lSql = 'DELETE FROM al_fie_choice WHERE id IN ('.$lIds.')';
    CCor_Qry::exec($lSql);
    $this -> redirect();
  }

}