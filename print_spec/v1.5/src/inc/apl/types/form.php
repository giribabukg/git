<?php
class CInc_Apl_Types_Form extends CHtm_Form {

  public function __construct($aAct, $aCaption, $aCancel = NULL) {
    parent::__construct($aAct, $aCaption, $aCancel);
    $this -> setAtt('class', 'tbl w600');

    $this -> addDef(fie('code', 'Code'));
    $this -> addDef(fie('name', 'Name'));
    $this -> addDef(fie('short', 'Short'));
    
    $lArr = array('1' => 'Approval Process', '2' => 'Collection Process');
    $this -> addDef(fie('apl_mode', lan('apl-types.type'), 'select', $lArr));

    $lArr = array('dom' => 'apt_flag');
    $this -> addDef(fie('flags', lan('lib.flags'), 'bitset', $lArr));

    $lArr = array('res' => 'eve');
    $this -> addDef(fie('event_completed', lan('apl-types.oncompleted'), 'resselect', $lArr));
  }

  public function load($aId) {
    $lId = intval($aId);
    $lSql = 'SELECT * FROM al_apl_types WHERE id='.$lId;
    $lQry = new CCor_Qry($lSql);
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