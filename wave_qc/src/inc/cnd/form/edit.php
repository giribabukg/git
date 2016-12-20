<?php
class CInc_Cnd_Form_Edit extends CInc_Cnd_Form_Base {

  /**
   * Constructor
   *
   * @param $aAct
   * @param $aCaption
   * @param $aCancel
   * @param $aCndId
   */
  public function __construct($aCndId) {
    parent::__construct('cnd.sedt', lan('cnd.edt'));

    $this -> mCndId = $aCndId;

    $this -> setParam('val[id]', $this -> mCndId);
    $this -> setParam('old[id]', $this -> mCndId);

    $this -> load();
  }

  /**
   * Load
   *
   * @access protected
   */
  protected function load() {
    $lQry = new CCor_Qry('SELECT * FROM al_cnd_master WHERE id='.$this -> mCndId);
    if ($lRow = $lQry -> getAssoc()) {
      $this -> assignVal($lRow);
    } else {
      $this -> msg(lan('cnd.notfound'), mtUser, mlError);
    }
  }

}