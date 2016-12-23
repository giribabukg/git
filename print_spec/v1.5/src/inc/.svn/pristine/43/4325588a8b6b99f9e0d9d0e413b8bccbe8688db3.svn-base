<?php
class CInc_Fie_Map_Item_Form extends CHtm_Form {

  public function __construct() {
    parent::__construct('', '');
    $this -> addDef(fie('alias',  'Alias'));
    $this -> addDef(fie('native',  'Native'));
    $this -> addDef(fie('default_value',  'Default'));

    if (CCor_Cfg::get('validate.available')) {
      $lArr = CCor_Res::extract('id', 'name', 'validate');
      $lArr = array(0 => '') + $lArr;
      $lPar['lis'] = $lArr;
      $this->addDef(fie('validate_rule', lan('validate.rule'), 'select', $lArr));
    }
  }

  public function getForm() { // make this public
    $lRet = '<form>'.parent::getForm().'</form>';
    return $lRet;
  }

  public function load($aId) {
    $lQry = new CCor_Qry('SELECT * FROM al_fie_map_items WHERE id='.intval($aId));
    if ($lRow = $lQry -> getAssoc()) {
      $this -> assignVal($lRow);
    } else {
      $this -> msg('Record not found', mtUser, mlError);
    }
  }

}
