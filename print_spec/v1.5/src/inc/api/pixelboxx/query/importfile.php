<?php
class CInc_Api_Pixelboxx_Query_Importfile extends CApi_Pixelboxx_Query {
  
  protected function init() {
    $this->mMethod = 'importFile';
    $this->mAttr = array();
  }
  
  public function addAttribute($aNative, $aValue) {
    $this->mAttr[$aNative] = $aValue;
  }

  protected function logMsg($aVar) {
    $lTxt = (is_scalar($aVar)) ? $aVar : var_export($aVar, true);
    error_log($lTxt.LF);
  }
  
  protected function addAttributeTag() {
  
    $lAtt = array();
    foreach ($this->mAttr as $lKey => $lVal) {
      $lItm = array('n' => $lKey, 'lang' => 'en', '_' => $lVal);
      $lAtt[] = $lItm;
    }
    $lTag = array('A' => $lAtt);
    $this->mParam['Attributes'] = $lTag;
  }
  
  public function upload($aFolderId, $aTempFileLocation, $aName, $aAtt = NULL) {
    $this->setParam('TargetFolderId', $aFolderId);
    $this->addAttribute('filename', $aName);

    if (!empty($aAtt)) {
      foreach ($aAtt as $lKey => $lVal) {
        $this->addAttribute($lKey, $lVal);
      }
    }
    $this->addAttributeTag();

    $lUrl = CCor_Cfg::get('base.url').$aTempFileLocation;
    $lUrl = strtr($lUrl, array(' ' => '%20'));
    $lArr['Location'] = array('URL' => $lUrl);

    //$lDat = file_get_contents($aTempFileLocation);
    //$lArr['Location'] = array('IncludedData' => $lDat);
    $lArr['type'] = 'fine';
    $this->setParam('Data', $lArr); 
    $lRes = $this->query($this->mMethod, $this->mParam);
    
    if (!$this->hasPath($lRes, 'Object.doi')) {
      return false;
    }
    return $lRes->Object->doi;
  }

}
