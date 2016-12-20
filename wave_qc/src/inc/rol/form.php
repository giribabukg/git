<?php
class CInc_Rol_Form extends CHtm_Form {

  public function __construct($aAct, $aCaption, $aCancel = NULL) {
    parent::__construct($aAct, $aCaption, $aCancel);
    $this -> addDef(fie('name',  'Name'));
    
    $lArr = array('' => '', 'User' => 'User Role', 'Group' => 'Group Role');
    $lAttr = array('onchange' => 'javascript:defRole(this);');
    $this -> addDef(fie('typ', lan('lib.roletyp'), 'select', $lArr, $lAttr));

    $lFie = CCor_Res::get('fie');
    $lArr = array('' => '');
    foreach ($lFie as $lRow) {
      if ($lRow['typ'] == 'uselect' || $lRow['typ'] == 'gselect') {
        $lArr[$lRow['alias']] = $lRow['name_'.LAN];
      }
    }
    $this -> addDef(fie('alias', lan('lib.jobfield'), 'select', $lArr));
  }

  public function load($aId) {
    $lId = intval($aId);
    $lQry = new CCor_Qry('SELECT * FROM al_rol WHERE id='.$lId);
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