<?php
class CInc_Pixelboxx_View extends CCor_Tpl {
  
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
  
  public function getIterator() {
    if (isset($this->mIte)) {
      return $this->mIte;
    }
    $lQry = new CPixelboxx_Search();
    $lQry->setSearchPrefs($this->getPref('ser'));
    $lRet = $lQry->getIterator();
    $this->mIte = $lRet;
    return $this->mIte;
  }

}