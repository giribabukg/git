<?php
class CInc_Svn_Wrap extends CCor_Ren {
  
  public function __construct($aTree) {
    $this->mTree = $aTree;
  }
  
  protected function getCont() {
    $lRet = '';
    $lRet.= '<div class="svn-wrap">';
    
    $lRet.= '<div class="fl w300">';
    $lRet.= '<div class="cap">Folders</div>';
    $lRet.= $this->mTree->getContent();
    $lRet.= '</div>';
    
    $lRet.= '<div class="fl w800">';
    $lRet.= '<div class="cap svn-dir">Content</div>';
    $lRet.= '<div class="svn-content" style="min-height:400px"><br /></div>';
    $lRet.= '</div>';
    
    
    $lRet.= '</div>';
    $lRet.= '<div class="clr"></div>';
    
    return $lRet;
  }

}