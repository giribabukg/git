<?php
class CInc_Pixelboxx_View_Cards extends CPixelboxx_View {
  
  protected function onBeforeContent() {
    $this->mMap = CPixelboxx_Utils::getMetaMap();
    $this->mFie = CCor_Res::extract('alias', 'name_'.LAN, 'fie');
    $this->openProjectFile('pixelboxx/view_cards.htm');
    $lItems = $this->getItems();
    $this->setPat('items', $lItems);
  }
  
  protected function getItems() {
    $this->getIterator();
  
    $lTpl = new CCor_Tpl();
    $lTpl->openProjectFile('pixelboxx/view_cards_item.htm');
  
    $lRet = '';
    foreach ($this->mIte as $lRow) {
      $lTpl->setPat('image', base64_encode($lRow['image']));
      $lTpl->setPat('name', htm($lRow['name']));
      $lTpl->setPat('doi', htm($lRow['doi']));
      
      $lLine = '';
      foreach ($this->mMap as $lAlias => $lNative) {
        $lLine.= '<tr><td class="b p4">';
        $lFieldName = isset($this->mFie[$lAlias]) ? $this->mFie[$lAlias] : $lAlias; 
        $lLine.= htm($lFieldName);
        $lLine.= '</td><td>';
        
        $lNat = 'meta.'.$lNative;
        $lFieldValue = isset($lRow[$lNat]) ? $lRow[$lNat] : '';
        $lLine.= htm($lFieldValue);
        $lLine.= '</td></tr>';
      }
      $lTpl->setPat('lines', $lLine);
      $lRet.= $lTpl->getContent();
    }
    return $lRet;
  }

}