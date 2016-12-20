<?php
class CInc_Usg_Info_Mod extends CCor_Mod_Base {

  public function __construct() {
    $lSql = 'SELECT DISTINCT(iid) AS iid FROM al_usr_info ORDER BY iid';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $this -> addField(fie($lRow['iid'], $lRow['iid']));
    }
  }

  protected function doUpdate() {
    $lUid = intval($this -> getVal('id'));
    $lQry = new CCor_Qry();
    foreach ($this -> mUpd as $lKey => $lVal) {
      if ('' == $lVal) {
        $lSql = 'DELETE FROM al_usr_info WHERE iid='.esc($lKey).' ';
        $lSql.= 'AND uid='.$lUid;
      } else {
        $lSql = 'REPLACE INTO al_usr_info SET iid='.esc($lKey).',';
        $lSql.= 'uid='.$lUid.',';
        $lSql.= 'val='.esc($lVal);
      }
      $lQry -> query($lSql);
    }
  }

  protected function doInsert() {
  }

  protected function doDelete($aId) {
  }

}