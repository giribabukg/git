<?php
class CInc_Usg_Info_Form extends CHtm_Form {

  public function __construct($aUserId) {
    $this -> mUid = intval($aUserId);
    parent::__construct('usg-info.sedt', lan('usg-info.menu'), FALSE);

    $this -> getFields();
    $this -> getValues();
    $this -> setParam('id', $this -> mUid);
    $this -> setParam('old[id]', $this -> mUid);
    $this -> setParam('val[id]', $this -> mUid);

    $this -> setReadOnly(); // setzt alle Editierfelder auf READONLY
    $this -> setButtons(FALSE); // entfernt die Button-Zeile
    $this -> setAltLan(TRUE); // nutzt lan(wec-usr) etc. statt htm($variablenname['name_'.LAN])
  }

  protected function getFields() {
    $lSql = 'SELECT DISTINCT(iid) AS iid FROM al_usr_info ORDER BY iid';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $this -> addDef(fie($lRow['iid'], $lRow['iid']));
    }
  }

  protected function getValues() {
    $lSql = 'SELECT iid,val FROM al_usr_info WHERE uid='.$this -> mUid;
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $this -> setVal($lRow['iid'], $lRow['val']);
    }
  }

}