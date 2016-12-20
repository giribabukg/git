<?php
class CInc_Pck_Form_Base extends CHtm_Form {

  public function __construct($aAct, $aCaption, $aCancel = NULL) {
    parent::__construct($aAct, $aCaption, $aCancel);

    $this -> addDef(fie('domain', lan('lib.code')));
    $this -> mAvailLang = CCor_Res::get('languages');
    foreach ($this -> mAvailLang as $lLang => $lName) {
      $this -> addDef(fie('description_'.$lLang, lan('lib.name').' ('.strtoupper($lLang).')'));
    }

    #    $this -> addDef(fie('colcount', lan('lib.colcount')));
    $this -> addDef(fie('width', lan('lib.width')));
    $this -> addDef(fie('height', lan('lib.height')));
    $this -> addDef(fie('mand','','hidden'));
    $this -> setVal('mand', MID);
  }

  public function setDom($aDom) {
    $this -> setParam('dom', $aDom);
    $this -> setParam('val[domain]', $aDom);
    $this -> setParam('old[domain]', $aDom);
    /*
     $lSql = 'SELECT description_'.LAN.' FROM al_pck_master WHERE mand="'.MID.'" AND domain="'.addslashes($aDom).'"';
    if ($lCap = CCor_Qry::getStr($lSql)) {
    $this -> mCap.= ' ('.$lCap.')'; //Überschrift
    }
    */
  }
}