<?php
class CInc_Cor_Anygrp extends CCor_Obj {

  private $mId;
  private $mPriv;
  private $mVal;

  public function __construct($aId) {
    $this -> mId = intval($aId);
    $this -> loadVals();
  }

  protected function loadVals() {
    if (empty($this -> mId)) {
      return;
    }
    $lQry = new CCor_Qry('SELECT * FROM al_gru WHERE id='.$this -> mId);
    $this -> mVal = $lQry -> getAssoc();
  }

  public function exist() {
    if (!empty($this -> mVal)) {
      return TRUE;
    } else {
      return FALSE;
    }
  }

  public function getVal($aKey) {
    return (isset($this -> mVal[$aKey])) ? $this -> mVal[$aKey] : '';
  }

  public function getId() {
    return $this -> mId;
  }
}