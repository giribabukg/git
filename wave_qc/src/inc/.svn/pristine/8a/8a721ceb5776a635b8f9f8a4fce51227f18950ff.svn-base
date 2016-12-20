<?php
class CInc_Api_Pixelboxx_Query_Createfolder extends CApi_Pixelboxx_Query {

  protected function init() {
    $this->mMethod = 'createFolder';
    $this->mAttr = array();
  }
  
  public function create($aName, $aParentFolderId) {
    $this->setParam('ParentFolderId', $aParentFolderId);
    $this->setParam('Folder', array('name' => $aName, 'doi'=>'?', 'oid'=>'?'));
    $lRes = $this->query($this->mMethod, $this->mParam);
    if (!$this->hasPath($lRes, 'Folder.doi')) {
      return false;
    }
    return $lRes->Folder->doi;
  }
  
}