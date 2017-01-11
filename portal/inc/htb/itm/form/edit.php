<?php
class CInc_Htb_Itm_Form_Edit extends CHtb_Itm_Form_Base {

  public function __construct($aId, $aDom) {
    parent::__construct('htb-itm.sedt', lan('htb-itm.edt'), 'htb-itm&dom='.$aDom);
    $this -> mId = intval($aId);
    $this -> setParam('val[id]', $this -> mId);
    $this -> setParam('old[id]', $this -> mId);
    $this -> setDom($aDom);
    $this -> load();
    $this -> setParam('mand', $this -> getVal('mand'));
  }

  protected function load() {
    $lQry = new CCor_Qry('SELECT * FROM al_htb_itm WHERE mand IN(0,'.MID.') AND id='.$this -> mId);
    if ($lRow = $lQry -> getAssoc()) {
      $this -> assignVal($lRow);
    } else {
      $this -> msg('Record not found', mtUser, mlError);
    }
  }
}