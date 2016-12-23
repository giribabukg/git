<?php
class CInc_Gru_Menu extends CHtm_Vmenu {

  public function __construct($aId, $aKey) {
    $this -> mId = intval($aId);
    $lQry = new CCor_Qry('SELECT * FROM al_gru WHERE id='.$this -> mId);
    $this -> mDat = $lQry -> getDat();
    parent::__construct($this -> mDat['name']);
    $this -> setKey($aKey);
    $this -> addItems();
  }

  protected function addItems() {
    $this -> addItem('dat', 'index.php?act=gru.edt&amp;id='.$this -> mId, lan('lib.data'));
    $this -> addItem('mem', 'index.php?act=gru-mem&amp;id='.$this -> mId, lan('usr-mem.menu'));
    
    $this -> addItem('add', '#', lan('lib.additional'));
    $this -> addSubItem('add', 'his', 'index.php?act=gru-his&amp;id='.$this -> mId, lan('lib.history'));
    
    $this -> addItem('rig', '#', lan('lib.rights'));
    $this -> addSubItem('rig', 'mid_0', 'index.php?act=gru-rig&amp;mid=0&amp;id='.$this -> mId, lan('usr-rig.global'));

    // Anzeige der Hilfstabelle - nur wenn die Gruppe das Recht dazu hat.
    $lMemRig = '';
    $lSql = 'SELECT level FROM al_gru_rig WHERE group_id ='.$this -> mId.' AND code="htg" AND mand=0';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lMemRig = intval($lRow['level']);
    }
    if(!empty($lMemRig)) //Helptables.Lesen == true
      $this -> addSubItem('rig', 'mid_htg', 'index.php?act=gru-rig&amp;mid='.MID.'&amp;rig=htg&amp;id='.$this -> mId, lan('htb.menu'));

    if (0 < MID) {

      $lArr = CCor_Res::extract('id', 'name_'.LAN, 'mand');
  #    foreach ($lArr as $lKey => $lVal) {
  #      $this -> addSubItem('rig', 'mid_'.$lKey, 'index.php?act=gru-rig&amp;mid='.$lKey.'&amp;id='.$this -> mId, $lVal);
  #    }
      $this -> addSubItem('rig', 'mid_'.MID, 'index.php?act=gru-rig&amp;mid='.MID.'&amp;id='.$this -> mId, $lArr[MID]);
    }
     $this -> addSubItem('rig', 'mid_fie', 'index.php?act=gru-rig&amp;mid='.MID.'&amp;rig=fie&amp;id='.$this -> mId, lan('fie.menu'));
     $this -> addSubItem('rig', 'mid_htb', 'index.php?act=gru-rig&amp;mid='.MID.'&amp;rig=htg&amp;id='.$this -> mId, lan('htb.menu'));
      

     // Status wechsel
     $this -> addItem('crp', '#', lan('crp-stp.menu'));
     $lArr = CCor_Res::extract('id', 'name_'.LAN, 'crpmaster');
     foreach ($lArr as $lKey => $lVal) {
       $this -> addSubItem('crp', 'crp_'.$lKey, 'index.php?act=gru-crp-step&amp;crp='.$lKey.'&amp;id='.$this -> mId, $lVal);
     }

    // Status Edit Rights
    $this -> addItem('crp_status_edit', '#', lan('crp-status-edit.menu'));
    $lArr = CCor_Res::extract('id', 'name_'.LAN, 'crpmaster');
    foreach ($lArr as $lKey => $lVal) {
      $this -> addSubItem('crp_status_edit', 'crp_status_edit_'.$lKey, 'index.php?act=gru-crp-status&amp;crp='.$lKey.'&amp;id='.$this -> mId, $lVal);
    }


    $this -> addItem('bck', 'index.php?act=gru', lan('lib.back'));
  }
}