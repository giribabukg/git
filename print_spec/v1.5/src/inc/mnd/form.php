<?php
class CInc_Mnd_Form extends CHtm_Form {

  public function __construct($aAct, $aCaption, $aCancel = NULL) {
    parent::__construct($aAct, $aCaption, $aCancel);

    $this -> setAtt('class', 'tbl w700');
    $this -> addDef(fie('id', 'Mand ID'));
    $this -> addDef(fie('code', 'Mand Code'));
    $this -> addDef(fie('name_en', 'English Name'));
    $this -> addDef(fie('name_de', 'German Name'));
    $this -> addDef(fie('pass', 'Mandator Admin Password'));

    // If the action is new, than add the following field options to the form.
    if (strpos($this -> mAct, '.snew')) {
      $lArr = CCor_Res::extract('id', 'name_'.LAN, 'mand');
      $this -> addDef(fie('copy', 'Copy from', 'select', $lArr));
      // Collect Infos to the cfg File  -- This section will be moves soon to the configuration frontend-interface.
      $this -> addDef(fie('repNr', 'KNR_Repro'));
      $this -> addDef(fie('artNr', 'KNR_ART'));
      $this -> addDef(fie('appName', 'Alink Username'));

      $this -> addDef(fie('sysPref','Update "al_sys_pref" Table','boolean'));
      $this -> addDef(fie('alFie','Update "al_fie" Table','boolean'));
  	}
  }

  public function load($aId) {
    $lId = intval($aId);
    $lSql = 'SELECT * FROM al_sys_mand WHERE id='.$lId;
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