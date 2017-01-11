<?php
class CInc_Htb_Itm_Form_Base extends CHtm_Form {

  public function __construct($aAct, $aCaption, $aCancel = NULL) {
    parent::__construct($aAct, $aCaption, $aCancel);
    $this -> addDef(fie('value',    lan('lib.key')));

    $lArr[0] = '[All]';
    $lArr[MID] = MANDATOR_NAME;

    $this -> addDef(fie('mand', lan('lib.mand'), 'select', $lArr ));
    $this -> mAvailLang = CCor_Res::get('languages');
    foreach ($this -> mAvailLang as $lLang => $lName) {
      $this -> addDef(fie('value_'.$lLang, lan('lib.value').' ('.strtoupper($lLang).')'));
    }

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