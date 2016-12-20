<?php
class CInc_Wiz_Itm_Mod extends CCor_Obj {

  public function __construct() {
    $this -> mVal = array();
  }

  public function getPost(ICor_Req $aReq) {
    $this -> mVal['mainfield_id'] = $aReq -> getVal('mainfield_id');
    $lArr = $aReq -> getVal('dst');
    if (!empty($lArr)) {
      $this -> mVal['secondary_fields'] = implode(',', $lArr);
    }
    $this -> mVal['wiz_id'] = $aReq -> getVal('id');
    $this -> mId = $aReq -> getVal('sid');
  }

  public function insert() {
    $lNew = 0;
    $lQry = new CCor_Qry('SELECT COUNT(*) AS max_h FROM al_wiz_items WHERE mand='.intval(MID).' AND wiz_id='.$this -> mVal['wiz_id']);
    if ($lRow = $lQry -> getDat()) {
      $lNew = intval($lRow['max_h']);
    }
    $this -> mVal['hierarchy'] = $lNew;
    $lSql = 'INSERT INTO al_wiz_items SET ';
    $lSql.= 'mand='.esc(MID).',';
    foreach ($this -> mVal as $lKey => $lVal) {
      if (!empty($lVal)) {
        $lSql.= $lKey.'="'.addslashes($lVal).'",';
      }
    }
    $lSql = strip($lSql);
    $this -> dbg($lSql);
    $lQry -> query($lSql);
  }

  public function update() {
    $lSql = 'UPDATE al_wiz_items SET ';
    foreach ($this -> mVal as $lKey => $lVal) {
      $lSql.= $lKey.'="'.addslashes($lVal).'",';
    }
    $lSql = strip($lSql);
    $lSql.= ' WHERE mand='.intval(MID).' AND id='.intval($this -> mId);
    CCor_Qry::exec($lSql);
  }

  public function delete($aId, $aWiz) {
    $lId  = intval($aId);
    $lWiz = intval($aWiz);
    $lSql = 'DELETE FROM al_wiz_items WHERE mand='.intval(MID).' AND id='.$lId;
    $lQry = new CCor_Qry($lSql);
    $lCtr = 0;
    $lSql = 'SELECT id,hierarchy FROM al_wiz_items WHERE mand='.intval(MID).' AND wiz_id='.$lWiz.' ORDER BY hierarchy';
    $lQry -> query($lSql);
    foreach ($lQry as $lRow) {
      $lHie = intval($lRow['hierarchy']);
      if ($lHie != $lCtr) {
        CCor_Qry::exec('UPDATE al_wiz_items SET hierarchy='.$lCtr.' WHERE mand='.intval(MID).' AND id='.$lRow['id']);
      }
      $lCtr++;
    }
  }

}