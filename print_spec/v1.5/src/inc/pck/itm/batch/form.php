<?php
class CInc_Pck_Itm_Batch_Form extends CHtm_Form {

  public function __construct($aAct, $aCaption, $aFields, $aCancel = NULL) {
    parent::__construct($aAct, $aCaption, $aCancel);
    $this -> setAtt('class', 'w1000 tbl');
    $this->mFields = $aFields;

    //Mand
    $lArr[0] = '[All]';
    $lArr[MID] = MANDATOR_NAME;
    $this -> addDef(fie('mand', lan('lib.mand'), 'select', $lArr, array("class" => "w500")));

    //Batch Input
    $lPlaceholder = "";
    foreach($this->mFields as $key => $value) {
      $lPlaceholder .= $value["alias"].";";
    }
    $lPlaceholder = substr($lPlaceholder, 0, -1);
    $this -> addDef(fie('batch', lan("lib.data"), 'memo', "", array("placeholder" => $lPlaceholder, "class" => "w800")));
  }

  public function setDom($aDom) {
    $this -> setParam('dom', $aDom);
    $this -> setParam('val[domain]', $aDom);
    $this -> setParam('old[domain]', $aDom);

    $lMst = CCor_Res::extract('domain', 'description_'.LAN, 'pckmaster');
    if(isset($lMst[$aDom]) AND !empty($lMst[$aDom])) {
      $this -> mCap.= ' ('.$lMst[$aDom].')';
    }
  }
}