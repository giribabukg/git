<?php
class CInc_Tab_Form_Base extends CHtm_Form {

  protected $mModule = 'tab_master';

  public function __construct($aAct, $aCaption, $aCancel = NULL, $aStillAvailableTabTypes = NULL) {
    parent::__construct($aAct, $aCaption, $aCancel);

    $this -> addDef(fie('mand', '', 'hidden'));
    $this -> addDef(fie('name', lan($this -> mModule.'.name')));

    # if $aStillAvailableTabTypes is not an array we're obviously coming from Tab_Form_Edit and need to make it an array
    if (!is_array($aStillAvailableTabTypes)) {
      $aStillAvailableTabTypes = array($aStillAvailableTabTypes);
    }

    $lStillAvailableTabTypes = $aStillAvailableTabTypes;
    foreach ($lStillAvailableTabTypes as $lKey => $lValue) {
      $lStillAvailableTabTypes[$lValue] = lan($this -> mModule.'.type.'.$lValue);
      unset($lStillAvailableTabTypes[$lKey]);
    }
    $this -> addDef(fie('type', lan($this -> mModule.'.type'), 'select', $lStillAvailableTabTypes));

    $this -> setVal('mand', MID);
  }
}