<?php
class CInc_Cor_Res_Reg extends CCor_Obj {

  protected $mRes;

  public function __construct() {
    $this -> addRes('fie', lan('fie.menu'));
    $this -> addRes('gru', lan('gru.menu'));
    $this -> addRes('usr', lan('usr.menu'));
    $this -> addRes('htb', lan('htb.menu'));
    $this -> addRes('mand', lan('mand.menu'));
    $this -> addRes('rol', lan('rol.menu'));
    $this -> addRes('tpl', lan('tpl.menu'));
    $this -> addRes('eve', lan('eve.menu'));
    $this -> addRes('crp', lan('crp.menu'));
    $this -> addRes('jfl', lan('jfl.menu'));
    $this -> addRes('pck', lan('pck.menu'));
    $this -> addRes('syspref', lan('prf.menu'));
  }

  protected function & addRes($aKey, $aName) {
    $lRes = new CCor_Dat();
    $lRes['key'] = $aKey;
    $lRes['cap'] = $aName;
    $lRes['par'] = NULL;
    $this -> mRes[$aKey] = & $lRes;
    return $lRes;
  }

  public function getResArray() {
    return $this -> mRes;
  }

  public function isValid($aKey) {
    return isset($this -> mRes[$aKey]);
  }

  public function & getType($aKey) {
    $lRet = NULL;
    if ($this -> isValid($aKey)) {
      $lRet = & $this -> mRes[$aKey];
    }
    return $lRet;
  }

}