<?php
class CInc_Usr_Fil_Form extends CHtm_Form {

  public function __construct($aUid) {
    $lUid = intval($aUid);
    parent::__construct('usr-fil.sedt', 'Job Filter', FALSE);
    $this -> setParam('id', $lUid);
    $this -> addDef(fie('marke', 'Brand'));
    $this -> addDef(fie('land_besteller', 'Order Country'));
    $this -> addDef(fie('land_vertrieb', 'Distribution Country'));
    $this -> addDef(fie('fie3', 'Printer'));
  }

}