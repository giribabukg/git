<?php
class CInc_Pck_Menu extends CHtm_Vmenu {

  public function __construct($aDom, $aKey) {
    $this -> mDom = $aDom;
    $lNam = CCor_Qry::getStr('SELECT description_'.LAN.' FROM al_pck_master WHERE mand IN (0,'.MID.') AND domain='.esc($this -> mDom));
    parent::__construct($lNam);
    $this -> setKey($aKey);
    $this -> addItem('dat', 'index.php?act=pck.edt&amp;dom='.$this -> mDom, lan('lib.data'));
    $this -> addItem('col', 'index.php?act=pck-col&amp;dom='.$this -> mDom, lan('pck.column'));
    $this -> addItem('itm', 'index.php?act=pck-itm&amp;xx=1&amp;dom='.$this -> mDom, lan('lib.items'));
    $this -> addItem('bck', 'index.php?act=pck', lan('lib.backtolist'));
  }
}