<?php
class CInc_Pixelboxx_View_Grid extends CPixelboxx_View {
  
  protected function onBeforeContent() {
    $this->openProjectFile('pixelboxx/view_grid.htm');
    $lItems = $this->getItems();
    $this->setPat('items', $lItems);
  }
  
  protected function getItems() {
    $this->getIterator();
    
    $lTpl = new CCor_Tpl();
    $lTpl->openProjectFile('pixelboxx/view_grid_item.htm');
    
    $lRet = '';
    foreach ($this->mIte as $lRow) {
      $lTpl->setPat('image', base64_encode($lRow['image']));
      $lTpl->setPat('name', htm($lRow['name']));
      $lTpl->setPat('doi', htm($lRow['doi']));
      $lRet.= $lTpl->getContent();
    }
    return $lRet;
  }

}