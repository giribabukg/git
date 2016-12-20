<?php
class CInc_Cnd_Itm_Form_Edit extends CInc_Cnd_Itm_Form_Base {

  /**
   * Constructor
   *
   * @param $aCndId
   * @param $aCndItmId
   */
  public function __construct($aCndId, $aCndItmId) {
    parent::__construct('cnd-itm.sedt', lan('cnd-itm.edt'), 'cnd-itm&cnd_id='.$aCndItmId);

    $this -> mCndId = $aCndId;
    $this -> mCndItmId = $aCndItmId;
    
    $this -> setParam('id', $this -> mCndId);
    $this -> setParam('val[id]', $this -> mCndId);
    $this -> setParam('old[id]', $this -> mCndId);

    $this -> setParam('cnd_id', $this -> mCndItmId);
    $this -> setParam('val[cnd_id]', $this -> mCndItmId);
    $this -> setParam('old[cnd_id]', $this -> mCndItmId);
    
    $this -> load();
 }

  /**
   * Load
   *
   * @access protected
   */
  protected function load() {
    $lQry = new CCor_Qry('SELECT * FROM al_cnd_items WHERE id='.$this -> mCndId);
    if ($lRow = $lQry -> getAssoc()) {
      $this -> assignVal($lRow);
    } else {
      $this -> msg(lan('cnd-itm.notfound'), mtUser, mlError);
    }
  }

}