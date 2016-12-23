<?php
class CInc_Ldt_Facform extends CHtm_Form {
  
  public function __construct($aAct, $aCaption, $aCancel = NULL) {
    parent::__construct($aAct, $aCaption, $aCancel);
    $lFie = CCor_Res::extract('id', 'name_'.LAN, 'fie', 'pro,tpl,pac');
    
    for ($i=1; $i<6; $i++) {
      $this -> addDef(fie('fac'.$i, 'Factor '.$i, 'select', $lFie));
    }
  }
  
  public function load($aId) {
    $lId = intval($aId);
    $lSql = 'SELECT fac_cols FROM al_ldt_master WHERE id='.$lId;
    $lQry = new CCor_Qry($lSql);
    if ($lRow = $lQry -> getAssoc()) {
      $lVal = $lRow['fac_cols'];
      if (!empty($lVal)) {
        $lArr = explode(',', $lVal);
        $lCnt = 1;
        foreach ($lArr as $lFid) {
          $this -> setVal('fac'.$lCnt, $lFid);
          $lCnt++;
        }
      }
      $this -> setParam('id', $lId);
    } else {
      $this -> msg('Record not found', mtUser, mlError);
    }
  }
}