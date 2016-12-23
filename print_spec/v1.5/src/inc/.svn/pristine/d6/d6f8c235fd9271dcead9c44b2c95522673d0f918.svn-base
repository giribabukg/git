<?php
class CInc_Conditions_Form extends CHtm_Form {

  public function __construct($aAct, $aCaption, $aCancel = NULL) {
    parent::__construct($aAct, $aCaption, $aCancel);

    $this -> setAtt('class', 'tbl w800');

    $this -> addDef(fie('id',    '',              'hidden'));
    $this -> addDef(fie('param', '',              'hidden'));
    $this -> addDef(fie('type',  '',              'hidden'));
    $this -> addDef(fie('name',  lan('lib.name'), 'text', NULL, array('class' => 'inp w600')));

    $this -> setVal('mand', MID);
  }

  public function load($aId) {
    $lId = intval($aId);
    $lQry = new CCor_Qry('SELECT * FROM al_cond WHERE id='.$lId);
    if ($lRow = $lQry -> getAssoc()) {
      $this -> assignVal($lRow);
    } else {
      $this -> msg('Record not found', mtUser, mlError);
    }
  }

  protected function getFieldForm() {
    $lType = $this -> getVal('type', 'simple');
    $lParams = $this -> getVal('params', array());

    $lReg = new CInc_App_Condition_Registry();
    $lObj = $lReg -> factory($lType);

    $lRet = parent::getFieldForm();
    $lRet.= $lObj -> getSubForm($lParams);
    return $lRet;
  }
}