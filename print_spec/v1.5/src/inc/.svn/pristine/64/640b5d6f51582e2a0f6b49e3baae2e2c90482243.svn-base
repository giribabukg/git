<?php
class CInc_Pck_Col_Form extends CHtm_Form {

  public function __construct($aAct, $aCaption, $aCancel = NULL) {
    parent::__construct($aAct, $aCaption, $aCancel);

    $this -> addDef(fie('alias', lan('lib.field'), 'resselect', array('res' => 'fie', 'key' => 'alias', 'val' => 'name_'.LAN)));

    $lArr = array();
    for ($i=1; $i<26; $i++) {
      $lArr[$i] = $i;
    }
    $this -> addDef(fie('position', lan('lib.pos'), 'select', $lArr, array('class' => 'w50')));
    $this -> addDef(fie('col', lan('pck.column'), 'select', $lArr, array('class' => 'w50')));
    $this -> addDef(fie('hidden', lan('lib.hidden'), 'select', array('N' => lan('lib.no'), 'Y' => lan('lib.yes'))));
    $this -> addDef(fie('image', lan('lib.img'), 'select', array('N' => lan('lib.no'), 'Y' => lan('lib.yes'))));
     $this -> addDef(fie('color', lan('lib.color'), 'select', array('N' => lan('lib.no'), 'Y' => lan('lib.yes'))));
    $this -> addDef(fie('pck_id', '', 'hidden'));
  }

  public function load($aId) {
    $lId = intval($aId);
    $lQry = new CCor_Qry('SELECT * FROM al_pck_columns WHERE id='.$lId);
    if ($lRow = $lQry -> getAssoc()) {
      $this -> assignVal($lRow);
      $this -> setParam('sid', $lId);
      $this -> setParam('old[id]', $lId);
      $this -> setParam('val[id]', $lId);
    } else {
      $this -> msg('Record not found', mtUser, mlError);
    }
  }


}