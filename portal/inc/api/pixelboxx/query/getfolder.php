<?php
class CInc_Api_Pixelboxx_Query_Getfolder extends CApi_Pixelboxx_Query {
  
  protected function init() {
    $this->mMethod = 'getFolder';
    $this->mAttr = array();
    $this->addAttribute('size', 'filesize');
    $this->addAttribute('name', 'filename');
    $this->addAttribute('date', 'importdate');
  }
  
  public function addAttribute($aAlias, $aNative) {
    $this->mAttr[$aAlias] = $aNative;
  }
  
  protected function addAttributeTag() {
    
    $lAtt = array();
    foreach ($this->mAttr as $lKey => $lVal) {
      $lItm = array('mandatory' => 'false', '_' => $lVal);
      $lAtt[] = $lItm; 
    }
    $lTag = array('attributes' => 'explicit', 'accesscontrol' => 'false', 'mandatory' => 'false', 'Attribute' => $lAtt);
    $this->mParam['WithMetadata'] = $lTag;
  }
  
  public function getFolder($aFolderId) {
    $this->setParam('FolderId', $aFolderId);
    if (!empty($this->mAttr)) {
      $this->addAttributeTag();
    }
    $lRes = $this->query($this->mMethod, $this->mParam);
    if (!$this->hasPath($lRes, 'Folder.Content.Item')) {
      return false;
    }
    
    $lRet = array();
    $lRoot = $lRes->Folder->Content->Item;
    if ($this->hasPath($lRoot, 'Object')) {
      $lRoot = array($lRoot);
    }
    foreach ($lRoot as $lRow) {
      #var_dump($lRow);
      $lItm = array();
      $lItm['name'] = (string)$lRow->Object->name;
      $lItm['doi'] = (string)$lRow->Object->doi;
      if (isset($lRow->Object->Attributes->A)) {
        $lAttr = $lRow->Object->Attributes->A;
        foreach ($lAttr as $lAtt) {
          $lNat = $lAtt->n;
          $lVal = (string)$lAtt->{'_'};
          if ($lKey = array_search($lNat, $this->mAttr)) {
            if ('importdate' == $lNat) {
              $lVal = strtotime($lVal);
            }
            $lItm[$lKey] = $lVal;
          }
        }
      }
      if (!empty($this->mAttr)) {
        foreach ($this->mAttr as $lKey => $lVal) {
          
        }
      }
      $lRet[] = $lItm;
    }
    #var_dump($lRet);
    return $lRet;
  }

}