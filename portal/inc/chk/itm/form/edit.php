<?php
class CInc_Chk_Itm_Form_Edit extends CChk_Itm_Form_Base {

  public function __construct($aId, $aDomain) {
    parent::__construct('chk-itm.sedt', lan('chk.itm.edt'), 'chk-itm&domain='.$aDomain);

    $this -> mId = intval($aId);
    $this -> setParam('val[id]', $this -> mId);
    $this -> setParam('old[id]', $this -> mId);
    $this -> setDomain($aDomain);
    $this -> load();
  }

  protected function load() {
    $lQry = new CCor_Qry('SELECT * FROM al_chk_items WHERE id='.$this -> mId);
    if ($lRow = $lQry -> getAssoc()) {
      $this -> assignVal($lRow);
    } else {
      $this -> msg('Record not found', mtUser, mlError);
    }
  }
}