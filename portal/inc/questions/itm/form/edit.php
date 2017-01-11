<?php
class CInc_Questions_Itm_Form_Edit extends CQuestions_Itm_Form_Base {

  public function __construct($aId, $aDomain, $aCancel = NULL) {
    parent::__construct('questions-itm.sedt', lan('questions.itm.edt'), $aCancel);

    $this -> mId = intval($aId);
    $this -> setParam('val[id]', $this -> mId);
    $this -> setParam('old[id]', $this -> mId);
    $this -> setDomain($aDomain);
    $this -> load();
  }

  protected function load() {
    $lQry = new CCor_Qry('SELECT * FROM al_questions_items WHERE id='.$this -> mId);
    if ($lRow = $lQry -> getAssoc()) {
      $this -> assignVal($lRow);
    } else {
      $this -> msg('Record not found', mtUser, mlError);
    }
  }
}