<?php
class CInc_Api_Alink_Param extends CCor_Obj {
  
  protected $mMethod;
  protected $mParams;
  protected $mDom;
  
  public function __construct($aMethod) {
    $this -> mMethod = $aMethod;
    $this -> clear();
    
    $this -> mDom  = new DOMDocument('1.0', 'UTF-8');
    $this -> mRoot = $this -> mDom -> appendChild($this -> mDom -> createElement('query'));
    
    $lMet  = $this -> mRoot -> appendChild($this -> mDom -> createElement('method'));
    $lMet -> appendChild($this -> mDom -> createTextNode($this -> mMethod));

    $this -> mParamNode = $this -> mRoot -> appendChild($this -> mDom -> createElement('params'));
  }
  
  public function add($aKey, $aVal) {
    $this -> mParams[$aKey] = $aVal;
  }
  
  public function clear() {
    $this -> mParams = array();
    $this -> add('utf8', 1);
	$this -> add('version', 2);
  }
  
  public function getXml() {
    foreach ($this -> mParams as $lKey => $lVal) {
      $lChild = $this -> mParamNode -> appendChild($this -> mDom -> createElement($lKey));
      $this -> insertValue($lChild, $lVal);
    }
    return $this -> mDom -> saveXml();   
  }
  
  protected function insertValue(& $aNode, $aVal) {
    if (is_array($aVal)) {
      foreach ($aVal as $lKey => $lVal) {
        if (is_numeric($lKey)) {
          $lNode = $aNode -> appendChild($this -> mDom -> createElement('item'));
        } else {
          $lNode = $aNode -> appendChild($this -> mDom -> createElement($lKey));
        }        
        $this -> insertValue($lNode, $lVal);
      }
    } else {
      #$aNode -> appendChild($this -> mDom -> createTextNode(utf8_encode($aVal)));
      $aNode -> appendChild($this -> mDom -> createTextNode($aVal));
    }
  }
  
}