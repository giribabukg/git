<?php
class CInc_Fie_Map_Item_Form extends CHtm_Form {

  public function __construct() {
    parent::__construct('', '');
    $this -> addDef(fie('alias',  'Alias'));
    $this -> addDef(fie('native',  'Native'));
    $this -> addDef(fie('default_value',  'Default'));

    $lItems = CApp_Filter::getTypes();
    $this -> addDef(fie('read_filter',  'Read Filter', 'valselect', array('lis' => $lItems)));
    $this -> addDef(fie('write_filter',  'Write Filter', 'valselect', array('lis' => $lItems)));

    if (CCor_Cfg::get('validate.available')) {
      $lArr = CCor_Res::extract('id', 'name', 'validate');
      $lArr = array(0 => '') + $lArr;
      $lPar['lis'] = $lArr;
      $this->addDef(fie('validate_rule', lan('validate.rule'), 'select', $lArr));
    }
  }

  protected function loadMap($aId) {
    $lId = intval($aId);
    $lSql = 'SELECT * FROM al_fie_map_master WHERE id='.$lId;
    $lQry = new CCor_Qry($lSql);
    return $lQry->getDat();
  }

  public function setMapId($aId) {
    $lMap = $this->loadMap($aId);
    if (!$lMap['has_native']) {
      unset($this->mFie['native']);
    }
    if (!$lMap['has_default']) {
      unset($this->mFie['default']);
    }
    if (!$lMap['has_read_filter']) {
      unset($this->mFie['read_filter']);
    }
    if (!$lMap['has_write_filter']) {
      unset($this->mFie['write_filter']);
    }
    if (!$lMap['has_validate_rule']) {
      unset($this->mFie['validate_rule']);
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
      $this->setMapId($lRow['map_id']);
    } else {
      $this -> msg('Record not found', mtUser, mlError);
    }
  }

}
