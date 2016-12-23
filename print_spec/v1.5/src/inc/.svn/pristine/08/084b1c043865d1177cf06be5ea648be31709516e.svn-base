<?php
class CInc_Tab_Form extends CHtm_Form {

  protected $mModule = 'tab_master';

  public function __construct($aAct, $aCaption, $aCancel = NULL, $aView = NULL) {
    parent::__construct($aAct, $aCaption, $aCancel);

    $this -> addDef(fie('mand', '', 'hidden'));
    $this -> addDef(fie('name', lan($this -> mModule.'.name')));

    $lArr['job'] = lan($this -> mModule.'.type.job');
    $lArr['mainmenu'] = lan($this -> mModule.'.type.mainmenu');
    if (!empty($aView)) {
      $lNewArr = array();
      foreach ($aView as $lKey => $lValue) {
        if ($lArr[$lValue]) {
          $lNewArr[$lValue] = $lArr[$lValue];
        }
      }
      $lArr = $lNewArr;
    }
    $this -> addDef(fie('type', lan($this -> mModule.'.type'), 'select', $lArr));

    $this -> setVal('mand', MID);
  }

  public function load($aId) {
    $lId = intval($aId);
    $lSql = 'SELECT * FROM al_'.$this -> mModule.' WHERE mand='.MID.' AND id='.$lId;
    $lQry = new CCor_Qry($lSql);
    if ($lRow = $lQry -> getDat()) {
      $this -> assignVal($lRow);
      $this -> setParam('old[id]', $lId);
      $this -> setParam('val[id]', $lId);
    } else {
      $this -> msg('Record not found', mtUser, mlError);
    }
  }

}