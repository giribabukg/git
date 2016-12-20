<?php
class CInc_Htb_Itm_List extends CHtm_List {

  public function __construct($aDom) {
    parent::__construct('htb-itm');
    $this -> mDom = $aDom;
    $this -> setAtt('width', '100%');
    $this -> mName = CCor_Qry::getStr('SELECT description FROM al_htb_master WHERE domain="'.addslashes($this -> mDom).'"');
    $this -> mTitle = lan('htb-itm.menu').' - '.htm($this -> mName);
    $this -> getPriv('htb');

    $this -> addCtr();
    $this -> addColumn('value', lan('lib.key'), TRUE, array('width' => '16'));
    $this -> addColumn('value_'.LAN, lan('lib.value'), TRUE, array('width' => '100%'));
    $this -> mDefaultOrder = 'value_'.LAN;

    if ($this -> mCanDelete) {
      $this -> addDel();
    }

    $this -> mOrdLnk = 'index.php?act=htb-itm.ord&amp;dom='.$this -> mDom.'&amp;fie=';
    $this -> mStdLnk = 'index.php?act=htb-itm.edt&amp;dom='.$this -> mDom.'&amp;id=';
    $this -> mDelLnk = 'index.php?act=htb-itm.del&amp;dom='.$this -> mDom.'&amp;id=';

    $this -> addBtn(lan('lib.back'), "go('index.php?act=htb')", '<i class="ico-w16 ico-w16-back-hi"></i>');
    if ($this -> mCanInsert) {
      $this -> addBtn(lan('lib.new_item'), "go('index.php?act=htb-itm.new&dom=".$this -> mDom."')", '<i class="ico-w16 ico-w16-plus"></i>');
      $this -> addBtn(lan("lib.addMultiple"), "go('index.php?act=htb-itm.batchNew&dom=".$this -> mDom."')", '<i class="ico-w16 ico-w16-plus"></i>');
    }
    $this -> addBtn(lan("csv-exp"),"go('index.php?act=htb-itm.csvexp&dom=".$this -> mDom."')",'<i class="ico-w16 ico-w16-txtfile"></i>');

    $this -> getPrefs();

    $this -> mIte = new CCor_TblIte('al_htb_itm');
    $this -> mIte -> addCnd('domain="'.addslashes($this -> mDom).'"');
    $this -> mIte -> addCnd('mand IN(0,'.MID.')');
    $this -> mMaxLines = $this -> mIte -> getCount();

    if ($this -> mMaxLines <= $this -> mPage * $this -> mLpp) {
      $this -> mPage = 0;
      $this -> mFirst = 0;
    }

    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);
    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);

    $this -> addPanel('nav', $this -> getNavBar());
  }

  protected function getNavBar() {
    if (!$this -> mNavBar) {
      return '';
    }

    $lNav = new CHtm_NavBar($this -> mMod, $this -> mPage, $this -> mMaxLines, $this -> mLpp);
    $lNav -> setParam('dom', $this -> mDom);
    return $lNav -> getContent();
  }

}