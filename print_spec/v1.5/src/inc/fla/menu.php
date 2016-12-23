<?php
class CInc_Fla_Menu extends CHtm_Vmenu {

  public function __construct($aId, $aKey) {
    $this -> mId = intval($aId);
    $lNam = CCor_Qry::getStr('SELECT name_'.LAN.' FROM al_fla WHERE mand='.MID.' AND id='.$this -> mId);
    parent::__construct($lNam);
    $this -> setKey($aKey);
    $this -> addItem('dat', 'index.php?act=fla.edt&amp;id='.$this -> mId, lan('lib.data'));
    $this -> addItem('bck', 'index.php?act=fla', lan('lib.backtolist'));
  }

}