<?php
class CInc_Wiz_Menu extends CHtm_Vmenu {

  public function __construct($aId, $aKey) {
    $this -> mId = intval($aId);
    $lNam = CCor_Qry::getStr('SELECT name_'.LAN.' FROM al_wiz_master WHERE id='.$this -> mId);
    parent::__construct($lNam);
    $this -> setKey($aKey);
    $this -> addItem('dat', 'index.php?act=wiz.edt&amp;id='.$this -> mId, lan('lib.data'));
    $this -> addItem('itm', 'index.php?act=wiz-itm&amp;id='.$this -> mId, lan('fie.menu'));
    $this -> addItem('bck', 'index.php?act=wiz', lan('lib.back'));
  }
}