<?php
class CInc_Gru_Form_Edit extends CGru_Form_Base {

  public function __construct($aGid) {
    parent::__construct('gru.sedt', lan('gru.edt'), NULL, NULL);

    $this -> mGid = intval($aGid);
    $this -> setParam('val[id]', $this -> mGid);
    $this -> setParam('old[id]', $this -> mGid);
    $this -> load();
  }

  protected function load() {
    $lQry = new CCor_Qry('SELECT * FROM al_gru WHERE id='.$this -> mGid);
    if ($lRow = $lQry -> getAssoc()) {
      $lRow = array_map('stripslashes', $lRow);

      #START #23375 "Extended user conditions"
      $lCndQry = new CCor_Qry('SELECT cond,cnd_id FROM al_cnd WHERE grp_id='.$this -> mGid.' AND mand='.MID);
      $lCndRow = $lCndQry -> getAssoc();
      if ($lCndRow['cnd_id'] > 0) {
        $lRow['cnd'] = $lCndRow['cnd_id'];
      } elseif (!empty($lCndRow['cond'])) {
        $lRow['cnd'] = $lCndRow['cond'];
      }

      $lCndQry = new CCor_Qry('SELECT procnd FROM al_gru WHERE id='.$this -> mGid);
      $lProCndRow = $lCndQry -> getAssoc();
      if ($lProCndRow['procnd'] > 0) {
        $lRow['procnd'] = $lProCndRow['procnd'];
      }
      #STOP #23375 "Extended user conditions"

      $this -> assignVal($lRow);

      #START #23375 "Extended user conditions"
      $this -> setParam('cond_id', $lRow['cnd']);
      #STOP #23375 "Extended user conditions"
    } else {
      $this -> msg('Group record not found', mtUser, mlError);
    }
  }

}