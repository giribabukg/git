<?php
class CInc_Cnd_Itm_Form_Base extends CHtm_Form {

  /**
   * Constructor
   *
   * @param $aAct
   * @param $aCaption
   * @param $aCancel
   */
  public function __construct($aAct, $aCaption, $aCancel = NULL) {
    parent::__construct($aAct, $aCaption, $aCancel);

    $this -> addDef(fie('field', lan('cnd-itm.field'), 'resselect', array('res' => 'fie', 'key' => 'alias', 'val' => 'name_'.LAN)));

    $lOperations = array('dom' => 'op');
    $this -> addDef(fie('operator', lan('cnd-itm.operator'), 'tselect', $lOperations));

    $this -> addDef(fie('value', lan('cnd-itm.value')));

    $lConjucntions = array('dom' => 'con');
    $this -> addDef(fie('conjunction', lan('cnd-itm.conjunction'), 'tselect', $lConjucntions));
  }

  /**
   * setCndId
   *
   * @param $aCndItmId
   */
  public function setCndId($aCndItmId) {
    $this -> mCndItmId = $aCndItmId;

    $this -> setParam('cnd_id', $this -> mCndItmId);
    $this -> setParam('val[cnd_id]', $this -> mCndItmId);
    $this -> setParam('old[cnd_id]', $this -> mCndItmId);
  }

}