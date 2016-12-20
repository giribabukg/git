<?php
class CInc_Tpl_Form extends CHtm_Form {

  public function __construct($aAct, $aCaption, $aCancel = NULL) {
    parent::__construct($aAct, $aCaption, $aCancel);
    $this ->setAtt('class', 'tbl w700');
    $lArr[0] = '[Alle]';
    $lArr[MID] = MANDATOR_NAME;
    $this -> addDef(fie('mand', lan('lib.mand'), 'select', $lArr));
    $lAvailLang = CCor_Res::get('languages');
    $lArr = '';
    foreach ($lAvailLang as $lLang => $lName) {
      $lArr[$lLang] = lan('lan.'.$lLang);
    }
    $this -> addDef(fie('lang', lan('lib.lang'), 'select', $lArr));
    $this -> addDef(fie('name', 'Internal Name', 'string', NULL, array('class' => 'inp w400')));
    $this -> addDef(fie('subject', lan('lib.sbj'), 'string', NULL, array('class' => 'inp w400')));
    $this -> addDef(fie('msg', lan('lib.msg'), 'memo', NULL, array('class' => 'inp w400', 'rows' => '10')));

    $this -> setVal('mand', 0); // als Default bei neuem Eintrag 0 = F�r alle Mandanten
  }

  public function load($aId) {
    $lId = intval($aId);
    $lSql = 'SELECT * FROM al_eve_tpl WHERE mand IN(0,'.MID.') AND id='.$lId;
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