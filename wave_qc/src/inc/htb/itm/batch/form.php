<?php
class CInc_Htb_Itm_Batch_Form extends CHtm_Form {

  public function __construct($aAct, $aCaption, $aCancel = NULL) {
    parent::__construct($aAct, $aCaption, $aCancel);
    //Mand
    $lArr[0] = '[All]';
    $lArr[MID] = MANDATOR_NAME;
    $this -> addDef(fie('mand', lan('lib.mand'), 'select', $lArr ));

    //Batch Input
    if(isset($_GET["batch"])) {
      $lPre = $_GET["batch"];
    }
    else {
      $lPre ="";
    }
    $this -> addDef(fie('batch', lan("lib.data"), 'memo', '', array("placeholder" => "key;value_de;value_en\nkey;value_de;value_en\nkey;value_de;value_en\nkey;value_de;value_en")));
    $this -> setVal('mand', MID); // als Default bei neuem Eintrag
  }

  public function setDom($aDom) {
    $this -> setParam('dom', $aDom);
    $this -> setParam('val[domain]', $aDom);
    $this -> setParam('old[domain]', $aDom);

    $lSql = 'SELECT description FROM al_htb_master WHERE domain="'.addslashes($aDom).'"';
    if ($lCap = CCor_Qry::getStr($lSql)) {
      $this -> mCap.= ' ('.$lCap.')';
    }
  }

}