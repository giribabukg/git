<?php
class CInc_Usg_Form_Edit extends CUsg_Form_Base {

  public function __construct($aUid) {
    parent::__construct('usg.sedt', lan('usg.edt'), '');

    $this -> mUid = intval($aUid);
    $this -> setParam('val[id]', $this -> mUid);
    $this -> setParam('old[id]', $this -> mUid);
    $this -> mAdminLevel = $this -> getAdminLevelValue();
    $this -> load();
    $this -> remDef('gadmin');
  }

  protected function load() {
    $lQry = new CCor_Qry('SELECT * FROM al_usr WHERE id='.$this -> mUid);
    if ($lRow = $lQry -> getAssoc()) {
      $lRow = array_map('stripslashes', $lRow);

      #START #23375 "Extended user conditions"
      if (false != CCor_Cfg::get('extcnd')) {
        $lCndQry = new CCor_Qry('SELECT cond,cnd_id FROM al_cnd WHERE usr_id='.$this -> mUid.' AND mand='.MID);
        $lCndRow = $lCndQry -> getAssoc();
        if ($lCndRow['cnd_id'] > 0) {
          $lRow['cnd'] = $lCndRow['cnd_id'];
        } elseif (!empty($lCndRow['cond'])) {
          $lRow['cnd'] = $lCndRow['cond'];
        }
  
        $lCndQry = new CCor_Qry('SELECT procnd FROM al_usr WHERE id='.$this -> mUid);
        $lProCndRow = $lCndQry -> getAssoc();
        if ($lProCndRow['procnd'] > 0) {
          $lRow['procnd'] = $lProCndRow['procnd'];
        }
      } else {
        $lCndQry = new CCor_Qry('SELECT cond FROM al_cnd WHERE usr_id='.$this -> mUid.' AND mand='.MID);
        $lCndRow = $lCndQry -> getAssoc();
        $lRow['cnd'] = $lCndRow['cond'];
      }
      #STOP #23375 "Extended user conditions"

      $this -> assignVal($lRow);
      $this -> setVal('admlvl', $this -> mAdminLevel);
      $this -> setParam('cond_id', $lRow['cnd']);
    } else {
      $this -> msg('User record not found', mtUser, mlError);
    }
  }

  protected function remDef($aDef) {
    if (isset($this -> mFie[$aDef])) {
      unset($this -> mFie[$aDef]);
    }
  }
  
  protected function getAdminLevelValue() {
    $lRet = new CCor_Anyusr($this -> mUid);
    return $lRet -> getAdminLevel();
  }

}