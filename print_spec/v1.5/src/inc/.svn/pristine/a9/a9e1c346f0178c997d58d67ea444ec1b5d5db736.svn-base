<?php
class CInc_Fie_Learn_Form extends CHtm_Form {

  public function __construct($aAct, $aCaption) {
    parent::__construct($aAct, $aCaption);

    $lSql = 'SELECT DISTINCT(f1.learn) AS alias,f2.name_'.LAN.' FROM al_fie f1, al_fie f2 ';
    $lSql.= 'WHERE f1.mand='.MID.' AND f2.mand='.MID.' AND f1.learn=f2.alias ORDER BY f2.name_'.LAN;
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lArr[$lRow['alias']] = $lRow['name_'.LAN];
    }
    $this -> addDef(fie('alias', 'Selection', 'select', $lArr));
    $this -> addDef(fie('val', 'Value'));
  }

}