<?php
class CInc_Rol_Menu extends CHtm_Vmenu {

  public function __construct($aId, $aKey) {
    $this -> mId = intval($aId);
    $lNam = CCor_Qry::getStr('SELECT name FROM al_rol WHERE id='.$this -> mId);
    parent::__construct($lNam);
    $this -> setKey($aKey);
    $this -> addItem('dat', 'index.php?act=rol.edt&amp;id='.$this -> mId, lan('lib.data'));
    #/*
    #$this -> addItem('rig', '#', 'Rights');
    #$this -> addSubItem('rig', 'mid_0', 'index.php?act=rol-rig&amp;mid=0&amp;id='.$this -> mId, lan('usr-rig.global'));
    #$lArr = CCor_Res::extract('id', 'name_'.LAN, 'mand');
    #foreach ($lArr as $lKey => $lVal) {
    #  $this -> addSubItem('rig', 'mid_'.$lKey, 'index.php?act=rol-rig&amp;mid='.$lKey.'&amp;id='.$this -> mId, $lVal);
    #}
    #*/
    $this -> addItem('crp', '#',  lan('crp-stp.menu'));
    $this -> addItem('stp', '#',  lan('crp-stp-inmytask'));
    $lArr = CCor_Res::extract('id', 'name_'.LAN, 'crpmaster');
    foreach ($lArr as $lKey => $lVal) {
      $this -> addSubItem('crp', 'crp_'.$lKey, 'index.php?act=rol-crp&amp;crp='.$lKey.'&amp;id='.$this -> mId, $lVal);
      $this -> addSubItem('stp', 'stp_'.$lKey, 'index.php?act=rol-stp&amp;crp='.$lKey.'&amp;id='.$this -> mId, $lVal);
    }

    $this -> addItem('bck', 'index.php?act=rol', lan('lib.back'));
  }
}