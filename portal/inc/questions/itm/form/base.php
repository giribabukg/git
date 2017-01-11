<?php
class CInc_Questions_Itm_Form_Base extends CHtm_Form {

  public function __construct($aAct, $aCaption, $aCancel = NULL) {
    parent::__construct($aAct, $aCaption, $aCancel);
    $this -> setAtt('class', 'tbl w700');
    
    $this -> mAvailLang = CCor_Res::get('languages');
    $lArr['class'] = 'inp w550';
    $lArr['rows']  = 5;
    foreach ($this -> mAvailLang as $lLang => $lName) {
      $this -> addDef(fie('name_'.$lLang, lan('lib.question').' ('.strtoupper($lLang).')', 'memo', NULL, $lArr));
    }
    
    $lArr = array('string'=>'Standard', 'boolean' => 'Checkbox');
    $this -> addDef(fie('question_type', lan('lib.type'), 'select', $lArr));

    $this->addDef(fie('size', lan('lib.opt.lines'), 'int'));
    $this->mVal['size'] = 2;

    $lPar = array('res' => 'cond', 'key' => 'id', 'val' => 'name');
    $this -> addDef(fie('cnd_id', lan('lib.condition'), 'resselect', $lPar));
  }

  public function setDomain($aDomain) {
    $this -> setParam('domain', $aDomain);
    $this -> setParam('val[domain]', $aDomain);
    $this -> setParam('old[domain]', $aDomain);
  }
  
  public function setMasterId($aMasterId) {
    $this -> setParam('master_id', $aMasterId);
    $this -> setParam('val[master_id]', $aMasterId);
    $this -> setParam('old[master_id]', $aMasterId);
  }
}