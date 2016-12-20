<?php
class CInc_Eve_Type_Form extends CHtm_Form {

  public function __construct($aAct, $aCaption, $aCancel = NULL) {
    parent::__construct($aAct, $aCaption, $aCancel);

    $this -> addDef(fie('mand', '', 'hidden'));
    $this -> addDef(fie('code', lan('lib.code')));
    $this -> addDef(fie('name', lan('lib.name')));

  }

  public function load($aCode) {
    $lSql = 'SELECT * FROM al_eve_types WHERE mand='.MID.' AND code='.esc($aCode);
    $lQry = new CCor_Qry($lSql);
    if ($lRow = $lQry -> getDat()) {
      $this -> assignVal($lRow);
      $this -> setParam('old[code]', $aCode);
      $this -> setParam('val[code]', $aCode);
    } else {
      $this -> msg('Record not found', mtUser, mlError);
    }
  }

}