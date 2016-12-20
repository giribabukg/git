<?php
class CInc_Sys_Msg_Form_Edit extends CSys_Msg_Form_Base {

  public function __construct($aUid) {
    parent::__construct("sys-msg.edtMsg",lan("sys-msg.caption"), '');

    $this -> mUid = intval($aUid);
    $this -> setParam('val[id]', $this -> mUid);
    $this -> setParam('old[id]', $this -> mUid);
    $this -> load();
  }

  protected function load() {
    $lQry = new CCor_Qry('SELECT * FROM al_sys_msg WHERE id='.$this -> mUid);
    if ($lRow = $lQry -> getAssoc()) {
      $lRow = array_map('stripslashes', $lRow);

      $this -> assignVal($lRow);

    } else {
      $this -> msg('System Message record not found');
    }
  }
}