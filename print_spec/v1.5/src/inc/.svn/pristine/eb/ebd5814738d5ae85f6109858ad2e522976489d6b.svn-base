<?php
class CInc_Pck_Form extends CHtm_Form {

  public function __construct($aAct, $aCaption, $aCancel = NULL) {
    parent::__construct($aAct, $aCaption, $aCancel);
    $this -> addDef(fie('description_'.LAN,  lan('lib.description')));
    $this -> addDef(fie('code',  lan('lib.code'),'string',NULL, array('class' => 'inp w50')));
    $this -> addDef(fie('width', lan('lib.width'), 'integer',NULL, array('class' => 'inp w50')));
    $this -> addDef(fie('height', lan('lib.height'), 'integer',NULL, array('class' => 'inp w50')));
    $this -> addDef(fie('mand','','hidden'));
    $this -> setVal('mand', MID);
    
  }

  public function load($aId) {
    $lId = intval($aId);
    $lQry = new CCor_Qry('SELECT * FROM al_pck_master WHERE id='.$lId);
    if ($lRow = $lQry -> getAssoc()) {
      $this -> assignVal($lRow);
      $this -> setParam('id', $lId);
      $this -> setParam('old[id]', $lId);
      $this -> setParam('val[id]', $lId);
    } else {
      $this -> msg('Record not found', mtUser, mlError);
    }
  }


}