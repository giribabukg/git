<?php
class CInc_Eve_Type_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('eve-type.menu');

    // Ask If user has right for this page
    $lpn = 'eve-type';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdRead);
    }
  }

  protected function actStd() {
    $lVie = new CEve_Type_List();
    $this -> render($lVie);
  }

  protected function actEdt() {
    $lCode = $this -> getVal('id');
    $lFrm = new CEve_Type_Form('eve-type.sedt', lan('eve-type.act.edt'));
    $lFrm -> load($lCode);
    $this -> render($lFrm);
  }

  protected function actSedt() {
    $lMod = new CEve_Type_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> update();
    $this -> redirect();
  }

  protected function actNew() {
    $lFrm = new CEve_Type_Form('eve-type.snew', lan('eve-type.act.new'));
    $lFrm -> setVal('mand', MID);
    $this -> render($lFrm);
  }

  protected function actSnew() {
    $lMod = new CEve_Type_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> insert();
    $this -> redirect();
  }

  protected function actDel() {
    $lMod = new CEve_Type_Mod();
    $lMod->delete($lCode);
    $this -> redirect();
  }

  protected function actFields() {
    $lCode = $this -> getVal('code');

    $lView = new CHtm_Fpr('eve-type.savefields');
    $lView->setParam('code', $lCode);
    $lSource = CCor_Res::extract('alias', 'name_'.LAN, 'fie');

    $lMod = new CEve_Type_Mod();
    $lData = $lMod->load(array('code' => $lCode, 'mand' => MID));

    $lView -> setSrc($lSource);
    $lView -> setSel($lData['fields']);

    $this->render($lView);
  }

  protected function actSavefields() {
    $lFields = $this->getval('dst');
    $lCode = $this -> getVal('code');

    $lMod = new CEve_Type_Mod();
    $lDst = $this->getVal('dst', '');
    $lDst = (empty($lDst)) ? '' : implode(',', $lDst);
    $lMod -> saveFields($lCode, $lDst);
    $this->redirect();
  }

}