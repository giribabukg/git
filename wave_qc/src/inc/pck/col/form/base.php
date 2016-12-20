<?php
class CInc_Pck_Col_Form_Base extends CHtm_Form {

  public function __construct($aAct, $aDom, $aCaption, $aCancel = NULL) {
    parent::__construct($aAct, $aCaption, $aCancel);

    $lArr = array();
    $lArr[0] = '[All]';
    $lArr[MID] = MANDATOR_NAME;
    $this -> addDef(fie('mand', lan('lib.mand'), 'select', $lArr ));
    $this -> addDef(fie('alias', lan('lib.field'), 'resselect', array('res' => 'fie', 'key' => 'alias', 'val' => 'name_'.LAN)));
    $lArr = array();
    for ($i=1; $i<26; $i++) {
      $lArr[$i] = $i;
    }
    $this -> addDef(fie('col', lan('pck.column'), 'select', $lArr, array('class' => 'w50')));
    $this -> addDef(fie('position', lan('lib.pos'), 'select', $lArr, array('class' => 'w50')));
    $this -> addDef(fie('hidden', lan('lib.hidden'), 'select', array('N' => lan('lib.no'), 'Y' => lan('lib.yes'))));
    $this -> addDef(fie('image', lan('lib.img'), 'select', array('N' => lan('lib.no'), 'Y' => lan('lib.yes'))));
    $this -> addDef(fie('color', lan('lib.color'), 'select', array('N' => lan('lib.no'), 'Y' => lan('lib.yes'))));
    $this -> addDef(fie('ignoretype', lan('pck.ignoretype'), 'select', array('N' => lan('lib.no'), 'Y' => lan('lib.yes'))));
    $lHtb = array('' => ' ') + CCor_Res::extract('domain', 'description', 'htbmaster');
    $this -> addDef(fie('htb', lan('htb.menu'), 'select', $lHtb));
    $this -> addDef(fie('domain', '', 'hidden'));
    $this -> setVal('domain', $aDom); // als Default bei neuem Eintrag
    $this -> setVal('mand', MID); // als Default bei neuem Eintrag
  }

  public function setDom($aDom) {
    $this -> setParam('dom', $aDom);
    $this -> setParam('val[domain]', $aDom);
    $this -> setParam('old[domain]', $aDom);
/*
    $lSql = 'SELECT description_'.LAN.' FROM al_pck_master WHERE mand="'.MID.'" AND domain="'.addslashes($aDom).'"';
    if ($lCap = CCor_Qry::getStr($lSql)) {
      $this -> mCap.= ' ('.$lCap.')'; //Überschrift
    }
*/
  }

}