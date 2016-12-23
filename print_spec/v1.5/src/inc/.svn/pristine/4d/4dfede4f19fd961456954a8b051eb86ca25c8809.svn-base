<?php
class CInc_Fie_Blocks_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('job-fil.blo');
    $lpn = 'fie';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdNone);
    }
  }

  protected function actStd() {
    $lVie = new CFie_Blocks_List();
    $this -> render($lVie);
  }

  protected function actEdt() {
    $lId = $this -> getReqInt('id');
    $lQry = new CCor_Qry('SELECT * FROM al_fie_blocks WHERE id='.$lId);
    if ($lRec = $lQry -> getAssoc()) {
      $lFrm = new CFie_Blocks_Form('fie-blocks.sedt', 'Edit Field');
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
    $lMod = new CFie_Blocks_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> update();
    $this -> redirect();
  }

  protected function actNew() {
    $lFrm = new CFie_Blocks_Form('fie-blocks.snew', 'New Block');
    $lFrm -> setVal('src', 'pro');
    $this -> render($lFrm);
  }

  protected function actSnew() {
    $lMod = new CFie_Blocks_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> insert();
    $this -> redirect();
  }

}