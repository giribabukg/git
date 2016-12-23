<?php
class CInc_Sys_Svc_Form_Base extends CHtm_Form {

  public function __construct($aAct, $aCaption, $aCancel = NULL) {
    parent::__construct($aAct, $aCaption, $aCancel);
    $this -> addDef(fie('name', 'Name'));
    $this -> addDef(fie('act', 'Action'));

    $lMand = CCor_Res::extract('id', 'name_'.LAN, 'mand');
    $lMand = array(0 => 'Global') + $lMand;
    unset($lMand[999]);
    $this -> addDef(fie('mand', lan('lib.mand'), 'select', $lMand));
    
    $this -> addDef(fie('pos', 'Position'));
    $this -> addDef(fie('from_time', 'From'));
    $this -> addDef(fie('to_time', 'To'));

    $lArr[0] = 'always';
    $lArr[60] = 'every minute';
    $lArr[300] = 'every 5 minutes';
    $lArr[600] = 'every 10 minutes';
    $lArr[3600] = 'every hour';
    $lArr[SECS_PER_DAY] = 'once per day';
    $this -> addDef(fie('tick', 'Repeat', 'select', $lArr));

    $this -> addDef(fie('params', 'Paramters', 'params'));
    $this -> addDef(fie('dow', 'Week Days', 'bitset', array('dom' => 'dow')));
    $this -> addDef(fie('flags', 'Flags', 'bitset', array('dom' => 'svc')));
  }
  
  public function load($aId) {
    $lQry = new CCor_Qry('SELECT * FROM al_sys_svc WHERE id='.intval($aId));
    if ($lRow = $lQry -> getAssoc()) {
      $this -> assignVal($lRow);
    } else {
      $this -> msg('Record not found', mtUser, mlError);
    }
  }

}