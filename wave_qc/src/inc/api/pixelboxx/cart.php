<?php
class CInc_Api_Pixelboxx_Cart extends CCor_Obj {
  
  public function __construct() {
    $lUsr = CCor_Usr::getInstance();
    $this->mCart = $lUsr->getPref('pbox.cart');
    if (is_string($this->mCart)) {
      $this->mCart = unserialize($this->mCart);
    }
    if (empty($this->mCart)) {
      $this->mCart = array();
    }
    $this->mDirty = false;
  }
  
  public function getCart() {
    return $this->mCart;
  }
  
  public function addToCart($aDoi, $aName, $aSize, $aDate) {
    $lRec = array();
    $lRec['doi'] = $aDoi;
    $lRec['name'] = $aName;
    $lRec['size'] = $aSize;
    $lRec['date'] = $aDate;
    $this->mCart[$aDoi] = $lRec;
    $this->mDirty = true;
  }
  
  public function remove($aDoi) {
    if (isset($this->mCart[$aDoi])) {
      unset($this->mCart[$aDoi]);
      $this->mDirty = true;
    }
  }
  
  public function isInCart($aDoi) {
    return isset($this->mCart[$aDoi]);
  }
  
  public function save() {
    if (!$this->mDirty) return;
    $lUsr = CCor_Usr::getInstance();
    $lUsr->setPref('pbox.cart', $this->mCart);
    $this->mDirty = false;
  }
  
  public function clear() {
    $this->mCart = array();
    $this->mDirty = true;
  }
  
  public function __destruct() {
    $this->save();
  }

}