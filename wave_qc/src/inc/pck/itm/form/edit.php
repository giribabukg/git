<?php
class CInc_Pck_Itm_Form_Edit extends CPck_Itm_Form_Base {

    public $mAdminView = '';
  
  public function __construct($aId, $aDom, $aFields, $aFieldCaptions, $aAdminView = FALSE) {
    $this -> mAdminView = $aAdminView;
    if ($this -> mAdminView) {
      $lUrl_Param = '&xx=1';
    } else {
      $lUrl_Param = '';
    }
    parent::__construct('pck-itm.sedt', lan('pck-itm.edt'), $aFields, $aFieldCaptions, 'pck-itm'.$lUrl_Param.'&dom='.$aDom);
    $this -> mId = intval($aId);
    $this -> setParam('val[id]', $this -> mId);
    $this -> setParam('old[id]', $this -> mId);
    $this -> setDom($aDom);
    $this -> load();
    $this -> setParam('mand', $this -> getVal('mand'));
  }

  protected function load() {
    $lQry = new CCor_Qry('SELECT * FROM al_pck_items WHERE mand IN(0,'.MID.') AND id='.$this -> mId);
    if ($lRow = $lQry -> getAssoc()) {
      $this -> assignVal($lRow);
    } else {
      $this -> msg('Record not found', mtUser, mlError);
    }
  }
}