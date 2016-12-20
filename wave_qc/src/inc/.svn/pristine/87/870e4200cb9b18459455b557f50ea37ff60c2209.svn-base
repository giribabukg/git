<?php
class CInc_Tab_Itm_Form_Edit extends CTab_Itm_Form_Base {

  protected $mModule = 'tab_slave';

  public function __construct($aID, $aTabType) {
    parent::__construct('tab-itm.sedt', lan($this -> mModule.'.act.edit'), 'tab-itm&type='.$aTabType);

    $this -> mID = intval($aID);
    $this -> setParam('val[id]', $this -> mID);
    $this -> setParam('old[id]', $this -> mID);
    $this -> setTabType($aTabType);
    $this -> load();
    $this -> setParam('mand', $this -> getVal('mand'));
  }

  protected function load() {
    $lQry = new CCor_Qry('SELECT * FROM al_tab_slave WHERE mand IN (0,'.MID.') AND id='.$this -> mID);

    if ($lRow = $lQry -> getDat()) {
      $this -> assignVal($lRow);
    } else {
      $this -> msg('Record not found', mtUser, mlError);
    }
  }
}