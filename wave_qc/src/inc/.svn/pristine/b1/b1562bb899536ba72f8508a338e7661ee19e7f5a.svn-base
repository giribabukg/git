<?php

class CInc_Fie_Validate_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this->mTitle = lan('fie-validate.menu');
    $this->mMmKey = 'opt';

    $lpn = 'fie-validate';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr->canRead($lpn)) {
      $this->setProtection('*', $lpn, rdRead);
    }
  }

  protected function actStd() {
    $lVie = new CFie_Validate_List();
    $lVal = CCor_Res::get('validate');
    $this->dump($lVal);
    $this->render($lVie);
  }

  protected function actEdt() {
    $lId = $this->getInt('id');
    $lRec = CFie_Validate_Mod::loadItem($lId);
    $lIsGlobalRec = $lRec['mand'] == CFie_Validate_Mod::WAVE_GLOBAL;
    $lWeAreOnGlobal = CFie_Validate_Mod::areWeOnGlobal();
    if ($lIsGlobalRec && ! $lWeAreOnGlobal) {
      $this->msg(lan('lib.perm'), mtUser, mlError);
      $this->redirect();
    }
    if ($lRec) {
      $lFrm = new CFie_Validate_Form('fie-validate.sedt', lan('fie-validate.edt'));
      $lFrm->setParam('id', $lId);
      $lFrm -> setParam('val[id]', $lId);
      $lFrm -> setParam('old[id]', $lId);
      $lFrm->assignVal($lRec);
      $this->render($lFrm);
    } else {
      $this->redirect();
    }
  }

  protected function actSedt() {
    $lMod = new CFie_Validate_Mod();
    $lMod->getPost($this->mReq);
    $lMandOld = $lMod->getold('mand');
    $lMandNew = $lMod->getVal('mand');
    $lGlob = CFie_Validate_Mod::WAVE_GLOBAL;
    $lIsGlobalRec = ($lMandOld == $lGlob) || ($lMandNew == $lGlob);
    $lWeAreOnGlobal = CFie_Validate_Mod::areWeOnGlobal();
    if ($lIsGlobalRec && ! $lWeAreOnGlobal) {
      $this->msg(lan('lib.perm'), mtUser, mlError);
      $this->redirect();
    }
    if ($lMod->update()) {
      $lId = $this->getInt('id');
      $lMod->saveOptions($lId, $this->getReq('par_val'));
      $this->redirect();
    } else {
      $lId = $this->getInt('id');
      $this->redirect('index.php?act=fie-validate.edt&id='.urlencode($lId));
    }
  }

  protected function actCpy() {
    $lId = $this->getInt('id');
    $lRec = CFie_Validate_Mod::loadItem($lId);
    $lIsGlobalRec = $lRec['mand'] == CFie_Validate_Mod::WAVE_GLOBAL;
    $lWeAreOnGlobal = CFie_Validate_Mod::areWeOnGlobal();
    if ($lIsGlobalRec && ! $lWeAreOnGlobal) {
      $this->msg(lan('lib.perm'), mtUser, mlError);
      $this->redirect();
    }
    if ($lRec) {
      $lFrm = new CFie_Validate_Form('fie-validate.snew', lan('fie-validate.copy'));
      $lFrm->setParam('id', '');
      $lFrm->setParam('val[id]', '');
      $lFrm->setParam('old[id]', '');
      $lFrm->assignVal($lRec);
      $this->render($lFrm);
    } else {
      $this->redirect();
    }
  }

  protected function actNew() {
    $lFrm = new CFie_Validate_Form('fie-validate.snew', lan('fie-validate.new'));
    $lFrm->setVal('validate_type', 'int');
    $this->render($lFrm);
  }

  protected function actSnew() {
    $lMod = new CFie_Validate_Mod();
    $lMod->getPost($this->mReq);
    $lMandNew = $lMod->getVal('mand');
    $lGlob = CFie_Validate_Mod::WAVE_GLOBAL;
    $lIsGlobalRec = ($lMandNew == $lGlob);
    $lWeAreOnGlobal = CFie_Validate_Mod::areWeOnGlobal();
    if ($lIsGlobalRec && ! $lWeAreOnGlobal) {
      $this->msg(lan('lib.perm'), mtUser, mlError);
      $this->redirect();
    }
    if ($lMod->insert()) {
      $lId = $lMod->getInsertId();
      $lMod->saveOptions($lId, $this->getReq('par_val'), false);
      $this->redirect();
    } else {
      $this->redirect('index.php?act=fie-validate.new');
    }
  }

  protected function actDel() {
    $lId = $this->getInt('id');
    $lRec = CFie_Validate_Mod::loadItem($lId);
    $lIsGlobalRec = $lRec['mand'] == CFie_Validate_Mod::WAVE_GLOBAL;
    $lWeAreOnGlobal = CFie_Validate_Mod::areWeOnGlobal();
    if ($lIsGlobalRec && ! $lWeAreOnGlobal) {
      $this->msg(lan('lib.perm'), mtUser, mlError);
      $this->redirect();
    }
    $lMod = new CFie_Validate_Mod();
    $lMod->delete($lId);
    $this->redirect();
  }

}
