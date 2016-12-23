<?php
class CInc_Htm_Tree_List extends CCor_Ren {

  public function __construct() {
    $this -> mRoot = new CHtm_Tree_Node('root');
  }
  
  public function & getRoot() {
    return $this -> mRoot;
  }
  
  /**
  * return CHtm_Tree_Node
  */
  
  public function & add($aCaption, $aProps = array()) {
    return $this -> mRoot -> add($aCaption, $aProps);
  }
  
  protected function getCont() {
    $lRet = $this -> getComment('start');
    $lRet.= '<ul class="tree">';
    foreach ($this -> mRoot as $lVal) {
      $lRet.= $this -> getNode($lVal);
    }
    $lRet.= '</ul>';
    $lRet.= $this -> getComment('end');    
    return $lRet;
  }
  
  protected function getNode($aNode, $aDepth = 0) {
    try {
      $lRet = '<li id="'.$aNode -> getId().'">';

      $lRet.= $this -> getNodeSpan($aNode);
      
      if ($aNode -> hasChildren()) {
        $lDis = ($aNode -> isExpanded()) ? '' : ' style="display:none"';
        $lRet.= '<ul'.$lDis.'>';
        $lChi = $aNode -> getChildren();
        foreach ($lChi as $lChild) {
          $lRet.= $this -> getNode($lChild, $aDepth +1);
        }
        $lRet.= '</ul>';
      }
      $lRet.= '</li>'.LF;
    } catch (Exception $e) {
      $this -> dbg($e -> getMessage());
    }
    return $lRet;
  }
  
  protected function getNodeSpan($aNode) {
    $lRet.= '<span class="nav" id="'.$aNode -> getId().'s" ';
    if ($aNode -> hasChildren()) {
      $lRet.= ' onclick="$(this).up(\'li\').down(\'ul\').toggle()"';
    }
    $lRet.= '>'.htm($aNode -> getVal('caption')).'</span>';
    return $lRet;
  }

}