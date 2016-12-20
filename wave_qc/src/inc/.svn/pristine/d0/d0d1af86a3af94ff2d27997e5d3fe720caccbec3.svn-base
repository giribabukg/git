<?php
class CInc_Htm_Tree_Node extends CCor_Obj implements Iterator {

  protected $mParent;
  protected $mChildren;
  protected $mVal;

  protected $mCur;
  protected $mKey;

  protected $mExpanded = FALSE;

  public function __construct($aCaption = '', $aParent = NULL, $aProps = array()) {
    $this -> mVal = new CCor_Dat();
    if (!empty($aProps)) {
      $this -> mVal-> assignArray($aProps);
    }
    $this -> mVal['id'] = getNum('t');
    $this -> mVal['caption'] = $aCaption;

    #$this -> dbg($aCaption.' '.$this -> mVal['id']);

    $this -> mParent   = $aParent;
    $this -> mChildren = array();
  }

  public function getProps() {
    return $this -> mVal;
  }

  public function getVal($aKey) {
    return $this -> mVal[$aKey];
  }

  public function getId() {
    return $this -> mVal['id'];
  }

  /**
  * return CHtm_Tree_Node
  */

  public function & add($aCaption, $aProps = array()) {
    $lNod = new CHtm_Tree_Node($aCaption, $this, $aProps);
    $this -> mChildren[$lNod -> getId()] = $lNod;
    return $lNod;
  }

  public function hasParent() {
    return (NULL !== $this -> mParent);
  }

  public function getParent() {
    return $this -> mParent;
  }

  public function hasChildren() {
    return (!empty($this -> mChildren));
  }

  public function getChildren() {
    return $this -> mChildren;
  }

  public function isExpanded() {
    return $this -> mExpanded;
  }

  public function setExpanded($aFlag = TRUE) {
    $this -> mExpanded = $aFlag;
  }

  public function current() {
    return current($this -> mChildren);
  }

  public function key() {
    return key($this -> mChildren);
  }

  public function next() {
    return next($this -> mChildren);
  }

  public function rewind() {
    return reset($this -> mChildren);
  }

  public function valid() {
    return $this -> current();
  }

}