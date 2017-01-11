<?php
class CInc_Pixelboxx_Search extends CCor_Obj implements IteratorAggregate {
  
  public function __construct() {
    $this->mQry = new CApi_Pixelboxx_Query_Extendedsearch();
    $this->mQry->addMetaOption();
    $this->mMap = CPixelboxx_Utils::getMetaMap();
    $this->mSearchTerms = array();
    
    $lRoot = CCor_Cfg::get('pbox.folder.root');
    $this->mQry->setFolder($lRoot);
    #$lTerm = $this->mQry->getTermFolder($lRoot); // does not work anymore!
    #$this->addTerm($lTerm);
  }
    
  public function addTerm($aTerm) {
    $this->mSearchTerms[] = $aTerm;
  }
  
  public function setSearchPrefs($aSearchPrefs) {
    if (empty($aSearchPrefs)) {
      return;
    }
    $lTerm = array();
    foreach ($aSearchPrefs as $lAlias => $lSearchVal) {
      if (empty($lSearchVal)) continue;
      $lNative = isset($this->mMap[$lAlias]) ? $this->mMap[$lAlias] : null;
      if (empty($lNative)) {
        $this->dbg('Search field '.$lAlias.' has no native', mlWarn);
        continue;
      }
      $this->addTerm($this->mQry->getTermAttribute($lNative, 'eq', $lSearchVal));
    }
  }
  
  protected function buildTerms() {
    $lTerms = $this->mSearchTerms;
    if (!empty($lTerms)) {
      if (count($lTerms) == 1) {
        $this->mQry->setTerm($lTerms[0]);
      } else {
        $lTerm = $this->mQry->getTermAnd($lTerms);
        $this->mQry->setTerm($lTerm);
      }
    }
  }
  
  public function setUserCondition($aConditionId) {
    return; // doesn't work with new Pixelboxx!!!
    $lCondBuilder = new CPixelboxx_Conditionbuilder();
    $lTerm = $lCondBuilder->getCondition($aConditionId);
    if (false !== $lTerm) {
      $this->addTerm($lTerm);
    }
  }
  
  public function query() {
    $this->buildTerms();
    $lRes = $this->mQry->getSearch();
    return $lRes;
  }
   
  public function getIterator() {
    if (!isset($this->mIte)) {
      $this->mIte = $this->query();
    }
    return $this->mIte;
  }

}