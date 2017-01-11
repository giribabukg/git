<?php
class CInc_Pixelboxx_Cnt extends CCor_Cnt {
  
  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('pixelboxx.menu');
    $this -> mMmKey = 'pixelboxx';
  
    $lpn = 'pixelboxx';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdNone);
    }
  }
  
  protected function actStd() {
    $lScreen = new CPixelboxx_Screen();
    $lScreen->setPrefsFrom($this->mPrf);
    $this->render($lScreen);
  }
  
  protected function actGetcont() {
    $lUsr = CCor_Usr::getInstance();
    $lF = $this->getReq('f');
    if (!empty($lF)) {
      $lCurrentSearch = $lUsr->getPref($this->mPrf.'.ser');
      if (empty($lCurrentSearch)) {
        $lCurrentSearch = array();
      }
      if (is_string($lCurrentSearch)) {
        $lCurrentSearch = unserialize($lCurrentSearch);
      }
      $lF = array_reverse($lF, true);
      $lStruc = CPixelboxx_Utils::getStructureFields();
      foreach ($lStruc as $lNum => $lAlias) {
        $lVal = isset($lF[$lNum]) ? $lF[$lNum] : '';
        $lCurrentSearch[$lAlias] = $lVal;
      }
      $lUsr->setPref($this->mPrf.'.f', $lF);
      $lUsr->setPref($this->mPrf.'.ser', $lCurrentSearch);
    }
    
    $lAllPrefs = CPixelboxx_Utils::getPrefsFrom($this->mPrf);
    
    $lBar = new CPixelboxx_Bar();
    $lBar->setPrefs($lAllPrefs);
    echo $lBar->getContent();
    
    $lCrumbs = new CPixelboxx_Crumbs();
    $lCrumbs->setPrefs($lAllPrefs);
    echo $lCrumbs->getContent();
    
    $lPref = $lUsr->getPref($this->mPrf.'.view', 'grid');
    if ('list' == $lPref) {
      $lView = new CPixelboxx_View_Cards();
    } else {
      $lView = new CPixelboxx_View_Grid();
    }
    $lVie = new CHtm_MsgBox();
    echo $lVie -> getContent();
    $lMsg = CCor_Msg::getInstance();
    $lMsg -> clear();
    
    $lView->setPrefs($lAllPrefs);
    echo $lView->getContent();
    
    $lUsr = CCor_Usr::getInstance();
    $lUsr->setPref($this->mPrf.'.hash', $this->getReq('hash'));
    exit;
  }
  
  protected function actSetpref() {
    $lPref = $this->getReq('pref');
    $lVal  = $this->getReq('val');
    $lUsr  = CCor_Usr::getInstance();
    $lUsr -> setPref($this -> mPrf.'.'.$lPref, $lVal);
    
    $lNoRender = $this->getInt('norender');
    if (!$lNoRender) {
      $this->redirect();
    }
    exit;
  }
  
  protected function actTogpref() {
    $lPref = $this->getReq('pref');
    $lPrefKey = $this -> mPrf.'.'.$lPref;
    
    $lUsr  = CCor_Usr::getInstance();
    $lOld = $lUsr->getPref($lPrefKey);
    $lNew = $lOld ? 0 : 1;
    $lUsr -> setPref($lPrefKey, $lNew);
    echo 'ok';
    exit;
  }
  
  protected function actSpr() {
    $lVie = new CHtm_Fpr($this -> mMod.'.sspr');
    $lVie -> setTitle(lan('lib.opt.spr'));
    $lDef = CCor_Res::extract('alias', 'name_'.LAN, 'fie');
    $lAvail = CPixelboxx_Utils::getMetaFields();
    $lUsr  = CCor_Usr::getInstance();
    
    $lCurrent = $lUsr -> getPref($this -> mPrf.'.sfie');
    if (is_null($lCurrent)) {
      $lStruc = CPixelboxx_Utils::getStructureFields();
      $lCurrent = implode(',', $lStruc);
    }

    $lArr = array();
    foreach ($lAvail as $lAlias) {
      if (!isset($lDef[$lAlias])) {
        continue;
      }
      $lName = $lDef[$lAlias];
      $lArr[$lAlias] = $lName;
    }
    $lVie -> setSrc($lArr);
    $lVie -> setSel($lCurrent);
    $this -> render($lVie);
  }
  
  protected function actSspr() {
    $lDst = $this -> mReq -> getVal('dst');
    $lUsr = CCor_Usr::getInstance();
    $lUsr -> setPref($this -> mPrf.'.sfie', implode(',', $lDst));
    $this -> redirect();
  }
  

}