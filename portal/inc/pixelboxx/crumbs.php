<?php
class CInc_Pixelboxx_Crumbs extends CCor_Ren {
  
  public function setPrefs($aPreferences) {
    $this->mPref = $aPreferences;
  }
  
  public function getPref($aKey, $aDefault = null) {
    return isset($this->mPref[$aKey]) ? $this->mPref[$aKey] : $aDefault;
  }
  
  protected function getCont() {
    $lRet = '';
    $lRet.= '<div class="pbox-crumbs">';
    
    $lF = $this->getPref('f');
    if (empty($lF)) {
      return '';
    }
    $lRet.= '<b>Showing</b>: ';
    
    $lItm = array(); 
    $lHashes = CPixelboxx_Utils::getHashes($lF);
    foreach ($lF as $lKey => $lVal) {
      $lOldHash = $lHash;
      $lHash = $lHashes[$lKey];
      $lItm[] = '<a onclick="Flow.Pbox.selectHash('.$lHash.')" class="nav">'.htm($lVal).'</a>';
    }
    if (!empty($lOldHash)) {
      $lRet.= '<a onclick="Flow.Pbox.selectHash('.$lOldHash.')" class="nav"><img src="img/wave8/ico/16/nav-up.gif" /></a>';
    }
    
    $lRet.= implode(' / ', $lItm);
    $lRet.= '</div>';
    return $lRet;
  }

}