<?php
class CInc_Tab_Itm_List extends CHtm_List {

  protected $mModule = 'tab_slave';
  protected $mLowestId;
  protected $mHighestId;

  public function __construct($aTabType) {
    parent::__construct('tab-itm');

    $this -> mTabType = $aTabType;
    $this -> mName = CCor_Qry::getStr('SELECT name FROM al_'.$this -> mModule.' WHERE type="'.addslashes($this -> mTabType).'"');

    $this -> setAtt('width', '100%');
    $this -> mTitle = lan($this -> mModule.'.menu').' - '.htm($this -> mName);

    $this -> getPriv('tab');

    $this -> addCtr();
    $this -> addColumn('name', lan($this -> mModule.'.name'), FALSE);
    $this -> addColumn('link', lan($this -> mModule.'.link'), FALSE);
    $this -> addColumn('code', lan($this -> mModule.'.code'), FALSE);

    $this -> mDefaultOrder = 'id';

    if ($this -> mCanDelete) {
      $this -> addDel();
    }

    $this -> mStdLnk = 'index.php?act=tab-itm.edt&amp;type='.$this -> mTabType.'&amp;id=';
    $this -> mDelLnk = 'index.php?act=tab-itm.del&amp;type='.$this -> mTabType.'&amp;id=';

    $this -> addBtn(lan('lib.back'), "go('index.php?act=tab')", 'img/ico/16/back-hi.gif');
    $this -> addBtn(lan($this -> mModule.'.act.new'), "go('index.php?act=tab-itm.new&type=".$this -> mTabType."')", 'img/ico/16/plus.gif');

    $this -> getPrefs();

    $this -> mIte = new CCor_TblIte('al_'.$this -> mModule);
    $this -> mIte -> addCnd('mand IN (0, '.MID.')');
    $this -> mIte -> addCnd('type="'.addslashes($this -> mTabType).'"');

    $this -> mMaxLines = $this -> mIte -> getCount();
    if ($this -> mMaxLines <= $this -> mPage * $this -> mLpp) {
      $this -> mPage = 0;
      $this -> mFirst = 0;
    }

    $this -> mIte -> setOrder('id', 'asc');
    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);

    $this -> addPanel('nav', $this -> getNavBar());
    $this -> addPanel('vie', $this -> getViewMenu());
  }

  protected function getNavBar() {
    if (!$this -> mNavBar) {
      return '';
    }

    $lNav = new CHtm_NavBar($this -> mMod, $this -> mPage, $this -> mMaxLines, $this -> mLpp);
    $lNav -> setParam('type', $this -> mTabType);
    return $lNav -> getContent();
  }

  protected function getLink() {
    $lID = $this -> getVal('id');
    $lTypeID = $this -> getVal('type');

    return 'index.php?act=tab-itm.edt&amp;id='.$lID.'&amp;type='.$lTypeID;
  }
}