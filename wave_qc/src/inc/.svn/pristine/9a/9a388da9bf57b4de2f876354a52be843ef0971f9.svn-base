<?php
class CInc_Hol_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('hol.menu');
    $lpn = 'hol';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdNone);
    }
  }

  protected function actStd() {
    $lUsr = CCor_Usr::getInstance();
    $lDay = $lUsr -> getPref('hol.day', date('Y-m-d'));
    $lCal = new CHtm_Cal('hol', $lDay);
    $lCal -> setWeekSelectEnabled(FALSE);
    $lCal -> setMonthSelectEnabled(FALSE);

    $lGrid = new CHtm_Grid(3,1);
    $lGrid -> setCnt(0,0, $lCal -> getContent());
    $lGrid -> setCnt(1,0, '<img src="img/d.gif" width="16" alt="" />');
    $lFrm = new CHol_Form($lDay);
    $lGrid -> setCnt(2,0, $lFrm -> getContent());

    $lRet = $lGrid -> getContent();
    $lRet.= BR;
    $lLis = new CHol_List(substr($lDay, 0, 4));
    $lRet.= $lLis -> getContent();

    $this -> render($lRet);
  }

  public function setMonthSelectEnabled($aFlag = TRUE) {

    $lRet = $lCal -> getContent().BR.BR;
    $this -> render($lRet);
  }

  protected function actDay() {
    $lDay  = $this -> mReq -> d;
    $lUsr = CCor_Usr::getInstance();
    $lUsr -> setPref('hol.day', $lDay);
    $this -> redirect();
  }

  protected function actSedt() {
    $lDay = $this -> getReq('day');
    $lMand = $this -> getReq('mand');
    $lLan = array();
    $this -> mAvailLang = CCor_Res::get('languages');
    foreach ($this -> mAvailLang as $lLang => $lName) {
      $lLan[$lLang] = $this -> getReq($lLang);
    }
    $lFree = $this -> getReq('free');
    $lFix = $this -> getReq('fix');

    $lSql = 'REPLACE INTO al_sys_holidays SET ';
    $lSql.= 'datum='.esc($lDay).',';
    $lSql.= 'mand='.esc($lMand).',';
    foreach ($lLan as $lLang => $lName) {
      $lSql.= backtick('name_'.$lLang).'='.esc($lName).',';
    }
    $lSql.= 'free='.esc($lFree).',';
    $lSql.= 'fix='.esc($lFix);

    CCor_Qry::exec($lSql);
    $this -> redirect();
  }

  protected function actDel() {
    $lDay = $this -> getReq('day');
    $lUsr = CCor_Usr::getInstance();
    $lUsr -> setPref('hol.day', $lDay);
    $lSql = 'DELETE FROM al_sys_holidays WHERE mand='.MID.' AND datum='.esc($lDay);
    CCor_Qry::exec($lSql);
    $this -> redirect();
  }

/*muss fuer mehrere Sprachen anders geloest werden!!
  protected function add($aDate, $aMand, $aEng, $aGer, $aFree, $aFix) {
    $lSql = 'INSERT INTO al_sys_holidays SET ';
    $lSql.= 'datum='.esc($aDate).',';
    $lSql.= 'mand='.esc($aMand).',';
    $this -> mAvailLang = CCor_Res::get('languages');
    foreach ($this -> mAvailLang as $lLang => $lName) {
      $lSql.= backtick('name_'.$lLang).'='.esc($lName).',';
    }
    $lSql.= 'name_de='.esc($aGer).',';
    $lSql.= 'name_en='.esc($aEng).',';
    $lSql.= 'free='.esc($aFree).',';
    $lSql.= 'fix='.esc($aFix);
    CCor_Qry::exec($lSql);
  }

  protected function actInsertstd() {
    $lY = $this -> mReq -> y;
    if (!empty($lY)) {
      $this -> add($lY.'-01-01', MID,'New Year', 'Neujahr', 'Y', 'Y');
      $this -> add($lY.'-01-06', MID,'Three Magi', 'Heilige Drei Könige', 'N', 'Y');
      $this -> add($lY.'-05-01', MID,'Labor Day', 'Tag der Arbeit', 'Y', 'Y');
      $this -> add($lY.'-08-15', MID,'Assumption Day', 'Maria Himmelfahrt', 'N', 'Y');
      $this -> add($lY.'-10-03', MID,'National Holiday', 'Tag d. deutschen Einheit', 'Y', 'Y');
      $this -> add($lY.'-11-01', MID,'All Hallows', 'Allerheiligen', 'Y', 'Y');
      $this -> add($lY.'-08-15', MID,'Assumption Day', 'Mariä Himmelfahrt', 'N', 'Y');
      $this -> add($lY.'-12-08', MID,'Conception', 'Mariä Empfängnis', 'N', 'Y');
      $this -> add($lY.'-12-24', MID,'Christmas Eve', 'Heiligabend', 'Y', 'Y');
      $this -> add($lY.'-12-25', MID,'Christmas Day', '1. Weihnachtstag', 'Y', 'Y');
      $this -> add($lY.'-12-26', MID,'Boxing Day', '2. Weihnachtstag', 'Y', 'Y');
      $this -> add($lY.'-12-31', MID,"New Year's Eve", 'Silvester', 'Y', 'Y');
    }
    $this -> redirect();
  }
*/
}