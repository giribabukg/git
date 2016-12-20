<?php
class CInc_Eve_Menu extends CHtm_Vmenu {

  public function __construct($aId, $aKey) {
    $this -> mId = intval($aId);
    $lNam = CCor_Qry::getStr('SELECT name_'.LAN.' FROM al_eve WHERE mand='.MID.' AND id='.$this -> mId);
    parent::__construct($lNam);
    $this -> setKey($aKey);
    $this -> addItem('dat', 'index.php?act=eve.edt&amp;id='.$this -> mId, lan('lib.data'));
    $this -> addItem('act', 'index.php?act=eve-act&amp;id='.$this -> mId, lan('eve.act'));
    $this -> addItem('bck', 'index.php?act=eve', lan('lib.backtolist'));
  }

}