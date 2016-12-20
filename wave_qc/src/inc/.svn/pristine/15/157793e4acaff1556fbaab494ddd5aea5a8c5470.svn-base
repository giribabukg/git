<?php
class CInc_Content_Form extends CHtm_Form {

  public function __construct($aAct, $aCaption, $aCancel = NULL) {
    parent::__construct($aAct, $aCaption, $aCancel);
    $this -> setAtt('class', 'tbl w450');

    $this -> addDef(fie('id', '', 'hidden'));
    $this -> addDef(fie('alias','Alias'));
    $this -> addDef(fie('name_en', 'Name EN'));
    $this -> addDef(fie('name_de', 'Name DE'));
    
    $this -> addDef(fie('content_en', 'Content (EN)', 'rich'));
    $this -> addDef(fie('content_de', 'Content (DE)', 'rich'));
    $this -> setVal('mand', MID);
    
    $lPag = CHtm_Page::getInstance();
    $lPag -> addJsSrc('js/mce/tiny_mce.js');
  }

  public function load($aId) {
    $lId = intval($aId);
    $lQry = new CCor_Qry('SELECT * FROM al_text_content WHERE id='.$lId);
    if ($lRow = $lQry -> getAssoc()) {
      $this -> assignVal($lRow);
    } else {
      $this -> msg('Record not found', mtUser, mlError);
    }
  }
}