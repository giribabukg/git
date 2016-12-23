<?php
class CInc_Api_Pixelboxx_Query_Getfolderstructure extends CApi_Pixelboxx_Query {
  
  protected function init() {
    $this->mMethod = 'getFolderStructure';
  }
  
  public function getResult($aParentFolderId = null) {
    if (!empty($aParentFolderId)) {
      $this->setParam('TopFolderId', $aVal);
    }
    $lRes = $this->query($this->mMethod, $this->mParams);
    return $lRes;
  }

}