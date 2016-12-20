<?php
class CInc_Crp_Menu extends CHtm_Vmenu {

  public function __construct($aId, $aKey) {
    $this -> mId = intval($aId);

    if (0 == MID) {
      $lSql = 'SELECT name_'.LAN.' FROM al_crp_mastertpl WHERE mand='.MID.' AND id='.$this -> mId;
    } else {
      $lSql = 'SELECT name_'.LAN.' FROM al_crp_master WHERE mand='.MID.' AND id='.$this -> mId;
    }
    $lNam = CCor_Qry::getStr($lSql);
    $this -> dbg($lSql);

    parent::__construct($lNam);

    $this -> setKey($aKey);
    $this -> addItem('dat', 'index.php?act=crp.edt&amp;id='.$this -> mId, lan('lib.data'));
    $this -> addItem('sta', 'index.php?act=crp-sta&amp;id='.$this -> mId, lan('lib.status'));
    $this -> addItem('ddl', 'index.php?act=crp-ddl&amp;id='.$this -> mId, lan('lib.ddl'));
    $this -> addItem('bck', 'index.php?act=crp', lan('lib.backtolist'));
  }
}