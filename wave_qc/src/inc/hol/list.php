<?php
class CInc_Hol_List extends CHtm_List {

  public function __construct($aYear) {
    parent::__construct('hol');
    $this -> mYear = intval($aYear);

    $this -> mStdLnk = 'index.php?act=hol.day&amp;d=';
    $this -> mDelLnk = 'index.php?act=hol.del&amp;d=';
    $this -> mIdField = 'datum';

    $this -> setAtt('width', '100%');
    $this -> mTitle = lan('hol.menu').' '.$aYear;

    $this -> addCtr();
    $this -> addColumn('datum', lan('lib.date'),  FALSE, array('width' => '10%'));
    $this -> addColumn('mand', lan('lib.mand'),  TRUE, array('width' => '10%'));
    $this -> mAvailLang = CCor_Res::get('languages');
    foreach ($this -> mAvailLang as $lLang => $lName) {
      $this -> addColumn('name_'.$lLang, lan('lan.'.$lLang),  TRUE);
    }
    $this -> addColumn('free', lan('lib.free'),  TRUE, array('width' => '16'));
    if ($this -> mCanDelete) {
      $this -> addDel();
    }

    $lUsr = CCor_Usr::getInstance();
    $this -> mOrd  = $lUsr -> getPref('hol.ord', 'datum');
    $this -> mDir  = 'asc';
    if (substr($this -> mOrd, 0, 1) == '-') {
      $this -> mOrd = substr($this -> mOrd,1);
      $this -> mDir = 'desc';
    }

    $this -> mIte = new CCor_TblIte('al_sys_holidays');
    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);
    $this -> mIte -> set2Order('mand', 'asc');
    $this -> mIte -> addCnd('YEAR(datum)='.$this -> mYear);

    $lUrl = esc('index.php?act=hol.insertstd&y='.$this -> mYear);
   # $this -> addBtn(htm('Insert Standard Holidays for '.$this -> mYear), "go($lUrl)", 'img/ico/16/mt-4.gif');

  }

  protected function getLink() {
    $lId = $this -> getVal('datum');
    return $this -> mStdLnk.$lId;
  }

  protected function getTdDatum() {
    $lTag = new CHtm_Tag('td');
    $lTag -> setAtt('class', $this -> mCls.' nw');
    $lTag -> setAtt('style', 'text-align:right');
    $lVal = $this -> getVal('datum');
    $lDat = new CCor_Date($lVal);
    $lTag -> setCnt($lDat -> getFmt(lan('lib.date.week')));
    return $lTag -> getContent();
  }

  protected function getTdMand() {
    $lTag = new CHtm_Tag('td');
    $lTag -> setAtt('class', $this -> mCls.' ac nw');
    $lVal = $this -> getVal($this -> mColKey);
    if (empty($lVal)) {
      $lRet = "Global";
    } elseif ($lVal == -1) {
      $lRet = lan('lib.mand.all');
    }else {
      $lArr = CCor_Res::extract('id', 'name_'.LAN, 'mand');
      $lRet = $lArr[$lVal];
    }

    $lRet = htm($lRet);
    $lTag -> setCnt($lRet);
    return $lTag -> getContent();
  }

}