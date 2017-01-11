<?php
class CInc_Usr_Menu extends CHtm_Vmenu {

  public function __construct($aId, $aKey) {
    $this -> mId = intval($aId);
    $lNam = CCor_Qry::getStr('SELECT lastname FROM al_usr WHERE id='.$this -> mId);
    parent::__construct($lNam);
    $lUsr = CCor_Usr::getInstance();

    $this -> setKey($aKey);
    $this -> addItem('dat', 'index.php?act=usr.edt&amp;id='.$this -> mId, lan('lib.data'));
    $this -> addItem('allmem', '#', lan('usr-mem.menu'));
    $this -> addSubItem('allmem','mem', 'index.php?act=usr-mem&amp;id='.$this -> mId, lan('usr-gru-mem.menu'));
    $this -> addSubItem('allmem','chmand', 'index.php?act=usr-chmand&amp;id='.$this -> mId, lan('usr-mand-mem.menu'));
    #$this -> addItem('fil', 'index.php?act=usr-fil&amp;id='.$this -> mId, lan('job-filt'));
    $this -> addItem('add', '#', lan('lib.additional'));

    $this -> addSubItem('add', 'his', 'index.php?act=usr-his&amp;id='.$this -> mId, lan('lib.history'));
    $this -> addSubItem('add', 'hom2', 'index.php?act=usr.hom2&amp;id='.$this -> mId, lan('usr-hom2'));
    $this -> addSubItem('add', 'rep2', 'index.php?act=usr.rep2&amp;id='.$this -> mId, lan('usr-rep2'));

    if ($lUsr -> canRead('usr-info')) {
      $this -> addSubItem('add', 'info', 'index.php?act=usr-info&amp;id='.$this -> mId, lan('usr-info.menu'));
    }
    if ($lUsr -> canRead('usr-wecusr')) {
      $this -> addSubItem('add', 'wecu', 'index.php?act=usr.wecusr&amp;id='.$this -> mId, lan('usr-wecusr.menu'));
    }
    //Rights
    if ($lUsr -> canRead('usr-rig')) {
      $this -> addItem('rig', '#', lan('lib.rights'));
      $this -> addSubItem('rig', 'mid_0', 'index.php?act=usr-rig&amp;mid=0&amp;id='.$this -> mId, lan('usr-rig.global'));
      $this -> addSubItem('rig', 'mid_htg', 'index.php?act=usr-rig&amp;rig=htg&amp;id='.$this -> mId, lan('htb.menu'));
      if (0 < MID) {
        $lArr = CCor_Res::extract('id', 'name_'.LAN, 'mand');
        $this -> addSubItem('rig', 'mid_'.MID, 'index.php?act=usr-rig&amp;mid='.MID.'&amp;id='.$this -> mId, $lArr[MID]);
      }
      $this -> addSubItem('rig', 'mid_fie', 'index.php?act=usr-rig&amp;mid='.MID.'&amp;rig=fie&amp;id='.$this -> mId, lan('fie.menu'));
    }

    //Status wechsel (Step) Rights
    if ($lUsr -> canRead('usr-crp-step')) {
      $this -> addItem('crp', '#', lan('crp-stp.menu'));
      $lArr = CCor_Res::extract('id', 'name_'.LAN, 'crpmaster');
      foreach ($lArr as $lKey => $lVal) {
        $this -> addSubItem('crp', 'crp_'.$lKey, 'index.php?act=usr-crp-step&amp;crp='.$lKey.'&amp;id='.$this -> mId, $lVal);
      }
    }

    //Status Edit Rights
    if ($lUsr -> canRead('usr-crp-sta')) {
      $this -> addItem('crp_status_edit', '#', lan('crp-status-edit.menu'));
      $lArr = CCor_Res::extract('id', 'name_'.LAN, 'crpmaster');
      foreach ($lArr as $lKey => $lVal) {
        $this -> addSubItem('crp_status_edit', 'crp_status_edit_'.$lKey, 'index.php?act=usr-crp-status&amp;crp='.$lKey.'&amp;id='.$this -> mId, $lVal);
      }
    }

    $this -> addItem('opt', 'index.php?act=usr-opt&amp;id='.$this -> mId, lan('lib.opt'));
    if ($lUsr -> canRead('usr-agent')) {
      $this -> addItem('agent', 'index.php?act=usr-agent&amp;id='.$this -> mId, lan('lib.agent'));
    }
    $this -> addItem('bck', 'index.php?act=usr', lan('lib.back'));
  }

}