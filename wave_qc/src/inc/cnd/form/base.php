<?php
class CInc_Cnd_Form_Base extends CHtm_Form {

  /**
   * Constructor
   *
   * @param $aAct
   * @param $aCaption
   * @param $aCancel
   */
  public function __construct($aAct, $aCaption, $aCancel = NULL) {
    parent::__construct($aAct, $aCaption, $aCancel);

    $this -> addDef(fie('name', lan('cnd.name')));

    $lFlags = array('dom' => 'cnd');
    $this -> addDef(fie('flags', lan('cnd.flags'), 'bitset', $lFlags));
  }

}