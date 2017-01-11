<?php
class CInc_Crp_Action_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);

    $this -> mTitle = lan('lib.actions');
    $lpn = 'crp';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdNone);
    }
  }

  protected function actStd() {
    $lCrp = $this -> getReqInt('id');
    $lMen = new CCrp_Menu($lCrp, 'act');
    $lVie = new CCrp_Action_List($lCrp);
    $this -> render(CHtm_Wrap::wrap($lMen, $lVie));
  }

  protected function actNew() {
    $lCid = $this -> getReqInt('id');
    $lVie = new CCrp_Sta_Form_Base('crp-sta.snew', 'New Status', $lCid);
    $lMen = new CCrp_Menu($lCid, 'sta');
    $this -> render(CHtm_Wrap::wrap($lMen, $lVie));
  }

  protected function actSnew() {
    $lCid = $this -> getReqInt('cid');
    $lMod = new CCrp_Sta_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> insert();
    $this -> redirect('index.php?act=crp-sta&id='.$lCid);
  }

  protected function actEdt() {
    $lId  = $this -> getReqInt('id');
    $lCid = $this -> getReqInt('cid');
    $lMen = new CCrp_Menu($lCid, 'sta');
    $lVie = new CCrp_Sta_Form_Edit($lId, $lCid);
    $this -> render(CHtm_Wrap::wrap($lMen, $lVie));
  }

  protected function actSedt() {
    $lCid = $this -> getReqInt('cid');
    $lMod = new CCrp_Sta_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> update();
    $this -> redirect('index.php?act=crp-sta&id='.$lCid);
  }

  protected function actDel() {
    $lId  = $this -> getReqInt('id');
    $lCid = $this -> getReqInt('cid');
    $lMod = new CCrp_Sta_Mod();
    $lMod -> delete($lId);
    $this -> redirect('index.php?act=crp-sta&id='.$lCid);
  }

  // steps

  protected function actNewstp() {
    $lCid = $this -> getReqInt('id');
    $lVie = new CCrp_Sta_Stp_Form_Base('crp-sta.snewstp', 'New Step', $lCid);
    $lMen = new CCrp_Menu($lCid, 'sta');
    $this -> render(CHtm_Wrap::wrap($lMen, $lVie));
  }

  protected function actSnewstp() {
    $lCid = $this -> getReqInt('cid');
    $lMod = new CCrp_Sta_Stp_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> insert();
    $this -> redirect('index.php?act=crp-sta&id='.$lCid);
  }

  protected function actEdtstp() {
    $lId  = $this -> getReqInt('id');
    $lCid = $this -> getReqInt('cid');
    $lMen = new CCrp_Menu($lCid, 'sta');
    $lVie = new CCrp_Sta_Stp_Form_Edit($lId, $lCid);
    $this -> render(CHtm_Wrap::wrap($lMen, $lVie));
  }

  protected function actSedtstp() {
    $lCid = $this -> getReqInt('cid');
    $lMod = new CCrp_Sta_Stp_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> update();
    $this -> redirect('index.php?act=crp-sta&id='.$lCid);
  }

  protected function actDelstp() {
    $lId  = $this -> getReqInt('id');
    $lCid = $this -> getReqInt('cid');
    $lMod = new CCrp_Sta_Stp_Mod();
    $lMod -> delete($lId);
    $this -> redirect('index.php?act=crp-sta&id='.$lCid);
  }

}