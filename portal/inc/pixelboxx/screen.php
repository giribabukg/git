<?php
class CInc_Pixelboxx_Screen extends CCor_Tpl {
  
  
  public function __construct() {
    $this->openProjectFile('pixelboxx/screen.htm');
    $this->mClient = $this->getPbClient();
  }
  
  protected function getPbClient() {
    $lOpt = array('login' => '5Flow', 'password' => '5flow', 'soap_version' => SOAP_1_1);
    //$lWsdl = 'http://dam21.demo.pixelboxx.com/servlet/ws/WebServiceInterface?WSDL';
    $lWsdl = 'D:/Web/5flow/pixelboxx/src/inc/api/pixelboxx/data/WebServiceInterface.wsdl';
     
    $lClient = new CApi_Pixelboxx_Client();
    $lClient->loadAuthFromConfig();
     
    return $lClient;
  }

  public function setPrefsFrom($aPrefix) {
    $this->setPrefs(CPixelboxx_Utils::getPrefsFrom($aPrefix));
  }
  
  public function setPrefs($aPreferences) {
    $this->mPref = $aPreferences;
  }
  
  public function setPref($aKey, $aValue) {
    $this->mPref[$aKey] = $aValue;
  }
  
  public function getPref($aKey, $aDefault = null) {
    return isset($this->mPref[$aKey]) ? $this->mPref[$aKey] : $aDefault;
  }
  
  public function setFolder($aDoi) {
    $this->mFolder = $aDoi;
  }
  
  public function setViewStyle($aStyle) {
    $this->mViewStyle = $aStyle;
  }
  
  public function setArea($aKey, $aContent) {
    $this->mArea[$aKey] = $aContent;
  }
  
  protected function getArea($aKey) {
    if (isset($this->mArea[$aKey])) {
      $lRet = $this->mArea[$aKey];
    } else {
      $lRet = $this->createArea($aKey);
      $this->mArea[$aKey] = $lRet;
    }
    return $lRet;
  }
  
  protected function createArea($aKey) {
    $lFunc = 'createArea'.$aKey;
    $lRet = 'Unknown Area '.$aKey;
    if ($this->hasMethod($lFunc)) {
      $lRet = $this->$lFunc();
    } 
    return $lRet;
  }
  
  protected function createAreaTree() {
    $lTree = new CPixelboxx_Tree($this->mClient, 0);
    $lPick = CCor_Cfg::get('pbox.picklist', 'UBE');
    $lTree->setPicklist($lPick);
    $lFields = CPixelboxx_Utils::getStructureFields();
    $lTree->setFields($lFields);
    $lTree->loadItems();
    return $lTree;
  }
  
  protected function createAreaBar() {
    $lBar = new CPixelboxx_Bar();
    $lBar->setPrefs($this->mPref);
    //$lBar->setFields($this->mFields);
    return $lBar;
  }
  
  protected function createAreaContent() {
    $lRet = '';
    
    $lBar = $this->createAreaBar();
    $lBar->setPrefs($this->mPref);
    $lRet.= $lBar->getContent();
    
    $lPref = $this->getPref('view', 'grid');
    if ('list' == $lPref) {
      $lView = new CPixelboxx_View_Cards();
    } else {
      $lView = new CPixelboxx_View_Grid();
    }
    $lView->setPrefs($this->mPref);
    $lRet.= $lView->getContent();
    return $lRet;
    
  }
  
  protected function onBeforeContent() {
    $this->dump($this->mPref, 'PREFERENCES: ');
    $lPatterns = $this->findPatterns('view.');
    foreach ($lPatterns as $lPattern) {
      $lCont = $this->getArea($lPattern);
      if (is_a($lCont, 'CCor_Ren')) {
        $lCont = $lCont->getContent();
      }
      //echo $lCont.BR;
      $this->setPat('view.'.$lPattern, $lCont);
    }
    $lPref = $this->getPref('collapse');
    $lCollapse = empty($lPref) ? 0 : 1;
    $this->setPat('preference.collapse', $lCollapse);
    $this->setPat('preference.hash', $this->getPref('hash'));
  }

}