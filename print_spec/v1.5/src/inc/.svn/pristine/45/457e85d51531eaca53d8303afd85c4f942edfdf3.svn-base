<?php
class CInc_App_Condition_Registry extends CCor_Obj {

  public function __construct() {
    $this -> addCond('simple',    'Simple',     'CApp_Condition_Simple');
    $this -> addCond('or',        'Or',         'CApp_Condition_Or');
    $this -> addCond('and',       'And',        'CApp_Condition_And');
    $this -> addCond('complex',   'Complex',    'CApp_Condition_Complex');
    $this -> addCond('aplstate',  'APL Loop',   'CApp_Condition_Aplstate');
    $this -> addCond('aplstates', 'APL States', 'CApp_Condition_Aplstates');
  }

  protected function addCond($aType, $aName, $aClass) {
    $lAct = new CCor_Dat();
    $lAct['type'] = $aType;
    $lAct['name'] = $aName;
    $lAct['class'] = $aClass;
    $this -> mReg[$aType] = & $lAct;
    return $lAct;
  }

  public function getConditions() {
    return $this -> mReg;
  }

  public function getCond($aKey) {
    $lRet = NULL;
    if ($this -> isValid($aKey)) {
      $lRet = $this -> mReg[$aKey];
    }
    return $lRet;
  }

  public function isValid($aType) {
    if (! isset($this -> mReg[$aType])) {
      $this -> dbg('Unknown Type '.$aType, mlWarn);
    }
    return isset($this -> mReg[$aType]);
  }

  public function factory($aType) {
    $lCond = $this -> getCond($aType);
    if (NULL == $lCond) {
      return NULL;
    }
    $lCls = $lCond['class'];
    return new $lCls($aContext);
  }

  public function loadFromDb($aId) {
    $lId = intval($aId);
    $lQry = new CCor_Qry('SELECT * FROM al_cond WHERE id='.$lId);
    if (! $lRow = $lQry -> getDat())
      return FALSE;
    $lCls = $this -> factory($lRow['type']);
    if (! is_object($lCls))
      return FALSE;
    $lCls -> setParams($lRow['params']);
    return $lCls;
  }

  public function loadFromDbByName($aName) {
    $lQry = new CCor_Qry('SELECT * FROM al_cond WHERE name='.esc($aName).' AND mand='.MID);
    if (! $lRow = $lQry -> getDat())
      return FALSE;
    $lCls = $this -> factory($lRow['type']);
    if (! is_object($lCls))
      return FALSE;
    $lCls -> setParams($lRow['params']);
    return $lCls;
  }
}