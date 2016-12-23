<?php
class CInc_Api_Alink_Query_Callquery extends CApi_Alink_Query {

  public function __construct($aQry, $aPrm = null) {
    parent::__construct('callQuery');

    $this -> addParam('sid', MAND);
    $this -> addParam('qry', $aQry);

    if (NULL !== $aPrm) {
      $this -> addParam('prm', $aPrm);
    }
  }
}