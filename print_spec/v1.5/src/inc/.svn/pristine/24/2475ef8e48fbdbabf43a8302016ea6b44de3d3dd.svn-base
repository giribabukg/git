<?php
class CInc_Tab_Form_Edit extends CTab_Form_Base {

  protected $mModule = 'tab_master';

  public function __construct($aID) {
    $this -> mID = intval($aID);
    $lCurrentTabType = CCor_Qry::getStr('SELECT type FROM al_'.$this -> mModule.' WHERE mand='.MID.' AND id='.$this -> mID);

    parent::__construct('tab.sedt', lan($this -> mModule.'.act.edit'), NULL, $lCurrentTabType);

    $this -> setParam('val[id]', $this -> mID);
    $this -> setParam('old[id]', $this -> mID);
    $this -> load();
  }

  protected function load() {
    $lQry = new CCor_Qry('SELECT * FROM al_'.$this -> mModule.' WHERE mand='.MID.' AND id='.$this -> mID);
    if ($lRow = $lQry -> getDat()) {
      $this -> assignVal($lRow);
    } else {
      $this -> msg('Record not found', mtUser, mlError);
    }
  }
}